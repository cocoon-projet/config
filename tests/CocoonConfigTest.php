<?php

use Cocoon\Config\Config;
use PHPUnit\Framework\TestCase;
use Cocoon\Config\LoadConfigFiles;

class CocoonConfigTest extends TestCase
{
    private $config;
    private $items;

    public function setUp()
    {
        $this->items = LoadConfigFiles::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config');
        $this->config = Config::getInstance($this->items);
    }

    public function testItemsIsArray()
    {
        $this->assertTrue(is_array($this->items));
    }

    public function testIfKeyConfigExist()
    {
        $this->assertTrue($this->config->has('app.url'));
    }

    public function testIsArrayAllConfigItems()
    {
        $this->assertTrue(is_array($this->config->all()));
        $this->assertCount(2, $this->config->all());
    }

    public function testGetConfigKey()
    {
        $this->assertEquals('http://localhost', $this->config->get('app.url'));
        $this->assertEquals('cocoon', $this->config->get('database.mysql.db'));
        $this->assertEquals('http://localhost', $this->config->get('app.url'));
    }

    public function testIfKeyDoesNotExistReturnNull()
    {
        $this->assertNull($this->config->get('app.none'));
    }
}