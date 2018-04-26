<?php

namespace Cocoon\Config;


/**
 * class qui retourne les valeurs des fichiers de configuration
 * au format: dot notation
 *
 * Class Config
 * @package Cocoon\Config
 */
class Config
{
    /**
     * Valeurs des fichiers de configuration
     *
     * @var array
     */
    private $items = [];
    /**
     * Cache les données d'une valeur d'un fichier de configuration
     *
     * @var array
     */
    private $cache = [];
    /**
     * instance de la classe Config
     *
     * @var object
     */
    private static $instances = null;

    /**
     * Config constructor.
     * @param array $items
     */
    private function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * Retourne une instance de Config::class
     *
     * @param array $items
     * @return self Config::class instance
     */
    public static function getInstance($items)
    {
        if (self::$instances == null) {
            self::$instances = new self($items);
        }
        return self::$instances;
    }
    /**
     * On interdit le clonage
     *
     * @return LogicException::class
     */
    public function __clone()
    {
        throw new \LogicException('La classe Config ne peut pas etre clonee !');
    }
    /**
     * Retourne une valeur de configuration (dot  notation)
     *
     * @param $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->hasInCache($key)) {
            $array = $this->cache[$key];
        } else {
            $array = $this->items;
            $arrs = explode('.', $key);
            foreach ($arrs as $segment) {
                if (isset($array[$segment])) {
                    $array = $array[$segment];
                } else {
                    $array = $default;
                    break;
                }
            }
            $this->cache[$key] = $array;
        }
        return $array;
    }
    /**
     * Verifie si une clef de configuration existe.
     *
     * @param string $key
     * @return boolean
     */
    public function has($key) :bool
    {
        return !is_null($this->get($key));
    }

    /**
     * Verifie si une valeur de configuration est en cache
     *
     * @param string $key clef d'une valeur de configuration
     * @return null ou differrent de null
     */
    protected function hasInCache($key) :bool
    {
        return isset($this->cache[$key]);
    }
    /**
     * Retourne tous les items des fichiers de configurations
     *
     * @return void
     */
    public function all() :array
    {
        return $this->items;
    }
}
