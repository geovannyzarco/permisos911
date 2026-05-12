<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Estado;
use Illuminate\Auth\Access\HandlesAuthorization;

class EstadoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EstadoResource');
    }

    public function view(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('View:EstadoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EstadoResource');
    }

    public function update(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Update:EstadoResource');
    }

    public function delete(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Delete:EstadoResource');
    }

    public function restore(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Restore:EstadoResource');
    }

    public function forceDelete(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('ForceDelete:EstadoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EstadoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EstadoResource');
    }

    public function replicate(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Replicate:EstadoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EstadoResource');
    }

}