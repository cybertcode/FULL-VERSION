<?php

use App\Http\Controllers\Frontend\HomeController;
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
