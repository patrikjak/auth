<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Services;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;
use Patrikjak\Auth\Events\RegisteredViaInviteEvent;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Exceptions\InvalidCredentialsException;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Notifications\RegisterInviteNotification;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthManager $authManager,
        private PasswordBroker $passwordBroker,
        private Config $config,
        private AnonymousNotifiable $anonymousNotifiable,
    ) {
    }

    public function createUserAndLogin(User $newUser): void
    {
        $user = $this->userRepository->createAndReturnUser($newUser);

        event(new Registered($user));

        $this->authManager->login($user);
    }

    public function createUserAndLoginViaInvitation(User $newUser): void
    {
        $this->createUserAndLogin($newUser);

        event(new RegisteredViaInviteEvent($newUser));
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

    /**
     * @param array<string> $credentials
     */
    public function resetPasswordWithTokenValidation(array $credentials, string $newPassword): string
    {
        return $this->passwordBroker->reset($credentials, function (User $user) use ($newPassword): void {
            $this->userRepository->updatePassword($user, $newPassword);
            event(new PasswordReset($user));
        });
    }

    public function sendRegisterInvite(string $email): void
    {
        $token = $this->getNewToken();

        $this->userRepository->saveRegisterInviteToken($email, $token);

        $this->anonymousNotifiable
            ->route('mail', $email)
            ->notify(new RegisterInviteNotification(
                sprintf('%s?email=%s', route('register.invitation', ['token' => $token]), $email),
            ));
    }

    public function getNewToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->config->get('app.key'));
    }

    /**
     * @throws EmailInInvitesNotFoundException
     */
    public function inviteTokenIsValid(string $token, string $email): bool
    {
        $databaseInviteToken = $this->userRepository->getRegisterInviteToken($email);

        return hash_equals($databaseInviteToken, $token);
    }

    public function changePasswordForUser(string $userId, string $newPassword): void
    {
        $user = $this->userRepository->getById($userId);

        $this->userRepository->updatePassword($user, $newPassword);
    }
}