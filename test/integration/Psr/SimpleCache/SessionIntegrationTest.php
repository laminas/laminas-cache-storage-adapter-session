<?php

namespace LaminasTest\Cache\Storage\Adapter\Psr\SimpleCache;

use Laminas\Cache\Psr\CacheItemPool\CacheException;
use Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator;
use Laminas\Cache\Storage\Adapter\Session;
use PHPUnit\Framework\TestCase;

class SessionIntegrationTest extends TestCase
{
    /**
     * The session adapter doesn't support TTL
     */
    public function testAdapterNotSupported()
    {
        $this->expectException(CacheException::class);
        $storage = new Session();
        new CacheItemPoolDecorator($storage);
    }
}
