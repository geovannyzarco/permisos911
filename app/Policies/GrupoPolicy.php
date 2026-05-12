<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Grupo;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrupoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GrupoResource');
    }

    public function view(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('View:GrupoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GrupoResource');
    }

    public function update(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Update:GrupoResource');
    }

    public function delete(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Delete:GrupoResource');
    }

    public function restore(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Restore:GrupoResource');
    }

    public function forceDelete(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('ForceDelete:GrupoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GrupoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GrupoResource');
    }

    public function replicate(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Replicate:GrupoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GrupoResource');
    }

}