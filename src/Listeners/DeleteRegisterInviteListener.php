<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Listeners;

use Patrikjak\Auth\Events\RegisteredViaInviteEvent;
use Patrikjak\Auth\Repositories\Contracts\RegisterInviteRepository;

readonly class DeleteRegisterInviteListener
{
    public function __construct(private RegisterInviteRepository $registerInviteRepository)
    {
    }

    public function handle(RegisteredViaInviteEvent $event): void
    {
        $this->registerInviteRepository->delete($event->user->email);
    }
}
