<?php

declare(strict_types=1);

namespace Cgrate\Laravel\DTOs;

use Cgrate\Laravel\Enums\ResponseCode;
use stdClass;

/**
 * Data Transfer Object for payment response from Cgrate API.
 */
final class PaymentResponseDTO
{
    /**
     * Create a new payment response DTO.
     *
     * @param  ResponseCode  $responseCode  The response code from the API
     * @param  string  $responseMessage  The response message from the API
     * @param  string|null  $paymentID  The ID of the processed payment, if successful
     */
    public function __construct(
        public readonly ResponseCode $responseCode,
        public readonly string $responseMessage,
        public readonly ?string $paymentID,
    ) {}

    /**
     * Create a new payment response DTO from an API response.
     *
     * @param  array|stdClass  $response  The raw response from the API
     * @return self New payment response DTO instance
     */
    public static function fromResponse(array|stdClass $response): self
    {
        $data = is_object($response) ? (array) $response : $response;

        return new self(
            responseCode: ResponseCode::fromValue($data['responseCode']),
            responseMessage: $data['responseMessage'] ?? '',
            paymentID: $data['paymentID'] ?? '',
        );
    }

    /**
     * Check if the response indicates a successful operation.
     *
     * @return bool True if the operation was successful
     */
    public function isSuccessful(): bool
    {
        return $this->responseCode->is(ResponseCode::SUCCESS);
    }
}
