<?php

declare(strict_types=1);

namespace Cocoon\Config\Tests\Exception;

use Cocoon\Config\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigurationExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $message = 'Configuration error message';
        $exception = new ConfigurationException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionWithCode(): void
    {
        $message = 'Configuration error message';
        $code = 500;
        $exception = new ConfigurationException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $message = 'Configuration error message';
        $previous = new \Exception('Previous exception');
        $exception = new ConfigurationException($message, 0, $previous);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionWithAllParameters(): void
    {
        $message = 'Configuration error message';
        $code = 500;
        $previous = new \Exception('Previous exception');
        $exception = new ConfigurationException($message, $code, $previous);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
} 