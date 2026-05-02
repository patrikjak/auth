<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Repositories\Implementations;

use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Repositories\Contracts\RegisterInviteRepository as RegisterInviteRepositoryInterface;
use Patrikjak\Auth\ValueObjects\RegisterInvite;

readonly class EloquentRegisterInviteRepository implements RegisterInviteRepositoryInterface
{
    public function __construct(private DatabaseManager $databaseManager)
    {
    }

    /**
     * @throws EmailInInvitesNotFoundException
     */
    public function getToken(string $email): string
    {
        return $this->get($email)->token;
    }

    /**
     * @throws EmailInInvitesNotFoundException
     */
    public function get(string $email): RegisterInvite
    {
        $invite = $this->databaseManager->table('register_invites')
            ->select(['token', 'role_id'])
            ->where('email', $email)
            ->first();

        if ($invite === null) {
            throw new EmailInInvitesNotFoundException();
        }

        return new RegisterInvite(
            $invite->token,
            $invite->role_id,
        );
    }

    public function save(string $email, string $token, string $roleId): void
    {
        $this->databaseManager->table('register_invites')
            ->insert([
                'email' => $email,
                'token' => $token,
                'role_id' => $roleId,
                'created_at' => CarbonImmutable::now(),
            ]);
    }

    public function delete(string $email): void
    {
        $this->databaseManager->table('register_invites')
            ->where('email', $email)
            ->delete();
    }
}
