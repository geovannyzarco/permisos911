<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permiso;
use App\Models\Empleado;

class AprobacionPermisoPolicy
{
    public function aprobarVB(User $user, Permiso $permiso): bool
    {
        $emp = $user->empleado;

        if (!$emp) return false;

        // No auto-aprobación
        if ($permiso->empleado->oni === $emp->oni) {
            return false;
        }

        return $emp->nivel_id === Empleado::NIVEL_JEFE_GRUPO
            && $permiso->empleado->grupo_id === $emp->grupo_id
            && is_null($permiso->id_estado_vb);
    }

    public function aprobarFinal(User $user, Permiso $permiso): bool
    {
        $emp = $user->empleado;

        if (!$emp) return false;

        if ($permiso->empleado->oni === $emp->oni) {
            return false;
        }

        return $emp->nivel_id === Empleado::NIVEL_JEFE_UNIDAD
            && $permiso->empleado->unidad_id === $emp->unidad_id
            && is_null($permiso->id_estado_aprobacion);
    }
}
