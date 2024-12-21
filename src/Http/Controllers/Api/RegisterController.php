<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Patrikjak\Auth\Http\Requests\RegisterRequest;
use Patrikjak\Auth\Services\UserService;

class RegisterController
{
    public function store(RegisterRequest $request, UserService $userService): JsonResponse
    {
        $userService->createUserAndLogin($request->getNewUser());

        return new JsonResponse();
    }
}