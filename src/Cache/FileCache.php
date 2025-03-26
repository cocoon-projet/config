<?php

namespace Cocoon\Config\Cache;

class FileCache
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function get(string $key): mixed
    {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            return null;
        }
        return unserialize(file_get_contents($file));
    }

    public function set(string $key, mixed $value): void
    {
        $file = $this->getFilePath($key);
        file_put_contents($file, serialize($value));
    }

    public function has(string $key): bool
    {
        return file_exists($this->getFilePath($key));
    }

    public function delete(string $key): void
    {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function clear(): void
    {
        array_map('unlink', glob($this->cacheDir . '/*'));
    }

    private function getFilePath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key);
    }
} 