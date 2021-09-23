<?php

namespace LaminasBench\Cache;

use Laminas\Cache\Storage\Adapter\Benchmark\AbstractStorageAdapterBenchmark;
use Laminas\Cache\Storage\Adapter\Session;
use Laminas\Cache\Storage\Adapter\SessionOptions;
use Laminas\Session\Container as SessionContainer;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;

/**
 * @Revs(100)
 * @Iterations(10)
 * @Warmup(1)
 */
class SessionStorageAdapterBench extends AbstractStorageAdapterBenchmark
{
    public function __construct()
    {
        SessionContainer::setDefaultManager(null);
        $sessionContainer = new SessionContainer('Default');

        parent::__construct(new Session(new SessionOptions([
            'session_container' => $sessionContainer,
        ])));
    }
}
