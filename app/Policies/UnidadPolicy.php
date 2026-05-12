<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Unidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UnidadResource');
    }

    public function view(AuthUser $authUser, Unidad $unidad): bool
    {
        return $authUser->can('View:UnidadResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UnidadResource');
    }

    public function update(AuthUser $authUser, Unidad $unidad): bool
    {
        return $authUser->can('Update:UnidadResource');
    }

    public function delete(AuthUser $authUser, Unidad $unidad): bool
    {
        return $authUser->can('Delete:UnidadResource');
    }

    public function restore(AuthUser $authUser, Unidad $unidad): bool
    {
        return $authUser->can('Restore:UnidadResource');
    }

    public function forceDelete(AuthUser $authUser, Unidad $unidad): bool
    {
        return $authUser->can('ForceDelete:UnidadResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UnidadResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UnidadResource');
    }

    public function replicate(AuthUser $authUser, Unidad $unidad): bool
    {
        return $authUser->can('Replicate:UnidadResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UnidadResource');
    }

}