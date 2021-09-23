<?php

namespace LaminasTest\Cache\Storage\Adapter;

use Laminas\Cache\Storage\Adapter\Session;
use Laminas\Cache\Storage\Adapter\SessionOptions;
use Laminas\Session\Container as SessionContainer;

/**
 * @template-extends AbstractCommonAdapterTest<Session,SessionOptions>
 */
final class SessionTest extends AbstractCommonAdapterTest
{
    public function setUp(): void
    {
        $_SESSION = [];
        SessionContainer::setDefaultManager(null);
        $sessionContainer = new SessionContainer('Default');

        $this->options = new SessionOptions([
            'session_container' => $sessionContainer,
        ]);
        $this->storage = new Session();
        $this->storage->setOptions($this->options);
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $_SESSION = [];
        SessionContainer::setDefaultManager(null);
    }
}
