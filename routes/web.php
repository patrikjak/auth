<?php

use Illuminate\Support\Facades\Route;
use Patrikjak\Auth\Http\Controllers\AuthenticatedSessionController;
use Patrikjak\Auth\Http\Controllers\RegisterController;
use Patrikjak\Auth\Http\Controllers\ResetPasswordController;

Route::middleware(['web', 'guest'])->group(static function(): void {

    $registerEnabled = config('pjauth.features.register');
    $loginEnabled = config('pjauth.features.login');
    $passwordResetEnabled = config('pjauth.features.password_reset');

    if ($registerEnabled) {
        Route::get('/register', [RegisterController::class, 'index'])
            ->name('register');
    }

    if ($loginEnabled) {
        Route::get('/login', [AuthenticatedSessionController::class, 'index'])
            ->name('login');
    }

    if ($passwordResetEnabled) {
        Route::prefix('password')
            ->name('password.')
            ->group(static function () {
                Route::get('/forgot', [ResetPasswordController::class, 'forgot'])
                    ->name('request');

                Route::get('/reset/{token}', [ResetPasswordController::class, 'reset'])
                    ->name('reset');
            });
    }

});