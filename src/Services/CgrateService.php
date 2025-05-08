<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Services;

use Cgrate\Laravel\DTOs\BalanceResponseDTO;
use Cgrate\Laravel\DTOs\PaymentRequestDTO;
use Cgrate\Laravel\DTOs\PaymentResponseDTO;
use Cgrate\Laravel\DTOs\ReversePaymentResponseDTO;
use Cgrate\Laravel\Events\PaymentFailed;
use Cgrate\Laravel\Events\PaymentProcessed;
use Cgrate\Laravel\Events\PaymentReversed;
use Cgrate\Laravel\Exceptions\ConnectionException;
use Cgrate\Laravel\Exceptions\InvalidResponseException;
use Cgrate\Laravel\Validation\ConfigValidator;
use Cgrate\Laravel\Validation\PaymentValidator;
use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;

/**
 * Service for interacting with the Cgrate API.
 *
 * This service provides methods to perform operations with Cgrate payment gateway
 * including getting account balance, processing customer payments, querying
 * transaction status and reversing payments.
 */
final class CgrateService
{
    private ?SoapClient $client = null;

    /**
     * Create a new Cgrate service instance.
     *
     * @param  array  $config  The configuration array
     *
     * @throws \Cgrate\Laravel\Exceptions\ConnectionException If connection to the API fails
     * @throws \Illuminate\Validation\ValidationException If configuration is invalid
     */
    public function __construct(array $config)
    {
        $validatedConfig = ConfigValidator::validate($config);

        $this->initializeClient($validatedConfig);
    }

    /**
     * Get the account balance from Cgrate.
     *
     * @return BalanceResponseDTO The account balance response
     *
     * @throws \Cgrate\Laravel\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Laravel\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function getAccountBalance(): BalanceResponseDTO
    {
        try {
            $response = $this->client->getAccountBalance();

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw new InvalidResponseException('Unexpected response format from getAccountBalance');
            }

            $dto = BalanceResponseDTO::fromResponse((array) $response->return);

            if (! $dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to get account balance');
        }
    }

    /**
     * Process a customer payment.
     *
     * @param  PaymentRequestDTO  $payment  The payment request data
     * @return PaymentResponseDTO The payment response
     *
     * @throws \Cgrate\Laravel\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Laravel\Exceptions\InvalidResponseException If the API returns an error response
     * @throws \Illuminate\Validation\ValidationException If the payment request is invalid
     */
    public function processCustomerPayment(PaymentRequestDTO $payment): PaymentResponseDTO
    {
        try {
            PaymentValidator::validate($payment);

            $response = $this->client->processCustomerPayment($payment->toArray());

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw new InvalidResponseException('Unexpected response format from processCustomerPayment');
            }

            $dto = PaymentResponseDTO::fromResponse((array) $response->return + [
                'customerMobile' => $payment->customerMobile,
                'transactionReference' => $payment->paymentReference,
                'transactionAmount' => $payment->transactionAmount,
            ]);

            if (! $dto->isSuccessful()) {
                PaymentFailed::dispatch($payment, $dto->responseMessage, $dto->responseCode);
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
            }

            PaymentProcessed::dispatch($dto, $payment->toArray());

            return $dto;
        } catch (SoapFault $e) {
            PaymentFailed::dispatch($payment, $e->getMessage());

            throw ConnectionException::fromSoapFault($e, 'Failed to process customer payment');
        }
    }

    /**
     * Query the status of a transaction.
     *
     * @param  string  $transactionReference  The reference of the transaction to query
     * @return PaymentResponseDTO The transaction status response
     *
     * @throws \Cgrate\Laravel\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Laravel\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function queryTransactionStatus(string $transactionReference): PaymentResponseDTO
    {
        try {
            $response = $this->client->queryTransactionStatus([
                'transactionReference' => $transactionReference,
            ]);

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw new InvalidResponseException('Unexpected response format from queryTransactionStatus');
            }

            $dto = PaymentResponseDTO::fromResponse((array) $response->return + [
                'transactionReference' => $transactionReference,
            ]);

            if (! $dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to query transaction status');
        }
    }

    /**
     * Reverse a customer payment.
     *
     * @param  string  $paymentReference  The reference of the payment to reverse
     * @return ReversePaymentResponseDTO The reverse payment response
     *
     * @throws \Cgrate\Laravel\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Laravel\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function reverseCustomerPayment(string $paymentReference): ReversePaymentResponseDTO
    {
        try {
            $response = $this->client->reverseCustomerPayment([
                'paymentReference' => $paymentReference,
            ]);

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw new InvalidResponseException('Unexpected response format from reverseCustomerPayment');
            }

            $dto = ReversePaymentResponseDTO::fromResponse((array) $response->return + [
                'transactionReference' => $paymentReference,
            ]);

            if (! $dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
            }

            PaymentReversed::dispatch($dto, $paymentReference);

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to reverse customer payment');
        }
    }

    /**
     * Initialize the SOAP client.
     *
     * @param  array  $config  The configuration array
     */
    private function initializeClient(array $config): void
    {
        if ($this->client !== null) {
            return;
        }

        $wsseNs = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $securityXml = new SoapVar(
            '<wsse:Security xmlns:wsse="'.$wsseNs.'" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" soapenv:mustUnderstand="1" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                    <wsse:UsernameToken wsu:Id="UsernameToken-1">
                        <wsse:Username>'.$config['username'].'</wsse:Username>
                        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$config['password'].'</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>',
            XSD_ANYXML
        );

        $endpoint = $this->getEndpoint($config);
        $this->client = new SoapClient($endpoint, $config['options'] + ['location' => $endpoint]);
        $this->client->__setSoapHeaders([new SoapHeader($wsseNs, 'Security', $securityXml, true)]);
    }

    /**
     * Get the appropriate API endpoint based on configuration.
     *
     * @param  array  $config  The configuration array
     * @return string The endpoint URL
     */
    private function getEndpoint(array $config): string
    {
        return $config['test_mode'] ? $config['test_endpoint'] : $config['endpoint'];
    }
}
