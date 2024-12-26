<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories;

use Illuminate\Hashing\HashManager;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Models\UserFactory;
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
}