<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories\Interfaces;

use Patrikjak\Auth\Models\User;

interface UserRepository
{
    public function createAndReturnUser(User $user): User;

    public function getById(string $id): User;

    public function getByEmail(string $email): ?User;

    public function updateGoogleId(User $user, string $googleId): void;

    public function updatePassword(User $user, string $newPassword): void;
}