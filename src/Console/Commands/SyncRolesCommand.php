<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;

final class SyncRolesCommand extends Command
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'pjauth:sync-roles';

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Seed default user roles from config';

    public function handle(Config $config, RoleRepository $roleRepository): void
    {
        $roles = $config->get('pjauth.default_roles', []);

        foreach ($roles as $role) {
            $roleRepository->firstOrCreate($role['slug'], $role['name'], $role['is_superadmin'] ?? false);
        }

        $this->info('User roles seeded successfully.');
    }
}
