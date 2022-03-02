<?php

namespace Raneomik\WatchdogBundle\Watchdog;

use Raneomik\WatchdogBundle\Watchdog\Unit\Compound;

class Watchdog
{
    private Compound $dateCollectionToWatch;

    public function __construct(array $watchdogParameters)
    {
        $this->dateCollectionToWatch = new Compound(
            $watchdogParameters['dates'] ?? []
        );
    }

    public function isWoofTime(): bool
    {
        return $this->dateCollectionToWatch->isMatching();
    }
}
