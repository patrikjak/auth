<?php

namespace Patrikjak\Auth\View\Layouts;

use Illuminate\Config\Repository;
use Illuminate\View\Component;
use Illuminate\View\View;

class App extends Component
{
    public readonly string $logo;

    public function __construct(Repository $config, public string $title)
    {
        $this->logo = $config->get('pjauth.logo_path');
    }

    public function render(): View
    {
        return view('pjauth::layouts.app');
    }
}