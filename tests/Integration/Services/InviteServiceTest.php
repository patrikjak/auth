<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Exceptions\EmailInInvitesNotFoundException;
use Patrikjak\Auth\Models\Role;
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
        $roleId = Role::factory()->create()->id;

        $this->inviteService->sendInvite(self::TESTER_EMAIL, $roleId);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSendInviteStoresRoleId(): void
    {
        $roleId = Role::factory()->create()->id;

        $this->inviteService->sendInvite(self::TESTER_EMAIL, $roleId);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL, 'role_id' => $roleId]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSendInviteUsesDefaultRoleWhenNoRoleProvided(): void
    {
        $this->seedDefaultRole();
        $defaultRole = Role::where('slug', config('pjauth.default_role_slug'))->firstOrFail();

        $this->inviteService->sendInvite(self::TESTER_EMAIL);

        $this->assertDatabaseHas('register_invites', [
            'email' => self::TESTER_EMAIL,
            'role_id' => $defaultRole->id,
        ]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSendInviteSendsNotification(): void
    {
        Notification::fake();

        $roleId = Role::factory()->create()->id;

        $this->inviteService->sendInvite(self::TESTER_EMAIL, $roleId);

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
    public function testValidateTokenAndGetRoleIdReturnsRoleId(): void
    {
        $token = 'test_token';
        $roleId = Role::factory()->create()->id;
        $this->insertInvite(token: $token, roleId: $roleId);

        $returnedRoleId = $this->inviteService->validateTokenAndGetRoleId($token, self::TESTER_EMAIL);

        $this->assertSame($roleId, $returnedRoleId);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testValidateTokenAndGetRoleIdThrowsForInvalidToken(): void
    {
        $roleId = Role::factory()->create()->id;
        $this->insertInvite(token: 'correct_token', roleId: $roleId);

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
        string $token,
        string $roleId,
        string $email = self::TESTER_EMAIL,
    ): void {
        $this->databaseManager->table('register_invites')->insert([
            'email' => $email,
            'token' => $token,
            'role_id' => $roleId,
        ]);
    }
}
