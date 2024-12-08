<?php

namespace Patrikjak\Auth\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

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