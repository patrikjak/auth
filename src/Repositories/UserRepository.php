<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories;

use Illuminate\Hashing\HashManager;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository as UserRepositoryInterface;

final readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(private HashManager $hashManager)
    {
    }

    public function createAndReturnUser(User $user): User
    {
        $user->save();

        return $user;
    }

    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function updateGoogleId(User $user, string $googleId): void
    {
        $user->google_id = $googleId;
        $user->save();
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        $user->forceFill([
            'password' => $this->hashManager->make($newPassword),
            'remember_token' => null,
        ]);

        $user->save();
    }
}