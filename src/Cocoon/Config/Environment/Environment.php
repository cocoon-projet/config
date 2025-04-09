<?php

declare(strict_types=1);

namespace Cocoon\Config\Environment;

use Dotenv\Dotenv;
use InvalidArgumentException;

/**
 * Gestionnaire d'environnement d'exécution
 */
final class Environment
{
    private const DEFAULT_ENV = 'development';
    
    /**
     * @var string Current environment
     */
    private static string $currentEnv;

    /**
     * Initialize the environment
     */
    public static function init(?string $env = null): void
    {
        if ($env !== null) {
            self::$currentEnv = $env;
            return;
        }

        $envVar = EnvironmentVariables::get('APP_ENV');
        self::$currentEnv = $envVar !== null ? $envVar : self::DEFAULT_ENV;
    }

    /**
     * Get current environment
     */
    public static function current(): string
    {
        return self::$currentEnv ?? self::DEFAULT_ENV;
    }

    /**
     * Check if current environment matches given environment
     */
    public static function is(string $env): bool
    {
        return self::current() === $env;
    }

    /**
     * Check if current environment is development
     */
    public static function isDevelopment(): bool
    {
        return self::is('development') || self::is('dev');
    }

    /**
     * Check if current environment is production
     */
    public static function isProduction(): bool
    {
        return self::is('production') || self::is('prod');
    }

    /**
     * Check if current environment is testing
     */
    public static function isTesting(): bool
    {
        return self::is('testing') || self::is('test');
    }

    /**
     * Reset environment to default value
     */
    public static function reset(): void
    {
        self::$currentEnv = self::DEFAULT_ENV;
    }

} 