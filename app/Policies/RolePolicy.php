<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('roles.viewAny');
    }

    public function view(User $user, Role $_role): bool
    {
        return $user->can('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        // El rol Super-Admin es intocable
        return $user->can('roles.edit') && $role->name !== 'Super-Admin';
    }

    public function delete(User $user, Role $role): bool
    {
        $protectedRoles = ['Super-Admin', 'admin', 'user'];

        return $user->can('roles.delete') && ! \in_array($role->name, $protectedRoles, true);
    }

    public function assignPermissions(User $user, Role $role): bool
    {
        return $user->can('roles.assignPermissions') && $role->name !== 'Super-Admin';
    }
}

