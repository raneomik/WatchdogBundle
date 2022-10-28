<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Event;

class WatchdogWoofCheckEvent
{
    private array $parameters;

    private ?string $watchdogId;

    public function __construct(array $parameters = [], ?string $watchdogId = null)
    {
        $this->parameters = $parameters;
        $this->watchdogId = $watchdogId;
    }

    public function eventParams(): array
    {
        return $this->parameters;
    }

    public function concernedWatchdogId(): ?string
    {
        return $this->watchdogId;
    }
}
