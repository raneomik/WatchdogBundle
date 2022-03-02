<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

use Raneomik\WatchdogBundle\Watchdog\Unit\WatchdogUnitFactory;

class Compound implements WatchdogUnitInterface
{
    private bool $logicalAndMode = false;
    private array $unitCollection = [];

    public function __construct(array $data, bool $logicalAnd = false)
    {
        $this->logicalAndMode = $logicalAnd;

        /** @var array $value */
        foreach ($data as $value) {
            $this->unitCollection[] = WatchdogUnitFactory::create($value);
        }
    }

    public function isMatching(): bool
    {
        $match = $this->logicalAndMode;

        /** @var WatchdogUnitInterface $unit */
        foreach ($this->unitCollection as $unit) {
            $match = $this->match($match, $unit);
        }

        return $match;
    }

    private function match(bool $match, WatchdogUnitInterface $unit): bool
    {
        if ($this->logicalAndMode) {
            return $match && $unit->isMatching();
        }

        return $match || $unit->isMatching();
    }
}