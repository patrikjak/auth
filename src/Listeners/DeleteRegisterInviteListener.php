<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Listeners;

use Patrikjak\Auth\Events\RegisteredViaInviteEvent;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

readonly class DeleteRegisterInviteListener
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(RegisteredViaInviteEvent $event): void
    {
        $this->userRepository->deleteRegisterInvite($event->user->email);
    }
}
