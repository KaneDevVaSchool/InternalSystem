<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Google\Exceptions;

use Exception;

final class GoogleAuthDomainMismatchException extends Exception
{
    public function __construct(string $message = 'Domain not allowed.', int $code = 403, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
