<?php

declare(strict_types = 1);

namespace Integration\Http;

use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class AuthenticatedSessionControllerTest extends TestCase
{
    public function testLoginScreenCanBeRendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $this->assertMatchesHtmlSnapshot($response->getContent());
    }

    #[DefineEnvironment('disableLoginFeature')]
    public function testLoginPageWithoutLoginFeature(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->get(route('login'));
    }

    #[DefineEnvironment('disablePasswordResetFeature')]
    public function testLoginPageWithoutResetPasswordFeature(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $this->assertMatchesHtmlSnapshot($response->getContent());
    }
}
