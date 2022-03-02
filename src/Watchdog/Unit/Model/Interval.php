<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;

class Interval implements WatchdogUnitInterface
{
    private \DateTimeInterface $start;
    private \DateTimeInterface $end;

    public function __construct(string $start, string $end)
    {
        $this->start = new \DateTime($start);
        $this->end = new \DateTime($end);

        if ($this->start > $this->end) {
            throw new IllogicConfigurationException('start time cannot occur after end time');
        }

        if ($this->start->getTimestamp() === $this->end->getTimestamp()) {
            throw new IllogicConfigurationException('start and end times must differ');
        }
    }

    public function isMatching(): bool
    {
        $now = (new \DateTime())->format('YmdHi');

        return $this->start->format('YmdHi') <= $now
            && $now <= $this->end->format('YmdHi')
        ;
    }
}
