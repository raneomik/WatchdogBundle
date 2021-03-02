<?php

namespace Raneomik\WatchdogBundle\Event;

class WatchdogWoofCheckEvent
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
