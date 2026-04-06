<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class RoleNotFoundException extends RuntimeException
{
    public function __construct(string $identifier, string $identifierType = 'id')
    {
        parent::__construct(
            sprintf('Role with %s %s not found.', $identifierType, $identifier),
            Response::HTTP_NOT_FOUND,
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
