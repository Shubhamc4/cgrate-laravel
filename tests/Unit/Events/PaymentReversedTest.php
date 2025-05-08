<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\ReversePaymentResponseDTO;
use Cgrate\Laravel\Enums\ResponseCode;
use Cgrate\Laravel\Events\PaymentReversed;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

it('uses Dispatchable and SerializesModels traits', function () {
    $traits = class_uses(PaymentReversed::class);

    expect($traits)->toHaveKey(Dispatchable::class);
    expect($traits)->toHaveKey(SerializesModels::class);
});

it('can be instantiated with reverse payment response and reference', function () {
    $responseData = [
        'responseCode' => 0,
        'responseMessage' => 'Payment reversed successfully',
    ];

    $response = ReversePaymentResponseDTO::fromResponse($responseData);
    $paymentReference = 'PAYMENT-REF-123';

    $event = new PaymentReversed($response, $paymentReference);

    expect($event->response)->toBe($response);
    expect($event->paymentReference)->toBe($paymentReference);
    expect($event->response->responseCode)->toBe(ResponseCode::SUCCESS);
    expect($event->response->responseMessage)->toBe('Payment reversed successfully');
});

it('can be dispatched and listened to', function () {
    $responseData = [
        'responseCode' => 0,
        'responseMessage' => 'Payment reversed successfully',
    ];

    $response = ReversePaymentResponseDTO::fromResponse($responseData);
    $paymentReference = 'PAYMENT-REF-123';

    // Fake Event facade
    Event::fake([
        PaymentReversed::class,
    ]);

    // Dispatch the event
    PaymentReversed::dispatch($response, $paymentReference);

    // Assert it was dispatched
    Event::assertDispatched(PaymentReversed::class, function ($event) use ($response, $paymentReference) {
        return $event->response === $response &&
               $event->paymentReference === $paymentReference;
    });
});
