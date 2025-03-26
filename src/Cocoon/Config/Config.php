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
     * @param array<string, mixed> $items Configuration values
     * @param array<string, mixed> $cache Cached configuration values
     */
    public function __construct(
        private array $items = [],
        private array $cache = []
    ) {}

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
     * Set a configuration value using dot notation
     *
     * @param string $key Configuration key in dot notation (e.g., 'database.mysql.host')
     * @param mixed $value Value to set
     */
    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $current = &$this->items;

        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
        unset($this->cache[$key]);
    }

    /**
     * Delete a configuration value using dot notation
     *
     * @param string $key Configuration key in dot notation (e.g., 'database.mysql.host')
     */
    public function delete(string $key): void
    {
        $keys = explode('.', $key);
        $current = &$this->items;

        foreach ($keys as $i => $k) {
            if (!isset($current[$k])) {
                return;
            }
            if ($i === count($keys) - 1) {
                unset($current[$k]);
                unset($this->cache[$key]);
                return;
            }
            $current = &$current[$k];
        }
    }

    /**
     * Clear all configuration values
     */
    public function clear(): void
    {
        $this->items = [];
        $this->cache = [];
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
     * Check if a configuration value is a string
     */
    public function isString(string $key): bool
    {
        return is_string($this->get($key));
    }

    /**
     * Check if a configuration value is an integer
     */
    public function isInt(string $key): bool
    {
        return is_int($this->get($key));
    }

    /**
     * Check if a configuration value is a boolean
     */
    public function isBool(string $key): bool
    {
        return is_bool($this->get($key));
    }

    /**
     * Check if a configuration value is an array
     */
    public function isArray(string $key): bool
    {
        return is_array($this->get($key));
    }

    /**
     * Check if a configuration value is null
     */
    public function isNull(string $key): bool
    {
        return $this->get($key) === null;
    }
}
