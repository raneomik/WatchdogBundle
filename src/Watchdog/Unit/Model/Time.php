<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class Time extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Hi') === (new \DateTime())->format('Hi');
    }
}
