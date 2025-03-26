<?php

declare(strict_types=1);

namespace Cocoon\Config\Security;

use Cocoon\Config\Exception\ConfigurationException;

/**
 * Secure configuration file loader
 */
final class ConfigurationLoader
{
    /**
     * List of allowed file extensions
     *
     * @var array<string>
     */
    private static array $allowedExtensions = ['php'];

    /**
     * Load a configuration file securely
     *
     * @param string $file Path to the configuration file
     * @return array<string, mixed> Configuration data
     * @throws ConfigurationException If the file is invalid or cannot be loaded
     */
    public static function load(string $file): array
    {
        self::validateFile($file);

        try {
            $data = require $file;
        } catch (\Throwable $e) {
            throw new ConfigurationException(
                sprintf('Failed to load configuration file "%s": %s', $file, $e->getMessage()),
                0,
                $e
            );
        }

        if (!is_array($data)) {
            throw new ConfigurationException(
                sprintf('Configuration file "%s" must return an array, %s returned', $file, gettype($data))
            );
        }

        return $data;
    }

    /**
     * Validate a configuration file
     *
     * @param string $file Path to the configuration file
     * @throws ConfigurationException If the file is invalid
     */
    private static function validateFile(string $file): void
    {
        if (!file_exists($file)) {
            throw new ConfigurationException(
                sprintf('Configuration file "%s" does not exist', $file)
            );
        }

        if (!is_readable($file)) {
            throw new ConfigurationException(
                sprintf('Configuration file "%s" is not readable', $file)
            );
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (!in_array($extension, self::$allowedExtensions, true)) {
            throw new ConfigurationException(
                sprintf(
                    'Configuration file "%s" has invalid extension "%s". Allowed extensions: %s',
                    $file,
                    $extension,
                    implode(', ', self::$allowedExtensions)
                )
            );
        }

        // Validate real path to prevent directory traversal
        $realPath = realpath($file);
        if ($realPath === false) {
            throw new ConfigurationException(
                sprintf('Failed to resolve real path for "%s"', $file)
            );
        }

        // Ensure the file is within the allowed directory
        if (!str_starts_with($realPath, realpath(getcwd()))) {
            throw new ConfigurationException(
                sprintf('Configuration file "%s" is outside the allowed directory', $file)
            );
        }
    }
} 