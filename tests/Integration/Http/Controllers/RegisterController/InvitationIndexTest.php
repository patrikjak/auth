<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Http\Controllers\RegisterController;

use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class InvitationIndexTest extends TestCase
{
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testIndexCanBeRendered(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('useCustomAppName')]
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testIndexCanBeRenderedWithCustomAppName(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableLogo')]
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testIndexCanBeRenderedWithoutLogo(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableIcon')]
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testIndexCanBeRenderedWithoutIcon(): void
    {
        $this->makeRequestAndAssertSuccess();
    }

    #[DefineEnvironment('disableRegisterViaInvitationFeature')]
    public function testIndexPageWithoutRegisterFeature(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->get(route('register.invitation', ['token' => 'test-token']));
    }

    private function makeRequestAndAssertSuccess(): void
    {
        $response = $this->get(sprintf(
            '%s?email=%s',
            route('register.invitation', ['token' => 'test-token']),
            'tester@test.com',
        ));

        $response->assertOk();
        $this->assertMatchesHtmlSnapshot($response->getContent());
    }
}
