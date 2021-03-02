<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

class TimeUnit extends AbstractWatchdogUnit
{
    private $matchHourOnly = false;

    public function setMatchHourOnly(bool $matchHourOnly): self
    {
        $this->matchHourOnly = $matchHourOnly;

        return $this;
    }

    public function isMatching(): bool
    {
        if (true === $this->matchHourOnly) {
            return $this->dateConfig->format('H') === $this->currentDateTime()->format('H');
        }

        return $this->dateConfig->format('Hi') === $this->currentDateTime()->format('Hi');
    }
}
