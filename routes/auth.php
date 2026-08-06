<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ForcePasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin auth routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])
        ->middleware('throttle:admin-login');

    Route::get('/admin/forgot', [PasswordResetController::class, 'showLinkRequestAdmin'])
        ->name('admin.password.request');
    Route::post('/admin/forgot', [PasswordResetController::class, 'sendResetLinkAdmin'])
        ->middleware('throttle:password-reset')
        ->name('admin.password.email');
    Route::get('/admin/reset/{token}', [PasswordResetController::class, 'showResetAdmin'])
        ->name('admin.password.reset');
    Route::post('/admin/reset', [PasswordResetController::class, 'resetAdmin'])
        ->name('admin.password.update');
});

Route::middleware('auth:admin')->group(function () {
    Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');

    // M10-T06: force-password-change flow for accounts with the flag set.
    Route::get('/admin/password/force', [ForcePasswordController::class, 'showAdmin'])
        ->name('admin.password.force');
    Route::post('/admin/password/force', [ForcePasswordController::class, 'updateAdmin'])
        ->name('admin.password.force.update');
});

Route::middleware('auth:web')->group(function () {
    Route::get('/password/force', [ForcePasswordController::class, 'show'])
        ->name('password.force');
    Route::post('/password/force', [ForcePasswordController::class, 'update'])
        ->name('password.force.update');
});
