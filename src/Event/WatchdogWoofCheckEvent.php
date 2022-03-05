<?php

namespace Raneomik\WatchdogBundle\Event;

class WatchdogWoofCheckEvent
{
    private array $parameters;
    private ?string $watchdog;

    public function __construct(array $parameters = [], ?string $watchdog = null)
    {
        $this->parameters = $parameters;
        $this->watchdog = $watchdog;
    }

    public function eventParams(): array
    {
        return $this->parameters;
    }

    public function concernedWatchdog(): ?string
    {
        return $this->watchdog;
    }
}
