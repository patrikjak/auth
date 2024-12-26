<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Traits;

use Illuminate\Foundation\Application;
use Patrikjak\Auth\Models\User;

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

    protected function disablePasswordResetFeature(Application $application): void
    {
        $application['config']->set('pjauth.features.password_reset', false);
    }

    protected function disableChangePasswordFeature(Application $application): void
    {
        $application['config']->set('pjauth.features.change_password', false);
    }

    protected function useCustomUserModel(Application $application): void
    {
        $customUserModel = new class extends User {};

        $application['config']->set('pjauth.models.user', $customUserModel::class);
    }

    protected function withGoogleSocialiteConfig(Application $application): void
    {
        $application['config']->set('services.google.client_id', 'test_client_id');
        $application['config']->set('services.google.client_secret', 'test_client_secret');
        $application['config']->set('services.google.redirect', 'http://localhost');
    }
}