<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class RegistrationNotAllowedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Registration is not allowed', Response::HTTP_FORBIDDEN);
    }
}
