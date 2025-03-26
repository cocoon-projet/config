<?php

declare(strict_types=1);

namespace Cocoon\Config\Tests\Environment;

use Cocoon\Config\Environment\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Environment::reset();
    }

    public function testInitSetsEnvironment(): void
    {
        Environment::init('production');
        $this->assertEquals('production', Environment::current());
    }

    public function testDefaultEnvironmentIsDevelopment(): void
    {
        unset($_ENV['APP_ENV'], $_SERVER['APP_ENV']);
        putenv('APP_ENV');
        Environment::init();
        $this->assertEquals('development', Environment::current());
    }

    public function testIsProduction(): void
    {
        Environment::init('production');
        $this->assertTrue(Environment::isProduction());
        $this->assertFalse(Environment::isDevelopment());
        $this->assertFalse(Environment::isTesting());
    }

    public function testIsDevelopment(): void
    {
        Environment::init('development');
        $this->assertTrue(Environment::isDevelopment());
        $this->assertFalse(Environment::isProduction());
        $this->assertFalse(Environment::isTesting());
    }

    public function testIsTesting(): void
    {
        Environment::init('testing');
        $this->assertTrue(Environment::isTesting());
        $this->assertFalse(Environment::isProduction());
        $this->assertFalse(Environment::isDevelopment());
    }

    public function testReset(): void
    {
        Environment::init('production');
        $this->assertEquals('production', Environment::current());
        
        Environment::reset();
        $this->assertEquals('development', Environment::current());
    }
} 