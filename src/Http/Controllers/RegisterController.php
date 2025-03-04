<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RegisterController
{
    public function index(Repository $config): View
    {
        return view('pjauth::register', [
            'redirectAfterRegister' => $config->get('pjauth.redirect_after_login'),
        ]);
    }

    public function invitationIndex(Repository $config, Request $request): View
    {
        return view('pjauth::register-via-invitation', [
            'redirectAfterRegister' => $config->get('pjauth.redirect_after_login'),
            'email' => $request->input('email'),
            'token' => $request->route('token'),
        ]);
    }
}