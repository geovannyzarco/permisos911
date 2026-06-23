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
            || $authUser->can('ViewAny:AprobacionPermisoResource')
            || $authUser->can('ViewAny:ProgramarCompensadosResource');
    }

    public function view(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('View:PermisosResource')
            || $authUser->can('View:GestionPermisoResource')
            || $authUser->can('View:AprobacionPermisoResource')
            || $authUser->can('View:ProgramarCompensadosResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PermisosResource')
            || $authUser->can('Create:GestionPermisoResource')
            || $authUser->can('Create:AprobacionPermisoResource')
            || $authUser->can('Create:ProgramarCompensadosResource');
    }

    public function update(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Update:PermisosResource')
            || $authUser->can('Update:GestionPermisoResource')
            || $authUser->can('Update:AprobacionPermisoResource')
            || $authUser->can('Update:ProgramarCompensadosResource');
    }

    public function delete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Delete:PermisosResource')
            || $authUser->can('Delete:GestionPermisoResource')
            || $authUser->can('Delete:AprobacionPermisoResource')
            || $authUser->can('Delete:ProgramarCompensadosResource');
    }

    public function restore(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Restore:PermisosResource')
            || $authUser->can('Restore:GestionPermisoResource')
            || $authUser->can('Restore:AprobacionPermisoResource')
            || $authUser->can('Restore:ProgramarCompensadosResource');
    }

    public function forceDelete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('ForceDelete:PermisosResource')
            || $authUser->can('ForceDelete:GestionPermisoResource')
            || $authUser->can('ForceDelete:AprobacionPermisoResource')
            || $authUser->can('ForceDelete:ProgramarCompensadosResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PermisosResource')
            || $authUser->can('ForceDeleteAny:GestionPermisoResource')
            || $authUser->can('ForceDeleteAny:AprobacionPermisoResource')
            || $authUser->can('ForceDeleteAny:ProgramarCompensadosResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PermisosResource')
            || $authUser->can('RestoreAny:GestionPermisoResource')
            || $authUser->can('RestoreAny:AprobacionPermisoResource')
            || $authUser->can('RestoreAny:ProgramarCompensadosResource');
    }

    public function replicate(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Replicate:PermisosResource')
            || $authUser->can('Replicate:GestionPermisoResource')
            || $authUser->can('Replicate:AprobacionPermisoResource')
            || $authUser->can('Replicate:ProgramarCompensadosResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PermisosResource')
            || $authUser->can('Reorder:GestionPermisoResource')
            || $authUser->can('Reorder:AprobacionPermisoResource')
            || $authUser->can('Reorder:ProgramarCompensadosResource');
    }

}