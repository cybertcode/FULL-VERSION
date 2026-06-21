<?php

namespace App\Actions\Admin\Role;

use App\Exceptions\BusinessException;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class UpdateRole
{
    public function handle(Role $role, array $data): Role
    {
        if ($role->name === 'Super-Admin') {
            throw new BusinessException('El rol Super-Admin no puede modificarse.');
        }

        $role->update(['name' => $data['name']]);

        if (array_key_exists('permissions', $data)) {
            $permissions = Permission::whereIn('name', $data['permissions'] ?? [])->get();
            $role->syncPermissions($permissions);
        }

        return $role->load('permissions');
    }
}

