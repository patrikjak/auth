<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Patrikjak\Auth\Models\RoleType;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Patrikjak\Auth\Tests\Mocks\Models\CustomRoles;
use Patrikjak\Auth\Tests\Mocks\Models\InvalidCustomRoles;
use PHPUnit\Framework\Attributes\DataProvider;

class SeedUserRolesTest extends TestCase
{
    use RefreshDatabase;

    public function testSeedUserRolesCommand(): void
    {
        $this->artisan('seed:user-roles')
            ->expectsOutput('User roles seeded successfully.');

        $this->assertDatabaseHas('roles', [
            'name' => RoleType::SUPERADMIN->name,
            'id' => RoleType::SUPERADMIN->value,
        ]);

        $this->assertDatabaseCount('roles', 3);
    }

    public function testSeedUserRolesCommandWithCustomRoles(): void
    {
        $this->artisan('seed:user-roles', [
            '--enum' => CustomRoles::class,
        ])->expectsOutput('User roles seeded successfully.');

        $this->assertDatabaseHas('roles', [
            'name' => 'ROLE',
            'id' => 1,
        ]);

        $this->assertDatabaseCount('roles', 1);
    }

    #[DataProvider('enumDataProvider')]
    public function testSeedUserRolesWithInvalidEnum(string $enumClass): void
    {
        $this->artisan('seed:user-roles', [
            '--enum' => $enumClass,
        ])->assertFailed();
    }

    /**
     * @return array<string>
     */
    public static function enumDataProvider(): iterable
    {
        yield 'Non existing enum' => ['Invalid\InvalidEnum'];

        yield 'Enum without EnumValues trait' => [InvalidCustomRoles::class];
    }
}