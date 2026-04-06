<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Services;

use Illuminate\Config\Repository as Config;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Exceptions\RoleNotFoundException;
use Patrikjak\Auth\Notifications\RegisterInviteNotification;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

final readonly class InviteService
{
    public function __construct(
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
        private Config $config,
        private AnonymousNotifiable $anonymousNotifiable,
    ) {
    }

    /**
     * @throws RoleNotFoundException
     */
    public function sendInvite(string $email, string $roleId): void
    {
        if ($this->roleRepository->findById($roleId) === null) {
            throw new RoleNotFoundException($roleId);
        }

        $token = $this->getNewToken();

        $this->userRepository->saveRegisterInviteToken($email, $token, $roleId);

        $this->anonymousNotifiable
            ->route('mail', $email)
            ->notify(new RegisterInviteNotification(
                sprintf('%s?email=%s', route('register.invitation', ['token' => $token]), $email),
            ));
    }

    /**
     * @throws EmailInInvitesNotFoundException
     * @throws InvalidArgumentException when token does not match
     */
    public function validateTokenAndGetRoleId(string $token, string $email): string
    {
        $invite = $this->userRepository->getRegisterInvite($email);

        if (!hash_equals($invite->token, $token)) {
            throw new InvalidArgumentException('Invalid invite token.');
        }

        return $invite->roleId;
    }

    private function getNewToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->config->get('app.key'));
    }
}
