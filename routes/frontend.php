<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
| Rutas públicas del sitio (landing, blog, portal, etc.).
| Sin middleware de autenticación — acceso libre.
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Frontend\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
