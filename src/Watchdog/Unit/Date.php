<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class Date extends WatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Ymd') === (new \DateTime())->format('Ymd');
    }
}
