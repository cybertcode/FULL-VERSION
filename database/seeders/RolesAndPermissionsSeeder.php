<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permisos con su label en español para el modal de permisos.
     * Formato: 'nombre' => 'Label visible'
     */
    private array $permissions = [
        // ── Usuarios ────────────────────────────────────────────────────
        'users.viewAny'    => 'Ver listado de usuarios',
        'users.view'       => 'Ver detalle de usuario',
        'users.create'     => 'Crear usuario',
        'users.edit'       => 'Editar usuario',
        'users.delete'     => 'Eliminar usuario',
        'users.restore'    => 'Restaurar usuario eliminado',
        'users.forceDelete'=> 'Eliminar permanentemente',

        // ── Roles ────────────────────────────────────────────────────────
        'roles.viewAny'         => 'Ver listado de roles',
        'roles.view'            => 'Ver detalle de rol',
        'roles.create'          => 'Crear rol',
        'roles.edit'            => 'Editar rol',
        'roles.delete'          => 'Eliminar rol',
        'roles.assignPermissions' => 'Asignar permisos a rol',

        // ── Configuración ────────────────────────────────────────────────
        'settings.view'       => 'Ver configuración del sistema',
        'settings.edit'       => 'Editar configuración del sistema',
        'settings.testMail'   => 'Enviar correo de prueba',
        'settings.runArtisan' => 'Ejecutar comandos Artisan',

        // ── Registro de actividad ────────────────────────────────────────
        'activitylog.viewAny' => 'Ver registro de actividad',
        'activitylog.export'  => 'Exportar registro de actividad',

        // ── Dashboard ────────────────────────────────────────────────────
        'dashboard.view'      => 'Ver dashboard',
        'dashboard.viewStats' => 'Ver estadísticas del dashboard',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $name => $label) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
            // Actualizar label si ya existía sin él
            if ($permission->label !== $label) {
                $permission->update(['label' => $label]);
            }
        }

        // Super-Admin — bypass total via Gate::before (sin permisos explícitos)
        Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);

        // Admin — permisos operativos del panel
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'users.viewAny', 'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.viewAny', 'roles.view',
            'settings.view',
            'activitylog.viewAny',
            'dashboard.view', 'dashboard.viewStats',
        ]);

        // Editor — acceso básico
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions(['dashboard.view']);

        // User — acceso mínimo
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions(['dashboard.view']);
    }
}
