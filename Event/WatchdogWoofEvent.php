<?php

namespace Raneomik\WatchdogBundle\Event;

class WatchdogWoofEvent
{
    private $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function eventParams(): array
    {
        return $this->parameters;
    }
}
