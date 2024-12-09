<?php

declare(strict_types = 1);

namespace Integration\Http;

use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class ResetPasswordControllerTest extends TestCase
{
    public function testForgotPasswordScreenCanBeRendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
        $this->assertMatchesHtmlSnapshot($response->getContent());
    }

    #[DefineEnvironment('disablePasswordResetFeature')]
    public function testForgotPasswordScreenWithDisabledPasswordResetFeature(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->get(route('password.request'));
    }

    public function testResetPasswordScreenCanBeRendered(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'test-token']));

        $response->assertOk();
        $this->assertMatchesHtmlSnapshot($response->getContent());
    }

    #[DefineEnvironment('disablePasswordResetFeature')]
    public function testResetPasswordScreenWithDisabledPasswordResetFeature(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->get(route('password.reset', ['token' => 'test-token']));
    }
}
