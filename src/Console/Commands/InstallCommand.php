<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class InstallCommand extends Command
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'install:pjauth';

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Install patrikjak/auth package';

    public function handle(): void
    {
        $migrationsToDelete = new Collection(scandir(database_path('migrations')))
            ->filter(static function (string $file) {
                return str_contains($file, 'create_users_table') || str_contains($file, 'create_roles_table');
            });

        foreach ($migrationsToDelete as $migration) {
            exec(sprintf('rm -rf %s', database_path(sprintf('migrations/%s', $migration))));
        }

        $this->call('install:pjutils');
        $this->call('vendor:publish', ['--tag' => 'pjauth-assets', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'pjauth-config']);
        $this->call('vendor:publish', ['--tag' => 'pjauth-migrations', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'pjauth-translations', '--force' => true]);
        $this->call('migrate:fresh', ['--force' => true]);
        $this->call('seed:user-roles', ['--enum' => 'Patrikjak\\Auth\\Models\\RoleType']);
    }
}
