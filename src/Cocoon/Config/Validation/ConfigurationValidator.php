<?php

declare(strict_types=1);

namespace Cocoon\Config\Validation;

use Cocoon\Config\Exception\InvalidConfigurationException;

/**
 * Validator for configuration data
 */
final class ConfigurationValidator
{
    /**
     * Validate configuration data
     *
     * @param array<string, mixed> $data Configuration data to validate
     * @throws InvalidConfigurationException If the configuration is invalid
     */
    public static function validate(array $data): void
    {
        self::validateStructure($data);
        self::validateValues($data);
    }

    /**
     * Validate the structure of configuration data
     *
     * @param array<string, mixed> $data Configuration data to validate
     * @throws InvalidConfigurationException If the structure is invalid
     */
    private static function validateStructure(array $data): void
    {
        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                throw new InvalidConfigurationException(
                    sprintf('Configuration keys must be strings, %s given', gettype($key))
                );
            }

            if (str_contains($key, '.')) {
                throw new InvalidConfigurationException(
                    sprintf('Configuration key "%s" cannot contain dots', $key)
                );
            }

            if (is_array($value)) {
                self::validateStructure($value);
            }
        }
    }

    /**
     * Validate configuration values
     *
     * @param array<string, mixed> $data Configuration data to validate
     * @throws InvalidConfigurationException If any value is invalid
     */
    private static function validateValues(array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_resource($value)) {
                throw new InvalidConfigurationException(
                    sprintf('Configuration value for key "%s" cannot be a resource', $key)
                );
            }

            if ($value instanceof \Closure) {
                throw new InvalidConfigurationException(
                    sprintf('Configuration value for key "%s" cannot be a Closure', $key)
                );
            }

            if (is_array($value)) {
                self::validateValues($value);
            }
        }
    }
} 