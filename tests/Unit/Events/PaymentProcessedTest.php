<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\PaymentResponseDTO;
use Cgrate\Laravel\Enums\ResponseCode;
use Cgrate\Laravel\Events\PaymentProcessed;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

it('uses Dispatchable and SerializesModels traits', function () {
    $traits = class_uses(PaymentProcessed::class);

    expect($traits)->toHaveKey(Dispatchable::class);
    expect($traits)->toHaveKey(SerializesModels::class);
});

it('can be instantiated with payment response and data', function () {
    $responseData = [
        'responseCode' => 0,
        'responseMessage' => 'Success',
        'paymentID' => 'PAY-123456',
    ];

    $response = PaymentResponseDTO::fromResponse($responseData);
    $paymentData = [
        'transactionAmount' => 10.00,
        'customerMobile' => '260970000000',
        'paymentReference' => 'INVOICE-123',
    ];

    $event = new PaymentProcessed($response, $paymentData);

    expect($event->response)->toBe($response);
    expect($event->paymentData)->toBe($paymentData);
    expect($event->response->paymentID)->toBe('PAY-123456');
    expect($event->response->responseCode)->toBe(ResponseCode::SUCCESS);
});

it('can be dispatched and listened to', function () {
    $responseData = [
        'responseCode' => 0,
        'responseMessage' => 'Success',
        'paymentID' => 'PAY-123456',
    ];

    $response = PaymentResponseDTO::fromResponse($responseData);
    $paymentData = [
        'transactionAmount' => 10.00,
        'customerMobile' => '260970000000',
        'paymentReference' => 'INVOICE-123',
    ];

    $wasDispatched = false;

    // Fake Event facade
    Event::fake([
        PaymentProcessed::class,
    ]);

    // Dispatch the event
    PaymentProcessed::dispatch($response, $paymentData);

    // Assert it was dispatched
    Event::assertDispatched(PaymentProcessed::class, function ($event) use ($response, $paymentData) {
        return $event->response === $response &&
               $event->paymentData === $paymentData;
    });
});
