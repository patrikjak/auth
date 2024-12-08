<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GoogleLoginButton extends Component
{
    public function __construct(public string $label)
    {
    }

    public function render(): View
    {
        return view('pjauth::components.google-login-button');
    }
}