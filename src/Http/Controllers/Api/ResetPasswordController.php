<?php

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Patrikjak\Auth\Http\Requests\PasswordLinkRequest;

class ResetPasswordController
{
    public function sendLink(PasswordLinkRequest $request, PasswordBroker $passwordBroker): JsonResponse
    {
        $status = $passwordBroker->sendResetLink($request->getCredentials());

        return new JsonResponse(['message' => __($status)], $status === PasswordBroker::RESET_LINK_SENT ? 200 : 422);
    }
}
