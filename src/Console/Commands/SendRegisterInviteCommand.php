<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;
use Patrikjak\Auth\Notifications\RegisterInviteNotification;

class SendRegisterInviteCommand extends Command
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'send:register-invite {email}';

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Send register invite to email';

    public function __construct(
        private readonly AnonymousNotifiable $anonymousNotifiable,
        private readonly Config $config,
        private readonly DatabaseManager $databaseManager,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $email = $this->argument('email');

        $confirmed = $this->confirm('Do you want to send register invite to ' . $email . '?', true);

        if (!$confirmed) {
            $this->info('Register invite not sent');

            return;
        }

        $token = hash_hmac('sha256', Str::random(40), $this->config->get('app.key'));

        $this->databaseManager->table('register_invites')
            ->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => CarbonImmutable::now(),
            ]);

        $this->anonymousNotifiable
            ->route('mail', $email)
            ->notify(new RegisterInviteNotification(
                sprintf('%s?email=%s', route('register.invitation', ['token' => $token]), $email),
            ));

        $this->info('Register invite sent to ' . $email);
    }
}
