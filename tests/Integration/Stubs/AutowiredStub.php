<?php

namespace Raneomik\WatchdogBundle\Tests\Integration\Stubs;

use Raneomik\WatchdogBundle\Watchdog\Watchdog;

class AutowiredStub
{
    private Watchdog $watchdog;

    public function __construct(Watchdog $watchdog)
    {
        $this->watchdog = $watchdog;
    }

    public function watchdog(): Watchdog
    {
        return $this->watchdog;
    }
}
