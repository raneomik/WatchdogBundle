<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

class Time extends WatchdogUnit
{
    private bool $matchHourOnly = false;

    public function matchHourOnly(): Time
    {
        $this->matchHourOnly = true;

        return $this;
    }

    public function isMatching(): bool
    {
        if (true === $this->matchHourOnly) {
            return $this->dateConfig->format('H') === (new \DateTime())->format('H');
        }

        return $this->dateConfig->format('Hi') === (new \DateTime())->format('Hi');
    }
}
