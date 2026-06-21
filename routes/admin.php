<?php

use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings/{group}', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/test-mail', [SettingController::class, 'testMail'])->name('settings.test-mail');
        Route::post('settings/artisan', [SettingController::class, 'runArtisan'])->name('settings.artisan');

    });
