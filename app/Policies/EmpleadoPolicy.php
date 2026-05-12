<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Empleado;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmpleadoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmpleadoResource');
    }

    public function view(AuthUser $authUser, Empleado $empleado): bool
    {
        return $authUser->can('View:EmpleadoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmpleadoResource');
    }

    public function update(AuthUser $authUser, Empleado $empleado): bool
    {
        return $authUser->can('Update:EmpleadoResource');
    }

    public function delete(AuthUser $authUser, Empleado $empleado): bool
    {
        return $authUser->can('Delete:EmpleadoResource');
    }

    public function restore(AuthUser $authUser, Empleado $empleado): bool
    {
        return $authUser->can('Restore:EmpleadoResource');
    }

    public function forceDelete(AuthUser $authUser, Empleado $empleado): bool
    {
        return $authUser->can('ForceDelete:EmpleadoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmpleadoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmpleadoResource');
    }

    public function replicate(AuthUser $authUser, Empleado $empleado): bool
    {
        return $authUser->can('Replicate:EmpleadoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmpleadoResource');
    }

}