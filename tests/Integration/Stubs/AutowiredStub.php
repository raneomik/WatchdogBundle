<?php

namespace Raneomik\WatchdogBundle\Test\Integration\Stubs;

use Raneomik\WatchdogBundle\Watchdog\Watchdog;

class AutowiredStub
{
    private $watchdog;

    public function __construct(Watchdog $watchdog)
    {
        $this->watchdog = $watchdog;
    }

    public function watchdog(): Watchdog
    {
        return $this->watchdog;
    }
}
