<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class Compound implements WatchdogUnitInterface
{
    private bool $logicalAndMode = false;
    private array $unitCollection = [];

    public static function createFromCompoundConfig(array $data, bool $logicalAnd = false): self
    {
        $self = new self();
        $self->logicalAndMode = $logicalAnd;

        foreach ($data as $value) {
            $self->unitCollection[] = WatchdogUnit::create($value);
        }

        return $self;
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
