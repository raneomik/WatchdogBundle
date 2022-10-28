<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

class DateTime extends AbstractSimpleWatchdogUnit
{
    public function isMatching(): bool
    {
        return $this->dateConfig->format('YmdHi') === (new \DateTime())->format('YmdHi');
    }

    public function type(): string
    {
        return self::DATE_TIME;
    }

    public function __toString(): string
    {
        return sprintf('DateTime : %s', $this->dateConfig->format('Y-m-d H:i'));
    }
}
