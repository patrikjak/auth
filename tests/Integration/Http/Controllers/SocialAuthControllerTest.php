<?php

declare(strict_types = 1);

namespace Integration\Http\Controllers;

use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Patrikjak\Auth\Tests\Traits\SocialiteMocker;

class SocialAuthControllerTest extends TestCase
{
    use SocialiteMocker;

    #[DefineEnvironment('withGoogleSocialiteConfig')]
    public function testGoogleRedirect(): void
    {
        $response = $this->get(route('auth.google'));

        $response->assertStatus(302);
    }

    #[DefineEnvironment('withGoogleSocialiteConfig')]
    public function testGoogleCallback(): void
    {
        $this->mockSocialiteProvider();

        $response = $this->get(route('auth.google.callback'));

        $response->assertStatus(302);
    }
}