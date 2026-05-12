<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoPermiso;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoPermisoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoPermisoResource');
    }

    public function view(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('View:TipoPermisoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoPermisoResource');
    }

    public function update(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Update:TipoPermisoResource');
    }

    public function delete(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Delete:TipoPermisoResource');
    }

    public function restore(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Restore:TipoPermisoResource');
    }

    public function forceDelete(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('ForceDelete:TipoPermisoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoPermisoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoPermisoResource');
    }

    public function replicate(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Replicate:TipoPermisoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoPermisoResource');
    }

}