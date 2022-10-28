<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

use Raneomik\WatchdogBundle\Watchdog\Unit\Model\WatchdogUnitInterface;

class WatchdogUnitFactory
{
    public static function create(array $data): WatchdogUnitInterface
    {
        return (new UnitProcessor())->process($data);
    }
}
