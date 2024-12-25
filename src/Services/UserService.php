<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Services;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\PasswordBroker;
use Patrikjak\Auth\Exceptions\InvalidCredentialsException;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthManager $authManager,
        private PasswordBroker $passwordBroker,
    ) {
    }

    public function createUserAndLogin(User $newUser): void
    {
        $user = $this->userRepository->createAndReturnUser($newUser);

        event(new Registered($user));

        $this->authManager->login($user);
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(string $email, string $password, bool $remember): void
    {
        if (!$this->authManager->attempt(['email' => $email, 'password' => $password], $remember)) {
            throw new InvalidCredentialsException();
        }
    }

    public function resetPasswordWithTokenValidation(array $credentials, string $newPassword): string
    {
        return $this->passwordBroker->reset($credentials, function (User $user) use ($newPassword): void {
            $this->userRepository->resetPassword($user, $newPassword);
            event(new PasswordReset($user));
        });
    }
}