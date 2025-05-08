<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Exceptions;

use SoapFault;
use Throwable;

/**
 * Exception thrown when a connection to the CGrate API fails.
 */
class ConnectionException extends CgrateException
{
    /**
     * Create a new connection exception.
     *
     * @param  string  $message  The exception message
     * @param  Throwable|null  $previous  The previous exception
     */
    public function __construct(
        string $message = 'Failed to connect to Cgrate API',
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            previous: $previous
        );
    }

    /**
     * Create an exception from a response code.
     *
     * @param  SoapFault  $fault  Soap fault exception
     * @param  string  $defaultMessage  Default Message
     * @return self New connect exception instance
     */
    public static function fromSoapFault(SoapFault $fault, string $defaultMessage): self
    {
        return new self(
            message: $defaultMessage.': '.$fault->getMessage(),
            previous: $fault
        );
    }
}
