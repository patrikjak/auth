<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Patrikjak\Auth\Tests\Integration\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSendLinkWithInvalidData(): void
    {
        $this->skipRecaptcha();

        $response = $this->post(route('api.password.email'), [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testSendLinkWithInvalidUser(): void
    {
        $this->skipRecaptcha();
        $this->createUser();

        $response = $this->post(route('api.password.email'), [
            'email' => 'user@not.found',
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testSendLinkWithThrottle(): void
    {
        $this->skipRecaptcha();
        $this->createUser();

        for($i = 0; $i < 5; $i++) {
            $this->post(route('api.password.email'), [
                'email' => self::TESTER_EMAIL,
            ]);
        }

        $response = $this->post(route('api.password.email'), [
            'email' => self::TESTER_EMAIL,
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testSendLink(): void
    {
        $this->skipRecaptcha();
        $this->createUser();

        $response = $this->post(route('api.password.email'), [
            'email' => self::TESTER_EMAIL,
        ]);

        $response->assertStatus(200);
    }
}