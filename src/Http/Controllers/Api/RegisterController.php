<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Http\Requests\InviteRegisterRequest;
use Patrikjak\Auth\Http\Requests\RegisterRequest;
use Patrikjak\Auth\Services\InviteService;
use Patrikjak\Auth\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class RegisterController
{
    public function store(RegisterRequest $request, UserService $userService): JsonResponse
    {
        $userService->createUserAndLogin($request->getNewUser());

        return new JsonResponse();
    }

    public function invitationStore(
        InviteRegisterRequest $request,
        UserService $userService,
        InviteService $inviteService,
    ): JsonResponse {
        $email = $request->getEmail();

        try {
            $roleId = $inviteService->validateTokenAndGetRoleId($request->getToken(), $email);
        } catch (EmailInInvitesNotFoundException) {
            return $this->invalidInviteResponse(__('pjauth::validation.invalid_invite_email'));
        } catch (InvalidArgumentException) {
            return $this->invalidInviteResponse(__('pjauth::validation.invalid_invite_token'));
        }

        $userService->createUserAndLoginViaInvitation($request->getNewUser(), $roleId);

        return new JsonResponse();
    }

    private function invalidInviteResponse(string $message): JsonResponse
    {
        return new JsonResponse(
            [
                'errors' => [
                    'email' => [$message],
                ],
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
