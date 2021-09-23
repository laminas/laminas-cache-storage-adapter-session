<?php

declare(strict_types=1);

namespace Laminas\Cache\Storage\Adapter\Session;

use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\Adapter\Session;
use Laminas\Cache\Storage\AdapterPluginManager;
use Laminas\ServiceManager\Factory\InvokableFactory;

use function assert;

final class AdapterPluginManagerDelegatorFactory
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback): AdapterPluginManager
    {
        $pluginManager = $callback();
        assert($pluginManager instanceof AdapterPluginManager);

        $pluginManager->configure([
            'factories' => [
                Session::class => InvokableFactory::class,
            ],
            'aliases'   => [
                'session' => Session::class,
                'Session' => Session::class,
            ],
        ]);

        return $pluginManager;
    }
}
