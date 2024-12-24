<?php

use Illuminate\Support\Facades\Route;
use Patrikjak\Auth\Http\Controllers\LoginController;
use Patrikjak\Auth\Http\Controllers\RegisterController;
use Patrikjak\Auth\Http\Controllers\ResetPasswordController;
use Patrikjak\Auth\Http\Controllers\SocialAuthController;

Route::middleware(['web', 'guest'])->group(static function(): void {

    $registerEnabled = config('pjauth.features.register');
    $loginEnabled = config('pjauth.features.login');
    $passwordResetEnabled = config('pjauth.features.password_reset');
    $socialLoginEnabled = config('pjauth.social_login.google.enabled');

    if ($registerEnabled) {
        Route::get('/register', [RegisterController::class, 'index'])
            ->name('register');
    }

    if ($loginEnabled) {
        Route::get('/login', [LoginController::class, 'index'])
            ->name('login');
    }

    if ($passwordResetEnabled) {
        Route::prefix('password')
            ->name('password.')
            ->group(static function (): void {
                Route::get('/forgot', [ResetPasswordController::class, 'forgot'])
                    ->name('request');

                Route::get('/reset/{token}', [ResetPasswordController::class, 'reset'])
                    ->name('reset');
            });
    }
    
    if ($socialLoginEnabled) {
        Route::prefix('auth')
            ->name('auth.')
            ->group(static function (): void {
                Route::get('google', [SocialAuthController::class, 'redirect'])
                    ->name('google');

                Route::get('/google/callback', [SocialAuthController::class, 'callback'])
                    ->name('google.callback');
            });
    }

});