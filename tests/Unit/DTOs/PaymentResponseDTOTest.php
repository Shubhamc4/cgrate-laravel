<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\PaymentResponseDTO;
use Cgrate\Laravel\Enums\ResponseCode;

it('creates a payment response DTO with constructor', function (): void {
    $dto = new PaymentResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Payment successful',
        paymentID: 'PAY-123456'
    );

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment successful')
        ->and($dto->paymentID)->toBe('PAY-123456');
});

it('creates a payment response DTO from array response', function (): void {
    $response = [
        'responseCode' => 0,
        'responseMessage' => 'Payment successful',
        'paymentID' => 'PAY-789012',
    ];

    $dto = PaymentResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment successful')
        ->and($dto->paymentID)->toBe('PAY-789012');
});

it('handles missing optional fields in response', function (): void {
    $response = [
        'responseCode' => 0,
        'responseMessage' => 'Payment successful',
    ];

    $dto = PaymentResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment successful')
        ->and($dto->paymentID)->toBe(null)
        ->and($dto->customerMobile)->toBe(null)
        ->and($dto->transactionReference)->toBe(null)
        ->and($dto->transactionAmount)->toBe(null);
});

it('correctly identifies successful responses', function (): void {
    $successDto = new PaymentResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Success',
        paymentID: 'PAY-123'
    );

    $failureDto = new PaymentResponseDTO(
        responseCode: ResponseCode::GENERAL_ERROR,
        responseMessage: 'Error',
        paymentID: null
    );

    expect($successDto->isSuccessful())->toBeTrue()
        ->and($failureDto->isSuccessful())->toBeFalse();
});
