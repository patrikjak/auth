<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository;
use Patrikjak\Auth\Services\InviteService;

final class SendRegisterInviteCommand extends Command
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'pjauth:send-invite {email} {--role= : Role ID to assign to the invited user}';

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Send register invite to email';

    public function handle(InviteService $inviteService, RoleRepository $roleRepository, Config $config): void
    {
        $email = $this->argument('email');

        $confirmed = $this->confirm(sprintf('Do you want to send register invite to %s?', $email), true);

        if (!$confirmed) {
            $this->info('Register invite not sent');

            return;
        }

        $roleId = $this->option('role') ?? $this->askForRoleId($roleRepository, $config);

        $inviteService->sendInvite($email, $roleId);

        $this->info(sprintf('Register invite sent to %s', $email));
    }

    private function askForRoleId(RoleRepository $roleRepository, Config $config): string
    {
        $roles = $roleRepository->getAll();
        $defaultSlug = $config->get('pjauth.default_role_slug');
        $defaultRole = $roles->first(static fn (Role $role) => $role->slug === $defaultSlug);

        $this->info(
            sprintf(
                'Available roles: %s',
                $roles->map(static fn (Role $role) => sprintf('%s (%s)', $role->name, $role->id))->implode(' | '),
            ),
        );

        return $this->ask('Role ID:', $defaultRole?->id);
    }
}
