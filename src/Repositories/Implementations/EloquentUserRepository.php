<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Repositories\Implementations;

use Illuminate\Hashing\HashManager;
use Patrikjak\Auth\Factories\UserFactory;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Contracts\UserRepository as UserRepositoryInterface;
use SensitiveParameter;

readonly class EloquentUserRepository implements UserRepositoryInterface
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
        /** @var class-string $userModel */
        $userModel = UserFactory::getUserModelClass();

        return $userModel::query()->with('role')->findOrFail($id);
    }

    public function getByEmail(string $email): ?User
    {
        /** @var class-string $userModel */
        $userModel = UserFactory::getUserModelClass();

        return $userModel::query()->with('role')->where('email', $email)->first();
    }

    public function updateGoogleId(User $user, string $googleId): void
    {
        $user->google_id = $googleId;
        $user->save();
    }

    public function updatePassword(User $user, #[SensitiveParameter] string $newPassword): void
    {
        $user->forceFill([
            'password' => $this->hashManager->make($newPassword),
            'remember_token' => null,
        ]);

        $user->save();
    }
}
