<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;

class Interval implements WatchdogUnitInterface
{
    private \DateTimeInterface $start;
    private \DateTimeInterface $end;

    public function __construct(string $start, string $end)
    {
        $this->start = new \DateTime($start);
        $this->end = new \DateTime($end);

        if ($this->start >= $this->end) {
            throw new IllogicConfigurationException('start time cannot occur after end time');
        }
    }

    public static function createFromIntervalConfig(string $start, string $end): self
    {
        return new self($start, $end);
    }

    public function isMatching(): bool
    {
        return $this->start <= ($now = new \DateTime()) && $this->end >= $now;
    }
}
