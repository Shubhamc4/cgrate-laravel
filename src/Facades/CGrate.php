<?php

declare(strict_types=1);

namespace CGrate\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for interacting with the CGrate payment service.
 *
 * This facade provides a simple interface to interact with the CGrate payment gateway.
 * It handles payment processing, payment status check, payment reversal, and account balance queries.
 *
 * @method  static  \CGrate\Php\DTOs\BalanceResponseDTO
 * getAccountBalance()  Get the current account balance
 * @method  static  \CGrate\Php\DTOs\PaymentResponseDTO
 * processCustomerPayment(\CGrate\Php\DTOs\PaymentRequestDTO $request)
 * Process a payment for a customer
 * @method  static  \CGrate\Php\DTOs\PaymentResponseDTO
 * queryCustomerPayment(string $transactionReference)  Query the status of a transaction
 * @method  static  \CGrate\Php\DTOs\ReversePaymentResponseDTO
 * reverseCustomerPayment(string $paymentReference)  Reverse a previously processed payment
 * @method  static  string generateTransactionReference(string $prefix = 'CG')
 * Generate a unique transaction reference
 *
 * @see \CGrate\Php\Services\CGrateService
 */
final class CGrate extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'CGrate';
    }
}
