<?php

declare(strict_types=1);

namespace Cocoon\Config\Tests\Cache;

use Cocoon\Config\Cache\FileCache;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    private string $cacheDir;
    private FileCache $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheDir = sys_get_temp_dir() . '/cocoon-config-test-' . uniqid();
        mkdir($this->cacheDir);
        $this->cache = new FileCache($this->cacheDir);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->cacheDir);
        parent::tearDown();
    }

    public function testSetAndGet(): void
    {
        $key = 'test-key';
        $value = ['test' => 'value'];

        $this->cache->set($key, $value);
        $this->assertEquals($value, $this->cache->get($key));
    }

    public function testGetNonExistentKey(): void
    {
        $this->assertNull($this->cache->get('non-existent-key'));
    }

    public function testHas(): void
    {
        $key = 'test-key';
        $value = ['test' => 'value'];

        $this->assertFalse($this->cache->has($key));
        $this->cache->set($key, $value);
        $this->assertTrue($this->cache->has($key));
    }

    public function testDelete(): void
    {
        $key = 'test-key';
        $value = ['test' => 'value'];

        $this->cache->set($key, $value);
        $this->assertTrue($this->cache->has($key));
        
        $this->cache->delete($key);
        $this->assertFalse($this->cache->has($key));
    }

    public function testClear(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');

        $this->assertTrue($this->cache->has('key1'));
        $this->assertTrue($this->cache->has('key2'));

        $this->cache->clear();

        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
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