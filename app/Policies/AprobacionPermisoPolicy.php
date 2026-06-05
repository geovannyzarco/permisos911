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
        if ($permiso->empleado->oni == $emp->oni) {
            return false;
        }

        return $emp->nivel_id == Empleado::NIVEL_JEFE_GRUPO
            && in_array($permiso->empleado->grupo_id, $emp->obtenerGruposAsignados())
            && (is_null($permiso->id_estado_vb) || $permiso->id_estado_vb == 4);
    }

    public function aprobarFinal(User $user, Permiso $permiso): bool
    {
        $emp = $user->empleado;

        if (!$emp) return false;

        if ($permiso->empleado->oni == $emp->oni) {
            return false;
        }

        return $emp->nivel_id == Empleado::NIVEL_JEFE_UNIDAD
            && in_array($permiso->empleado->unidad_id, $emp->obtenerUnidadesAsignadas())
            && (is_null($permiso->id_estado_aprobacion) || $permiso->id_estado_aprobacion == 4);
    }

    public function aprobarJefeDivision(User $user, Permiso $permiso): bool
    {
        $emp = $user->empleado;

        if (!$emp) return false;

        if ($permiso->empleado->oni == $emp->oni) {
            return false;
        }

        return $emp->nivel_id == 4
            && (is_null($permiso->id_estado_aprobacion_jefe_division) || $permiso->id_estado_aprobacion_jefe_division == 4);
    }
}
