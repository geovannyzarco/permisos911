<?php

namespace App\Services;

use App\Models\Permiso;
use App\Models\Empleado;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

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

    public function aprobarFinal(Permiso $permiso, int $estadoId, User $user): void
    {
        if (!$user->can('aprobarFinal', $permiso)){
            throw new AuthorizationException();
        }

        $permiso->update([
            'id_estado_aprobacion' => $estadoId,
            'fecha_aprobacion' => now(),
            'id_jefe_aprobacion' => $user->empleado->id,
        ]);
    }
}
