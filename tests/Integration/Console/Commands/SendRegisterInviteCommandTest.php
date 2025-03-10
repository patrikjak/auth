<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Console\Commands;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Orchestra\Testbench\Attributes\DefineEnvironment;
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
        $this->artisan('send:register-invite', ['email' => self::TESTER_EMAIL])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL), 'yes')
            ->expectsOutput('Register invite sent to ' . self::TESTER_EMAIL);

        $this->assertDatabaseHas('register_invites', ['email' => self::TESTER_EMAIL]);
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testCommandNotificationIsSent(): void
    {
        Notification::fake();

        $this->artisan('send:register-invite', ['email' => self::TESTER_EMAIL])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL), 'yes')
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
        
        $this->artisan('send:register-invite', ['email' => self::TESTER_EMAIL])
            ->expectsConfirmation(sprintf('Do you want to send register invite to %s?', self::TESTER_EMAIL))
            ->expectsOutput('Register invite not sent');
        
        $this->assertDatabaseMissing('register_invites', ['email' => self::TESTER_EMAIL]);
        
        Notification::assertCount(0);
    }
}