<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Http\Requests\InviteRegisterRequest;
use Patrikjak\Auth\Http\Requests\RegisterRequest;
use Patrikjak\Auth\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class RegisterController
{
    public function store(RegisterRequest $request, UserService $userService): JsonResponse
    {
        $userService->createUserAndLogin($request->getNewUser());

        return new JsonResponse();
    }

    public function invitationStore(InviteRegisterRequest $request, UserService $userService): JsonResponse
    {
        $invalidErrorMessage = __('pjauth::validation.invalid_invite_token');

        try {
            $isTokenValid = $userService->inviteTokenIsValid($request->getToken(), $request->getEmail());
        } catch (EmailInInvitesNotFoundException) {
            $isTokenValid = false;
            $invalidErrorMessage = __('pjauth::validation.invalid_invite_email');
        }

        if (!$isTokenValid) {
            return new JsonResponse(
                [
                    'errors' => [
                        'email' => [$invalidErrorMessage],
                    ],
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $userService->createUserAndLoginViaInvitation($request->getNewUser());

        return new JsonResponse();
    }
}