<?php

declare(strict_types=1);

namespace Cocoon\Config\Contracts;

/**
 * Contract for configuration managers
 */
interface ConfigInterface
{
    /**
     * Get a configuration value using dot notation
     *
     * @param string $key Configuration key in dot notation (e.g., 'database.mysql.host')
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Configuration value or default if not found
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Check if a configuration key exists
     */
    public function has(string $key): bool;

    /**
     * Get all configuration items
     *
     * @return array<string, mixed>
     */
    public function all(): array;
} 