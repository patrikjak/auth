<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class AuthenticatedSessionController
{
    public function store(): JsonResponse
    {
        return new JsonResponse();
    }
}