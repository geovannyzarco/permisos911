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

        return $fechaDesde->lessThan($fechaHasta);
    }

    /**
     * Valida que la hora de inicio (desde) y hora de fin (hasta) no sean ambas 00:00.
     * Devuelve true si el rango es válido (no son ambas 00:00), de lo contrario false.
     *
     * @param string|Carbon|null $desde
     * @param string|Carbon|null $hasta
     * @return bool
     */
    public function validarHorasNoCero($desde, $hasta): bool
    {
        if (!$desde || !$hasta) {
            return true;
        }

        $timeDesde = Carbon::parse($desde)->format('H:i');
        $timeHasta = Carbon::parse($hasta)->format('H:i');

        return !($timeDesde === '00:00' && $timeHasta === '00:00');
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

        // Si el empleado no tiene grupo, pertenece al grupo especial "Sin Grupo" (ID 12)
        // o el grupo no tiene límite configurado, la validación pasa automáticamente.
        if (!$grupo || $grupo->id == 12 || empty($grupo->permisos_diarios)) {
            return true;
        }

        $limite = $grupo->permisos_diarios;

        $fechaDesde = Carbon::parse($desde)->startOfDay();
        $fechaHasta = Carbon::parse($hasta)->startOfDay();

        // Generamos el periodo de días que abarca el permiso solicitado
        $periodo = \Carbon\CarbonPeriod::create($fechaDesde, $fechaHasta);

        foreach ($periodo as $fecha) {
            // MODIFICACIÓN: Cambiamos de contar permisos totales a contar usuarios distintos del mismo grupo.
            // Obtenemos los IDs únicos de empleados que ya tienen permisos registrados en este día.
            $empleadosConPermiso = Permiso::query()
                ->whereHas('empleado', function ($query) use ($grupo) {
                    $query->where('grupo_id', $grupo->id);
                })
                ->whereDate('desde', '<=', $fecha)
                ->whereDate('hasta', '>=', $fecha)
                ->when($permisoActual, function ($query) use ($permisoActual) {
                    $query->where('id', '!=', $permisoActual->id);
                })
                // Excluimos permisos rechazados (estado 5) ya que no restan cupo
                ->where('id_estado_aprobacion', '!=', 5)
                ->pluck('empleado_id')
                ->unique()
                ->values()
                ->toArray();

            $totalEmpleados = count($empleadosConPermiso);
            
            // Si el empleado actual no está en la lista de empleados que ya tienen permiso,
            // esta nueva solicitud agregará un nuevo empleado a la lista para ese día.
            if (!in_array($empleado->id, $empleadosConPermiso)) {
                $totalEmpleados += 1;
            }

            // Validamos contra el límite de empleados distintos permitidos
            if ($totalEmpleados > $limite) {
                throw new DomainException(
                    "No se puede registrar el permiso. El día {$fecha->format('d/m/Y')} excede el límite de {$limite} empleados distintos con permisos diarios permitidos para el grupo '{$grupo->nombre}'."
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

        // Omitimos la verificación si no hay grupo, es el grupo "Sin Grupo" (ID 12) o faltan datos
        if (!$grupo || $grupo->id == 12 || empty($grupo->permisos_diarios) || empty($desde) || empty($hasta)) {
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
            // MODIFICACIÓN: Contamos empleados distintos en lugar de permisos totales.
            $empleadosConPermiso = Permiso::query()
                ->whereHas('empleado', function ($query) use ($grupo) {
                    $query->where('grupo_id', $grupo->id);
                })
                ->whereDate('desde', '<=', $fecha)
                ->whereDate('hasta', '>=', $fecha)
                ->when($permisoActual, function ($query) use ($permisoActual) {
                    $query->where('id', '!=', $permisoActual->id);
                })
                // Excluimos permisos rechazados (estado 5)
                ->where('id_estado_aprobacion', '!=', 5)
                ->pluck('empleado_id')
                ->unique()
                ->values()
                ->toArray();

            $totalEmpleados = count($empleadosConPermiso);
            if (!in_array($empleado->id, $empleadosConPermiso)) {
                $totalEmpleados += 1;
            }

            $resultado[] = [
                'fecha' => $fecha->format('d/m/Y'),
                'ocupados' => count($empleadosConPermiso),
                'limite' => $limite,
                'disponible' => $totalEmpleados <= $limite
            ];
        }

        return $resultado;
    }

    /**
     * NUEVO MÉTODO: Valida que el empleado no tenga otro permiso que se traslape
     * en horario (coincida total o parcialmente) con el rango de fechas solicitado.
     * Esta validación es estricta y se ejecuta siempre para evitar inconsistencias estadísticas.
     *
     * @param Empleado $empleado
     * @param string|Carbon $desde
     * @param string|Carbon $hasta
     * @param Permiso|null $permisoActual Para no contarlo si se está editando
     * @return bool
     * @throws DomainException
     */
    public function validarNoTraslapeHoras(
        Empleado $empleado,
        $desde,
        $hasta,
        ?Permiso $permisoActual = null
    ): bool {
        // MODIFICACIÓN: Para bases de datos que almacenan fecha/hora con offset (ej: datetimeoffset en SQL Server),
        // debemos pasar los valores formateados explícitamente con su offset timezone ('Y-m-d H:i:s P') en lugar de
        // objetos Carbon crudos, ya que Laravel los serializa a UTC en las consultas sql perdiendo la congruencia local.
        $fechaDesdeStr = Carbon::parse($desde)->format('Y-m-d H:i:s P');
        $fechaHastaStr = Carbon::parse($hasta)->format('Y-m-d H:i:s P');

        // Buscamos si existe algún permiso para el mismo empleado que se traslape con este rango de tiempo.
        // Se excluyen los permisos rechazados (estado 5).
        $traslape = Permiso::query()
            ->where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion', '!=', 5)
            ->where(function ($query) use ($fechaDesdeStr, $fechaHastaStr) {
                // Condición de traslape: inicio_existente < fin_solicitado AND fin_existente > inicio_solicitado
                $query->where('desde', '<', $fechaHastaStr)
                      ->where('hasta', '>', $fechaDesdeStr);
            })
            ->when($permisoActual, function ($query) use ($permisoActual) {
                $query->where('id', '!=', $permisoActual->id);
            })
            ->first();

        if ($traslape) {
            $desdeStr = Carbon::parse($traslape->desde)->format('d/m/Y H:i');
            $hastaStr = Carbon::parse($traslape->hasta)->format('d/m/Y H:i');
            throw new DomainException(
                "El empleado ya tiene un permiso registrado para este horario que se traslapa con la solicitud actual (Permiso #{$traslape->id} desde {$desdeStr} hasta {$hastaStr})."
            );
        }

        return true;
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
                'La fecha/hora "hasta" debe ser mayor que "desde".'
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
        // MODIFICACIÓN: Usamos DATEDIFF (Sintaxis de SQL Server 2012) y filtramos solo por permisos aprobados (ID 3)
        $minutosUsados = Permiso::query()
            ->where('empleado_id', $empleado->id)
            ->where('tipo_permiso_id', 1)
            ->where('id_estado_aprobacion', 3) // Solo permisos ya aprobados restan del saldo
            ->whereYear('desde', now()->year)
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

    /**
     * INICIO CAMBIO: Validación de antigüedad máxima de 6 meses para periodos compensados
     *
     * Valida que la fecha "desde" de cada periodo compensado no supere los 6 meses de antigüedad.
     * Si la opción de ignorar validaciones (retroactivo) está activa, la validación se omite.
     *
     * @param array $compensados
     * @param bool $ignorarValidaciones
     * @return bool
     * @throws DomainException
     */
    public function validarAntiguedadCompensados(array $compensados, bool $ignorarValidaciones = false): bool
    {
        // Si el administrador activó la opción retroactiva, omitir la validación de fecha
        if ($ignorarValidaciones) {
            return true;
        }

        // Definir el límite de antigüedad (6 meses atrás a partir del inicio del día actual)
        $limiteSeisMeses = now()->subMonths(6)->startOfDay();

        foreach ($compensados as $item) {
            if (isset($item['desde']) && !empty($item['desde'])) {
                $desdeCompensado = Carbon::parse($item['desde'])->startOfDay();

                // Si la fecha desde del compensado es anterior al límite de 6 meses, arrojar error
                if ($desdeCompensado->lessThan($limiteSeisMeses)) {
                    $fechaFormateada = Carbon::parse($item['desde'])->format('d/m/Y');
                    throw new DomainException(
                        "El periodo compensado con fecha inicial {$fechaFormateada} excede el límite de 6 meses de antigüedad permitido."
                    );
                }
            }
        }

        return true;
    }
    // FIN CAMBIO

    /**
     * Valida que no haya más de 2 empleados con la categoría 24 (Telefonista)
     * con permisos activos o pendientes en cada día del rango solicitado.
     *
     * @param Empleado $empleado
     * @param string|Carbon $desde
     * @param string|Carbon $hasta
     * @param Permiso|null $permisoActual
     * @return bool
     * @throws DomainException
     */
    public function validarLimiteTelefonistas(
        Empleado $empleado,
        $desde,
        $hasta,
        ?Permiso $permisoActual = null
    ): bool {
        // Solo aplica si el empleado tiene la categoría 24
        if ($empleado->categoria_id != 24) {
            return true;
        }

        $limite = 2;
        $fechaDesde = Carbon::parse($desde)->startOfDay();
        $fechaHasta = Carbon::parse($hasta)->startOfDay();

        $periodo = \Carbon\CarbonPeriod::create($fechaDesde, $fechaHasta);

        foreach ($periodo as $fecha) {
            // Obtenemos los IDs de empleados distintos con categoría 24 que ya tienen permisos ese día
            $empleadosConPermiso = Permiso::query()
                ->whereHas('empleado', function ($query) {
                    $query->where('categoria_id', 24);
                })
                ->whereDate('desde', '<=', $fecha)
                ->whereDate('hasta', '>=', $fecha)
                ->when($permisoActual, function ($query) use ($permisoActual) {
                    $query->where('id', '!=', $permisoActual->id);
                })
                // Excluimos rechazados (estado 5)
                ->where('id_estado_aprobacion', '!=', 5)
                ->pluck('empleado_id')
                ->unique()
                ->values()
                ->toArray();

            $totalEmpleados = count($empleadosConPermiso);
            if (!in_array($empleado->id, $empleadosConPermiso)) {
                $totalEmpleados += 1;
            }

            if ($totalEmpleados > $limite) {
                throw new DomainException(
                    "No se puede registrar el permiso. El día {$fecha->format('d/m/Y')} excede el límite de {$limite} empleados telefonistas permitidos con permiso diario."
                );
            }
        }

        return true;
    }
}
