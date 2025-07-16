<?php

declare(strict_types=1);

namespace CGrate\Laravel\Events;

use CGrate\Php\DTOs\CashDepositResponseDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a cash deposit is successfully processed.
 */
final readonly class CashDeposit
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \CGrate\Php\DTOs\CashDepositResponseDTO  $response  The cash deposit response from CGrate API
     * @param  array  $cashDepositData  The original cash deposit request data
     */
    public function __construct(
        public CashDepositResponseDTO $response,
        public array $cashDepositData,
    ) {
    }
}
