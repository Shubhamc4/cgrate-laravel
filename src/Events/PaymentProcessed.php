<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Events;

use Cgrate\Laravel\DTOs\PaymentResponseDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a payment is successfully processed.
 */
final class PaymentProcessed
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
        public readonly PaymentResponseDTO $response,
        public readonly array $paymentData,
    ) {}
}
