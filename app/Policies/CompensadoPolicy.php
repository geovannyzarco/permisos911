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
        return $authUser->can('ViewAny:CompensadoResource');
    }

    public function view(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('View:CompensadoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CompensadoResource');
    }

    public function update(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Update:CompensadoResource');
    }

    public function delete(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Delete:CompensadoResource');
    }

    public function restore(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Restore:CompensadoResource');
    }

    public function forceDelete(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('ForceDelete:CompensadoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CompensadoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CompensadoResource');
    }

    public function replicate(AuthUser $authUser, Compensado $compensado): bool
    {
        return $authUser->can('Replicate:CompensadoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CompensadoResource');
    }

}