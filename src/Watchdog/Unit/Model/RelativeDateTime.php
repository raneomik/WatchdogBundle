<?php

declare(strict_types=1);

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

    public function __toString(): string
    {
        return sprintf('Relative : %s', $this->originalConfig);
    }
}
