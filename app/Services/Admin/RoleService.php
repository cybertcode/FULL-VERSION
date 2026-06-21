<?php

namespace App\Services\Admin;

use App\Actions\Admin\Role\CreateRole;
use Illuminate\Support\Facades\DB;
use App\Actions\Admin\Role\UpdateRole;
use App\Exceptions\BusinessException;
use Illuminate\Support\Collection;
use App\Models\Permission;
use App\Models\Role;

class RoleService
{
    public function __construct(
        private readonly CreateRole $createRole,
        private readonly UpdateRole $updateRole,
    ) {}

    public function all(): Collection
    {
        return Role::with(['permissions', 'topUsers'])
            ->addSelect([
                'users_count' => DB::table('model_has_roles')
                    ->selectRaw('count(*)')
                    ->whereColumn('model_has_roles.role_id', 'roles.id')
                    ->where('model_has_roles.model_type', \App\Models\User::class),
            ])
            ->get();
    }

    public function allPermissionsGrouped(): Collection
    {
        return Permission::all()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);
    }

    public function create(array $data): Role
    {
        $role = $this->createRole->handle($data);

        activity('roles')
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->event('created')
            ->log("Rol '{$role->name}' creado.");

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $updated = $this->updateRole->handle($role, $data);

        activity('roles')
            ->causedBy(auth()->user())
            ->performedOn($updated)
            ->event('updated')
            ->log("Rol '{$updated->name}' actualizado.");

        return $updated;
    }

    public function delete(Role $role): void
    {
        $protected = ['Super-Admin', 'admin', 'user'];

        if (\in_array($role->name, $protected, true)) {
            throw new BusinessException("El rol '{$role->name}' es un rol del sistema y no puede eliminarse.");
        }

        if ($role->users()->count() > 0) {
            throw new BusinessException("No se puede eliminar el rol '{$role->name}' porque tiene usuarios asignados.");
        }

        $role->delete();
    }
}

