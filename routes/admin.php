<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

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
        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::post('users/{id}/restore', [UserController::class, 'restore'])
            ->name('users.restore')
            ->withTrashed();

        // ── Roles ─────────────────────────────────────────────────────
        Route::resource('roles', RoleController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // ── Settings ──────────────────────────────────────────────────
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings/{group}', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/test-mail', [SettingController::class, 'testMail'])->name('settings.test-mail');
        Route::post('settings/artisan', [SettingController::class, 'runArtisan'])->name('settings.artisan');

    });
