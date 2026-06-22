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
        return $authUser->can('ViewAny:PermisosResource');
    }

    public function view(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('View:PermisosResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PermisosResource');
    }

    public function update(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Update:PermisosResource');
    }

    public function delete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Delete:PermisosResource');
    }

    public function restore(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Restore:PermisosResource');
    }

    public function forceDelete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('ForceDelete:PermisosResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PermisosResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PermisosResource');
    }

    public function replicate(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Replicate:PermisosResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PermisosResource');
    }

}