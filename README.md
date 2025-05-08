# CGrate Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shubhamc4/cgrate-laravel.svg)](https://packagist.org/packages/shubhamc4/cgrate-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/shubhamc4/cgrate-laravel.svg)](https://packagist.org/packages/shubhamc4/cgrate-laravel)
[![License](https://img.shields.io/packagist/l/shubhamc4/cgrate-laravel.svg)](https://github.com/shubhamc4/cgrate-laravel/blob/main/LICENSE)

A Laravel package for integrating with the CGrate payment service to process mobile money transactions in Zambia.

## Table of Contents

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Getting Account Balance](#getting-account-balance)
  - [Processing a Payment](#processing-a-payment)
  - [Checking Transaction Status](#checking-transaction-status)
  - [Reversing a Payment](#reversing-a-payment)
- [Available Methods](#available-methods)
- [Events](#events)
- [Response Codes](#response-codes)
- [Handling Exceptions](#handling-exceptions)
- [Data Transfer Objects](#data-transfer-objects)
- [Artisan Commands](#artisan-commands)
- [Testing](#testing)
- [Code Style](#code-style)
- [Security](#security)
- [Changelog](#changelog)
- [Credits](#credits)
- [License](#license)

## Introduction

[CGrate](https://cgrate.co.zm) ([543 Konse Konse](https://www.543.co.zm)) is a payment service provider based in Zambia that facilitates mobile money transactions. This Laravel package allows businesses to:

- Process payments from mobile money accounts
- Check account balances in real-time
- Verify transaction status
- Reverse/refund payments when necessary

The service operates via a SOAP API that requires WS-Security authentication. CGrate is widely used for integrating with local payment systems in Zambia, making it easier for businesses to accept mobile payments from customers.

For more information about CGrate payment service, visit their [official website](https://cgrate.co.zm) or contact their support team at support@cgrate.co.zm.

### Official Documentation

For detailed information on the CGrate SOAP API, including setup instructions, request formats, and response codes, please refer to the official [EVDSpec 2024.pdf](./docs/EVDSpec_2024.pdf) document. This comprehensive guide provides all the technical specifications required for integrating with the CGrate service.

## Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher
- PHP SOAP extension

## Installation

You can install the package via composer:

```bash
composer require shubhamc4/cgrate-laravel
```

The package will automatically register its service provider and facade.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Cgrate\Laravel\CgrateServiceProvider"
```

This will create a `config/cgrate.php` configuration file in your application where you can modify the settings.

### Environment Variables

The package requires the following environment variables to be set in your `.env` file:

```env
# Required variables
CGRATE_USERNAME=your_username           # Your CGrate account username
CGRATE_PASSWORD=your_password           # Your CGrate account password

# Optional variables (with defaults)
CGRATE_TEST_MODE=true                   # Set to false for production
```

## Usage

### Getting Account Balance

```php
use Cgrate\Laravel\Facades\Cgrate;

// Get account balance
try {
    $response = Cgrate::getAccountBalance();

    if ($response->isSuccessful()) {
        echo 'Account Balance: ' . $response->balance;
    } else {
        echo 'Error: ' . $response->responseMessage;
    }
} catch (\Cgrate\Laravel\Exceptions\CgrateException $e) {
    echo 'API Error: ' . $e->getMessage();
}
```

### Processing a Payment

```php
use Cgrate\Laravel\DTOs\PaymentRequestDTO;
use Cgrate\Laravel\Facades\Cgrate;

// Create a payment request
$payment = new PaymentRequestDTO(
    transactionAmount: 10.00,
    customerMobile: '260970000000', // Zambian mobile number format (without the + sign)
    paymentReference: 'INVOICE-123'
);

// Or use the factory method for convenience
$payment = PaymentRequestDTO::create(
    transactionAmount: 10.00,
    customerMobile: '260970000000',
    paymentReference: 'INVOICE-123'
);

// Process the payment
try {
    $response = Cgrate::processCustomerPayment($payment);

    if ($response->isSuccessful()) {
        echo 'Payment successful! Payment ID: ' . $response->paymentID;
    } else {
        echo 'Payment failed: ' . $response->responseMessage;
    }
} catch (\Cgrate\Laravel\Exceptions\ValidationException $e) {
    echo 'Validation Error: ' . $e->getMessage();
    foreach ($e->errors() as $field => $errors) {
        echo "\n- {$field}: " . implode(', ', $errors);
    }
} catch (\Cgrate\Laravel\Exceptions\CgrateException $e) {
    echo 'API Error: ' . $e->getMessage();
}
```

### Checking Transaction Status

```php
use Cgrate\Laravel\Facades\Cgrate;

// Query transaction status
try {
    $response = Cgrate::queryTransactionStatus('TRANSACTION-REF-123');

    if ($response->isSuccessful()) {
        echo 'Transaction Status: Success';
        echo 'Payment ID: ' . $response->paymentID;
    } else {
        echo 'Status query failed: ' . $response->responseMessage;
    }
} catch (\Cgrate\Laravel\Exceptions\CgrateException $e) {
    echo 'API Error: ' . $e->getMessage();
}
```

### Reversing a Payment

```php
use Cgrate\Laravel\Facades\Cgrate;

// Reverse a payment
try {
    $response = Cgrate::reverseCustomerPayment('PAYMENT-REF-123');

    if ($response->isSuccessful()) {
        echo 'Payment reversed successfully';
    } else {
        echo 'Reversal failed: ' . $response->responseMessage;
    }
} catch (\Cgrate\Laravel\Exceptions\CgrateException $e) {
    echo 'API Error: ' . $e->getMessage();
}
```

## Available Methods

| Method                                                 | Description                    |
| ------------------------------------------------------ | ------------------------------ |
| `getAccountBalance()`                                  | Get the account balance        |
| `processCustomerPayment(PaymentRequestDTO $payment)`   | Process a new customer payment |
| `queryTransactionStatus(string $transactionReference)` | Check the status of a payment  |
| `reverseCustomerPayment(string $paymentReference)`     | Reverse a customer payment     |

## Events

The package dispatches the following events that you can listen for in your application:

| Event              | Description                             | Properties                                                                                                           |
| ------------------ | --------------------------------------- | -------------------------------------------------------------------------------------------------------------------- |
| `PaymentProcessed` | Dispatched when a payment is successful | `response` (PaymentResponseDTO), `paymentData` (array)                                                               |
| `PaymentFailed`    | Dispatched when a payment fails         | `request` (PaymentRequestDTO), `errorMessage` (string), `responseCode` (ResponseCode), `exception` (CgrateException) |
| `PaymentReversed`  | Dispatched when a payment is reversed   | `response` (ReversePaymentResponseDTO), `paymentReference` (string)                                                  |

### Example: Listening for Events

Register your event listeners in your `EventServiceProvider`:

```php
use App\Listeners\HandlePaymentProcessed;
use Cgrate\Laravel\Events\PaymentProcessed;

protected $listen = [
    PaymentProcessed::class => [
        HandlePaymentProcessed::class,
    ],
];
```

## Response Codes

The package uses the following response codes from the CGrate API:

| Code | Description                                   |
| ---- | --------------------------------------------- |
| -1   | Unknown response code                         |
| 0    | Success                                       |
| 1    | Insufficient balance                          |
| 6    | General error                                 |
| 7    | Invalid MSISDN                                |
| 8    | Process delay                                 |
| 9    | Balance update failed                         |
| 10   | Balance retrieval failed                      |
| 31   | Account is active                             |
| 32   | Account is inactive                           |
| 33   | Account is suspended                          |
| 34   | Account is closed                             |
| 35   | Password tries exceeded                       |
| 36   | Incorrect password                            |
| 37   | Account does not exist                        |
| 51   | Insufficient stock                            |
| 52   | Invalid voucher request                       |
| 53   | Invalid recharge                              |
| 54   | Invalid recharge denomination                 |
| 55   | Voucher Provider content failed               |
| 56   | Invalid Voucher Provider                      |
| 101  | Invalid distribution channel                  |
| 102  | USSD transaction not available                |
| 151  | Reconciliation failed                         |
| 152  | No reconciliation found                       |
| 153  | Reconciliation flag not consistent with count |
| 154  | Error receiving reconciliation total          |

## Handling Exceptions

The package throws specific exceptions for different error scenarios:

- `CgrateException`: Base exception class for all package exceptions
- `ConnectionException`: Thrown when there's a connection issue with the API
- `InvalidResponseException`: Thrown when the API returns an unexpected or error response
- `ValidationException`: Thrown when payment request validation fails

### Common Validation Errors

The package includes validation to prevent common errors:

1. **Transaction Amount**: Must be greater than zero
2. **Mobile Number Format**: Must be a valid Zambian mobile number
   - Must start with `260` (country code without the + sign)
   - Must be in the format `260XXXXXXXXX` (total of 12 digits)
   - Must be a valid Zamtel, MTN, or Airtel number
3. **Payment Reference**: Must be non-empty and contain only alphanumeric characters and hyphens

## Data Transfer Objects

The package uses DTOs to handle API requests and responses:

### Request DTOs

- `PaymentRequestDTO`: Contains payment request data (transactionAmount, customerMobile, paymentReference)

### Response DTOs

- `BalanceResponseDTO`: Contains account balance information
- `PaymentResponseDTO`: Contains payment response information
- `ReversePaymentResponseDTO`: Contains payment reversal response information

## Artisan Commands

The package provides the following Artisan commands:

```bash
php artisan cgrate:balance
```

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## Security

If you discover any security issues, please email the author instead of using the issue tracker.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Shubham Chaudhary](https://github.com/shubhamc4)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
