<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Entidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EntidadResource');
    }

    public function view(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('View:EntidadResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EntidadResource');
    }

    public function update(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Update:EntidadResource');
    }

    public function delete(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Delete:EntidadResource');
    }

    public function restore(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Restore:EntidadResource');
    }

    public function forceDelete(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('ForceDelete:EntidadResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EntidadResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EntidadResource');
    }

    public function replicate(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Replicate:EntidadResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EntidadResource');
    }

}