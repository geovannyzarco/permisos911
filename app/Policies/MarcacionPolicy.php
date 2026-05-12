<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Marcacion;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarcacionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarcacionResource');
    }

    public function view(AuthUser $authUser, Marcacion $marcacion): bool
    {
        return $authUser->can('View:MarcacionResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarcacionResource');
    }

    public function update(AuthUser $authUser, Marcacion $marcacion): bool
    {
        return $authUser->can('Update:MarcacionResource');
    }

    public function delete(AuthUser $authUser, Marcacion $marcacion): bool
    {
        return $authUser->can('Delete:MarcacionResource');
    }

    public function restore(AuthUser $authUser, Marcacion $marcacion): bool
    {
        return $authUser->can('Restore:MarcacionResource');
    }

    public function forceDelete(AuthUser $authUser, Marcacion $marcacion): bool
    {
        return $authUser->can('ForceDelete:MarcacionResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarcacionResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarcacionResource');
    }

    public function replicate(AuthUser $authUser, Marcacion $marcacion): bool
    {
        return $authUser->can('Replicate:MarcacionResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarcacionResource');
    }

}