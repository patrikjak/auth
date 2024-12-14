<?php

use Illuminate\Support\Facades\Route;
use Patrikjak\Auth\Http\Controllers\Api\RegisterController;
use Patrikjak\Utils\Common\Http\Middlewares\VerifyRecaptcha;

Route::middleware(['web', 'guest'])
    ->prefix('api')
    ->name('api.')
    ->group(static function(): void {

        $registerEnabled = config('pjauth.features.register');
        $recaptchaEnabled = config('pjauth.recaptcha.enabled');

        if ($registerEnabled) {
            Route::post('/register', [RegisterController::class, 'store'])
                ->name('register')
                ->middleware($recaptchaEnabled ? VerifyRecaptcha::class : []);
        }

});