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
- [Available Methods](#available-methods)
- [Usage](#usage)
  - [Getting Account Balance](#getting-account-balance)
  - [Processing a Payment](#processing-a-payment)
  - [Checking Transaction Status](#checking-transaction-status)
  - [Reversing a Payment](#reversing-a-payment)
- [Events](#events)
- [Data Transfer Objects](#data-transfer-objects)
- [Artisan Commands](#artisan-commands)
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

## Available Methods

| Method                                                 | Description                    |
| ------------------------------------------------------ | ------------------------------ |
| `getAccountBalance()`                                  | Get the account balance        |
| `processCustomerPayment(PaymentRequestDTO $payment)`   | Process a new customer payment |
| `queryTransactionStatus(string $transactionReference)` | Check the status of a payment  |
| `reverseCustomerPayment(string $paymentReference)`     | Reverse a customer payment     |

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
} catch (\Illuminate\Validation\ValidationException $e) {
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

## Events

The package dispatches the following events that you can listen for in your application:

| Event              | Description                             | Properties                                                                                                                           |
| ------------------ | --------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------ |
| `PaymentProcessed` | Dispatched when a payment is successful | `response` (PaymentResponseDTO), `paymentData` (array)                                                                               |
| `PaymentFailed`    | Dispatched when a payment fails         | `request` (PaymentRequestDTO), `errorMessage` (string), `responseCode` (ResponseCode or null), `exception` (CgrateException or null) |
| `PaymentReversed`  | Dispatched when a payment is reversed   | `response` (ReversePaymentResponseDTO), `paymentReference` (string)                                                                  |

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

This command will check your account balance and display it in the console.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Shubham Chaudhary](https://github.com/shubhamc4)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
