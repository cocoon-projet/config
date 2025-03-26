<?php

declare(strict_types=1);

namespace Cocoon\Config\Factory;

use Cocoon\Config\Cache\GenericFileCache;
use Cocoon\Config\Config;
use Cocoon\Config\Contracts\ConfigInterface;
use Cocoon\Config\Environment\Environment;
use Cocoon\Config\Exception\ConfigurationException;

/**
 * Factory for creating configuration instances
 */
final class ConfigFactory
{
    /**
     * Create a new configuration instance from a directory
     *
     * @param string $directory Path to configuration directory
     * @param GenericFileCache|null $cache Optional cache instance
     * @throws ConfigurationException If the directory is invalid or contains invalid configuration files
     */
    public static function fromDirectory(string $directory, ?GenericFileCache $cache = null): ConfigInterface
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException("Directory does not exist: {$directory}");
        }

        $cacheKey = md5($directory . Environment::current());
        if ($cache && $cache->has($cacheKey)) {
            return new Config($cache->get($cacheKey));
        }

        $items = self::loadConfigFiles($directory);
        
        if ($cache) {
            $cache->set($cacheKey, $items);
        }

        return new Config($items);
    }

    private static function loadConfigFiles(string $directory): array
    {
        $items = [];
        $env = Environment::current();
        $files = glob($directory . '/*.php');

        foreach ($files as $file) {
            $basename = basename($file, '.php');
            if (str_contains($basename, '.')) {
                [$name, $fileEnv] = explode('.', $basename, 2);
                if ($fileEnv !== $env) {
                    continue;
                }
                $basename = $name;
            }

            $config = require $file;
            if (!is_array($config)) {
                throw new ConfigurationException("Configuration file must return an array: {$file}");
            }

            $items[$basename] = $config;
        }

        return $items;
    }

    /**
     * Create a new configuration instance from an array
     *
     * @param array<string, mixed> $items Configuration items
     * @throws ConfigurationException If trying to create multiple instances with different configurations
     */
    public static function fromArray(array $items): ConfigInterface
    {
        return new Config($items);
    }
} 