<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class Compound implements WatchdogUnitInterface
{
    private bool $logicalAndMode = false;
    private array $unitCollection = [];

    public function __construct(array $data, bool $logicalAnd = false)
    {
        $this->logicalAndMode = $logicalAnd;

        foreach ($data as $value) {
            $this->unitCollection[] = WatchdogUnit::create($value);
        }
    }

    public function isMatching(): bool
    {
        foreach ($this->unitCollection as $unit) {
            if (true === $this->logicalAndMode) {
                if (false === $unit->isMatching()) {
                    return false;
                }
            } else {
                if (true === $unit->isMatching()) {
                    return true;
                }
            }
        }

        return $this->logicalAndMode;
    }
}
