<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FileManagerController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\LoginAttemptController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\RestrictAdminIpMiddleware;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

// Verbos de rutas resource en español (aplica a todo este archivo)
Route::resourceVerbs(['create' => 'crear', 'edit' => 'editar']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RestrictAdminIpMiddleware::class,
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Dashboard ─────────────────────────────────────────────────
        Route::get('/', DashboardController::class)->name('dashboard');

        // ── Usuarios ──────────────────────────────────────────────────
        Route::get('usuarios/data', [UserController::class, 'data'])->name('users.data');
        Route::get('usuarios/exportar/pdf', [UserController::class, 'exportPdf'])->name('users.export.pdf');
        Route::get('usuarios/exportar/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
        Route::get('usuarios/exportar/csv', [UserController::class, 'exportCsv'])->name('users.export.csv');

        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'user'])
            ->names([
                'index' => 'users.index',
                'create' => 'users.create',
                'store' => 'users.store',
                'show' => 'users.show',
                'edit' => 'users.edit',
                'update' => 'users.update',
                'destroy' => 'users.destroy',
            ]);

        Route::post('usuarios/{user}/restore', [UserController::class, 'restore'])
            ->name('users.restore')
            ->withTrashed();

        Route::delete('usuarios/{user}/force-delete', [UserController::class, 'forceDelete'])
            ->name('users.force-delete')
            ->withTrashed();

        Route::post('usuarios/{user}/verify-email', [UserController::class, 'verifyEmail'])
            ->name('users.verify-email');

        Route::post('usuarios/{user}/resend-verification', [UserController::class, 'resendVerification'])
            ->name('users.resend-verification');

        Route::post('usuarios/bulk-action', [UserController::class, 'bulkAction'])
            ->name('users.bulk-action');

        Route::post('usuarios/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');

        Route::post('usuarios/{user}/reset-two-factor', [UserController::class, 'resetTwoFactor'])
            ->name('users.reset-two-factor');

        Route::post('usuarios/{user}/unlock', [UserController::class, 'unlock'])
            ->name('users.unlock');

        Route::post('usuarios/{user}/force-logout', [UserController::class, 'forceLogout'])
            ->name('users.force-logout');

        Route::post('usuarios/{user}/impersonate', [ImpersonateController::class, 'take'])
            ->name('users.impersonate');

        Route::post('impersonate/salir', [ImpersonateController::class, 'leave'])
            ->name('impersonate.leave');

        // ── Roles — rutas específicas ANTES del resource para evitar conflictos ──
        Route::get('roles/exportar/pdf', [RoleController::class, 'exportPdf'])->name('roles.export.pdf');
        Route::get('roles/exportar/excel', [RoleController::class, 'exportExcel'])->name('roles.export.excel');
        Route::get('roles/exportar/csv', [RoleController::class, 'exportCsv'])->name('roles.export.csv');
        Route::get('roles/usuarios/data', [RoleController::class, 'usersData'])->name('roles.users.data');
        Route::patch('roles/usuarios/{user}/asignar', [RoleController::class, 'assignRole'])->name('roles.users.assign');
        Route::get('roles/usuarios/{user}/historial', [RoleController::class, 'roleHistory'])->name('roles.users.history');
        Route::post('roles/usuarios/asignar-masivo', [RoleController::class, 'bulkAssign'])->name('roles.users.bulk-assign');
        Route::get('roles/{role}/historial-cambios', [RoleController::class, 'roleChangeHistory'])->name('roles.change-history');

        Route::resource('roles', RoleController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['roles' => 'role'])
            ->names([
                'index' => 'roles.index',
                'store' => 'roles.store',
                'update' => 'roles.update',
                'destroy' => 'roles.destroy',
            ]);

        // ── Permisos ──────────────────────────────────────────────────
        Route::get('permisos/data', [PermissionController::class, 'data'])->name('permissions.data');
        Route::get('permisos/exportar/pdf', [PermissionController::class, 'exportPdf'])->name('permissions.export.pdf');
        Route::get('permisos/exportar/excel', [PermissionController::class, 'exportExcel'])->name('permissions.export.excel');
        Route::get('permisos/exportar/csv', [PermissionController::class, 'exportCsv'])->name('permissions.export.csv');
        Route::get('permisos', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('permisos', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('permisos/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('permisos/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // ── Mi Perfil ─────────────────────────────────────────────────
        Route::get('mi-perfil', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('mi-perfil', [ProfileController::class, 'update'])->name('profile.update');

        // ── Buscador global ───────────────────────────────────────────
        Route::get('buscar', SearchController::class)->name('search');

        // ── Gestor de archivos ────────────────────────────────────────
        Route::get('archivos', [FileManagerController::class, 'index'])->name('files.index');

        // ── Notificaciones ────────────────────────────────────────────
        Route::get('notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notificaciones/{id}/leer', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notificaciones/leer-todas', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::delete('notificaciones/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('notificaciones/enviar', [NotificationController::class, 'broadcast'])
            ->middleware('throttle:6,1')
            ->name('notifications.broadcast');

        // ── Auditoría ─────────────────────────────────────────────────
        Route::get('auditoria/exportar/csv', [ActivityLogController::class, 'exportCsv'])->name('activity.export.csv');
        Route::get('auditoria', [ActivityLogController::class, 'index'])->name('activity.index');

        // ── Intentos de login ──────────────────────────────────────────
        Route::get('intentos-login', [LoginAttemptController::class, 'index'])->name('login-attempts.index');

        // ── Logs del servidor (rap2hpoutre/laravel-log-viewer) ────────
        Route::get('logs', [LogViewerController::class, 'index'])
            ->middleware('permission:logs.view')
            ->name('logs.index');

        // ── Menús (CMS frontend) ──────────────────────────────────────
        Route::resource('menus', MenuController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['menus' => 'menu'])
            ->names([
                'index' => 'menus.index',
                'store' => 'menus.store',
                'edit' => 'menus.edit',
                'update' => 'menus.update',
                'destroy' => 'menus.destroy',
            ]);

        Route::get('menus/{menu}/arbol', [MenuController::class, 'tree'])->name('menus.tree');
        Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('menus/{menu}/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/{menu}/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
        Route::post('menus/{menu}/mover', [MenuController::class, 'move'])->name('menus.move');

        // ── Configuración ─────────────────────────────────────────────
        Route::get('configuracion', [SettingController::class, 'index'])->name('settings.index');
        Route::get('configuracion/exportar', [SettingController::class, 'export'])->name('settings.export');
        Route::post('configuracion/importar', [SettingController::class, 'import'])->name('settings.import');
        Route::post('configuracion/test-mail', [SettingController::class, 'testMail'])
            ->middleware('throttle:6,1')
            ->name('settings.test-mail');
        Route::post('configuracion/test-recaptcha', [SettingController::class, 'testRecaptcha'])
            ->middleware('throttle:6,1')
            ->name('settings.test-recaptcha');
        Route::post('configuracion/artisan', [SettingController::class, 'runArtisan'])
            ->middleware('throttle:10,1')
            ->name('settings.artisan');
        Route::put('configuracion/{group}', [SettingController::class, 'update'])->name('settings.update');

    });
