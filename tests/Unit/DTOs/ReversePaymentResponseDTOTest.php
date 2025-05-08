<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\ReversePaymentResponseDTO;
use Cgrate\Laravel\Enums\ResponseCode;

it('creates a reverse payment response DTO with constructor', function (): void {
    $dto = new ReversePaymentResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Payment reversed successfully'
    );

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment reversed successfully');
});

it('creates a reverse payment response DTO from array response', function (): void {
    $response = [
        'responseCode' => 0,
        'responseMessage' => 'Payment reversed successfully',
    ];

    $dto = ReversePaymentResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment reversed successfully');
});

it('creates a reverse payment response DTO from stdClass response', function (): void {
    $response = (object) [
        'responseCode' => 0,
        'responseMessage' => 'Payment reversed successfully',
    ];

    $dto = ReversePaymentResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Payment reversed successfully');
});

it('handles missing optional fields in response', function (): void {
    $response = [
        'responseCode' => 0,
    ];

    $dto = ReversePaymentResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('');
});

it('correctly identifies successful responses', function (): void {
    $successDto = new ReversePaymentResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Success'
    );

    $failureDto = new ReversePaymentResponseDTO(
        responseCode: ResponseCode::GENERAL_ERROR,
        responseMessage: 'Error'
    );

    expect($successDto->isSuccessful())->toBeTrue()
        ->and($failureDto->isSuccessful())->toBeFalse();
});
