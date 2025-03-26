<?php

namespace Cocoon\Config;

use Cocoon\Config\Factory\ConfigFactory;

class LoadConfigFiles
{
    public static function load(string $directory): array
    {
        return ConfigFactory::fromDirectory($directory)->all();
    }
} 