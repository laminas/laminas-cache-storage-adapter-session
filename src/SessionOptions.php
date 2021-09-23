<?php

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Session\Container as SessionContainer;

/**
 * These are options specific to the APC adapter
 */
final class SessionOptions extends AdapterOptions
{
    /**
     * The session container
     *
     * @var null|SessionContainer
     */
    protected $sessionContainer;

    /**
     * Set the session container
     *
     * @return SessionOptions Provides a fluent interface
     */
    public function setSessionContainer(?SessionContainer $sessionContainer = null): self
    {
        if ($this->sessionContainer !== $sessionContainer) {
            $this->triggerOptionEvent('session_container', $sessionContainer);
            $this->sessionContainer = $sessionContainer;
        }

        return $this;
    }

    /**
     * Get the session container
     */
    public function getSessionContainer(): ?SessionContainer
    {
        return $this->sessionContainer;
    }
}
