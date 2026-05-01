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
use Patrikjak\Auth\Repositories\Contracts\RegisterInviteRepository;
use Patrikjak\Auth\Repositories\Contracts\RoleRepository;

final readonly class InviteService
{
    public function __construct(
        private RegisterInviteRepository $registerInviteRepository,
        private RoleRepository $roleRepository,
        private Config $config,
        private AnonymousNotifiable $anonymousNotifiable,
    ) {
    }

    /**
     * @throws RoleNotFoundException
     */
    public function sendInvite(string $email, ?string $roleId = null): void
    {
        $roleId = $this->resolveRoleId($roleId);

        $token = $this->getNewToken();

        $this->registerInviteRepository->save($email, $token, $roleId);

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
        $invite = $this->registerInviteRepository->get($email);

        if (!hash_equals($invite->token, $token)) {
            throw new InvalidArgumentException('Invalid invite token.');
        }

        return $invite->roleId;
    }

    /**
     * @throws RoleNotFoundException
     */
    private function resolveRoleId(?string $roleId): string
    {
        if ($roleId !== null) {
            if ($this->roleRepository->findById($roleId) === null) {
                throw new RoleNotFoundException($roleId);
            }

            return $roleId;
        }

        $slug = $this->config->get('pjauth.default_role_slug');
        $role = $this->roleRepository->findBySlug($slug);

        if ($role === null) {
            throw new RoleNotFoundException($slug, 'slug');
        }

        return $role->id;
    }

    private function getNewToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->config->get('app.key'));
    }
}
