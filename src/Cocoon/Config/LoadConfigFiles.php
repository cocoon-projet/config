<?php

declare(strict_types=1);

namespace Cocoon\Config;

use Cocoon\Config\Cache\ConfigurationCache;
use Cocoon\Config\Environment\Environment;
use Cocoon\Config\Exception\ConfigurationException;
use Cocoon\Config\Security\ConfigurationLoader;
use Cocoon\Config\Validation\ConfigurationValidator;
use Symfony\Component\Finder\Finder;

/**
 * Configuration files loader that maps PHP configuration files into an array
 */
final class LoadConfigFiles
{
    /**
     * Load configuration files from a directory and return their parameters as an array
     *
     * @param string $path Path to the configuration directory
     * @param bool $useCache Whether to use cache (default: true)
     * @return array<string, mixed> Configuration parameters
     * @throws ConfigurationException If the directory is invalid or contains invalid configuration files
     */
    public static function load(string $path, bool $useCache = true): array
    {
        // Try to load from cache first
        if ($useCache && ConfigurationCache::isFresh($path)) {
            try {
                return ConfigurationCache::load();
            } catch (ConfigurationException $e) {
                // If cache loading fails, continue with normal loading
            }
        }

        if (!is_dir($path)) {
            throw new ConfigurationException(
                sprintf('Configuration directory "%s" does not exist', $path)
            );
        }

        if (!is_readable($path)) {
            throw new ConfigurationException(
                sprintf('Configuration directory "%s" is not readable', $path)
            );
        }

        $items = [];
        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in($path)
            ->depth(0);

        // Load base configurations
        foreach ($finder as $file) {
            $filename = $file->getBasename('.php');
            $config = ConfigurationLoader::load($file->getRealPath());
            ConfigurationValidator::validate($config);
            $items[$filename] = $config;
        }

        // Load environment-specific configurations
        $env = Environment::current();
        $envPath = $path . DIRECTORY_SEPARATOR . $env;
        
        if (is_dir($envPath)) {
            $envFinder = Finder::create()
                ->files()
                ->name('*.php')
                ->in($envPath)
                ->depth(0);

            foreach ($envFinder as $file) {
                $filename = $file->getBasename('.php');
                $envConfig = ConfigurationLoader::load($file->getRealPath());
                ConfigurationValidator::validate($envConfig);
                
                // Merge environment-specific configuration with base configuration
                if (isset($items[$filename])) {
                    $items[$filename] = array_replace_recursive($items[$filename], $envConfig);
                } else {
                    $items[$filename] = $envConfig;
                }
            }
        }

        // Save to cache if enabled
        if ($useCache && !Environment::isDevelopment()) {
            try {
                ConfigurationCache::save($items);
            } catch (ConfigurationException $e) {
                // If cache saving fails, just continue without caching
            }
        }

        return $items;
    }
}
