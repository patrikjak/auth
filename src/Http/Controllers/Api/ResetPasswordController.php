<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Patrikjak\Auth\Http\Requests\PasswordLinkRequest;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController
{
    public function sendLink(PasswordLinkRequest $request, PasswordBroker $passwordBroker): JsonResponse
    {
        $status = $passwordBroker->sendResetLink($request->getCredentials());

        return new JsonResponse(
            ['message' => __('pjauth::' . $status)],
            $status === PasswordBroker::RESET_LINK_SENT
                ? Response::HTTP_OK
                : Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
