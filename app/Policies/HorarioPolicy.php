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
        return $authUser->can('ViewAny:Horario');
    }

    public function view(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('View:Horario');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Horario');
    }

    public function update(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Update:Horario');
    }

    public function delete(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Delete:Horario');
    }

    public function restore(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Restore:Horario');
    }

    public function forceDelete(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('ForceDelete:Horario');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Horario');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Horario');
    }

    public function replicate(AuthUser $authUser, Horario $horario): bool
    {
        return $authUser->can('Replicate:Horario');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Horario');
    }

}