<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\PaymentRequestDTO;
use Cgrate\Laravel\Validation\PaymentValidator;
use Illuminate\Validation\ValidationException;

it('validates correct payment request', function (): void {
    $payment = new PaymentRequestDTO(
        transactionAmount: 100.00,
        customerMobile: '260970123456',
        paymentReference: 'TEST-REF-123'
    );

    // This should not throw any exception
    PaymentValidator::validate($payment);
    $this->assertTrue(true); // Dummy assertion to avoid PHPUnit risky test warning
});

it('validates mobile number format correctly', function (): void {
    // Valid Zambian mobile numbers
    expect(PaymentValidator::isValidMobileNumber('260970123456'))->toBeTrue();
    expect(PaymentValidator::isValidMobileNumber('260950123456'))->toBeTrue();
    expect(PaymentValidator::isValidMobileNumber('260970123456'))->toBeTrue();

    // Invalid formats
    expect(PaymentValidator::isValidMobileNumber('+260970123456'))->toBeFalse(); // Has +
    expect(PaymentValidator::isValidMobileNumber('26097012345'))->toBeFalse(); // Too short
    expect(PaymentValidator::isValidMobileNumber('2609701234567'))->toBeFalse(); // Too long
    expect(PaymentValidator::isValidMobileNumber('270970123456'))->toBeFalse(); // Wrong country code
    expect(PaymentValidator::isValidMobileNumber('invalid-format'))->toBeFalse(); // Not a number
});

it('validates reference format correctly', function (): void {
    // Valid references
    expect(PaymentValidator::isValidReference('TEST-123'))->toBeTrue();
    expect(PaymentValidator::isValidReference('REF123'))->toBeTrue();
    expect(PaymentValidator::isValidReference('123456'))->toBeTrue();

    // Invalid formats
    expect(PaymentValidator::isValidReference(''))->toBeFalse(); // Empty
    expect(PaymentValidator::isValidReference('TEST/123'))->toBeFalse(); // Invalid character
    expect(PaymentValidator::isValidReference('TEST 123'))->toBeFalse(); // Space not allowed
});

it('throws exception for negative amount', function (): void {
    $payment = new PaymentRequestDTO(
        transactionAmount: -50.00,
        customerMobile: '260970123456',
        paymentReference: 'TEST-REF-123'
    );

    expect(fn () => PaymentValidator::validate($payment))->toThrow(
        ValidationException::class
    );
});

it('throws exception for zero amount', function (): void {
    $payment = new PaymentRequestDTO(
        transactionAmount: 0.00,
        customerMobile: '260970123456',
        paymentReference: 'TEST-REF-123'
    );

    expect(fn () => PaymentValidator::validate($payment))->toThrow(
        ValidationException::class
    );
});

it('throws exception for invalid mobile number', function (): void {
    $payment = new PaymentRequestDTO(
        transactionAmount: 100.00,
        customerMobile: 'invalid-mobile',
        paymentReference: 'TEST-REF-123'
    );

    expect(fn () => PaymentValidator::validate($payment))->toThrow(
        ValidationException::class
    );
});

it('throws exception for empty reference', function (): void {
    $payment = new PaymentRequestDTO(
        transactionAmount: 100.00,
        customerMobile: '260970123456',
        paymentReference: ''
    );

    expect(fn () => PaymentValidator::validate($payment))->toThrow(
        ValidationException::class
    );
});

it('throws exception for invalid reference format', function (): void {
    $payment = new PaymentRequestDTO(
        transactionAmount: 100.00,
        customerMobile: '260970123456',
        paymentReference: 'TEST/123'
    );

    expect(fn () => PaymentValidator::validate($payment))->toThrow(
        ValidationException::class
    );
});
