<?php

namespace RecurLoop\Recaptcha\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class InvalidTokenException extends Exception implements HttpExceptionInterface
{
    /**
     * Returns the status code.
     */
    public function getStatusCode(): int
    {
        return 409;
    }

    /**
     * Returns response headers.
     */
    public function getHeaders(): array
    {
        return [];
    }
}
