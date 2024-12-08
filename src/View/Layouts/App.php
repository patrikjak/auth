<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\View\Layouts;

use Illuminate\Config\Repository;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class App extends Component
{
    public readonly string $appName;

    public readonly ?string $logo;

    public readonly ?string $icon;

    public readonly ?string $iconType;

    public readonly bool $enabledRecaptcha;

    public readonly ?string $recaptchaSiteKey;

    public function __construct(Repository $config, public string $title)
    {
        $this->appName = $config->get('pjauth.app_name');
        $this->logo = $config->get('pjauth.logo_path');
        $this->icon = $config->get('pjauth.icon.path');
        $this->iconType = $config->get('pjauth.icon.type');
        $this->enabledRecaptcha = $config->get('pjauth.recaptcha.enabled');
        $this->recaptchaSiteKey = $config->get('pjauth.recaptcha.site_key');
    }

    public function render(): View
    {
        return view('pjauth::layouts.app');
    }
}