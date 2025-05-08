<?php

declare(strict_types=1);

use Cgrate\Laravel\Exceptions\CgrateException;

it('can be instantiated with just a message', function (): void {
    $exception = new CgrateException('Error message');

    expect($exception->getMessage())->toBe('Error message');
    expect($exception->getResponseCode())->toBeNull();
    expect($exception->getCode())->toBe(0);
});

it('can be instantiated with a message and response code', function (): void {
    $exception = new CgrateException('Error with code', 7);

    expect($exception->getMessage())->toBe('Error with code');
    expect($exception->getResponseCode())->toBe(7);
});

it('can be instantiated with a message, response code and code', function (): void {
    $exception = new CgrateException('Complete error', 6, 500);

    expect($exception->getMessage())->toBe('Complete error');
    expect($exception->getResponseCode())->toBe(6);
    expect($exception->getCode())->toBe(500);
});

it('can be instantiated with a message, response code, code and previous exception', function (): void {
    $previous = new Exception('Previous error');
    $exception = new CgrateException('Complete error', 6, 500, $previous);

    expect($exception->getMessage())->toBe('Complete error');
    expect($exception->getResponseCode())->toBe(6);
    expect($exception->getCode())->toBe(500);
    expect($exception->getPrevious())->toBe($previous);
});
