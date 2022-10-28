<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class Hour extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('H') === (new \DateTime())->format('H');
    }

    public function type(): string
    {
        return self::HOUR;
    }

    public function __toString(): string
    {
        return sprintf('Hour : %s', $this->dateConfig->format('H:i'));
    }
}
