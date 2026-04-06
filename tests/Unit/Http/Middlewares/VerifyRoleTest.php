<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Unit\Http\Middlewares;

use Illuminate\Http\Request;
use Mockery\MockInterface;
use Patrikjak\Auth\Factories\UserFactory;
use Patrikjak\Auth\Http\Middlewares\VerifyRole;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;
use Patrikjak\Auth\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyRoleTest extends TestCase
{
    public function testHandleSuccessful(): void
    {
        $user = $this->createUserWithRole('admin');
        $this->actingAs($user);

        $request = $this->mockRequest($user);
        $middleware = new VerifyRole($this->app->make(RoleRepository::class));

        $response = $middleware->handle($request, static function (): void {
        }, 'admin');

        $this->assertNull($response);
    }

    public function testHandleWithNonExistingRole(): void
    {
        $user = $this->createUserWithRole('admin');
        $this->actingAs($user);

        $request = $this->mockRequest($user);
        $middleware = new VerifyRole($this->app->make(RoleRepository::class));

        $this->expectException(HttpException::class);
        $middleware->handle($request, static function (): void {
        }, 'non-existing-role');
    }

    public function testHandleWithInvalidRole(): void
    {
        Role::factory()->withSlug('admin')->create();
        $user = $this->createUserWithRole('user');
        $this->actingAs($user);

        $request = $this->mockRequest($user);
        $middleware = new VerifyRole($this->app->make(RoleRepository::class));

        $this->expectException(HttpException::class);
        $middleware->handle($request, static function (): void {
        }, 'admin');
    }

    public function testHandleWithSuperAdmin(): void
    {
        Role::factory()->withSlug('admin')->create();
        $user = $this->createUserWithRole('superadmin', isSuperadmin: true);
        $this->actingAs($user);

        $request = $this->mockRequest($user);
        $middleware = new VerifyRole($this->app->make(RoleRepository::class));

        $response = $middleware->handle($request, static function (): void {
        }, 'admin');

        $this->assertNull($response);
    }

    public function testWithRole(): void
    {
        $this->assertSame('Patrikjak\Auth\Http\Middlewares\VerifyRole:superadmin', VerifyRole::withRole('superadmin'));
        $this->assertSame('Patrikjak\Auth\Http\Middlewares\VerifyRole:admin', VerifyRole::withRole('admin'));
        $this->assertSame('Patrikjak\Auth\Http\Middlewares\VerifyRole:user', VerifyRole::withRole('user'));
    }

    private function mockRequest(User $user): Request
    {
        $request = $this->mock(Request::class, static function (MockInterface $mock) use ($user): void {
            $mock->shouldReceive('user')->andReturn($user);
        });

        assert($request instanceof Request);

        return $request;
    }

    private function createUserWithRole(string $slug, bool $isSuperadmin = false): User
    {
        $userModel = UserFactory::getUserModelClass();

        $role = Role::factory()->create([
            'slug' => $slug,
            'name' => ucfirst($slug),
            'is_superadmin' => $isSuperadmin,
        ]);

        return $userModel::factory()->for($role)->create();
    }
}
