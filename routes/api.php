<?php

use Illuminate\Support\Facades\Route;
use Patrikjak\Auth\Http\Controllers\Api\AuthenticatedSessionController;
use Patrikjak\Auth\Http\Controllers\Api\NewPasswordController;
use Patrikjak\Auth\Http\Controllers\Api\RegisterController;
use Patrikjak\Auth\Http\Controllers\Api\ResetPasswordController;
use Patrikjak\Utils\Common\Http\Middlewares\VerifyRecaptcha;

Route::middleware(['web', 'guest'])
    ->prefix('api')
    ->name('api.')
    ->group(static function(): void {

        $recaptchaEnabled = config('pjauth.recaptcha.enabled');
        $registerEnabled = config('pjauth.features.register');
        $loginEnabled = config('pjauth.features.login');
        $passwordResetEnabled = config('pjauth.features.password_reset');

        if ($registerEnabled) {
            Route::post('/register', [RegisterController::class, 'store'])
                ->name('register')
                ->middleware($recaptchaEnabled ? VerifyRecaptcha::class : []);
        }

        if ($loginEnabled) {
            Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->name('login')
                ->middleware($recaptchaEnabled ? VerifyRecaptcha::class : []);
        }

        if ($passwordResetEnabled) {
            Route::prefix('password')
                ->name('password.')
                ->group(static function () use ($recaptchaEnabled): void {
                    Route::post('/forgot', [ResetPasswordController::class, 'sendLink'])
                        ->name('email')
                        ->middleware($recaptchaEnabled ? VerifyRecaptcha::class : []);

                    Route::patch('/reset', [NewPasswordController::class, 'reset'])
                        ->name('store')
                        ->middleware($recaptchaEnabled ? VerifyRecaptcha::class : []);
            });
        }
});

Route::middleware(['web', 'auth'])
    ->prefix('api')
    ->name('api.')
    ->group(static function(): void {

        $changePasswordEnabled = config('pjauth.features.change_password');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        if ($changePasswordEnabled) {
            Route::patch('/change-password', [NewPasswordController::class, 'change'])
                ->name('change-password');
        }
});