<?php
namespace App\Exceptions;

use RuntimeException;

/**
 * Custom exception for token validation errors.
 * Carries a message and an HTTP status code (default 401 Unauthorized).
 */
class TokenException extends RuntimeException
{
    public function __construct(string $message, int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
