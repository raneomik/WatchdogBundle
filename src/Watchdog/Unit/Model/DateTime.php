<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class DateTime extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('YmdHi') === (new \DateTime())->format('YmdHi');
    }
}
