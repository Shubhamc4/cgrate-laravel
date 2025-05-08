<?php

declare(strict_types=1);

namespace Cgrate\Laravel\DTOs;

use Cgrate\Laravel\Enums\ResponseCode;
use stdClass;

/**
 * Data Transfer Object for payment reversal response from Cgrate API.
 */
final class ReversePaymentResponseDTO
{
    /**
     * Create a new reverse payment response DTO.
     *
     * @param  ResponseCode  $responseCode  The response code from the API
     * @param  string  $responseMessage  The response message from the API
     */
    public function __construct(
        public readonly ResponseCode $responseCode,
        public readonly string $responseMessage
    ) {}

    /**
     * Create a new reverse payment response DTO from an API response.
     *
     * @param  array|stdClass  $response  The raw response from the API
     * @return self New reverse payment response DTO instance
     */
    public static function fromResponse(array|stdClass $response): self
    {
        $data = is_object($response) ? (array) $response : $response;

        return new self(
            responseCode: ResponseCode::fromValue($data['responseCode']),
            responseMessage: $data['responseMessage'] ?? '',
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
