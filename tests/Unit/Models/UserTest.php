<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Unit\Models;

use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Tests\Unit\TestCase;

class UserTest extends TestCase
{
    public function testRoleRelationship(): void
    {
        $user = $this->createUser();

        $this->assertInstanceOf(Role::class, $user->role);
    }
}