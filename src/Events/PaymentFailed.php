<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Events;

use Cgrate\Laravel\DTOs\PaymentRequestDTO;
use Cgrate\Laravel\Enums\ResponseCode;
use Cgrate\Laravel\Exceptions\CgrateException;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a payment fails.
 */
final class PaymentFailed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  PaymentRequestDTO  $request  The original payment request
     * @param  string  $errorMessage  The error message
     * @param  ResponseCode|null  $responseCode  The response code if available
     * @param  CgrateException|null  $exception  The exception that caused the failure if available
     */
    public function __construct(
        public readonly PaymentRequestDTO $request,
        public readonly string $errorMessage,
        public readonly ?ResponseCode $responseCode = null,
        public readonly ?CgrateException $exception = null,
    ) {}
}
