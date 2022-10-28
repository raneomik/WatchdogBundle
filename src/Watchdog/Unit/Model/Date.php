<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class Date extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Ymd') === (new \DateTime())->format('Ymd');
    }

    public function type(): string
    {
        return self::DATE;
    }

    public function __toString(): string
    {
        return sprintf('Date : %s', $this->dateConfig->format('Y-m-d'));
    }
}
