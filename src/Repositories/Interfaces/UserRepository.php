<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories\Interfaces;

use Patrikjak\Auth\Models\User;

interface UserRepository
{
    public function createAndReturnUser(User $user): User;

    public function getByEmail(string $email): ?User;

    public function updateGoogleId(User $user, string $googleId): void;
}