<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\Contracts\View\View;

class AuthenticatedSessionController
{
    public function index(): View
    {
        return view('pjauth::login');
    }
}
