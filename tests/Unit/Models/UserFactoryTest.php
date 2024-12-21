<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Unit\Models;

use AssertionError;
use Illuminate\Hashing\HashManager;
use Illuminate\Http\Request;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Models\UserFactory;
use Patrikjak\Auth\Tests\Unit\TestCase;

class UserFactoryTest extends TestCase
{
    private HashManager $hashManager;

    public function testCreateFromRequest(): void
    {
        $user = UserFactory::createFromRequest($this->getRequest());

        $this->assertEquals(self::TESTER_NAME, $user->name);
        $this->assertEquals(self::TESTER_EMAIL, $user->email);
        $this->assertTrue($this->hashManager->check(self::TESTER_PASSWORD, $user->password));
    }

    #[DefineEnvironment('useCustomUserModel')]
    public function testCreateFromRequestWithCustomModel(): void
    {
        $user = UserFactory::createFromRequest($this->getRequest());

        $this->assertEquals(self::TESTER_NAME, $user->name);
        $this->assertEquals(self::TESTER_EMAIL, $user->email);
        $this->assertTrue($this->hashManager->check(self::TESTER_PASSWORD, $user->password));
    }

    public function testGetUserModel(): void
    {
        $this->assertEquals(User::class, UserFactory::getUserModelClass());
    }

    #[DefineEnvironment('useCustomUserModel')]
    public function testGetCustomUserModelClass(): void
    {
        $userModel = config('pjauth.models.user');

        $this->assertEquals($userModel, UserFactory::getUserModelClass());
    }

    #[DefineEnvironment('useCustomInvalidUserModel')]
    public function testGetCustomInvalidUserModelClass(): void
    {
        $this->expectException(AssertionError::class);

        $model = UserFactory::getUserModelClass();
    }

    public function testGetCustomUserMode(): void
    {
        $userModel = UserFactory::getUserModel();

        $this->assertInstanceOf(User::class, $userModel);
    }

    #[DefineEnvironment('useCustomUserModel')]
    public function testGetCustomUserModel(): void
    {
        $userModel = UserFactory::getUserModel();

        $this->assertInstanceOf(User::class, $userModel);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashManager = $this->app->make(HashManager::class);
    }

    private function getRequest(): Request
    {
        return new Request([
            'name' => self::TESTER_NAME,
            'email' => self::TESTER_EMAIL,
            'password' => self::TESTER_PASSWORD,
        ]);
    }
}