<?php

declare(strict_types=1);

namespace LaminasTest\Cache\Storage\Adapter;

use Laminas\Cache\Storage\Adapter\AdapterOptions;
use Laminas\Cache\Storage\Adapter\SessionOptions;

/** @extends AbstractAdapterOptionsTest<SessionOptions> */
final class SessionOptionsTest extends AbstractAdapterOptionsTest
{
    protected function createAdapterOptions(): AdapterOptions
    {
        return new SessionOptions();
    }
}
