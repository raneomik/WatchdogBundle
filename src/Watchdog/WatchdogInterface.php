<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Watchdog;

interface WatchdogInterface
{
    public function isWoofTime(): bool;
}
