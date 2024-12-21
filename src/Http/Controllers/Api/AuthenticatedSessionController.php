<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Patrikjak\Auth\Exceptions\InvalidCredentialsException;
use Patrikjak\Auth\Http\Requests\LoginRequest;
use Patrikjak\Auth\Services\UserService;

class AuthenticatedSessionController
{
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
}