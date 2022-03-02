<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class Date extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Ymd') === (new \DateTime())->format('Ymd');
    }
}
