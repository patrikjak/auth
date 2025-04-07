<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Patrikjak\Auth\Factories\UserFactory;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

class CreateUsersCommand extends Command
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'create:users';

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Create users';

    private string $defaultPassword;

    private Collection $roles;

    private UserRepository $userRepository;

    /**
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $this->setUp();

        $user = $this->getUserData();
        $this->userRepository->createAndReturnUser($user);

        while ($this->confirm('Do you want to create another user?')) {
            $user = $this->getUserData();
            $this->userRepository->createAndReturnUser($user);
        }
    }

    private function getUserData(): User
    {
        $name = $this->ask('User name:', 'Admin');
        $email = $this->ask('User email:', 'admin@p.j');
        $password = $this->ask('User password:', $this->defaultPassword);

        $this->info(
            sprintf(
                'Available roles: %s',
                $this->roles->map(static fn (Role $role) => sprintf('%s => %s', $role->name, $role->id))
                    ->implode(' | ')
            ),
        );

        $role = $this->ask('Role:', '3');

        $userModel = UserFactory::getUserModel();
        $userModel->name = $name;
        $userModel->email = $email;
        $userModel->password = $password;
        $userModel->role_id = $role;

        return $userModel;
    }

    /**
     * @throws BindingResolutionException
     */
    private function setUp(): void
    {
        $userRepository = app()->make(UserRepository::class);
        $roleRepository = app()->make(RoleRepository::class);
        $config = app()->make(Repository::class);

        $this->defaultPassword = $config->get('pjauth.user_default_password');
        $this->roles = $roleRepository->getAll();
        $this->userRepository = $userRepository;
    }
}
