<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\BalanceResponseDTO;
use Cgrate\Laravel\Enums\ResponseCode;

it('creates a balance response DTO with constructor', function (): void {
    $dto = new BalanceResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Balance retrieved successfully',
        balance: 1000.50
    );

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Balance retrieved successfully')
        ->and($dto->balance)->toBe(1000.50);
});

it('creates a balance response DTO from array response', function (): void {
    $response = [
        'responseCode' => 0,
        'responseMessage' => 'Balance retrieved successfully',
        'balance' => 2500.75,
    ];

    $dto = BalanceResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Balance retrieved successfully')
        ->and($dto->balance)->toBe(2500.75);
});

it('creates a balance response DTO from stdClass response', function (): void {
    $response = (object) [
        'responseCode' => 0,
        'responseMessage' => 'Balance retrieved successfully',
        'balance' => 3750.25,
    ];

    $dto = BalanceResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Balance retrieved successfully')
        ->and($dto->balance)->toBe(3750.25);
});

it('handles string balance values in response', function (): void {
    $response = [
        'responseCode' => 0,
        'responseMessage' => 'Balance retrieved successfully',
        'balance' => '1234.56',
    ];

    $dto = BalanceResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('Balance retrieved successfully')
        ->and($dto->balance)->toBe(1234.56)
        ->and($dto->balance)->toBeFloat();
});

it('handles missing optional fields in response', function (): void {
    $response = [
        'responseCode' => 0,
    ];

    $dto = BalanceResponseDTO::fromResponse($response);

    expect($dto->responseCode)->toBe(ResponseCode::SUCCESS)
        ->and($dto->responseMessage)->toBe('')
        ->and($dto->balance)->toBeNull();
});

it('correctly identifies successful responses', function (): void {
    $successDto = new BalanceResponseDTO(
        responseCode: ResponseCode::SUCCESS,
        responseMessage: 'Success',
        balance: 500.00
    );

    $failureDto = new BalanceResponseDTO(
        responseCode: ResponseCode::GENERAL_ERROR,
        responseMessage: 'Error',
        balance: null
    );

    expect($successDto->isSuccessful())->toBeTrue()
        ->and($failureDto->isSuccessful())->toBeFalse();
});
