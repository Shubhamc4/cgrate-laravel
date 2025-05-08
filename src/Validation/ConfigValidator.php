<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Validation;

use Cgrate\Laravel\Exceptions\ValidationException;

/**
 * Validator for CGrate configuration.
 *
 * Validates configuration values to ensure all required settings
 * are properly configured before initializing the API client.
 */
final class ConfigValidator
{
    /**
     * Required configuration keys.
     *
     * @var array<string>
     */
    private static array $requiredKeys = [
        'username',
        'password',
        'endpoint',
        'test_endpoint',
    ];

    /**
     * Optional configuration keys with default values.
     *
     * @var array<string, mixed>
     */
    private static array $optionalKeys = [
        'test_mode' => false,
        'options' => [
            'soap_version' => SOAP_1_1,
            'connection_timeout' => 30,
            'keep_alive' => false,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => false,
            'exceptions' => false,
        ],
    ];

    /**
     * Validate CGrate configuration.
     *
     * @param  array  $config  The configuration array to validate
     * @return array The validated and normalized configuration
     *
     * @throws \Cgrate\Laravel\Exceptions\ValidationException If configuration is invalid
     */
    public static function validate(array $config): array
    {
        $errors = [];

        foreach (self::$requiredKeys as $key) {
            if (! isset($config[$key]) || empty($config[$key])) {
                $errors[$key] = "The '{$key}' configuration value is required";
            }
        }

        if (isset($config['endpoint']) && ! filter_var($config['endpoint'], FILTER_VALIDATE_URL)) {
            $errors['endpoint'] = 'The endpoint must be a valid URL';
        }

        if (isset($config['test_endpoint']) && ! filter_var($config['test_endpoint'], FILTER_VALIDATE_URL)) {
            $errors['test_endpoint'] = 'The test_endpoint must be a valid URL';
        }

        // Apply defaults for optional keys
        foreach (self::$optionalKeys as $key => $defaultValue) {
            if (! isset($config[$key])) {
                $config[$key] = $defaultValue;
            }
        }

        // Ensure options is an array
        if (! is_array($config['options'])) {
            $errors['options'] = 'The options setting must be an array';
            $config['options'] = [];
        }

        // If there are errors, throw exception
        if (! empty($errors)) {
            throw new ValidationException(
                'Invalid CGrate configuration',
                $errors
            );
        }

        return $config;
    }
}
