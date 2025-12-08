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
        return $authUser->can('ViewAny:Entidad');
    }

    public function view(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('View:Entidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Entidad');
    }

    public function update(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Update:Entidad');
    }

    public function delete(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Delete:Entidad');
    }

    public function restore(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Restore:Entidad');
    }

    public function forceDelete(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('ForceDelete:Entidad');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Entidad');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Entidad');
    }

    public function replicate(AuthUser $authUser, Entidad $entidad): bool
    {
        return $authUser->can('Replicate:Entidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Entidad');
    }

}