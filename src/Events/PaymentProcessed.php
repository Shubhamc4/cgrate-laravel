<?php

declare(strict_types=1);

namespace CGrate\Laravel\Events;

use CGrate\Php\DTOs\PaymentResponseDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a payment is successfully processed.
 */
final readonly class PaymentProcessed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  PaymentResponseDTO  $response  The payment response from CGrate API
     * @param  array  $paymentData  The original payment request data
     */
    public function __construct(
        public PaymentResponseDTO $response,
        public array $paymentData,
    ) {}
}
