<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Patrikjak\Auth\Models\User;

class RegisteredViaInviteEvent
{
    use Dispatchable;

    public function __construct(public readonly User $user)
    {
    }
}
