<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Tests\Integration\Stubs;

use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;

class MultiwiredStub
{
    private WatchdogInterface $testOne;

    private WatchdogInterface $testTwo;

    public function __construct(WatchdogInterface $testOne, WatchdogInterface $testTwo)
    {
        $this->testOne = $testOne;
        $this->testTwo = $testTwo;
    }

    public function testOne(): WatchdogInterface
    {
        return $this->testOne;
    }

    public function testTwo(): WatchdogInterface
    {
        return $this->testTwo;
    }
}
