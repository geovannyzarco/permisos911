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
        return $authUser->can('ViewAny:ProgramarCompensadosResource');
    }

    public function view(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('View:ProgramarCompensadosResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProgramarCompensadosResource');
    }

    public function update(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Update:ProgramarCompensadosResource');
    }

    public function delete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Delete:ProgramarCompensadosResource');
    }

    public function restore(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Restore:ProgramarCompensadosResource');
    }

    public function forceDelete(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('ForceDelete:ProgramarCompensadosResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProgramarCompensadosResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProgramarCompensadosResource');
    }

    public function replicate(AuthUser $authUser, Permiso $permiso): bool
    {
        return $authUser->can('Replicate:ProgramarCompensadosResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProgramarCompensadosResource');
    }

}