<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Traits;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\User as SocialUser;
use Mockery\MockInterface;

trait SocialiteMocker
{
    protected function mockSocialiteProvider(string $googleId = self::GOOGLE_ID): void
    {
        $user = $this->mock(SocialUser::class, static function (MockInterface $mock) use ($googleId): void {
            $mock->shouldReceive('getId')->andReturn($googleId);
            $mock->shouldReceive('getEmail')->andReturn(self::TESTER_EMAIL);
            $mock->shouldReceive('getName')->andReturn(self::TESTER_NAME);
        });

        $provider = $this->mock(Provider::class, static function (MockInterface $mock) use ($user): void {
            $mock->shouldReceive('driver')->andReturnSelf();
            $mock->shouldReceive('user')->andReturn($user);
            $mock->shouldReceive('request')->andReturn(app()->make(Session::class));
        });

        $this->mock(SocialiteManager::class, static function (MockInterface $mock) use ($provider): void {
            $mock->shouldReceive('driver')->andReturn($provider);
        });
    }

    private function mockSocialiteRequest(string $driver): Request
    {
        $request = $this->mock(Request::class, static function ($mock) use ($driver): void {
            $mock->shouldReceive('segment')->with(2)->andReturn($driver);
        });

        assert($request instanceof Request);

        return $request;
    }
}