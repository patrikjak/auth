<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Exceptions;

use Exception;

class EmailInInvitesNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct("Email in invitations not found");
    }
}
