<?php

namespace App\Actions\Admin\Role;

use App\Exceptions\BusinessException;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class UpdateRole
{
    public function handle(Role $role, array $data, ?User $editor = null): Role
    {
        if ($role->name === 'Super-Admin') {
            throw new BusinessException('El rol Super-Admin no puede modificarse.');
        }

        $role->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);

        if (array_key_exists('permissions', $data)) {
            $requested = collect($data['permissions'] ?? []);

            // Privilege escalation: editor no puede asignar permisos que él no tiene
            // (exento si es Super-Admin — Gate::before ya lo cubre, pero doble seguridad)
            if ($editor && ! $editor->hasRole('Super-Admin')) {
                $editorPerms = $editor->getAllPermissions()->pluck('name');
                $forbidden = $requested->diff($editorPerms);
                if ($forbidden->isNotEmpty()) {
                    throw new BusinessException(
                        'No puedes asignar permisos que tú mismo no posees: '.$forbidden->join(', ').'.'
                    );
                }
            }

            $permissions = Permission::whereIn('name', $requested->all())->get();
            $role->syncPermissions($permissions);
        }

        return $role->load('permissions');
    }
}
