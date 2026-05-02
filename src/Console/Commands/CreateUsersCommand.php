<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Hashing\HashManager;
use Patrikjak\Auth\Exceptions\RoleNotFoundException;
use Patrikjak\Auth\Factories\UserFactory;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Contracts\RoleRepository;
use Patrikjak\Auth\Repositories\Contracts\UserRepository;

final class CreateUsersCommand extends Command
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'pjauth:create-users';

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Create users';

    public function handle(
        Config $config,
        HashManager $hashManager,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
    ): void {
        $user = $this->getUserData($config, $hashManager, $roleRepository);
        $userRepository->createAndReturnUser($user);

        while ($this->confirm('Do you want to create another user?')) {
            $user = $this->getUserData($config, $hashManager, $roleRepository);
            $userRepository->createAndReturnUser($user);
        }
    }

    private function getUserData(Config $config, HashManager $hashManager, RoleRepository $roleRepository): User
    {
        $defaultPassword = $config->get('pjauth.user_default_password');
        $defaultRoleSlug = $config->get('pjauth.default_role_slug', 'superadmin');
        $roles = $roleRepository->getAll();

        $name = $this->ask('User name:', 'Admin');
        $email = $this->ask('User email:', 'email@example.com');
        $password = $this->ask('User password:', $defaultPassword);

        $this->info(
            sprintf(
                'Available roles: %s',
                $roles->map(static fn (Role $role) => $role->slug)->implode(' | '),
            ),
        );

        $roleSlug = $this->ask('Role slug:', $defaultRoleSlug);
        $role = $roleRepository->findBySlug($roleSlug);

        if ($role === null) {
            throw new RoleNotFoundException($roleSlug, 'slug');
        }

        $userModel = UserFactory::getUserModel();

        $userModel->name = $name;
        $userModel->email = $email;
        $userModel->password = $hashManager->make($password);
        $userModel->role_id = $role->id;

        return $userModel;
    }
}
