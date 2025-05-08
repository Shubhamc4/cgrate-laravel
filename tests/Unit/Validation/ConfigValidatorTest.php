<?php

declare(strict_types=1);

use Cgrate\Laravel\Exceptions\ValidationException;
use Cgrate\Laravel\Validation\ConfigValidator;

it('validates valid configuration', function () {
    $config = [
        'username' => 'testuser',
        'password' => 'password123',
        'endpoint' => 'https://example.com/api?wsdl',
        'test_endpoint' => 'https://test.example.com/api?wsdl',
        'test_mode' => true,
        'options' => [
            'timeout' => 30,
        ],
    ];

    $validated = ConfigValidator::validate($config);

    expect($validated)->toBeArray()->toHaveKeys([
        'username',
        'password',
        'endpoint',
        'test_endpoint',
        'test_mode',
        'options',
    ]);
    expect($validated['username'])->toBe('testuser');
    expect($validated['test_mode'])->toBeTrue();
    expect($validated['options'])->toBeArray()->toHaveKey('timeout');
});

it('provides default values for optional configurations', function () {
    $config = [
        'username' => 'testuser',
        'password' => 'password123',
        'endpoint' => 'https://example.com/api?wsdl',
        'test_endpoint' => 'https://test.example.com/api?wsdl',
    ];

    $validated = ConfigValidator::validate($config);

    expect($validated)->toHaveKeys(['test_mode', 'options']);
    expect($validated['test_mode'])->toBeFalse();
    expect($validated['options'])->toBeArray();
});

it('throws exception for missing required configurations', function () {
    $config = [
        'username' => 'testuser',
        // Missing password
        'endpoint' => 'https://example.com/api?wsdl',
        // Missing test_endpoint
    ];

    expect(fn () => ConfigValidator::validate($config))
        ->toThrow(ValidationException::class)
        ->and(function (ValidationException $e) {
            $errors = $e->errors();
            expect($errors)->toHaveKeys(['password', 'test_endpoint']);
        });
});

it('validates URL format for endpoints', function () {
    $config = [
        'username' => 'testuser',
        'password' => 'password123',
        'endpoint' => 'not-a-valid-url',
        'test_endpoint' => 'also-not-valid',
    ];

    expect(fn () => ConfigValidator::validate($config))
        ->toThrow(ValidationException::class)
        ->and(function (ValidationException $e) {
            $errors = $e->errors();
            expect($errors)->toHaveKeys(['endpoint', 'test_endpoint']);
            expect($errors['endpoint'])->toContain('valid URL');
        });
});

it('ensures options is an array', function () {
    $config = [
        'username' => 'testuser',
        'password' => 'password123',
        'endpoint' => 'https://example.com/api?wsdl',
        'test_endpoint' => 'https://test.example.com/api?wsdl',
        'options' => 'not-an-array',
    ];

    expect(fn () => ConfigValidator::validate($config))
        ->toThrow(ValidationException::class)
        ->and(function (ValidationException $e) {
            $errors = $e->errors();
            expect($errors)->toHaveKey('options');
            expect($errors['options'])->toContain('must be an array');
        });
});
