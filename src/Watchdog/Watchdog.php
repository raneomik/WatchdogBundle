<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Watchdog;

use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Compound;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\WatchdogUnitInterface;

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

    public function units(): array
    {
        return $this->dateCollectionToWatch->units();
    }

    public function hasUnits(): bool
    {
        return false === empty($this->units());
    }

    public function matchingUnits(): array
    {
        return array_filter(
            $this->dateCollectionToWatch->units(),
            fn (WatchdogUnitInterface $unit) => $unit->isMatching()
        );
    }

    public function hasMatches(): bool
    {
        return false === empty($this->matchingUnits());
    }
}
