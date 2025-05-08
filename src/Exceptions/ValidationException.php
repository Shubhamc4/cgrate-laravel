<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Exceptions;

/**
 * Exception thrown when validation fails for payment requests.
 *
 * This exception is used when the data provided for payment requests
 * does not meet the validation rules defined in the package.
 */
class ValidationException extends CgrateException
{
    /**
     * @var array<string, string[]>
     */
    private array $errors = [];

    /**
     * Create a new validation exception instance.
     *
     * @param  string  $message  The exception message
     * @param  array<string, string[]>  $errors  Validation errors by field
     */
    public function __construct(string $message, array $errors = [])
    {
        parent::__construct(message: $message);

        $this->errors = $errors;
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, string[]> The validation errors by field
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
