<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Patrikjak\Auth\Http\Requests\ChangePasswordRequest;
use Patrikjak\Auth\Http\Requests\ResetPasswordRequest;
use Patrikjak\Auth\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class NewPasswordController
{
    public function reset(ResetPasswordRequest $request, UserService $userService): JsonResponse
    {
        $status = $userService->resetPasswordWithTokenValidation(
            $request->getCredentials(),
            $request->getNewPassword(),
        );

        return new JsonResponse(
            ['message' => __($status)],
            $status === PasswordBroker::PASSWORD_RESET
                ? Response::HTTP_OK
                : Response::HTTP_BAD_REQUEST,
        );
    }

    public function change(ChangePasswordRequest $request, UserService $userService): JsonResponse
    {
        $userService->changePasswordForUser($request->getUserId(), $request->getNewPassword());

        return new JsonResponse(['message' => __('pjauth::passwords.changed')]);
    }
}
