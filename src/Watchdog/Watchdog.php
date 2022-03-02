<?php

namespace Raneomik\WatchdogBundle\Watchdog;

use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Compound;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Watchdog
{
    private Compound $dateCollectionToWatch;

    public function __construct(array $watchdogParameters = [])
    {
        if (false === \is_array($config = $watchdogParameters['dates'] ?? null)) {
            throw new InvalidConfigurationException('Expected at least an empty array at "dates" key');
        }

        $this->dateCollectionToWatch = new Compound($config);
    }

    public function isWoofTime(): bool
    {
        return $this->dateCollectionToWatch->isMatching();
    }
}
