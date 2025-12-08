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
        return $authUser->can('ViewAny:TipoPermiso');
    }

    public function view(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('View:TipoPermiso');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoPermiso');
    }

    public function update(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Update:TipoPermiso');
    }

    public function delete(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Delete:TipoPermiso');
    }

    public function restore(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Restore:TipoPermiso');
    }

    public function forceDelete(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('ForceDelete:TipoPermiso');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoPermiso');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoPermiso');
    }

    public function replicate(AuthUser $authUser, TipoPermiso $tipoPermiso): bool
    {
        return $authUser->can('Replicate:TipoPermiso');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoPermiso');
    }

}