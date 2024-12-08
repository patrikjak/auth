<?php

use Illuminate\Support\Facades\Route;
use Patrikjak\Auth\Http\Controllers\RegisterController;

Route::middleware(['web', 'guest'])->group(static function(): void {

    $registerEnabled = config('pjauth.features.register');

    if ($registerEnabled) {
        Route::get('/register', [RegisterController::class, 'index'])
            ->name('register');
    }

});