<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Permiso;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermisoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PermisosResource')
            || $authUser->can('ViewAny:GestionPermisoResource')
            || $authUser->can('ViewAny:AprobacionPermisoResource');
    }

    public function view(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('View:PermisosResource')
            || $authUser->can('View:GestionPermisoResource')
            || $authUser->can('View:AprobacionPermisoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PermisosResource')
            || $authUser->can('Create:GestionPermisoResource');
    }

    public function update(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Update:PermisosResource')
            || $authUser->can('Update:GestionPermisoResource')
            || $authUser->can('Update:AprobacionPermisoResource');
    }

    public function delete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Delete:PermisosResource')
            || $authUser->can('Delete:GestionPermisoResource')
            || $authUser->can('Delete:AprobacionPermisoResource');
    }

    public function restore(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Restore:PermisosResource')
            || $authUser->can('Restore:GestionPermisoResource');
    }

    public function forceDelete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('ForceDelete:PermisosResource')
            || $authUser->can('ForceDelete:GestionPermisoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PermisosResource')
            || $authUser->can('ForceDeleteAny:GestionPermisoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PermisosResource')
            || $authUser->can('RestoreAny:GestionPermisoResource');
    }

    public function replicate(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Replicate:PermisosResource')
            || $authUser->can('Replicate:GestionPermisoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PermisosResource')
            || $authUser->can('Reorder:GestionPermisoResource');
    }

}