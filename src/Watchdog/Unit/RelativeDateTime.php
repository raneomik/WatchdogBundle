<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class RelativeDateTime extends WatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Ymd') === (new \DateTime())->format('Ymd');
    }
}
