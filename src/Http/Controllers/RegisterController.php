<?php

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\View\View;

class RegisterController
{
    public function index(): View
    {
        return view('pjauth::register');
    }
}