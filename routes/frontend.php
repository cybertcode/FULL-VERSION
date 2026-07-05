<?php

use App\Http\Controllers\Frontend\Account\DashboardController;
use App\Http\Controllers\Frontend\Auth\AuthenticatedCustomerController;
use App\Http\Controllers\Frontend\Auth\NewPasswordCustomerController;
use App\Http\Controllers\Frontend\Auth\PasswordResetLinkCustomerController;
use App\Http\Controllers\Frontend\Auth\RegisteredCustomerController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
| Rutas públicas del sitio (landing, blog, portal, etc.).
| Sin middleware de autenticación — acceso libre.
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Auth de clientes (guard "customer")
|--------------------------------------------------------------------------
| Cuentas de usuarios finales, completamente separadas del panel /admin.
| Un cliente autenticado aquí NUNCA tiene acceso al panel administrativo:
| usa su propio guard, provider y tablas (customers, no users).
|--------------------------------------------------------------------------
*/

Route::middleware('guest:customer')->prefix('cuenta')->name('cuenta.')->group(function () {
    Route::get('registro', [RegisteredCustomerController::class, 'create'])->name('register');
    Route::post('registro', [RegisteredCustomerController::class, 'store'])->name('register.store');

    Route::get('login', [AuthenticatedCustomerController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedCustomerController::class, 'store'])->name('login.store');

    Route::get('olvide-password', [PasswordResetLinkCustomerController::class, 'create'])->name('password.request');
    Route::post('olvide-password', [PasswordResetLinkCustomerController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordCustomerController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordCustomerController::class, 'store'])->name('password.store');
});

Route::middleware('auth:customer')->prefix('cuenta')->name('cuenta.')->group(function () {
    Route::post('logout', [AuthenticatedCustomerController::class, 'destroy'])->name('logout');
    Route::get('panel', DashboardController::class)->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Páginas dinámicas (CMS)
|--------------------------------------------------------------------------
| Debe ir al final del archivo — {slug} es un catch-all de un segmento
| y no debe interceptar rutas más específicas definidas arriba.
|--------------------------------------------------------------------------
*/

Route::get('{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('pages.show');
