<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\Contracts\View\View;

class ResetPasswordController
{
    public function forgot(): View
    {
        return view('pjauth::forgot');
    }
}
