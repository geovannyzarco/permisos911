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
        return $authUser->can('ViewAny:Nivel');
    }

    public function view(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('View:Nivel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Nivel');
    }

    public function update(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Update:Nivel');
    }

    public function delete(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Delete:Nivel');
    }

    public function restore(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Restore:Nivel');
    }

    public function forceDelete(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('ForceDelete:Nivel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Nivel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Nivel');
    }

    public function replicate(AuthUser $authUser, Nivel $nivel): bool
    {
        return $authUser->can('Replicate:Nivel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Nivel');
    }

}