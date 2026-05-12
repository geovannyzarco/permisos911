<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Horario;
use Illuminate\Auth\Access\HandlesAuthorization;

class HorarioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HorarioResource');
    }

    public function view(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('View:HorarioResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HorarioResource');
    }

    public function update(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Update:HorarioResource');
    }

    public function delete(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Delete:HorarioResource');
    }

    public function restore(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Restore:HorarioResource');
    }

    public function forceDelete(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('ForceDelete:HorarioResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HorarioResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HorarioResource');
    }

    public function replicate(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Replicate:HorarioResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HorarioResource');
    }

}