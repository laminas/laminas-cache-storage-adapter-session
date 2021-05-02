<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Cache\Psr\SimpleCache;

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
