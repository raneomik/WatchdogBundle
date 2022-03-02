<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class Hour extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('H') === (new \DateTime())->format('H');
    }
}
