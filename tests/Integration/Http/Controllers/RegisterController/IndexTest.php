<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Http\Controllers\RegisterController;

use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class IndexTest extends TestCase
{
    public function testIndexCanBeRendered(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('useCustomAppName')]
    public function testIndexCanBeRenderedWithCustomAppName(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableRecaptcha')]
    public function testIndexCanBeRenderedWithoutRecaptcha(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableLogo')]
    public function testIndexCanBeRenderedWithoutLogo(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableIcon')]
    public function testIndexCanBeRenderedWithoutIcon(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableGoogleSocialLogin')]
    public function testIndexCanBeRenderedWithoutGoogleSocialLogin(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableRegisterFeature')]
    public function testIndexPageWithoutRegisterFeature(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->get(route('register'));
    }

    private function makeRequestAndAssertSuccess(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $this->assertMatchesHtmlSnapshot($response->getContent());
    }
}
