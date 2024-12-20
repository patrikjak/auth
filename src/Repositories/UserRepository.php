<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories;

use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository as UserRepositoryInterface;

final readonly class UserRepository implements UserRepositoryInterface
{
    public function createAndReturnUser(User $user): User
    {
        $user->save();

        return $user;
    }
}