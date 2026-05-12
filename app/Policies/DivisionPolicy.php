<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Division;
use Illuminate\Auth\Access\HandlesAuthorization;

class DivisionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DivisionResource');
    }

    public function view(AuthUser $authUser, Division $division): bool
    {
        return $authUser->can('View:DivisionResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DivisionResource');
    }

    public function update(AuthUser $authUser, Division $division): bool
    {
        return $authUser->can('Update:DivisionResource');
    }

    public function delete(AuthUser $authUser, Division $division): bool
    {
        return $authUser->can('Delete:DivisionResource');
    }

    public function restore(AuthUser $authUser, Division $division): bool
    {
        return $authUser->can('Restore:DivisionResource');
    }

    public function forceDelete(AuthUser $authUser, Division $division): bool
    {
        return $authUser->can('ForceDelete:DivisionResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DivisionResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DivisionResource');
    }

    public function replicate(AuthUser $authUser, Division $division): bool
    {
        return $authUser->can('Replicate:DivisionResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DivisionResource');
    }

}