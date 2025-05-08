<?php

declare(strict_types=1);

use Cgrate\Laravel\DTOs\ReversePaymentResponseDTO;
use Cgrate\Laravel\Enums\ResponseCode;
use Cgrate\Laravel\Events\PaymentReversed;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;

it('uses Dispatchable and SerializesModels traits', function () {
    $traits = class_uses(PaymentReversed::class);

    expect($traits)->toHaveKey(Dispatchable::class);
    expect($traits)->toHaveKey(SerializesModels::class);
});

it('can be instantiated with reverse payment response and reference', function () {
    $responseData = [
        'responseCode' => 0,
        'responseMessage' => 'Payment reversed successfully',
        'transactionReference' => 'PAYMENT-REF-123',
    ];

    $response = ReversePaymentResponseDTO::fromResponse($responseData);
    $event = new PaymentReversed($response);

    expect($event->response)->toBe($response);
    expect($event->response->responseCode)->toBe(ResponseCode::SUCCESS);
    expect($event->response->responseMessage)->toBe('Payment reversed successfully');
});

it('can be dispatched and listened to', function () {
    $responseData = [
        'responseCode' => 0,
        'responseMessage' => 'Payment reversed successfully',
        'transactionReference' => 'PAYMENT-REF-123',
    ];

    $response = ReversePaymentResponseDTO::fromResponse($responseData);

    Event::fake([PaymentReversed::class]);
    PaymentReversed::dispatch($response);
    Event::assertDispatched(PaymentReversed::class, function ($event) use ($response) {
        return $event->response === $response;
    });
});
