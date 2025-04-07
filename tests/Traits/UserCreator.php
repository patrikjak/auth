<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Traits;

use Patrikjak\Auth\Database\Factories\UserFactory;
use Patrikjak\Auth\Factories\UserFactory as UserModelFactory;
use Patrikjak\Auth\Models\RoleType;
use Patrikjak\Auth\Models\User;

trait UserCreator
{
    protected function createUser(?string $googleId = null, ?RoleType $roleType = null): User
    {
        $userModel = UserModelFactory::getUserModelClass();
        $userFactory = $userModel::factory();
        assert($userFactory instanceof UserFactory);
        
        $userFactory = $userFactory->withName(self::TESTER_NAME);
        $userFactory = $userFactory->withEmail(self::TESTER_EMAIL);
        $userFactory = $userFactory->withPassword(self::TESTER_PASSWORD);

        if ($googleId !== null) {
            $userFactory = $userFactory->withGoogleId($googleId);
        }

        if ($roleType !== null) {
            $userFactory = $userFactory->withRole($roleType);
        }

        return $userFactory->create();
    }
}