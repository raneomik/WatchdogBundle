<?php

namespace Raneomik\WatchdogBundle\Watchdog;

use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Compound;

final class Watchdog implements WatchdogInterface
{
    private Compound $dateCollectionToWatch;

    public function __construct(array $watchdogParameters = [])
    {
        $this->dateCollectionToWatch = new Compound($watchdogParameters);
    }

    public function isWoofTime(): bool
    {
        return $this->dateCollectionToWatch->isMatching();
    }
}
