<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class Time extends WatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Hi') === (new \DateTime())->format('Hi');
    }
}
