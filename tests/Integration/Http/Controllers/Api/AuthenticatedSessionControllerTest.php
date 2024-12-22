<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Http\Controllers\Api;

use Illuminate\Config\Repository;
use Patrikjak\Auth\Tests\Integration\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    public function testStore(): void
    {
        $this->skipRecaptcha();
        $user = $this->createUser();

        $response = $this->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => self::TESTER_PASSWORD,
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticated('web');
    }

    public function testLoginWithInvalidData(): void
    {
        $this->skipRecaptcha();
        $user = $this->createUser();

        $response = $this->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testLoginRateLimiter(): void
    {
        $this->skipRecaptcha();

        $user = $this->createUser();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('api.login'), [
                'email' => $user->email,
                'password' => 'invalid-password',
            ]);
        }

        $response = $this->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertUnprocessable();
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testLogout(): void
    {
        $configRepository = $this->app->make(Repository::class);
        
        $this->actingAs($this->createUser());

        $response = $this->postJson(route('api.logout'));

        $response->assertRedirect($configRepository->get('pjauth.redirect_after_logout'));
        $this->assertGuest();
    }
}