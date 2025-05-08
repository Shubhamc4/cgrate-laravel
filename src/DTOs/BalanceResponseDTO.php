<?php

declare(strict_types=1);

namespace Cgrate\Laravel\DTOs;

use Cgrate\Laravel\Enums\ResponseCode;
use stdClass;

/**
 * Data Transfer Object for balance inquiry response from Cgrate API.
 */
final class BalanceResponseDTO
{
    /**
     * Create a new balance response DTO.
     *
     * @param  ResponseCode  $responseCode  The response code from the API
     * @param  string  $responseMessage  The response message from the API
     * @param  float|null  $balance  The account balance, if successful
     */
    public function __construct(
        public readonly ResponseCode $responseCode,
        public readonly string $responseMessage,
        public readonly ?float $balance,
    ) {}

    /**
     * Create a new balance response DTO from an API response.
     *
     * @param  array|stdClass  $response  The raw response from the API
     * @return self New balance response DTO instance
     */
    public static function fromResponse(array|stdClass $response): self
    {
        $data = is_object($response) ? (array) $response : $response;

        return new self(
            responseCode: ResponseCode::fromValue($data['responseCode']),
            responseMessage: $data['responseMessage'] ?? '',
            balance: isset($data['balance']) ? (float) $data['balance'] : null
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
