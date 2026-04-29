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
        if ($hasta->lessThanOrEqualTo($desde)) {
            throw new DomainException(
                'La fecha/hora "hasta" debe ser mayor que "desde".'
            );
        }

        // 2. Calcular horas reales
        $horasSolicitadas = $desde->diffInMinutes($hasta) / 60;

        // 3. Validar saldo disponible
        if (! $this->puedeCrearPermisoPersonal($empleado, $horasSolicitadas)) {
            throw new DomainException(
                'El tiempo solicitado excede el saldo de horas personales disponibles.'
            );
        }

        return $horasSolicitadas;
    }

    /**
     * Valida si el empleado tiene saldo suficiente.
     */
    public function puedeCrearPermisoPersonal(
        Empleado $empleado,
        float $horasSolicitadas
    ): bool {
        $horasAsignadas = $empleado->horas_personales;

        $horasUsadas = Permiso::query()
            ->where('empleado_id', $empleado->id)
            ->where('id_tipo_permiso', 1)
            ->whereYear('desde', now()->year)
            ->sum('cantidad_horas');

        return ($horasUsadas + $horasSolicitadas) <= $horasAsignadas;
    }


    public function puedeGuardarPermisoPersonal(
        Empleado $empleado,
        float $horasSolicitadas,
        ?Permiso $permisoActual = null
    ): bool {
        $horasAsignadas = $empleado->horas_personales;

        $horasUsadas = Permiso::query()
            ->where('empleado_id', $empleado->id)
            ->where('id_tipo_permiso', 1)
            ->whereYear('desde', now()->year)
            ->when(
                $permisoActual,
                fn ($q) => $q->where('id', '!=', $permisoActual->id)
            )
            ->sum('cantidad_horas');

        return ($horasUsadas + $horasSolicitadas) <= $horasAsignadas;
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
