<?php

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class NewPasswordController
{
    public function reset(): JsonResponse
    {
        return new JsonResponse();
    }
}
