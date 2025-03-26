<?php

declare(strict_types=1);

namespace Cocoon\Config;

use Cocoon\Config\Contracts\ConfigInterface;
use Cocoon\Config\Exception\ConfigurationException;
use LogicException;

/**
 * Configuration manager that returns configuration values using dot notation.
 *
 * Example usage:
 * ```php
 * $config->get('database.mysql.host'); // Returns value from database.php['mysql']['host']
 * ```
 */
final class Config implements ConfigInterface
{
    /**
     * @var self|null Singleton instance
     */
    private static ?self $instance = null;

    /**
     * @param array<string, mixed> $items Configuration values
     * @param array<string, mixed> $cache Cached configuration values
     */
    private function __construct(
        private array $items = [],
        private array $cache = []
    ) {}

    /**
     * Returns a singleton instance of Config class
     *
     * @param array<string, mixed> $items Configuration values
     * @throws ConfigurationException If trying to create multiple instances with different configurations
     */
    public static function getInstance(array $items): self
    {
        if (self::$instance === null) {
            self::$instance = new self($items);
            return self::$instance;
        }

        if ($items !== self::$instance->items) {
            throw new ConfigurationException(
                'Cannot create multiple Config instances with different configurations'
            );
        }

        return self::$instance;
    }

    /**
     * Get a configuration value using dot notation
     *
     * @param string $key Configuration key in dot notation (e.g., 'database.mysql.host')
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Configuration value or default if not found
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->hasInCache($key)) {
            return $this->cache[$key];
        }

        $array = $this->items;
        $segments = explode('.', $key);

        foreach ($segments as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        $this->cache[$key] = $array;
        return $array;
    }

    /**
     * Check if a configuration key exists
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Check if a configuration value is cached
     */
    private function hasInCache(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    /**
     * Get all configuration items
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Prevent cloning of singleton instance
     * 
     * @throws LogicException
     */
    private function __clone()
    {
        throw new LogicException('Config instances cannot be cloned');
    }

    /**
     * Prevent unserialization of singleton instance
     * 
     * @throws LogicException
     */
    public function __wakeup()
    {
        throw new LogicException('Config instances cannot be unserialized');
    }
}
