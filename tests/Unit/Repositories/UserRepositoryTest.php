<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Unit\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;
use Patrikjak\Auth\Tests\Unit\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $userRepository;

    public function testGetById(): void
    {
        $user = $this->createUser();

        $this->assertEquals($user->id, $this->userRepository->getById($user->id)->id);
    }

    public function testGetByIdNonExisting(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->userRepository->getById("abcedfg");
    }

    #[DefineEnvironment('useCustomUserModel')]
    public function testGetByIdWithCustomUserModel(): void
    {
        $user = $this->createUser();

        $userFromDatabase = $this->userRepository->getById($user->id);

        $this->assertEquals($user->id, $userFromDatabase->id);
        $this->assertEquals(get_class($user), get_class($userFromDatabase));
    }

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(UserRepository::class);
    }
}