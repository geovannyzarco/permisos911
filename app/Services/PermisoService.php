<?php

namespace App\Services;

use App\Models\Permiso;
use App\Models\Empleado;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;
use App\Models\PermisoHistorial;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;

class PermisoService
{
    /**
     * Valida que la fecha "desde" no sea mayor que la fecha "hasta".
     * Devuelve true si el rango es válido (desde <= hasta), de lo contrario false.
     * Útil para usarlo en las reglas de validación de los formularios de Filament.
     *
     * @param string|Carbon|null $desde
     * @param string|Carbon|null $hasta
     * @return bool
     */
    public function validarRangoFechas($desde, $hasta): bool
    {
        if (!$desde || !$hasta) {
            return false;
        }

        $fechaDesde = Carbon::parse($desde);
        $fechaHasta = Carbon::parse($hasta);

        return $fechaDesde->lessThanOrEqualTo($fechaHasta);
    }

    /**
     * Valida que no se exceda el límite de permisos diarios configurado para el grupo del empleado.
     * Si en alguno de los días del rango solicitado ya se alcanzó el límite, lanza una excepción.
     *
     * @param Empleado $empleado
     * @param string|Carbon $desde
     * @param string|Carbon $hasta
     * @param Permiso|null $permisoActual Para no contarlo si se está editando
     * @return bool
     * @throws DomainException
     */
    public function validarLimitePermisosDiarios(
        Empleado $empleado,
        $desde,
        $hasta,
        ?Permiso $permisoActual = null
    ): bool {
        $grupo = $empleado->grupo;

        // Si el empleado no tiene grupo o el grupo no tiene límite configurado, la validación pasa
        if (!$grupo || empty($grupo->permisos_diarios)) {
            return true;
        }

        $limite = $grupo->permisos_diarios;

        $fechaDesde = Carbon::parse($desde)->startOfDay();
        $fechaHasta = Carbon::parse($hasta)->startOfDay();

        // Generamos el periodo de días que abarca el permiso solicitado
        $periodo = \Carbon\CarbonPeriod::create($fechaDesde, $fechaHasta);

        foreach ($periodo as $fecha) {
            // Contar cuántos permisos activos/existentes hay en este día para este grupo
            $permisosEnEsteDia = Permiso::query()
                ->whereHas('empleado', function ($query) use ($grupo) {
                    $query->where('grupo_id', $grupo->id);
                })
                ->whereDate('desde', '<=', $fecha)
                ->whereDate('hasta', '>=', $fecha)
                ->when($permisoActual, function ($query) use ($permisoActual) {
                    $query->where('id', '!=', $permisoActual->id);
                })
                // Aquí podrías agregar un filtro adicional si tienes un estado "Rechazado" o "Cancelado"
                // para que esos no sumen al límite. Ejemplo: ->whereNotIn('id_estado_aprobacion', [ESTADO_RECHAZADO])
                ->count();

            if ($permisosEnEsteDia >= $limite) {
                throw new DomainException(
                    "No se puede registrar el permiso. El día {$fecha->format('d/m/Y')} excede el límite de {$limite} permisos diarios permitidos para el grupo '{$grupo->nombre}'."
                );
            }
        }

        return true;
    }

    /**
     * Calcula la disponibilidad diaria de cupos para el grupo del empleado
     * en el rango de fechas proporcionado, útil para mostrar en la interfaz.
     */
    public function obtenerDisponibilidadDiaria(
        Empleado $empleado, 
        string $desde, 
        string $hasta, 
        ?Permiso $permisoActual = null
    ): array {
        $resultado = [];
        $grupo = $empleado->grupo;

        if (!$grupo || empty($grupo->permisos_diarios) || empty($desde) || empty($hasta)) {
            return $resultado;
        }

        $limite = $grupo->permisos_diarios;
        $fechaDesde = Carbon::parse($desde)->startOfDay();
        $fechaHasta = Carbon::parse($hasta)->startOfDay();

        if ($fechaHasta->lessThan($fechaDesde)) {
            return $resultado;
        }

        $periodo = \Carbon\CarbonPeriod::create($fechaDesde, $fechaHasta);
        
        // Limitamos a 31 días máximo para evitar consultas inmensas en caso de errores de usuario
        if ($periodo->count() > 31) {
            return ['error' => 'El rango de fechas es muy amplio.'];
        }

        foreach ($periodo as $fecha) {
            $permisosEnEsteDia = Permiso::query()
                ->whereHas('empleado', function ($query) use ($grupo) {
                    $query->where('grupo_id', $grupo->id);
                })
                ->whereDate('desde', '<=', $fecha)
                ->whereDate('hasta', '>=', $fecha)
                ->when($permisoActual, function ($query) use ($permisoActual) {
                    $query->where('id', '!=', $permisoActual->id);
                })
                ->count();

            $resultado[] = [
                'fecha' => $fecha->format('d/m/Y'),
                'ocupados' => $permisosEnEsteDia,
                'limite' => $limite,
                'disponible' => $permisosEnEsteDia < $limite
            ];
        }

        return $resultado;
    }

