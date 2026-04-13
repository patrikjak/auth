<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Services;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Config\Repository as Config;
use Patrikjak\Auth\Events\RegisteredViaInviteEvent;
use Patrikjak\Auth\Exceptions\InvalidCredentialsException;
use Patrikjak\Auth\Exceptions\RoleNotFoundException;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;
use SensitiveParameter;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
        private AuthManager $authManager,
        private PasswordBroker $passwordBroker,
        private Config $config,
    ) {
    }

    /**
     * @throws RoleNotFoundException
     */
    public function createUserAndLogin(User $newUser): void
    {
        $slug = $this->config->get('pjauth.default_role_slug');
        $role = $this->roleRepository->findBySlug($slug);

        if ($role === null) {
            throw new RoleNotFoundException($slug, 'slug');
        }

        $newUser->role_id = $role->id;

        $user = $this->userRepository->createAndReturnUser($newUser);

        event(new Registered($user));

        $this->authManager->login($user);
    }

    public function createUserAndLoginViaInvitation(User $newUser, string $roleId): void
    {
        $newUser->role_id = $roleId;

        $user = $this->userRepository->createAndReturnUser($newUser);

        event(new Registered($user));

        $this->authManager->login($user);
        event(new RegisteredViaInviteEvent($newUser));
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(string $email, #[SensitiveParameter] string $password, bool $remember): void
    {
        if (!$this->authManager->attempt(['email' => $email, 'password' => $password], $remember)) {
            throw new InvalidCredentialsException();
        }
    }

    /**
     * @param array<string> $credentials
     */
    public function resetPasswordWithTokenValidation(
        array $credentials,
        #[SensitiveParameter] string $newPassword,
    ): string
    {
        return $this->passwordBroker->reset($credentials, function (User $user) use ($newPassword): void {
            $this->userRepository->updatePassword($user, $newPassword);
            event(new PasswordReset($user));
        });
    }

    public function changePasswordForUser(string $userId, #[SensitiveParameter] string $newPassword): void
    {
        $user = $this->userRepository->getById($userId);

        $this->userRepository->updatePassword($user, $newPassword);
    }
}
