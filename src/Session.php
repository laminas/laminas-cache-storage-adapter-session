<?php

declare(strict_types=1);

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Cache\Exception;
use Laminas\Cache\Storage\Capabilities;
use Laminas\Cache\Storage\ClearByPrefixInterface;
use Laminas\Cache\Storage\FlushableInterface;
use Laminas\Cache\Storage\IterableInterface;
use Laminas\Session\Container;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function str_starts_with;

/**
 * @template-extends AbstractAdapter<SessionOptions>
 * @implements IterableInterface<array-key,mixed>
 */
final class Session extends AbstractAdapter implements
    ClearByPrefixInterface,
    FlushableInterface,
    IterableInterface
{
    /**
     * {@inheritDoc}
     */
    public function setOptions(iterable|AdapterOptions $options): self
    {
        if (! $options instanceof SessionOptions) {
            $options = new SessionOptions($options);
        }

        parent::setOptions($options);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): SessionOptions
    {
        $options = $this->options;
        if ($options === null) {
            $options = new SessionOptions();
            $this->setOptions($options);
        }
        return $options;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSessionContainer(): Container
    {
        $sessionContainer = $this->getOptions()->getSessionContainer();
        if (! $sessionContainer) {
            throw new Exception\RuntimeException("No session container configured");
        }
        return $sessionContainer;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): KeyListIterator
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if ($cntr->offsetExists($ns)) {
            $keys = array_keys($cntr->offsetGet($ns));
        } else {
            $keys = [];
        }

        return new KeyListIterator($this, $keys);
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): bool
    {
        $this->getSessionContainer()->exchangeArray([]);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clearByPrefix(string $prefix): bool
    {
        /** @psalm-suppress TypeDoesNotContainType To prevent deleting unexpected keys, we should double validate */
        if ($prefix === '') {
            throw new Exception\InvalidArgumentException('No prefix given');
        }

        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if (! $cntr->offsetExists($ns)) {
            return true;
        }

        $data = $cntr->offsetGet($ns);
        foreach ($data as $key => &$item) {
            if (str_starts_with($key, $prefix)) {
                unset($data[$key]);
            }
        }
        $cntr->offsetSet($ns, $data);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalGetItem(string $normalizedKey, ?bool &$success = null, mixed &$casToken = null): mixed
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if (! $cntr->offsetExists($ns)) {
            $success = false;
            return null;
        }

        $data    = $cntr->offsetGet($ns);
        $success = array_key_exists($normalizedKey, $data);
        if (! $success) {
            return null;
        }

        $value    = $data[$normalizedKey];
        $casToken = $value;
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalGetItems(array $normalizedKeys): array
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if (! $cntr->offsetExists($ns)) {
            return [];
        }

        $data   = $cntr->offsetGet($ns);
        $result = [];
        foreach ($normalizedKeys as $normalizedKey) {
            if (array_key_exists($normalizedKey, $data)) {
                $result[$normalizedKey] = $data[$normalizedKey];
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalHasItem(string $normalizedKey): bool
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if (! $cntr->offsetExists($ns)) {
            return false;
        }

        $data = $cntr->offsetGet($ns);
        return array_key_exists($normalizedKey, $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function internalHasItems(array $normalizedKeys): array
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if (! $cntr->offsetExists($ns)) {
            return [];
        }

        $data   = $cntr->offsetGet($ns);
        $result = [];
        foreach ($normalizedKeys as $normalizedKey) {
            if (array_key_exists($normalizedKey, $data)) {
                $result[] = $normalizedKey;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalSetItem(string $normalizedKey, mixed $value): bool
    {
        $cntr                 = $this->getSessionContainer();
        $ns                   = $this->getOptions()->getNamespace();
        $data                 = $cntr->offsetExists($ns) ? $cntr->offsetGet($ns) : [];
        $data[$normalizedKey] = $value;
        $cntr->offsetSet($ns, $data);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalSetItems(array $normalizedKeyValuePairs): array
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if ($cntr->offsetExists($ns)) {
            $data = array_merge($cntr->offsetGet($ns), $normalizedKeyValuePairs);
        } else {
            $data = $normalizedKeyValuePairs;
        }
        $cntr->offsetSet($ns, $data);

        return [];
    }

    /**
     * {@inheritDoc}
     */
    protected function internalAddItem(string $normalizedKey, mixed $value): bool
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if ($cntr->offsetExists($ns)) {
            $data = $cntr->offsetGet($ns);

            if (array_key_exists($normalizedKey, $data)) {
                return false;
            }

            $data[$normalizedKey] = $value;
        } else {
            $data = [$normalizedKey => $value];
        }

        $cntr->offsetSet($ns, $data);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalAddItems(array $normalizedKeyValuePairs): array
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        $result = [];
        if ($cntr->offsetExists($ns)) {
            $data = $cntr->offsetGet($ns);

            foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
                if (array_key_exists($normalizedKey, $data)) {
                    $result[] = $normalizedKey;
                } else {
                    $data[$normalizedKey] = $value;
                }
            }
        } else {
            $data = $normalizedKeyValuePairs;
        }

        $cntr->offsetSet($ns, $data);
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalReplaceItem(string $normalizedKey, mixed $value): bool
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if (! $cntr->offsetExists($ns)) {
            return false;
        }

        $data = $cntr->offsetGet($ns);
        if (! array_key_exists($normalizedKey, $data)) {
            return false;
        }
        $data[$normalizedKey] = $value;
        $cntr->offsetSet($ns, $data);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalReplaceItems(array $normalizedKeyValuePairs): array
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();
        if (! $cntr->offsetExists($ns)) {
            return array_keys($normalizedKeyValuePairs);
        }

        $data   = $cntr->offsetGet($ns);
        $result = [];
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (! array_key_exists($normalizedKey, $data)) {
                $result[] = $normalizedKey;
            } else {
                $data[$normalizedKey] = $value;
            }
        }
        $cntr->offsetSet($ns, $data);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalRemoveItem(string $normalizedKey): bool
    {
        $cntr = $this->getSessionContainer();
        $ns   = $this->getOptions()->getNamespace();

        if (! $cntr->offsetExists($ns)) {
            return false;
        }

        $data = $cntr->offsetGet($ns);
        if (! array_key_exists($normalizedKey, $data)) {
            return false;
        }

        unset($data[$normalizedKey]);

        if (! $data) {
            $cntr->offsetUnset($ns);
        } else {
            $cntr->offsetSet($ns, $data);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalGetCapabilities(): Capabilities
    {
        return $this->capabilities ??= new Capabilities(
            maxKeyLength: Capabilities::UNLIMITED_KEY_LENGTH,
            ttlSupported: false,
            namespaceIsPrefix: false,
            supportedDataTypes: [
                'NULL'     => true,
                'boolean'  => true,
                'integer'  => true,
                'double'   => true,
                'string'   => true,
                'array'    => 'array',
                'object'   => 'object',
                'resource' => false,
            ],
        );
    }
}
