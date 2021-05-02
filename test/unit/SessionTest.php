<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Cache\Storage\Adapter;

use Laminas\Cache;
use Laminas\Session\Container as SessionContainer;

/**
 * @group      Laminas_Cache
 * @covers Laminas\Cache\Storage\Adapter\Session<extended>
 */
class SessionTest extends AbstractCommonAdapterTest
{
    public function setUp(): void
    {
        $_SESSION = [];
        SessionContainer::setDefaultManager(null);
        $sessionContainer = new SessionContainer('Default');

        $this->options = new Cache\Storage\Adapter\SessionOptions([
            'session_container' => $sessionContainer,
        ]);
        $this->storage = new Cache\Storage\Adapter\Session();
        $this->storage->setOptions($this->options);

        parent::setUp();
    }

    public function tearDown(): void
    {
        $_SESSION = [];
        SessionContainer::setDefaultManager(null);
    }

    public function getCommonAdapterNamesProvider(): array
    {
        return [
            ['session'],
            ['Session'],
        ];
    }
}
