<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\SocialiteManager;
use Patrikjak\Auth\Exceptions\RegistrationNotAllowedException;
use Patrikjak\Auth\Services\SocialAuthService;

readonly class SocialAuthController
{
    public function redirect(
        Request $request,
        SocialiteManager $socialiteManager,
        SocialAuthService $socialAuthService,
    ): RedirectResponse {
        return $socialiteManager->driver($socialAuthService->getDriverFromRequest($request))->redirect();
    }

    public function callback(
        Request $request,
        Config $config,
        SocialAuthService $socialAuthService,
        UrlGenerator $urlGenerator,
    ): RedirectResponse {
        try {
            $socialAuthService->handleSocialUser($request);
        } catch (RegistrationNotAllowedException) {
            return new RedirectResponse($urlGenerator->route('login'), 302, []);
        }

        return new RedirectResponse($config->get('pjauth.redirect_after_login'));
    }
}
