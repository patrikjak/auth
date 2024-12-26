<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Traits;

use Patrikjak\Auth\Database\Factories\UserFactory;
use Patrikjak\Auth\Models\User;

trait UserCreator
{
    protected function createUser(?string $googleId = null): User
    {
        $userFactory = User::factory();
        assert($userFactory instanceof UserFactory);

        if ($googleId !== null) {
            $userFactory->withGoogleId($googleId);
        }

        return $userFactory->create([
            'name' => self::TESTER_NAME,
            'email' => self::TESTER_EMAIL,
            'password' => bcrypt(self::TESTER_PASSWORD),
        ]);
    }
}