<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Patrikjak\Auth\Services\InviteService;

class SendRegisterInviteCommand extends Command
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'send:register-invite {email} {--role= : Role ID to assign to the invited user}';

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Send register invite to email';

    public function __construct(private readonly InviteService $inviteService)
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

        $roleOption = $this->option('role');
        $roleId = $roleOption !== null ? (int) $roleOption : null;

        $this->inviteService->sendInvite($email, $roleId);

        $this->info('Register invite sent to ' . $email);
    }
}
