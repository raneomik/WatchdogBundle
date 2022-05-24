<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class RelativeDateTime extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Ymd') === (new \DateTime())->format('Ymd');
    }

    public function type(): string
    {
        return self::RELATIVE;
    }

    public function __toString()
    {
        return sprintf('Relative : %s', $this->dateConfig->format('Y-m-d'));
    }
}
