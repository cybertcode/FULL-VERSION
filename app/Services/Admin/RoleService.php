<?php

namespace App\Services\Admin;

use App\Actions\Admin\Role\CreateRole;
use Illuminate\Support\Facades\DB;
use App\Actions\Admin\Role\UpdateRole;
use App\Exceptions\BusinessException;
use Illuminate\Support\Collection;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class RoleService
{
    public function __construct(
        private readonly CreateRole $createRole,
        private readonly UpdateRole $updateRole,
    ) {}

    public function all(): Collection
    {
        return Role::select('roles.*')
            ->with(['permissions', 'topUsers', 'users'])
            ->addSelect([
                'users_count' => DB::table('model_has_roles')
                    ->selectRaw('count(*)')
                    ->whereColumn('model_has_roles.role_id', 'roles.id')
                    ->where('model_has_roles.model_type', \App\Models\User::class),
            ])
            ->orderByDesc('created_at')
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
        // Capturar estado anterior antes de modificar
        $oldName  = $role->name;
        $oldPerms = $role->permissions->pluck('name')->sort()->values()->all();

        $updated  = $this->updateRole->handle($role, $data, auth()->user());

        $newPerms  = $updated->permissions->pluck('name')->sort()->values()->all();
        $added     = array_values(array_diff($newPerms, $oldPerms));
        $removed   = array_values(array_diff($oldPerms, $newPerms));

        activity('roles')
            ->causedBy(auth()->user())
            ->performedOn($updated)
            ->event('updated')
            ->withProperties([
                'old_name'         => $oldName,
                'new_name'         => $updated->name,
                'permissions_added'   => $added,
                'permissions_removed' => $removed,
            ])
            ->log("Rol '{$updated->name}' actualizado.");

        return $updated;
    }

    public function delete(Role $role): void
    {
        if ($role->name === 'Super-Admin') {
            throw new BusinessException("El rol 'Super-Admin' es el rol del sistema y no puede eliminarse.");
        }

        if ($role->users()->count() > 0) {
            throw new BusinessException("No se puede eliminar el rol '{$role->name}' porque tiene usuarios asignados.");
        }

        $role->delete();
    }

    public function assignRole(User $user, string $roleName, ?User $editor = null): void
    {
        if ($user->hasRole('Super-Admin')) {
            throw new BusinessException("No se puede cambiar el rol del Super-Admin.");
        }

        // Evitar que el último Super-Admin sea degradado si alguien cambia su propio rol
        // (en este flujo el editor asigna a otro usuario, pero por seguridad lo validamos)
        if ($roleName !== 'Super-Admin') {
            $superAdminCount = User::role('Super-Admin')->count();
            if ($superAdminCount <= 1 && $user->hasRole('Super-Admin')) {
                throw new BusinessException("No puedes quitar el rol al único Super-Admin del sistema.");
            }
        }

        $role    = Role::findByName($roleName, 'web');
        $oldRole = $user->roles->first()?->name ?? '—';

        $user->syncRoles([$role]);

        activity('roles')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('role_assigned')
            ->withProperties(['old_role' => $oldRole, 'new_role' => $roleName])
            ->log("Rol de '{$user->name}' cambiado de '{$oldRole}' a '{$roleName}'.");
    }

    public function roleHistory(User $user): Collection
    {
        return \Spatie\Activitylog\Models\Activity::with('causer')
            ->where('log_name', 'roles')
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'role_assigned')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'fecha'     => $a->created_at->format('d/m/Y H:i'),
                'old_role'  => $a->properties['old_role'] ?? '—',
                'new_role'  => $a->properties['new_role'] ?? '—',
                'por'       => $a->causer?->name ?? 'Sistema',
            ]);
    }

    public function bulkAssignRole(array $userIds, string $roleName): array
    {
        $role     = Role::findByName($roleName, 'web');
        $assigned = 0;
        $skipped  = 0;

        foreach (User::whereIn('id', $userIds)->get() as $user) {
            if ($user->hasRole('Super-Admin')) {
                $skipped++;
                continue;
            }
            $oldRole = $user->roles->first()?->name ?? '—';
            $user->syncRoles([$role]);

            activity('roles')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->event('role_assigned')
                ->withProperties(['old_role' => $oldRole, 'new_role' => $roleName, 'bulk' => true])
                ->log("Rol de '{$user->name}' cambiado de '{$oldRole}' a '{$roleName}' (masivo).");

            $assigned++;
        }

        return compact('assigned', 'skipped');
    }

    public function roleChangeHistory(Role $role): Collection
    {
        return \Spatie\Activitylog\Models\Activity::with('causer')
            ->where('log_name', 'roles')
            ->where('subject_type', Role::class)
            ->where('subject_id', $role->id)
            ->where('event', 'updated')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($a) => [
                'fecha'               => $a->created_at->format('d/m/Y H:i'),
                'old_name'            => $a->properties['old_name']            ?? $role->name,
                'new_name'            => $a->properties['new_name']            ?? $role->name,
                'permissions_added'   => $a->properties['permissions_added']   ?? [],
                'permissions_removed' => $a->properties['permissions_removed'] ?? [],
                'por'                 => $a->causer?->name ?? 'Sistema',
            ]);
    }

    public function riskStats(): array
    {
        $privilegedRoles = ['Super-Admin', 'admin'];

        $inactivePrivileged = User::whereHas('roles', fn ($q) => $q->whereIn('name', $privilegedRoles))
            ->where(fn ($q) => $q
                ->whereNull('last_login_at')
                ->orWhere('last_login_at', '<', now()->subDays(30))
            )
            ->count();

        $sinRol = User::whereDoesntHave('roles')->count();

        $totalPrivileged = User::whereHas('roles', fn ($q) => $q->whereIn('name', $privilegedRoles))->count();

        return compact('inactivePrivileged', 'sinRol', 'totalPrivileged');
    }
}

