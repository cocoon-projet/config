<?php

namespace Cocoon\Config\Factory;

use Cocoon\Config\Cache\FileCache;
use Cocoon\Config\Config\Config;
use Cocoon\Config\Environment\Environment;
use Cocoon\Config\Exception\ConfigurationException;

class ConfigFactory
{
    public static function fromDirectory(string $directory, ?FileCache $cache = null): Config
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
} 