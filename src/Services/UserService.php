<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Services;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Registered;
use Patrikjak\Auth\Dto\CreateUserInterface;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

final readonly class UserService
{
    public function __construct(private UserRepository $userRepository, private AuthManager $authManager)
    {
    }

    public function createUserAndLogin(CreateUserInterface $newUser): void
    {
        $user = $this->userRepository->createAndReturnUser($newUser);

        event(new Registered($user));

        $this->authManager->login($user);
    }
}