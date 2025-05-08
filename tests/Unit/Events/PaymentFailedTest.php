<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\PaymentRequestDTO;
use Cgrate\Laravel\Enums\ResponseCode;
use Cgrate\Laravel\Events\PaymentFailed;
use Cgrate\Laravel\Exceptions\CgrateException;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

it('uses Dispatchable and SerializesModels traits', function () {
    $traits = class_uses(PaymentFailed::class);

    expect($traits)->toHaveKey(Dispatchable::class);
    expect($traits)->toHaveKey(SerializesModels::class);
});

it('can be instantiated with payment request and error message', function () {
    $request = new PaymentRequestDTO(
        transactionAmount: 10.00,
        customerMobile: '260970000000',
        paymentReference: 'INVOICE-123'
    );

    $event = new PaymentFailed($request, 'Payment failed');

    expect($event->request)->toBe($request);
    expect($event->errorMessage)->toBe('Payment failed');
    expect($event->responseCode)->toBeNull();
    expect($event->exception)->toBeNull();
});

it('can be instantiated with response code', function () {
    $request = new PaymentRequestDTO(
        transactionAmount: 10.00,
        customerMobile: '260970000000',
        paymentReference: 'INVOICE-123'
    );

    $event = new PaymentFailed(
        $request,
        'Insufficient balance',
        ResponseCode::from(1)
    );

    expect($event->request)->toBe($request);
    expect($event->errorMessage)->toBe('Insufficient balance');
    expect($event->responseCode)->toBe(ResponseCode::from(1));
    expect($event->exception)->toBeNull();
});

it('can be instantiated with exception', function () {
    $request = new PaymentRequestDTO(
        transactionAmount: 10.00,
        customerMobile: '260970000000',
        paymentReference: 'INVOICE-123'
    );

    $exception = new CgrateException('Connection error');

    $event = new PaymentFailed(
        $request,
        'Connection error',
        null,
        $exception
    );

    expect($event->request)->toBe($request);
    expect($event->errorMessage)->toBe('Connection error');
    expect($event->responseCode)->toBeNull();
    expect($event->exception)->toBe($exception);
});

it('can be dispatched and listened to', function () {
    $request = new PaymentRequestDTO(
        transactionAmount: 10.00,
        customerMobile: '260970000000',
        paymentReference: 'INVOICE-123'
    );

    // Fake Event facade
    Event::fake([
        PaymentFailed::class,
    ]);

    // Dispatch the event
    PaymentFailed::dispatch($request, 'Payment failed');

    // Assert it was dispatched
    Event::assertDispatched(PaymentFailed::class, function ($event) use ($request) {
        return $event->request === $request &&
               $event->errorMessage === 'Payment failed';
    });
});
