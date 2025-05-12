<?php

declare(strict_types=1);

use CGrate\Php\Enums\ResponseCode;

it('creates enum from integer value', function (): void {
    $responseCode = ResponseCode::fromValue(0);
    expect($responseCode)->toBe(ResponseCode::SUCCESS);

    $responseCode = ResponseCode::fromValue(1);
    expect($responseCode)->toBe(ResponseCode::INSUFFICIENT_BALANCE);
});

it('creates enum from string value', function (): void {
    $responseCode = ResponseCode::fromValue('0');
    expect($responseCode)->toBe(ResponseCode::SUCCESS);

    $responseCode = ResponseCode::fromValue('7');
    expect($responseCode)->toBe(ResponseCode::INVALID_MSISDN);
});

it('defaults to unknown for invalid values', function (): void {
    $responseCode = ResponseCode::fromValue(999);
    expect($responseCode)->toBe(ResponseCode::UNKNOWN);
});

it('gets description for response code', function (): void {
    $description = ResponseCode::SUCCESS->getDescription();
    expect($description)->toBe('Success');

    $description = ResponseCode::INSUFFICIENT_BALANCE->getDescription();
    expect($description)->toBe('Insufficient balance');
});

it('gets description from value', function (): void {
    $description = ResponseCode::descriptionFromValue(0);
    expect($description)->toBe('Success');

    $description = ResponseCode::descriptionFromValue('6');
    expect($description)->toBe('General error');
});

it('compares response codes correctly using is method', function (): void {
    $responseCode = ResponseCode::SUCCESS;

    expect($responseCode->is(ResponseCode::SUCCESS))->toBeTrue();
    expect($responseCode->is(ResponseCode::GENERAL_ERROR))->toBeFalse();
    expect($responseCode->is(0))->toBeTrue();
    expect($responseCode->is('0'))->toBeTrue();
    expect($responseCode->is(1))->toBeFalse();
});
