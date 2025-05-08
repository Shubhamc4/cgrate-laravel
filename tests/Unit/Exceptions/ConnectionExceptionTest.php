<?php

declare(strict_types=1);

use Cgrate\Laravel\Exceptions\ConnectionException;

it('can be instantiated with a default message', function (): void {
    $exception = new ConnectionException;

    expect($exception->getMessage())->toBe('Failed to connect to Cgrate API');
    expect($exception->getResponseCode())->toBeNull();
});

it('can be instantiated with a custom message', function (): void {
    $exception = new ConnectionException('Custom error message');

    expect($exception->getMessage())->toBe('Custom error message');
});

it('can be instantiated with a previous exception', function (): void {
    $previous = new Exception('Previous exception');
    $exception = new ConnectionException('Connection error', $previous);

    expect($exception->getMessage())->toBe('Connection error');
    expect($exception->getPrevious())->toBe($previous);
});

it('can be created from a SoapFault', function (): void {
    $fault = new \SoapFault('Server', 'SOAP error occurred');
    $exception = ConnectionException::fromSoapFault($fault, 'Failed to connect');

    expect($exception->getMessage())->toBe('Failed to connect: SOAP error occurred');
    expect($exception->getPrevious())->toBe($fault);
});
