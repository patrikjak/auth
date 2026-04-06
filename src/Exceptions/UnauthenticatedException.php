<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class UnauthenticatedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthenticated.', Response::HTTP_UNAUTHORIZED);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
