<?php

declare(strict_types=1);

use CGrate\Php\DTOs\ReversePaymentResponseDTO;
use CGrate\Php\Enums\ResponseCode;

it('creates a reverse payment response DTO with constructor', function (): void {
    $dto = new ReversePaymentResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Payment reversed successfully',
        transactionReference: 'PAY-123456'
    );

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment reversed successfully')
        ->and($dto->transactionReference)->toBe('PAY-123456');
});

it('creates a reverse payment response DTO from array response', function (): void {
    $response = [
        'responseCode' => 0,
        'responseMessage' => 'Payment reversed successfully',
        'transactionReference' => 'PAY-123456',
    ];

    $dto = ReversePaymentResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment reversed successfully')
        ->and($dto->transactionReference)->toBe('PAY-123456');
});

it('correctly identifies successful responses', function (): void {
    $successDto = new ReversePaymentResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Success',
        transactionReference: 'PAY-123456'
    );

    $failureDto = new ReversePaymentResponseDTO(
        responseCode: ResponseCode::GENERAL_ERROR,
        responseMessage: 'Error',
        transactionReference: 'PAY-123456'
    );

    expect($successDto->isSuccessful())->toBeTrue()
        ->and($failureDto->isSuccessful())->toBeFalse();
});
