<?php

declare(strict_types=1);

use Cocoon\Config\Environment\EnvironmentVariables;

if (!function_exists('env')) {
    /**
     * Récupère une variable d'environnement avec une valeur par défaut
     * 
     * @param string $key Clé de la variable d'environnement
     * @param mixed $default Valeur par défaut si la variable n'existe pas
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        return EnvironmentVariables::get($key, $default);
    }
} 