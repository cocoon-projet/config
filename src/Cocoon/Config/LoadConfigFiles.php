<?php

namespace Cocoon\Config;

use Symfony\Component\Finder\Finder;
/**
 * class qui charge les fichiers de configuration
 *
 * Class LoadConfigFiles
 * @package Cocoon\Config
 */
class LoadConfigFiles
{
    /**
     * Enregistre les fichiers de configuration et retourne
     * tous les items des fichiers.
     *
     * @param string $path
     * @return array items des fichiers de configuration
     */
    public static function load($path, $cache = false) :array
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
