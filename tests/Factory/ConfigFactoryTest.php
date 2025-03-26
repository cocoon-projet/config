<?php

declare(strict_types=1);

namespace Cocoon\Config\Tests\Factory;

use Cocoon\Config\Factory\ConfigFactory;
use Cocoon\Config\Cache\GenericFileCache;
use Cocoon\Config\Environment\Environment;
use PHPUnit\Framework\TestCase;

class ConfigFactoryTest extends TestCase
{
    private string $configDir;
    private string $cacheDir;

    protected function setUp(): void
    {
        parent::setUp();
        Environment::reset();
        
        $this->configDir = sys_get_temp_dir() . '/cocoon-config-test-' . uniqid();
        $this->cacheDir = sys_get_temp_dir() . '/cocoon-cache-test-' . uniqid();
        
        mkdir($this->configDir);
        mkdir($this->cacheDir);
        
        $this->createTestConfigFiles();
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->configDir);
        $this->removeDirectory($this->cacheDir);
        parent::tearDown();
    }

    public function testFromDirectory(): void
    {
        $config = ConfigFactory::fromDirectory($this->configDir);
        
        $this->assertEquals('http://example.com', $config->get('app.url'));
        $this->assertEquals('mysql', $config->get('database.engine'));
        $this->assertEquals('localhost', $config->get('database.mysql.host'));
    }

    public function testFromDirectoryWithCache(): void
    {
        $cache = new GenericFileCache($this->cacheDir);
        $config = ConfigFactory::fromDirectory($this->configDir, $cache);
        
        $this->assertEquals('http://example.com', $config->get('app.url'));
        $this->assertEquals('mysql', $config->get('database.engine'));
        $this->assertEquals('localhost', $config->get('database.mysql.host'));
    }

    public function testFromDirectoryWithEnvironment(): void
    {
        Environment::init('production');
        $config = ConfigFactory::fromDirectory($this->configDir);
        
        $this->assertEquals('https://example.com', $config->get('app.url'));
        $this->assertEquals('mysql', $config->get('database.engine'));
        $this->assertEquals('prod-db', $config->get('database.mysql.host'));
    }

    public function testFromDirectoryWithInvalidPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ConfigFactory::fromDirectory('/invalid/path');
    }

    private function createTestConfigFiles(): void
    {
        // app.php
        file_put_contents(
            $this->configDir . '/app.php',
            '<?php return ["url" => "http://example.com", "debug" => true];'
        );

        // app.production.php
        file_put_contents(
            $this->configDir . '/app.production.php',
            '<?php return ["url" => "https://example.com", "debug" => false];'
        );

        // database.php
        file_put_contents(
            $this->configDir . '/database.php',
            '<?php return [
                "engine" => "mysql",
                "mysql" => [
                    "host" => "localhost",
                    "dbname" => "testdb",
                    "user" => "testuser",
                    "password" => "testpass"
                ]
            ];'
        );

        // database.production.php
        file_put_contents(
            $this->configDir . '/database.production.php',
            '<?php return [
                "engine" => "mysql",
                "mysql" => [
                    "host" => "prod-db",
                    "dbname" => "proddb",
                    "user" => "produser",
                    "password" => "prodpass"
                ]
            ];'
        );
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
} 