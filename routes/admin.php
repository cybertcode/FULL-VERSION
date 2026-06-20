<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Todas las rutas del panel administrativo van aquí.
| Prefijo: /admin  |  Middleware: auth, verified, role:Super-Admin|admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        // Route::resource('users', UserController::class);

        // Roles & Permissions
        // Route::resource('roles', RoleController::class);

    });
