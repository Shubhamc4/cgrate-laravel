# Changelog

All notable changes to `cgrate-laravel` will be documented in this file.

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
