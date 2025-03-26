<?php

declare(strict_types=1);

namespace Cocoon\Config\Cache;

use Cocoon\Config\Environment\Environment;
use Cocoon\Config\Exception\ConfigurationException;

/**
 * Configuration cache manager
 */
final class ConfigurationCache
{
    private const CACHE_FILE_PREFIX = 'config_cache_';
    private const CACHE_FILE_EXTENSION = '.php';

    /**
     * Get cache file path for current environment
     */
    private static function getCacheFilePath(): string
    {
        $cacheDir = self::getCacheDirectory();
        return $cacheDir . DIRECTORY_SEPARATOR 
            . self::CACHE_FILE_PREFIX 
            . Environment::current() 
            . self::CACHE_FILE_EXTENSION;
    }

    /**
     * Get cache directory path
     */
    private static function getCacheDirectory(): string
    {
        $cacheDir = getcwd() . DIRECTORY_SEPARATOR . 'cache';
        
        if (!is_dir($cacheDir) && !mkdir($cacheDir, 0777, true) && !is_dir($cacheDir)) {
            throw new ConfigurationException(
                sprintf('Cache directory "%s" could not be created', $cacheDir)
            );
        }

        return $cacheDir;
    }

    /**
     * Check if cache exists and is fresh
     *
     * @param string $configDir Configuration directory path
     */
    public static function isFresh(string $configDir): bool
    {
        if (Environment::isDevelopment()) {
            return false;
        }

        $cacheFile = self::getCacheFilePath();
        if (!file_exists($cacheFile)) {
            return false;
        }

        $cacheTime = filemtime($cacheFile);
        if ($cacheTime === false) {
            return false;
        }

        // Check if any config file is newer than cache
        $configFiles = glob($configDir . DIRECTORY_SEPARATOR . '*.php');
        if ($configFiles === false) {
            return false;
        }

        foreach ($configFiles as $file) {
            $fileTime = filemtime($file);
            if ($fileTime === false || $fileTime > $cacheTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * Load configuration from cache
     *
     * @return array<string, mixed>
     * @throws ConfigurationException If cache cannot be loaded
     */
    public static function load(): array
    {
        $cacheFile = self::getCacheFilePath();
        
        if (!file_exists($cacheFile)) {
            throw new ConfigurationException('Cache file does not exist');
        }

        $data = require $cacheFile;
        if (!is_array($data)) {
            throw new ConfigurationException('Invalid cache data');
        }

        return $data;
    }

    /**
     * Save configuration to cache
     *
     * @param array<string, mixed> $data Configuration data
     * @throws ConfigurationException If cache cannot be saved
     */
    public static function save(array $data): void
    {
        $cacheFile = self::getCacheFilePath();
        
        $content = '<?php return ' . var_export($data, true) . ';';
        
        if (file_put_contents($cacheFile, $content) === false) {
            throw new ConfigurationException(
                sprintf('Failed to write cache file "%s"', $cacheFile)
            );
        }
    }

    /**
     * Clear configuration cache
     */
    public static function clear(): void
    {
        $cacheDir = self::getCacheDirectory();
        $pattern = $cacheDir . DIRECTORY_SEPARATOR . self::CACHE_FILE_PREFIX . '*' . self::CACHE_FILE_EXTENSION;
        
        $files = glob($pattern);
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            unlink($file);
        }
    }
} 