    /**
     * Valida y calcula las horas de un permiso personal.
     * Devuelve las horas calculadas si todo es válido.
     * Lanza excepciones de dominio si algo falla.
     */
    public function validarPermisoPersonal(
        Empleado $empleado,
        Carbon $desde,
        Carbon $hasta
    ): float {
        // 1. Validar rango horario
        if (!$this->validarRangoFechas($desde, $hasta)) {
            throw new DomainException(
                'La fecha/hora "hasta" debe ser mayor o igual que "desde".'
            );
        }

        // 2. Calcular horas reales
        $horasSolicitadas = $desde->diffInMinutes($hasta) / 60;

        // 3. Validar saldo disponible
        if (! $this->puedeGuardarPermisoPersonal($empleado, $horasSolicitadas)) {
            throw new DomainException(
                'El tiempo solicitado excede el saldo de horas personales disponibles.'
            );
        }

        return $horasSolicitadas;
    }


    /**
     * Obtiene el resumen de horas personales (asignadas, usadas y disponibles) de un empleado.
     */
    public function obtenerResumenHorasPersonales(Empleado $empleado, ?Permiso $permisoActual = null): array
    {
        // 1. Obtenemos las horas personales configuradas en el horario del empleado.
        $horasAsignadas = $empleado->horario?->horas_personales ?? 0;

        // 2. Calculamos los minutos que el empleado ya ha utilizado en otros permisos.
        $minutosUsados = Permiso::query()
            ->where('empleado_id', $empleado->id)
            ->where('tipo_permiso_id', 1)
            ->whereYear('desde', now()->year)
            ->when(
                $permisoActual,
                fn ($q) => $q->where('id', '!=', $permisoActual->id)
            )
            ->whereNotNull('desde')
            ->whereNotNull('hasta')
            ->selectRaw('SUM(DATEDIFF(MINUTE, desde, hasta)) as total')
            ->value('total') ?? 0;

        // 3. Convertimos los minutos utilizados a horas
        $horasUsadas = $minutosUsados / 60;
        
        // 4. Calculamos las horas disponibles
        $horasDisponibles = max(0, $horasAsignadas - $horasUsadas);

        return [
            'asignadas' => $horasAsignadas,
            'usadas' => round($horasUsadas, 2),
            'disponibles' => round($horasDisponibles, 2),
        ];
    }

    /**
     * Valida si el empleado tiene saldo suficiente de horas personales para solicitar/guardar un permiso.
     */
    public function puedeGuardarPermisoPersonal(
        Empleado $empleado,
        float $horasSolicitadas,
        ?Permiso $permisoActual = null
    ): bool {
        $resumen = $this->obtenerResumenHorasPersonales($empleado, $permisoActual);

        // Verificamos si las horas solicitadas son menores o iguales a las disponibles
        return $horasSolicitadas <= $resumen['disponibles'];
    }

    public function aprobarVB(Permiso $permiso, int $estadoId, User $user): void
    {
        if (!$user->can('aprobarVB', $permiso)){
            throw new AuthorizationException();
        }

        $permiso->update([
            'id_estado_vb' => $estadoId,
            'fecha_vb' => now(),
            'id_jefe_vb' => $user->empleado->id,
        ]);
    }

    public function aprobarFinal(Permiso $permiso, int $estadoId, User $user, ?string $comentario = null, ?Request $request = null ): void
    {
        // Verificar permiso de aprobación final
        if (!$user->can('aprobarFinal', $permiso)){
            throw new AuthorizationException();
        }

        DB::transaction(function () use ($permiso, $estadoId, $user, $comentario, $request) {
            //actualizar estado del permiso
            $permiso->update([
                'id_estado_aprobacion' => $estadoId,
                'fecha_aprobacion' => now(),
                'id_jefe_aprobacion' => $user->empleado->id,
            ]);

            // SOLO estados finales
            if(in_array($estadoId, [3,5])){
                $empleado = $user->empleado;

                PermisoHistorial::create([
                    'permiso_id' => $permiso->id,
                    'tipo_evento' => 'APROBACION_FINAL',
                    'empleado_id' => $empleado->id,
                    'empleado_oni' => $empleado->oni,
                    'empleado_nombre' => $empleado->nombre,

                    'division_id' => optional($empleado->division)->id,
                    'division_nombre' => optional($empleado->division)->nombre,

                    'unidad_id' => optional($empleado->unidad)->id,
                    'unidad_nombre' => optional($empleado->unidad)->nombre,
                    'grupo_id' => optional($empleado->grupo)->id,
                    'grupo_nombre' => optional($empleado->grupo)->nombre,

                    'tipo_permiso_id' => $permiso->tipo_permiso_id,
                    'tipo_permiso_nombre' => $permiso->tipoPermiso->nombre,

                    'desde' => $permiso->desde,
                    'hasta' => $permiso->hasta,
                    'duracion' => $permiso->duracion,

                    'motivo' => $permiso->motivo,
                    'adjunto' => $permiso->adjunto,

                    'estado_id' => $estadoId,
                    'estado_nombre' => $permiso->estadoAprobado->nombre,

                    'comentario' => $comentario,

                    'fecha_evento' => now(),

                    'ip' => $request?->ip(),
                    'user_agent' => $request?->userAgent(),
                ]);
            }
        });
    }
}
