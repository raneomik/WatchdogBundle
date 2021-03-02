<?php

namespace Raneomik\WatchdogBundle\Watchdog;

use Raneomik\WatchdogBundle\Watchdog\Model\WatchdogCompound;

class Watchdog
{
    private $dateCollectionToWatch;

    public function __construct(array $watchdogParameters)
    {
        $this->dateCollectionToWatch = WatchdogCompound::createFromCompoundConfig(
            $watchdogParameters['dates'] ?? []
        );
    }

    public function isWoofTime(): bool
    {
        return $this->dateCollectionToWatch->isMatching();
    }
}
