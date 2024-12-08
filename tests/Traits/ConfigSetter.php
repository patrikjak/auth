<?php

namespace Patrikjak\Auth\Tests\Traits;

trait ConfigSetter
{
    protected function useCustomAppName($app): void
    {
        $app['config']->set('pjauth.app_name', 'My custom app name');
    }

    protected function disableRecaptcha($app): void
    {
        $app['config']->set('pjauth.recaptcha.enabled', false);
    }

    protected function disableLogo($app): void
    {
        $app['config']->set('pjauth.logo_path', null);
    }

    protected function disableIcon($app): void
    {
        $app['config']->set('pjauth.icon.path', null);
    }

    protected function disableGoogleSocialLogin($app): void
    {
        $app['config']->set('pjauth.social_login.google.enabled', false);
    }

    protected function disableRegisterFeature($app): void
    {
        $app['config']->set('pjauth.features.register', false);
    }
}