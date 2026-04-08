<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Patrikjak\Auth\Tests\Integration\TestCase;

class SyncRolesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testSyncRolesCommand(): void
    {
        $this->artisan('pjauth:sync-roles')
            ->expectsOutput('User roles seeded successfully.');

        $this->assertDatabaseHas('roles', [
            'slug' => 'superadmin',
            'name' => 'Superadmin',
            'is_superadmin' => true,
        ]);

        $this->assertDatabaseCount('roles', 1);
    }
}
