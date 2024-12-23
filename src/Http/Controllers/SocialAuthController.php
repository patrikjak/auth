<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers;

use Illuminate\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\SocialiteManager;
use Patrikjak\Auth\Services\SocialAuthService;

class SocialAuthController
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
        Repository $config,
        SocialAuthService $socialAuthService,
    ): RedirectResponse {
        $socialAuthService->handleSocialUser($request);

        return new RedirectResponse($config->get('pjauth.redirect_after_login'));
    }
}
