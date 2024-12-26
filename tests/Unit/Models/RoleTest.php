<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Unit\Models;

use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Tests\Unit\TestCase;

class RoleTest extends TestCase
{
    public function testUsersRelationship(): void
    {
        $role = $this->createUser()->role;

        $this->assertInstanceOf(User::class, $role->users->first());
    }
}