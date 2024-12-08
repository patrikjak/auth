<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Traits;

use Illuminate\Foundation\Application;

trait ConfigSetter
{
    protected function useCustomAppName(Application $app): void
    {
        $app['config']->set('pjauth.app_name', 'My custom app name');
    }

    protected function disableRecaptcha(Application $app): void
    {
        $app['config']->set('pjauth.recaptcha.enabled', false);
    }

    protected function disableLogo(Application $app): void
    {
        $app['config']->set('pjauth.logo_path', null);
    }

    protected function disableIcon(Application $app): void
    {
        $app['config']->set('pjauth.icon.path', null);
    }

    protected function disableGoogleSocialLogin(Application $app): void
    {
        $app['config']->set('pjauth.social_login.google.enabled', false);
    }

    protected function disableRegisterFeature(Application $app): void
    {
        $app['config']->set('pjauth.features.register', false);
    }

    protected function disableLoginFeature(Application $app): void
    {
        $app['config']->set('pjauth.features.login', false);
    }
}