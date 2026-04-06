<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration\Console\Commands;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Patrikjak\Auth\Tests\Integration\TestCase;

class CreateUsersCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws BindingResolutionException
     */
    public function testCreateUsersCommand(): void
    {
        $this->seedDefaultRole();

        $this->artisan('pjauth:create-users')
            ->expectsQuestion('User name:', 'Admin')
            ->expectsQuestion('User email:', 'admin@p.j')
            ->expectsQuestion('User password:', 'password')
            ->expectsQuestion('Role slug:', 'admin')
            ->expectsConfirmation('Do you want to create another user?', 'yes')
            ->expectsQuestion('User name:', 'User')
            ->expectsQuestion('User email:', 'user@p.j')
            ->expectsQuestion('User password:', 'password')
            ->expectsQuestion('Role slug:', 'admin')
            ->expectsConfirmation('Do you want to create another user?', 'no')
            ->assertExitCode(0);

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', ['name' => 'Admin', 'email' => 'admin@p.j']);
        $this->assertDatabaseHas('users', ['name' => 'User', 'email' => 'user@p.j']);
    }
}
