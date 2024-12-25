<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ResetPasswordController
{
    public function forgot(): View
    {
        return view('pjauth::forgot');
    }

    public function reset(Request $request): View
    {
        return view('pjauth::reset', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }
}
