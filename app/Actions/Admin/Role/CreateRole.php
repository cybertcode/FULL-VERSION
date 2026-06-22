<?php

namespace App\Actions\Admin\Role;

use Spatie\Permission\Models\Permission;
use App\Models\Role;

class CreateRole
{
    public function handle(array $data): Role
    {
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web', 'description' => $data['description'] ?? null]);

        if (! empty($data['permissions'])) {
            $permissions = Permission::whereIn('name', $data['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return $role;
    }
}

