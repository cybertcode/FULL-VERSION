<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // ── Usuarios ──────────────────────────────────────────────
            'users.viewAny',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.restore',
            'users.forceDelete',

            // ── Roles ─────────────────────────────────────────────────
            'roles.viewAny',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assignPermissions',

            // ── Configuración del sistema ──────────────────────────────
            'settings.view',
            'settings.edit',
            'settings.testMail',
            'settings.runArtisan',

            // ── Activity Log ───────────────────────────────────────────
            'activitylog.viewAny',
            'activitylog.export',

            // ── Dashboard ─────────────────────────────────────────────
            'dashboard.view',
            'dashboard.viewStats',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super-Admin — bypass total via Gate::before (sin permisos explícitos en BD)
        Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);

        // Admin — todos los permisos operativos del panel
        $adminPermissions = [
            'users.viewAny', 'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.viewAny', 'roles.view',
            'settings.view',
            'activitylog.viewAny',
            'dashboard.view', 'dashboard.viewStats',
        ];
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($adminPermissions);

        // Editor — gestión de contenido, sin acceso a usuarios/roles/settings
        $editorPermissions = [
            'dashboard.view',
        ];
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions($editorPermissions);

        // User — acceso mínimo, solo su propio perfil
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions(['dashboard.view']);
    }
}
