<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Categoria;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CategoriaResource');
    }

    public function view(AuthUser $authUser, Categoria $categoria): bool
    {
        return $authUser->can('View:CategoriaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CategoriaResource');
    }

    public function update(AuthUser $authUser, Categoria $categoria): bool
    {
        return $authUser->can('Update:CategoriaResource');
    }

    public function delete(AuthUser $authUser, Categoria $categoria): bool
    {
        return $authUser->can('Delete:CategoriaResource');
    }

    public function restore(AuthUser $authUser, Categoria $categoria): bool
    {
        return $authUser->can('Restore:CategoriaResource');
    }

    public function forceDelete(AuthUser $authUser, Categoria $categoria): bool
    {
        return $authUser->can('ForceDelete:CategoriaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CategoriaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CategoriaResource');
    }

    public function replicate(AuthUser $authUser, Categoria $categoria): bool
    {
        return $authUser->can('Replicate:CategoriaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CategoriaResource');
    }

}