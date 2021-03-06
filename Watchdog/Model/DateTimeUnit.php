<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

class DateTimeUnit extends AbstractWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('YmdHi') === $this->currentDateTime()->format('YmdHi');
    }
}
