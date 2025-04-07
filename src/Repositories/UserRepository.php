<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Hashing\HashManager;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Factories\UserFactory;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository as UserRepositoryInterface;

final readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(private HashManager $hashManager, private DatabaseManager $databaseManager)
    {
    }

    public function createAndReturnUser(User $user): User
    {
        $user->save();

        return $user;
    }

    public function getById(string $id): User
    {
        $userModel = UserFactory::getUserModelClass();

        return $userModel::with('role')->findOrFail($id);
    }

    public function getByEmail(string $email): ?User
    {
        $userModel = UserFactory::getUserModelClass();

        return $userModel::with('role')->where('email', $email)->first();
    }

    public function updateGoogleId(User $user, string $googleId): void
    {
        $user->google_id = $googleId;
        $user->save();
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->forceFill([
            'password' => $this->hashManager->make($newPassword),
            'remember_token' => null,
        ]);

        $user->save();
    }

    /**
     * @throws EmailInInvitesNotFoundException
     */
    public function getRegisterInviteToken(string $email): string
    {
        $tokenResult = $this->databaseManager->table('register_invites')
            ->select(['token'])
            ->where('email', $email)
            ->get();

        if ($tokenResult->isEmpty()) {
            return throw new EmailInInvitesNotFoundException();
        }

        return $tokenResult->first()->token;
    }

    public function saveRegisterInviteToken(string $email, string $token): void
    {
        $this->databaseManager->table('register_invites')
            ->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => CarbonImmutable::now(),
            ]);
    }

    public function deleteRegisterInvite(string $email): void
    {
        $this->databaseManager->table('register_invites')
            ->where('email', $email)
            ->delete();
    }
}