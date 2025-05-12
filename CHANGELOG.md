# Changelog

All notable changes to `cgrate-laravel` will be documented in this file.

## 2.0.0 - 2025-05-12

feat: Introduce PHP 8.2+ compatible shubhamc4/cgrate-php v2.0

- This release adds shubhamc4/cgrate-php (version 2.0) to the project's composer dependencies. This version is required for full compatibility and optimal performance on PHP 8.2 and subsequent releases.

## 1.0.0 - 2025-05-08

### Added

- Initial release
- Support for CGrate payment service integration
- Core functionality:
  - Get account balance
  - Process customer payments
  - Query transaction status
  - Reverse payments
- Event system for payment endpoints (PaymentProcessed, PaymentFailed, PaymentReversed)
- Comprehensive exception handling
- Data Transfer Objects for convenient API interaction
- Artisan command for checking account balance
- Validation for payment requests
- WS-Security authentication for SOAP API
- Detailed documentation and usage examples
