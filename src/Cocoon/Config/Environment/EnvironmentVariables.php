<?php

declare(strict_types=1);

namespace Cocoon\Config\Environment;

use InvalidArgumentException;

/**
 * Gestionnaire de variables d'environnement
 */
final class EnvironmentVariables
{
    /**
     * Récupère une variable d'environnement avec une valeur par défaut
     * 
     * @param string $key Clé de la variable d'environnement
     * @param mixed $default Valeur par défaut si la variable n'existe pas
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Vérifier dans les variables d'environnement PHP
        $value = getenv($key);
        
        // Si la variable existe dans l'environnement, la retourner
        if ($value !== false) {
            // Conversion de type automatique
            return self::castValue($value);
        }

        // Vérifier dans $_ENV
        if (isset($_ENV[$key])) {
            return self::castValue($_ENV[$key]);
        }

        // Retourner la valeur par défaut
        return $default;
    }

    /**
     * Convertit automatiquement les valeurs de chaîne en types appropriés
     * 
     * @param string $value Valeur à convertir
     * @return mixed
     */
    private static function castValue(string $value): mixed
    {
        // Convertir les booléens
        if (strtolower($value) === 'true') return true;
        if (strtolower($value) === 'false') return false;

        // Convertir les valeurs nulles
        if (strtolower($value) === 'null') return null;

        // Convertir les nombres
        if (is_numeric($value)) {
            // Vérifier s'il s'agit d'un entier
            if (ctype_digit($value)) return (int)$value;
            
            // Vérifier s'il s'agit d'un nombre à virgule
            return (float)$value;
        }

        // Retourner la chaîne originale
        return $value;
    }

    /**
     * Définit une variable d'environnement
     * 
     * @param string $key Clé de la variable
     * @param mixed $value Valeur à définir
     * @throws InvalidArgumentException Si la clé ou la valeur est invalide
     */
    public static function set(string $key, mixed $value): void
    {
        // Vérifier si la clé est valide
        if (!preg_match('/^[A-Z_]+$/', $key)) {
            throw new InvalidArgumentException("Invalid environment variable key: $key");
        }

        // Vérifier si la valeur est valide
        if (!is_scalar($value) && !is_null($value)) {
            throw new InvalidArgumentException("Invalid environment variable value: " . gettype($value));
        }

        // Définir la variable d'environnement
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }

    /**
     * Supprime une variable d'environnement
     * 
     * @param string $key Clé de la variable à supprimer
     * @throws InvalidArgumentException Si la clé est invalide
     */
    public static function delete(string $key): void
    {
        // Vérifier si la clé est valide
        if (!preg_match('/^[A-Z_]+$/', $key)) {
            throw new InvalidArgumentException("Invalid environment variable key: $key");
        }

        // Supprimer la variable d'environnement
        putenv($key);
        unset($_ENV[$key]);
    }

    /**
     * Vérifie si une variable d'environnement existe
     * 
     * @param string $key Clé de la variable à vérifier
     * @return bool
     * @throws InvalidArgumentException Si la clé est invalide
     */
    public static function exists(string $key): bool
    {
        // Vérifier si la clé est valide
        if (!preg_match('/^[A-Z_]+$/', $key)) {
            throw new InvalidArgumentException("Invalid environment variable key: $key");
        }

        // Vérifier si la variable d'environnement existe
        return getenv($key) !== false || isset($_ENV[$key]);
    }

    /**
     * Récupère toutes les variables d'environnement
     * 
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        // Récupérer toutes les variables d'environnement
        return array_merge($_ENV, getenv());
    }

    /**
     * Charge les variables d'environnement depuis un fichier
     * 
     * @param string $file Chemin du fichier .env
     * @param bool $useDotenv Utiliser la bibliothèque Dotenv
     * @throws InvalidArgumentException Si le fichier n'existe pas
     */
    public static function load(string $directory): void
    {
        // Vérifier si le fichier existe
        if (!file_exists($directory . '/.env')) {
            throw new InvalidArgumentException("File not found: $directory");
        }
            // Charger le fichier .env avec Dotenv
            $dotenv = \Dotenv\Dotenv::createImmutable($directory);
            $dotenv->load();
    }
} 