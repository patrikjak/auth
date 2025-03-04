<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Patrikjak\Auth\Services\UserService;

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

    public function __construct(private readonly UserService $userService)
    {
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

        $this->userService->sendRegisterInvite($email);

        $this->info('Register invite sent to ' . $email);
    }
}
