<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Auth\AuthManager;
use Illuminate\Cache\RateLimiter;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Patrikjak\Auth\Exceptions\InvalidCredentialsException;
use Patrikjak\Auth\Http\Requests\LoginRequest;
use Patrikjak\Auth\Services\UserService;

class AuthenticatedSessionController
{
    /**
     * @throws BindingResolutionException
     */
    public function store(LoginRequest $request, UserService $userService, RateLimiter $rateLimiter): JsonResponse
    {
        $request->ensureIsNotRateLimited();

        try {
            $userService->login(
                $request->getEmail(),
                $request->getPassword(),
                $request->shouldRemember(),
            );
        } catch (InvalidCredentialsException) {
            $rateLimiter->hit($request->throttleKey());

            throw ValidationException::withMessages([
                'password' => __('pjauth::validation.credentials'),
            ]);
        }

        $request->session()->regenerate();

        return new JsonResponse();
    }

    public function destroy(Request $request, AuthManager $authManager, Repository $config): RedirectResponse
    {
        $authManager->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirectUrl = $config->get('pjauth.redirect_after_logout');

        return new RedirectResponse($redirectUrl);
    }
}