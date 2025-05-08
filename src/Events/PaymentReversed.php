<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Events;

use Cgrate\Laravel\DTOs\ReversePaymentResponseDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a payment is successfully reversed.
 */
final class PaymentReversed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  ReversePaymentResponseDTO  $response  The reverse payment response from CGrate API
     */
    public function __construct(
        public readonly ReversePaymentResponseDTO $response
    ) {}
}
