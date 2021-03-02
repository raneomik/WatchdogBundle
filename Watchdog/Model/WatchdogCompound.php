<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

class WatchdogCompound extends AbstractWatchdogUnit
{
    private $logicalAndMode;
    private $unitCollection = [];

    public static function createFromCompoundConfig(array $data, bool $logicalAnd = false): self
    {
        $self = new static();
        $self->logicalAndMode = $logicalAnd;

        foreach ($data as $value) {
            $self->unitCollection[] = parent::create($value);
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
