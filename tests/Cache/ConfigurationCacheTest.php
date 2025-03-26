<?php

declare(strict_types=1);

namespace Cocoon\Config\Tests\Cache;

use Cocoon\Config\Cache\ConfigurationCache;
use Cocoon\Config\Environment\Environment;
use Cocoon\Config\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigurationCacheTest extends TestCase
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

    public function testIsFreshReturnsFalseInDevelopment(): void
    {
        Environment::init('development');
        $this->assertFalse(ConfigurationCache::isFresh($this->configDir));
    }

    public function testIsFreshReturnsTrueWhenCacheIsValid(): void
    {
        Environment::init('production');
        
        $data = ['test' => 'value'];
        ConfigurationCache::save($data);
        
        $this->assertTrue(ConfigurationCache::isFresh($this->configDir));
    }

    public function testLoadThrowsExceptionWhenCacheDoesNotExist(): void
    {
        $this->expectException(ConfigurationException::class);
        ConfigurationCache::load();
    }

    public function testSaveAndLoad(): void
    {
        $data = ['test' => 'value'];
        ConfigurationCache::save($data);
        
        $loaded = ConfigurationCache::load();
        $this->assertEquals($data, $loaded);
    }

    public function testClear(): void
    {
        $data = ['test' => 'value'];
        ConfigurationCache::save($data);
        
        ConfigurationCache::clear();
        
        $this->expectException(ConfigurationException::class);
        ConfigurationCache::load();
    }

    private function createTestConfigFiles(): void
    {
        file_put_contents(
            $this->configDir . '/app.php',
            '<?php return ["url" => "http://example.com"];'
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