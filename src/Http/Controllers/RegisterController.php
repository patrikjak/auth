<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\View\View;

class RegisterController
{
    public function index(Repository $config): View
    {
        return view('pjauth::register', [
            'redirectAfterRegister' => $config->get('pjauth.redirect_after_login'),
        ]);
    }
}