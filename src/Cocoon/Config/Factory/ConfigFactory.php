<?php

declare(strict_types=1);

namespace Cocoon\Config\Factory;

use Cocoon\Config\Config;
use Cocoon\Config\Contracts\ConfigInterface;
use Cocoon\Config\LoadConfigFiles;
use Cocoon\Config\Exception\ConfigurationException;

/**
 * Factory for creating configuration instances
 */
final class ConfigFactory
{
    /**
     * Create a new configuration instance from a directory
     *
     * @param string $configPath Path to configuration directory
     * @throws ConfigurationException If the directory is invalid or contains invalid configuration files
     */
    public static function fromDirectory(string $configPath): ConfigInterface
    {
        $items = LoadConfigFiles::load($configPath);
        return Config::getInstance($items);
    }

    /**
     * Create a new configuration instance from an array
     *
     * @param array<string, mixed> $items Configuration items
     * @throws ConfigurationException If trying to create multiple instances with different configurations
     */
    public static function fromArray(array $items): ConfigInterface
    {
        return Config::getInstance($items);
    }
} 