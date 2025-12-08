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
        return $authUser->can('ViewAny:Permiso');
    }

    public function view(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('View:Permiso');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Permiso');
    }

    public function update(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Update:Permiso');
    }

    public function delete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Delete:Permiso');
    }

    public function restore(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Restore:Permiso');
    }

    public function forceDelete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('ForceDelete:Permiso');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Permiso');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Permiso');
    }

    public function replicate(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Replicate:Permiso');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Permiso');
    }

}