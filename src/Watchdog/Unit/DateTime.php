<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class DateTime extends WatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('YmdHi') === (new \DateTime())->format('YmdHi');
    }
}
