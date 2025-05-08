<?php

declare(strict_types=1);

namespace Cgrate\Laravel\DTOs;

/**
 * Data Transfer Object for payment request to Cgrate API.
 */
final class PaymentRequestDTO
{
    /**
     * Create a new payment request DTO.
     *
     * @param  float  $transactionAmount  The amount of the transaction
     * @param  string  $customerMobile  The mobile number of the customer
     * @param  string  $paymentReference  The unique reference for the payment
     */
    public function __construct(
        public readonly float $transactionAmount,
        public readonly string $customerMobile,
        public readonly string $paymentReference,
    ) {}

    /**
     * Create a new payment request DTO.
     *
     * @param  float  $transactionAmount  The amount of the transaction
     * @param  string  $customerMobile  The mobile number of the customer
     * @param  string  $paymentReference  The unique reference for the payment
     * @return self New payment request DTO instance
     */
    public static function create(
        float $transactionAmount,
        string $customerMobile,
        string $paymentReference,
    ): self {
        return new self(
            transactionAmount: $transactionAmount,
            customerMobile: $customerMobile,
            paymentReference: $paymentReference,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, float|string>
     */
    public function toArray(): array
    {
        return [
            'transactionAmount' => $this->transactionAmount,
            'customerMobile' => $this->customerMobile,
            'paymentReference' => $this->paymentReference,
        ];
    }
}
