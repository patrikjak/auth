<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Services\SocialAuthService;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Patrikjak\Auth\Tests\Traits\SocialiteMocker;
use PHPUnit\Framework\Attributes\DataProvider;

class SocialAuthServiceTest extends TestCase
{
    use SocialiteMocker;
    use RefreshDatabase;

    #[DataProvider('driverProvider')]
    public function testGetGoogleDriverFromRequest(string $driver): void
    {
        $request = $this->mockSocialiteRequest($driver);

        $socialAuthService = $this->app->make(SocialAuthService::class);
        $resolvedDriver = $socialAuthService->getDriverFromRequest($request);

        $this->assertSame($driver, $resolvedDriver);
    }

    #[DefineEnvironment('withGoogleSocialiteConfig')]
    #[DataProvider('googleIdProvider')]
    public function testHandleSocialUserForExistingUser(?string $googleId = null): void
    {
        $request = $this->mockSocialiteRequest('google');
        $user = $this->createUser($googleId);

        $this->mockSocialiteProvider($googleId === null ? self::GOOGLE_ID : $googleId);

        $socialAuthService = $this->app->make(SocialAuthService::class);
        $socialAuthService->handleSocialUser($request);
        
        $this->assertAuthenticatedAs($user);

        $googleId === null
            ? $this->assertDatabaseHas('users', ['email' => self::TESTER_EMAIL, 'google_id' => self::GOOGLE_ID])
            : $this->assertDatabaseHas('users', ['email' => self::TESTER_EMAIL, 'google_id' => $googleId]);
    }

    public function testHandleSocialUserForNonExistingUser(): void
    {
        $request = $this->mockSocialiteRequest('google');
        $this->mockSocialiteProvider();

        $socialAuthService = $this->app->make(SocialAuthService::class);
        $socialAuthService->handleSocialUser($request);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => self::TESTER_EMAIL, 'google_id' => self::GOOGLE_ID]);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function driverProvider(): iterable
    {
        yield 'Google' => ['google'];

        yield 'Facebook' => ['facebook'];
    }

    /**
     * @return iterable<string, array{string|null}>
     */
    public static function googleIdProvider(): iterable
    {
        yield 'With Google Id' => ['123456789'];

        yield 'Without Google Id' => [null];
    }
}