<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Nivel;
use Illuminate\Auth\Access\HandlesAuthorization;

class NivelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NivelResource');
    }

    public function view(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('View:NivelResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NivelResource');
    }

    public function update(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Update:NivelResource');
    }

    public function delete(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Delete:NivelResource');
    }

    public function restore(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Restore:NivelResource');
    }

    public function forceDelete(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('ForceDelete:NivelResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NivelResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NivelResource');
    }

    public function replicate(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Replicate:NivelResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NivelResource');
    }

}