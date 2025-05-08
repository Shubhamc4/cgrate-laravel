<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Validation;

use Cgrate\Laravel\DTOs\PaymentRequestDTO;
use Cgrate\Laravel\Exceptions\ValidationException;

/**
 * Validator for payment requests.
 *
 * Validates payment request data before sending to the API to prevent
 * common errors and improve error handling.
 */
class PaymentValidator
{
    /**
     * Validate a payment request DTO.
     *
     * @param  PaymentRequestDTO  $payment  The payment request to validate
     *
     * @throws \Cgrate\Laravel\Exceptions\ValidationException If validation fails
     */
    public static function validate(PaymentRequestDTO $payment): void
    {
        $errors = [];

        if ($payment->transactionAmount <= 0) {
            $errors['transactionAmount'][] = 'Transaction amount must be greater than zero';
        }

        if (! self::isValidMobileNumber($payment->customerMobile)) {
            $errors['customerMobile'][] = 'Invalid mobile number. Please ensure it starts with 260 and is a valid Zamtel, MTN, or Airtel number.';
        }

        if (! self::isValidReference($payment->paymentReference)) {
            $errors['paymentReference'][] = 'Payment reference contains invalid characters. Only alphanumeric characters and hyphens are allowed.';
        }

        if (! empty($errors)) {
            throw new ValidationException(
                'Validation failed for payment request',
                $errors
            );
        }
    }

    /**
     * Validate a mobile number.
     *
     * @param  string  $mobileNumber  The mobile number to validate
     * @return bool True if valid, false otherwise
     */
    public static function isValidMobileNumber(string $mobileNumber): bool
    {
        return (bool) preg_match("/^(260)[79][567]\d{7}$/", $mobileNumber);
    }

    /**
     * Validate a transaction reference.
     * Reference should be non-empty and alphanumeric with possible hyphens
     *
     * @param  string  $reference  The reference to validate
     * @return bool True if valid, false otherwise
     */
    public static function isValidReference(string $reference): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\-]+$/', $reference) && strlen($reference) > 0;
    }
}
