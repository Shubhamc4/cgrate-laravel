<?php

declare(strict_types=1);

namespace CGrate\Laravel\Events;

use CGrate\Php\DTOs\ReversePaymentResponseDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a payment is successfully reversed.
 */
final readonly class PaymentReversed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  ReversePaymentResponseDTO  $response  The reverse payment response from CGrate API
     */
    public function __construct(
        public ReversePaymentResponseDTO $response
    ) {}
}
