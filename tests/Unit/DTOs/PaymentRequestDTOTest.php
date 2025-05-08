<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\PaymentRequestDTO;

it('creates a payment request DTO with constructor', function (): void {
    $dto = new PaymentRequestDTO(
        transactionAmount: 100.00,
        customerMobile: '260970000000',
        paymentReference: 'TEST-REF-123'
    );

    expect($dto->transactionAmount)->toBe(100.00)
        ->and($dto->customerMobile)->toBe('260970000000')
        ->and($dto->paymentReference)->toBe('TEST-REF-123');
});

it('creates a payment request DTO with static create method', function (): void {
    $dto = PaymentRequestDTO::create(
        transactionAmount: 200.00,
        customerMobile: '260970000000',
        paymentReference: 'TEST-REF-456'
    );

    expect($dto->transactionAmount)->toBe(200.00)
        ->and($dto->customerMobile)->toBe('260970000000')
        ->and($dto->paymentReference)->toBe('TEST-REF-456');
});

it('converts to array correctly', function (): void {
    $dto = PaymentRequestDTO::create(
        transactionAmount: 300.00,
        customerMobile: '260970000000',
        paymentReference: 'TEST-REF-789'
    );

    $array = $dto->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['transactionAmount', 'customerMobile', 'paymentReference'])
        ->and($array['transactionAmount'])->toBe(300.00)
        ->and($array['customerMobile'])->toBe('260970000000')
        ->and($array['paymentReference'])->toBe('TEST-REF-789');
});
