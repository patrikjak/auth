<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories;

use Illuminate\Config\Repository;
use Patrikjak\Auth\Dto\CreateUserInterface;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository as UserRepositoryInterface;

final readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(private Repository $config)
    {
    }

    public function createAndReturnUser(CreateUserInterface $user): User
    {
        $userModel = $this->config->get('pjauth.models.user');
        assert($userModel instanceof User);

        return $userModel::create($user->getFillable());
    }
}