<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Verbos de rutas resource en español (aplica a todo este archivo)
Route::resourceVerbs(['create' => 'crear', 'edit' => 'editar']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\RestrictAdminIpMiddleware::class,
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Usuarios ──────────────────────────────────────────────────
        Route::get('usuarios/data', [UserController::class, 'data'])->name('users.data');

        Route::resource('usuarios', UserController::class)
            ->except(['show'])
            ->parameters(['usuarios' => 'user'])
            ->names([
                'index'   => 'users.index',
                'create'  => 'users.create',
                'store'   => 'users.store',
                'edit'    => 'users.edit',
                'update'  => 'users.update',
                'destroy' => 'users.destroy',
            ]);

        Route::post('usuarios/{user}/restore', [UserController::class, 'restore'])
            ->name('users.restore')
            ->withTrashed();

        // ── Roles ─────────────────────────────────────────────────────
        Route::resource('roles', RoleController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['roles' => 'role'])
            ->names([
                'index'   => 'roles.index',
                'store'   => 'roles.store',
                'update'  => 'roles.update',
                'destroy' => 'roles.destroy',
            ]);

        // ── Permisos ──────────────────────────────────────────────────
        Route::get('permisos/data', [PermissionController::class, 'data'])->name('permissions.data');
        Route::get('permisos', [PermissionController::class, 'index'])->name('permissions.index');

        // ── Configuración ─────────────────────────────────────────────
        Route::get('configuracion', [SettingController::class, 'index'])->name('settings.index');
        Route::put('configuracion/{group}', [SettingController::class, 'update'])->name('settings.update');
        Route::post('configuracion/test-mail', [SettingController::class, 'testMail'])->name('settings.test-mail');
        Route::post('configuracion/artisan', [SettingController::class, 'runArtisan'])->name('settings.artisan');

    });
