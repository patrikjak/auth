<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Notifications\RegisterInviteNotification;
use Patrikjak\Auth\Services\InviteService;
use Patrikjak\Auth\Tests\Integration\TestCase;

class InviteServiceTest extends TestCase
{
    use RefreshDatabase;

    private InviteService $inviteService;

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSendInviteStoresToken(): void
    {
        $this->inviteService->sendInvite(self::TESTER_EMAIL);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSendInviteStoresRoleId(): void
    {
        $this->inviteService->sendInvite(self::TESTER_EMAIL, 2);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL, 'role_id' => 2]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSendInviteWithoutRoleStoresNullRoleId(): void
    {
        $this->inviteService->sendInvite(self::TESTER_EMAIL);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL, 'role_id' => null]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSendInviteSendsNotification(): void
    {
        Notification::fake();

        $this->inviteService->sendInvite(self::TESTER_EMAIL);

        Notification::assertCount(1);
        Notification::assertSentTo(
            new AnonymousNotifiable(),
            RegisterInviteNotification::class,
            static fn (
                RegisterInviteNotification $notification,
                $channels,
                $notifiable,
            ) => $notifiable->routes['mail'] === self::TESTER_EMAIL,
        );
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testValidateTokenAndGetRoleIdReturnsNullWhenNoRole(): void
    {
        $token = 'test_token';
        $this->insertInvite(token: $token, roleId: null);

        $roleId = $this->inviteService->validateTokenAndGetRoleId($token, self::TESTER_EMAIL);

        $this->assertNull($roleId);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testValidateTokenAndGetRoleIdReturnsRoleId(): void
    {
        $token = 'test_token';
        $this->insertInvite(token: $token, roleId: 2);

        $roleId = $this->inviteService->validateTokenAndGetRoleId($token, self::TESTER_EMAIL);

        $this->assertSame(2, $roleId);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testValidateTokenAndGetRoleIdThrowsForInvalidToken(): void
    {
        $this->insertInvite(token: 'correct_token');

        $this->expectException(InvalidArgumentException::class);

        $this->inviteService->validateTokenAndGetRoleId('wrong_token', self::TESTER_EMAIL);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testValidateTokenAndGetRoleIdThrowsWhenEmailNotFound(): void
    {
        $this->expectException(EmailInInvitesNotFoundException::class);

        $this->inviteService->validateTokenAndGetRoleId('any_token', 'nonexistent@example.com');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->inviteService = $this->app->make(InviteService::class);
    }

    private function insertInvite(
        string $email = self::TESTER_EMAIL,
        string $token = 'token',
        ?int $roleId = null,
    ): void {
        $this->databaseManager->table('register_invites')->insert([
            'email' => $email,
            'token' => $token,
            'role_id' => $roleId,
        ]);
    }
}
