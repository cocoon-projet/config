<?php

namespace Cocoon\Config;

use Symfony\Component\Finder\Finder;

/**
 * Classe qui charge les fichiers de configuration et retourne
 * toutes les paramètres dans un tableau php
 *
 * Class LoadConfigFiles
 * @package Cocoon\Config
 */
class LoadConfigFiles
{
    /**
     * Enregistre les fichiers de configuration et retourne
     * tous les paramètres des fichiers dans un tableau php.
     *
     * @param string $path
     * @return array paramètres des fichiers de configuration
     */
    public static function load($path) :array
    {
        $items = [];
        $files = Finder::create()->files()->name('*.php')->in($path)->depth(0);
        foreach ($files as $file) {
              $key = pathinfo($file);
              $items[$key['filename']] = require $file;
        }
        return $items;
    }
}
