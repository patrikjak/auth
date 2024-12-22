<?php

use Illuminate\Support\Facades\Route;
use Patrikjak\Auth\Http\Controllers\Api\AuthenticatedSessionController;
use Patrikjak\Auth\Http\Controllers\Api\RegisterController;
use Patrikjak\Utils\Common\Http\Middlewares\VerifyRecaptcha;

Route::middleware(['web', 'guest'])
    ->prefix('api')
    ->name('api.')
    ->group(static function(): void {

        $recaptchaEnabled = config('pjauth.recaptcha.enabled');
        $registerEnabled = config('pjauth.features.register');
        $loginEnabled = config('pjauth.features.login');

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
});

Route::middleware(['web', 'auth'])
    ->prefix('api')
    ->name('api.')
    ->group(static function(): void {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
});