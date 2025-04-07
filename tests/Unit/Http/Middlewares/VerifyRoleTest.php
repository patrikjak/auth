<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Unit\Http\Middlewares;

use Illuminate\Http\Request;
use Mockery\MockInterface;
use Patrikjak\Auth\Factories\UserFactory;
use Patrikjak\Auth\Http\Middlewares\VerifyRole;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\RoleType;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Tests\Unit\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyRoleTest extends TestCase
{
    public function testHandleSuccessful(): void
    {
        $role = RoleType::ADMIN;
        $user = $this->createUserWithRole($role);
        $this->actingAs($user);

        $request = $this->mockRequest($user);

        $middleware = new VerifyRole();
        $response = $middleware->handle(
            $request,
            static function (): void {},
            $role->value,
        );

        $this->assertNull($response);
    }

    public function testHandleWithNonExistingRole(): void
    {
        $role = RoleType::ADMIN;
        $user = $this->createUserWithRole($role);
        $this->actingAs($user);

        $request = $this->mockRequest($user);

        $middleware = new VerifyRole();
        $this->expectException(HttpException::class);
        $middleware->handle(
            $request,
            static function (): void {},
            999,
        );
    }

    public function testHandleWithInvalidRole(): void
    {
        $user = $this->createUserWithRole(RoleType::USER);
        $this->actingAs($user);

        $request = $this->mockRequest($user);

        $middleware = new VerifyRole();
        $this->expectException(HttpException::class);
        $middleware->handle(
            $request,
            static function (): void {},
            RoleType::ADMIN->value,
        );
    }

    public function testHandleWithSuperAdmin(): void
    {
        $user = $this->createUserWithRole(RoleType::SUPERADMIN);
        $this->actingAs($user);

        $request = $this->mockRequest($user);

        $middleware = new VerifyRole();
        $response = $middleware->handle(
            $request,
            static function (): void {},
            RoleType::ADMIN->value,
        );

        $this->assertNull($response);
    }

    public function testWithRole(): void
    {
        $this->assertSame('Patrikjak\Auth\Http\Middlewares\VerifyRole:1', VerifyRole::withRole(RoleType::SUPERADMIN));
        $this->assertSame('Patrikjak\Auth\Http\Middlewares\VerifyRole:2', VerifyRole::withRole(RoleType::ADMIN));
        $this->assertSame('Patrikjak\Auth\Http\Middlewares\VerifyRole:3', VerifyRole::withRole(RoleType::USER));
    }

    private function mockRequest(User $user): Request
    {
        $request = $this->mock(Request::class, static function (MockInterface $mock) use ($user): void {
            $mock->shouldReceive('user')->andReturn($user);
        });

        assert($request instanceof Request);

        return $request;
    }

    private function createUserWithRole(RoleType $roleType): User
    {
        $userModel = UserFactory::getUserModelClass();

        $role = Role::factory()->create([
            'id' => $roleType->value,
            'name' => $roleType->name,
        ]);

        return $userModel::factory()
            ->for($role)
            ->create();
    }
}