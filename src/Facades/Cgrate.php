<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Facades;

use Cgrate\Laravel\DTOs\BalanceResponseDTO;
use Cgrate\Laravel\DTOs\PaymentRequestDTO;
use Cgrate\Laravel\DTOs\PaymentResponseDTO;
use Cgrate\Laravel\DTOs\ReversePaymentResponseDTO;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for interacting with the Cgrate payment service.
 *
 * This facade provides a simple interface to interact with the Cgrate payment gateway.
 * It handles payment processing, payment status check, payment reversal, and account balance queries.
 *
 * @method static BalanceResponseDTO getAccountBalance() Get the current account balance
 * @method static PaymentResponseDTO processCustomerPayment(PaymentRequestDTO $request) Process a payment for a customer
 * @method static PaymentResponseDTO queryTransactionStatus(string $transactionReference) Query the status of a transaction
 * @method static ReversePaymentResponseDTO reverseCustomerPayment(string $paymentReference) Reverse a previously processed payment
 *
 * @see \Cgrate\Laravel\Services\CgrateService
 */
final class Cgrate extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cgrate';
    }
}
