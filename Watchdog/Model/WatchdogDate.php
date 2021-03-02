<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

class WatchdogDate extends AbstractWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Ymd') === $this->currentDateTime()->format('Ymd');
    }
}
