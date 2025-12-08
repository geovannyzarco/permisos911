<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Compensado;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompensadoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Compensado');
    }

    public function view(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('View:Compensado');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Compensado');
    }

    public function update(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Update:Compensado');
    }

    public function delete(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Delete:Compensado');
    }

    public function restore(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Restore:Compensado');
    }

    public function forceDelete(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('ForceDelete:Compensado');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Compensado');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Compensado');
    }

    public function replicate(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Replicate:Compensado');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Compensado');
    }

}