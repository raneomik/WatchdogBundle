<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class Hour extends WatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('H') === (new \DateTime())->format('H');
    }
}
