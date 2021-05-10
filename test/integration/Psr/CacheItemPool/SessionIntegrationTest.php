<?php

namespace LaminasTest\Cache\Psr\CacheItemPool;

use Laminas\Cache\Psr\CacheItemPool\CacheException;
use Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator;
use Laminas\Cache\StorageFactory;
use PHPUnit\Framework\TestCase;

class SessionIntegrationTest extends TestCase
{
    /**
     * The session adapter doesn't support TTL
     */
    public function testAdapterNotSupported()
    {
        $this->expectException(CacheException::class);
        $storage = StorageFactory::adapterfactory('session');
        new CacheItemPoolDecorator($storage);
    }
}
