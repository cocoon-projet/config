<?php

namespace Cocoon\Config\Environment;

class Environment
{
    private static string $environment = 'development';

    public static function init(string $env = 'development'): void
    {
        self::$environment = $env;
    }

    public static function current(): string
    {
        return self::$environment;
    }

    public static function reset(): void
    {
        self::$environment = 'development';
    }

    public static function isProduction(): bool
    {
        return self::$environment === 'production';
    }

    public static function isDevelopment(): bool
    {
        return self::$environment === 'development';
    }

    public static function isTesting(): bool
    {
        return self::$environment === 'testing';
    }
} 