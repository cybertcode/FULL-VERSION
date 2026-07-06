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
        'users.viewAny' => 'Ver listado de usuarios',
        'users.view' => 'Ver detalle de usuario',
        'users.create' => 'Crear usuario',
        'users.edit' => 'Editar usuario',
        'users.delete' => 'Eliminar usuario',
        'users.restore' => 'Restaurar usuario eliminado',
        'users.forceDelete' => 'Eliminar permanentemente',
        'users.impersonate' => 'Iniciar sesión como otro usuario',
        'users.manageSecurity' => 'Restablecer 2FA, desbloquear cuenta y forzar cierre de sesión',

        // ── Roles ────────────────────────────────────────────────────────
        'roles.viewAny' => 'Ver listado de roles',
        'roles.view' => 'Ver detalle de rol',
        'roles.create' => 'Crear rol',
        'roles.edit' => 'Editar rol',
        'roles.delete' => 'Eliminar rol',
        'roles.assignPermissions' => 'Asignar permisos a rol',
        'permissions.viewAny' => 'Ver catálogo de permisos',
        'permissions.create' => 'Crear permiso',
        'permissions.edit' => 'Editar permiso',
        'permissions.delete' => 'Eliminar permiso',

        // ── Configuración ────────────────────────────────────────────────
        'settings.view' => 'Ver configuración del sistema',
        'settings.edit' => 'Editar configuración del sistema',
        'settings.testMail' => 'Enviar correo de prueba',
        'settings.runArtisan' => 'Ejecutar comandos Artisan',

        // ── Registro de actividad ────────────────────────────────────────
        'activitylog.viewAny' => 'Ver registro de actividad',
        'activitylog.export' => 'Exportar registro de actividad',

        // ── Dashboard ────────────────────────────────────────────────────
        'dashboard.view' => 'Ver dashboard',
        'dashboard.viewStats' => 'Ver estadísticas del dashboard',

        // ── Gestor de archivos ───────────────────────────────────────────
        'files.viewAny' => 'Ver gestor de archivos',
        'files.upload' => 'Subir archivos',
        'files.delete' => 'Eliminar archivos',
        'files.rename' => 'Renombrar archivos y carpetas',
        'files.folder' => 'Crear carpetas',

        // ── Logs del servidor ────────────────────────────────────────────
        'logs.view' => 'Ver logs del servidor',

        // ── Intentos de login ─────────────────────────────────────────────
        'login-attempts.viewAny' => 'Ver intentos de inicio de sesión',

        // ── Notificaciones ─────────────────────────────────────────────────
        'notifications.send' => 'Enviar notificaciones masivas',

        // ── Menús (CMS frontend) ─────────────────────────────────────────
        'menus.viewAny' => 'Ver menús de navegación',
        'menus.view' => 'Ver detalle de menú',
        'menus.create' => 'Crear menú',
        'menus.edit' => 'Editar menú y sus ítems',
        'menus.delete' => 'Eliminar menú',

        // ── Páginas (CMS frontend) ────────────────────────────────────────
        'pages.viewAny' => 'Ver listado de páginas',
        'pages.view' => 'Ver detalle de página',
        'pages.create' => 'Crear página',
        'pages.edit' => 'Editar página',
        'pages.delete' => 'Eliminar página',
        'pages.restore' => 'Restaurar página eliminada',
        'pages.forceDelete' => 'Eliminar página permanentemente',
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
            'users.manageSecurity',
            'roles.viewAny', 'roles.view', 'permissions.viewAny',
            'settings.view',
            'activitylog.viewAny',
            'login-attempts.viewAny',
            'dashboard.view', 'dashboard.viewStats',
            'files.viewAny', 'files.upload', 'files.delete', 'files.rename', 'files.folder',
            'menus.viewAny', 'menus.view', 'menus.create', 'menus.edit', 'menus.delete',
            'pages.viewAny', 'pages.view', 'pages.create', 'pages.edit', 'pages.delete',
            'pages.restore',
        ]);

        // Editor — acceso básico
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions(['dashboard.view']);

        // User — acceso mínimo
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions(['dashboard.view']);
    }
}
