<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DelegarAprobacion;
use Illuminate\Auth\Access\HandlesAuthorization;

class DelegarAprobacionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DelegarAprobacionResource');
    }

    public function view(AuthUser $authUser, DelegarAprobacion $delegarAprobacion): bool
    {
        return $authUser->can('View:DelegarAprobacionResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DelegarAprobacionResource');
    }

    public function update(AuthUser $authUser, DelegarAprobacion $delegarAprobacion): bool
    {
        return $authUser->can('Update:DelegarAprobacionResource');
    }

    public function delete(AuthUser $authUser, DelegarAprobacion $delegarAprobacion): bool
    {
        return $authUser->can('Delete:DelegarAprobacionResource');
    }

    public function restore(AuthUser $authUser, DelegarAprobacion $delegarAprobacion): bool
    {
        return $authUser->can('Restore:DelegarAprobacionResource');
    }

    public function forceDelete(AuthUser $authUser, DelegarAprobacion $delegarAprobacion): bool
    {
        return $authUser->can('ForceDelete:DelegarAprobacionResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DelegarAprobacionResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DelegarAprobacionResource');
    }

    public function replicate(AuthUser $authUser, DelegarAprobacion $delegarAprobacion): bool
    {
        return $authUser->can('Replicate:DelegarAprobacionResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DelegarAprobacionResource');
    }

}