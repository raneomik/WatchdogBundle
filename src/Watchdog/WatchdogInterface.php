<?php

namespace Raneomik\WatchdogBundle\Watchdog;

interface WatchdogInterface
{
    public function isWoofTime(): bool;
}
