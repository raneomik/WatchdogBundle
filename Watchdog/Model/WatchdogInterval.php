<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;

class WatchdogInterval extends AbstractWatchdogUnit
{
    private $start;
    private $end;

    public function __construct(string $start, string $end)
    {
        $this->start = new \DateTime($start);
        $this->end = new \DateTime($end);

        if ($this->start >= $this->end) {
            throw new IllogicConfigurationException('start time cannot occur after end time');
        }
    }

    public static function createFromIntervalConfig($start, $end): self
    {
        return new self($start, $end);
    }

    public function isMatching(): bool
    {
        return $this->start <= $this->currentDateTime() && $this->end >= $this->currentDateTime();
    }
}
