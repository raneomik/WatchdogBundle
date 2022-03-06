<?php

namespace Raneomik\WatchdogBundle\Tests\Integration\Stubs;

use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;

class SimplewiredStub
{
    private WatchdogInterface $watchdog;

    public function __construct(
        WatchdogInterface $watchdog
    ) {
        $this->watchdog = $watchdog;
    }

    public function watchdog(): WatchdogInterface
    {
        return $this->watchdog;
    }
}
