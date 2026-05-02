<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Repositories\Contracts;

use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\ValueObjects\RegisterInvite;

interface RegisterInviteRepository
{
    /**
     * @throws EmailInInvitesNotFoundException
     */
    public function getToken(string $email): string;

    /**
     * @throws EmailInInvitesNotFoundException
     */
    public function get(string $email): RegisterInvite;

    public function save(string $email, string $token, string $roleId): void;

    public function delete(string $email): void;
}
