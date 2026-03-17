<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Google\Exceptions;

use Exception;

final class GoogleAuthTokenInvalidException extends Exception
{
    public function __construct(string $message = 'Invalid token.', int $code = 401, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
