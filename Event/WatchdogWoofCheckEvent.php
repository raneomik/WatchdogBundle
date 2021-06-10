<?php

namespace Raneomik\WatchdogBundle\Event;

class WatchdogWoofCheckEvent
{
    private array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function eventParams(): array
    {
        return $this->parameters;
    }
}
