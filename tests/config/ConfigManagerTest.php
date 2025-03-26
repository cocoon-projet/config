<?php

declare(strict_types=1);

namespace Cocoon\Config\Tests\Config;

use Cocoon\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigManagerTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new Config([
            'app' => [
                'url' => 'http://example.com',
                'debug' => true,
                'timezone' => 'Europe/Paris'
            ],
            'database' => [
                'engine' => 'mysql',
                'mysql' => [
                    'host' => 'localhost',
                    'dbname' => 'testdb'
                ]
            ]
        ]);
    }

    public function testGet(): void
    {
        $this->assertEquals('http://example.com', $this->config->get('app.url'));
        $this->assertEquals('mysql', $this->config->get('database.engine'));
        $this->assertEquals('localhost', $this->config->get('database.mysql.host'));
    }

    public function testGetWithDefaultValue(): void
    {
        $this->assertNull($this->config->get('app.nonexistent'));
        $this->assertEquals('default', $this->config->get('app.nonexistent', 'default'));
    }

    public function testHas(): void
    {
        $this->assertTrue($this->config->has('app.url'));
        $this->assertTrue($this->config->has('database.mysql.host'));
        $this->assertFalse($this->config->has('app.nonexistent'));
    }

    public function testAll(): void
    {
        $all = $this->config->all();
        $this->assertIsArray($all);
        $this->assertArrayHasKey('app', $all);
        $this->assertArrayHasKey('database', $all);
    }

    public function testSet(): void
    {
        $this->config->set('app.new_key', 'new_value');
        $this->assertEquals('new_value', $this->config->get('app.new_key'));
    }

    public function testSetNested(): void
    {
        $this->config->set('database.redis.host', 'localhost');
        $this->assertEquals('localhost', $this->config->get('database.redis.host'));
    }

    public function testDelete(): void
    {
        $this->config->delete('app.url');
        $this->assertFalse($this->config->has('app.url'));
    }

    public function testDeleteNested(): void
    {
        $this->config->delete('database.mysql');
        $this->assertFalse($this->config->has('database.mysql.host'));
    }

    public function testClear(): void
    {
        $this->config->clear();
        $this->assertEmpty($this->config->all());
    }

    public function testIsString(): void
    {
        $this->assertTrue($this->config->isString('app.url'));
        $this->assertFalse($this->config->isString('app.debug'));
    }

    public function testIsInt(): void
    {
        $this->config->set('app.port', 8080);
        $this->assertTrue($this->config->isInt('app.port'));
        $this->assertFalse($this->config->isInt('app.url'));
    }

    public function testIsBool(): void
    {
        $this->assertTrue($this->config->isBool('app.debug'));
        $this->assertFalse($this->config->isBool('app.url'));
    }

    public function testIsArray(): void
    {
        $this->assertTrue($this->config->isArray('database.mysql'));
        $this->assertFalse($this->config->isArray('app.url'));
    }

    public function testIsNull(): void
    {
        $this->config->set('app.null_value', null);
        $this->assertTrue($this->config->isNull('app.null_value'));
        $this->assertFalse($this->config->isNull('app.url'));
    }
} 