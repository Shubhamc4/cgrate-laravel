<?php

declare(strict_types=1);

namespace CGrate\Laravel\Events;

use CGrate\Php\DTOs\PaymentRequestDTO;
use CGrate\Php\Enums\ResponseCode;
use CGrate\Php\Exceptions\CGrateException;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a payment fails.
 */
final readonly class PaymentFailed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  PaymentRequestDTO  $request  The original payment request
     * @param  string  $errorMessage  The error message
     * @param  ResponseCode|null  $responseCode  The response code if available
     * @param  CGrateException|null  $exception  The exception that caused the failure if available
     */
    public function __construct(
        public PaymentRequestDTO $request,
        public string $errorMessage,
        public ?ResponseCode $responseCode = null,
        public ?CGrateException $exception = null,
    ) {}
}
