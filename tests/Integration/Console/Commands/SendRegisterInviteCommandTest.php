<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration\Console\Commands;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Notifications\RegisterInviteNotification;
use Patrikjak\Auth\Tests\Integration\TestCase;

class SendRegisterInviteCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testCommand(): void
    {
        $role = Role::factory()->create();

        $this->artisan('pjauth:send-invite', ['email' => self::TESTER_EMAIL])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL), 'yes')
            ->expectsQuestion('Role ID:', $role->id)
            ->expectsOutput('Register invite sent to ' . self::TESTER_EMAIL);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testCommandNotificationIsSent(): void
    {
        Notification::fake();

        $role = Role::factory()->create();
        assert($role instanceof Role);

        $this->artisan('pjauth:send-invite', ['email' => self::TESTER_EMAIL])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL), 'yes')
            ->expectsQuestion('Role ID:', $role->id)
            ->expectsOutput('Register invite sent to ' . self::TESTER_EMAIL);

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

    public function testCommandWithNotConfirmedEmail(): void
    {
        Notification::fake();

        $this->artisan('pjauth:send-invite', ['email' => self::TESTER_EMAIL])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL))
            ->expectsOutput('Register invite not sent');

        $this->assertDatabaseMissing('register_invites', ['email' => self::TESTER_EMAIL]);

        Notification::assertCount(0);
    }

    /**
     * @throws Exception
     */
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testCommandWithRole(): void
    {
        $roleId = Role::factory()->create()->id;

        $this->artisan('pjauth:send-invite', ['email' => self::TESTER_EMAIL, '--role' => $roleId])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL), 'yes')
            ->expectsOutput('Register invite sent to ' . self::TESTER_EMAIL);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL, 'role_id' => $roleId]);
    }

    /**
     * @throws Exception
     */
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testCommandUsesDefaultRoleWhenNoRoleProvided(): void
    {
        $this->seedDefaultRole();
        $defaultRole = Role::where('slug', config('pjauth.default_role_slug'))->firstOrFail();

        $this->artisan('pjauth:send-invite', ['email' => self::TESTER_EMAIL])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL), 'yes')
            ->expectsQuestion('Role ID:', $defaultRole->id)
            ->expectsOutput('Register invite sent to ' . self::TESTER_EMAIL);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL, 'role_id' => $defaultRole->id]);
    }
}
