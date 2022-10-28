<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class Time extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('Hi') === (new \DateTime())->format('Hi');
    }

    public function type(): string
    {
        return self::TIME;
    }

    public function __toString(): string
    {
        return sprintf('Time : %s', $this->dateConfig->format('H:i'));
    }
}
