<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Exceptions;

use Exception;

class ModelIsIncompatibleException extends Exception
{
    public function __construct(string $model, string $baseModel)
    {
        $message = sprintf(
            'The model %s must be or must extend %s.',
            $model,
            $baseModel
        );

        parent::__construct($message);
    }
}
