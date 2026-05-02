<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Patrikjak\Auth\Repositories\Contracts\RoleRepository;
use Patrikjak\Auth\Tests\Integration\TestCase;

class RoleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private RoleRepository $roleRepository;

    public function testCreate(): void
    {
        $this->roleRepository->create('admin', 'Admin');

        $this->assertDatabaseHas('roles', [
            'slug' => 'admin',
            'name' => 'Admin',
            'is_superadmin' => false,
        ]);
    }

    public function testCreateSuperadmin(): void
    {
        $this->roleRepository->create('superadmin', 'Superadmin', true);

        $this->assertDatabaseHas('roles', [
            'slug' => 'superadmin',
            'name' => 'Superadmin',
            'is_superadmin' => true,
        ]);
    }

    public function testGetAll(): void
    {
        $this->roleRepository->create('admin', 'Admin');
        $this->roleRepository->create('user', 'User');

        $roles = $this->roleRepository->getAll();

        $this->assertCount(2, $roles);
    }

    public function testFindBySlug(): void
    {
        $this->roleRepository->create('editor', 'Editor');

        $role = $this->roleRepository->findBySlug('editor');

        $this->assertNotNull($role);
        $this->assertSame('editor', $role->slug);
    }

    public function testFindBySlugReturnsNullForMissing(): void
    {
        $role = $this->roleRepository->findBySlug('non-existing');

        $this->assertNull($role);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleRepository = $this->app->make(RoleRepository::class);
    }
}
