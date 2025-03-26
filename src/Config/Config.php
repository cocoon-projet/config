<?php

namespace Cocoon\Config\Config;

class Config
{
    private array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->items;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

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
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function all(): array
    {
        return $this->items;
    }

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
                return;
            }
            $current = &$current[$k];
        }
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function isString(string $key): bool
    {
        return is_string($this->get($key));
    }

    public function isInt(string $key): bool
    {
        return is_int($this->get($key));
    }

    public function isBool(string $key): bool
    {
        return is_bool($this->get($key));
    }

    public function isArray(string $key): bool
    {
        return is_array($this->get($key));
    }

    public function isNull(string $key): bool
    {
        return $this->get($key) === null;
    }
} 