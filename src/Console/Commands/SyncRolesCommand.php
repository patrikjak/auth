<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
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
    protected $description = 'Ensure the superadmin role exists';

    public function handle(RoleRepository $roleRepository): void
    {
        $roleRepository->firstOrCreate('superadmin', 'Superadmin', true);

        $this->info('User roles seeded successfully.');
    }
}
