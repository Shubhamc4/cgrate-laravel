<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Exceptions;

use Exception;
use Throwable;

/**
 * Base exception for all CGrate package exceptions.
 */
class CgrateException extends Exception
{
    /**
     * The response code from the API, if available.
     */
    protected ?int $responseCode = null;

    /**
     * Create a new CGrate exception.
     *
     * @param  string  $message  The exception message
     * @param  int|null  $responseCode  The response code from the API
     * @param  int  $code  The exception code
     * @param  Throwable|null  $previous  The previous exception
     */
    public function __construct(
        string $message,
        ?int $responseCode = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->responseCode = $responseCode !== null ? (int) $responseCode : null;
    }

    /**
     * Get the response code from the API.
     */
    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }
}
