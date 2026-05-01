<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Patrikjak\Auth\Repositories\Contracts\RoleRepository;

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
    protected $description = 'Ensure all default roles from config exist';

    public function handle(RoleRepository $roleRepository): void
    {
        $defaultRoles = config('pjauth.default_roles', [
            ['slug' => 'superadmin', 'name' => 'Superadmin', 'is_superadmin' => true],
        ]);

        foreach ($defaultRoles as $role) {
            $roleRepository->firstOrCreate($role['slug'], $role['name'], $role['is_superadmin'] ?? false);
        }

        $this->info('User roles seeded successfully.');
    }
}
