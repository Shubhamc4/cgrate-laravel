<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Exceptions;

use Cgrate\Laravel\Enums\ResponseCode;
use Throwable;

/**
 * Exception thrown when the CGrate API returns an unexpected or error response.
 */
class InvalidResponseException extends CgrateException
{
    /**
     * Create a new invalid response exception.
     *
     * @param  string  $message  The exception message
     * @param  ResponseCode|null  $responseCode  The response code from the API
     * @param  int  $code  The exception code
     * @param  Throwable|null  $previous  The previous exception
     */
    public function __construct(
        string $message = 'Invalid response from Cgrate API',
        ?ResponseCode $responseCode = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            responseCode: $responseCode?->value,
            code: $code,
            previous: $previous
        );
    }

    /**
     * Create an exception from a response code.
     *
     * @param  ResponseCode  $responseCode  The response code enum
     * @return self New invalid response exception instance
     */
    public static function fromResponseCode(ResponseCode $responseCode): self
    {
        return new self(
            message: $responseCode->getDescription(),
            responseCode: $responseCode
        );
    }
}
