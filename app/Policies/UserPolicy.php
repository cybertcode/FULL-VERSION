<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.viewAny');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.edit')
            && (! $model->hasRole('Super-Admin') || $user->hasRole('Super-Admin'));
    }

    public function delete(User $user, User $model): bool
    {
        // No puede eliminarse a sí mismo
        return $user->can('users.delete') && $user->id !== $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('users.restore');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('users.forceDelete') && $user->id !== $model->id;
    }

    public function impersonate(User $user, User $model): bool
    {
        return $user->can('users.impersonate')
            && $user->id !== $model->id
            && ! $model->hasRole('Super-Admin');
    }

    public function manageSecurity(User $user, User $model): bool
    {
        return $user->can('users.manageSecurity') && ! $model->hasRole('Super-Admin');
    }
}
