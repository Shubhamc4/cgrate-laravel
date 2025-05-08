<?php

declare(strict_types=1);

use Cgrate\Laravel\Enums\ResponseCode;
use Cgrate\Laravel\Exceptions\InvalidResponseException;

it('can be instantiated with a default message', function (): void {
    $exception = new InvalidResponseException;

    expect($exception->getMessage())->toBe('Invalid response from Cgrate API');
    expect($exception->getResponseCode())->toBeNull();
});

it('can be instantiated with a custom message', function (): void {
    $exception = new InvalidResponseException('Custom error message');

    expect($exception->getMessage())->toBe('Custom error message');
});

it('can be instantiated with a message and response code', function (): void {
    $responseCode = ResponseCode::INSUFFICIENT_BALANCE;
    $exception = new InvalidResponseException('Invalid response', $responseCode);

    expect($exception->getMessage())->toBe('Invalid response');
    expect($exception->getResponseCode())->toBe(1);
});

it('can be created from a response code', function (): void {
    $responseCode = ResponseCode::INVALID_MSISDN;
    $exception = InvalidResponseException::fromResponseCode($responseCode);

    expect($exception->getMessage())->toBe('Invalid MSISDN');
    expect($exception->getResponseCode())->toBe(7);
});
