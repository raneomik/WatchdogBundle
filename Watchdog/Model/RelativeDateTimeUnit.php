<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

class RelativeDateTimeUnit extends AbstractWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Ymd') === $this->currentDateTime()->format('Ymd');
    }
}
