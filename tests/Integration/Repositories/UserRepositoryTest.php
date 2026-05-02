<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Contracts\UserRepository;
use Patrikjak\Auth\Tests\Integration\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $userRepository;

    public function testCreateAndReturnUser(): void
    {
        $user = $this->userRepository->createAndReturnUser($this->getUser());

        $this->assertDatabaseHas('users', [
            'name' => self::TESTER_NAME,
            'email' => self::TESTER_EMAIL,
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDefaultRole();
        $this->userRepository = $this->app->make(UserRepository::class);
    }

    private function getUser(): User
    {
        $role = Role::where('slug', config('pjauth.default_role_slug', 'superadmin'))->firstOrFail();

        $user = new User();
        $user->name = self::TESTER_NAME;
        $user->email = self::TESTER_EMAIL;
        $user->password = bcrypt('password123');
        $user->role_id = $role->id;

        return $user;
    }
}
