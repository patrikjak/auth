<?php

declare(strict_types = 1);

namespace Integration\Repositories;

use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;
use Patrikjak\Auth\Tests\Integration\TestCase;

class RoleRepositoryTest extends TestCase
{
    private RoleRepository $roleRepository;

    public function testCreate(): void
    {
        $this->roleRepository->create(1, 'admin');

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => 'admin',
        ]);
    }

    public function testGetAll(): void
    {
        $this->roleRepository->create(1, 'admin');
        $this->roleRepository->create(2, 'user');

        $roles = $this->roleRepository->getAll();

        $this->assertCount(2, $roles);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleRepository = $this->app->make(RoleRepository::class);
    }
}