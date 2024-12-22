<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Patrikjak\Auth\Models\RoleType;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;
use Throwable;

class SeedUserRoles extends Command
{
    /**
     * @var string
     */
    protected $signature = 'seed:user-roles {--enum=}';

    /**
     * @var string
     */
    protected $description = 'Seed user roles from the Role model';

    private string $rolesEnum = RoleType::class;

    /**
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->rolesEnum = $this->option('enum') ?? RoleType::class;

        $this->ensureEnumExists();
        $this->ensureEnumHasEnumValuesTrait();

        $roles = $this->rolesEnum::getAll();
        $roleRepository = app()->make(RoleRepository::class);

        foreach ($roles as $role) {
            $roleRepository->create($role->value, $role->name);
        }

        $this->info('User roles seeded successfully.');
    }

    /**
     * @throws Throwable
     */
    private function ensureEnumExists(): void
    {
        if (!class_exists($this->rolesEnum)) {
            $this->fail('The enum class does not exist.');
        }
    }

    /**
     * @throws Throwable
     */
    private function ensureEnumHasEnumValuesTrait(): void
    {
        $traits = class_uses_recursive($this->rolesEnum);

        if (!in_array('Patrikjak\Utils\Common\Traits\EnumValues', $traits, true)) {
            $this->fail('The enum class does not use the EnumValues trait.');
        }
    }
}
