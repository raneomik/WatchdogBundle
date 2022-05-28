<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;

class Interval implements WatchdogUnitInterface
{
    private \DateTimeInterface $start;
    private \DateTimeInterface $end;
    private string $originalConfig;
    private string $type;

    public function __construct(string $start, string $end)
    {
        $this->originalConfig = sprintf('start : %s / end : %s', $start, $end);

        $this->type = $this->determineType($start, $end);

        $this->start = new \DateTime($start);
        $this->end = new \DateTime($end);

        if ($this->start > $this->end) {
            throw new IllogicConfigurationException('start time cannot occur after end time');
        }

        if ($this->start->getTimestamp() === $this->end->getTimestamp()) {
            throw new IllogicConfigurationException('start and end times must differ');
        }
    }

    private function determineType(string $start, string $end): string
    {
        $isDateType = str_contains($start, '-') && str_contains($end, '-');
        $isTimeType = str_contains($start, ':') && str_contains($end, ':');
        $isDateTimeType = $isTimeType && $isDateType;

        if ($isDateTimeType) {
            return self::DATE_TIME;
        }

        if ($isDateType) {
            return self::DATE;
        }

        if ($isTimeType) {
            return self::TIME;
        }

        throw new IllogicConfigurationException('start and end times have same date, time or dateTime format');
    }

    public function isMatching(): bool
    {
        $now = (new \DateTime())->format('YmdHi');

        return $this->start->format('YmdHi') <= $now
            && $now <= $this->end->format('YmdHi')
        ;
    }

    public function type(): string
    {
        return 'interval';
    }

    public function __toString()
    {
        $format = 'Y-m-d H:i';

        if (self::TIME === $this->type) {
            $format = 'H:i';
        }

        if (self::DATE === $this->type) {
            $format = 'Y-m-d';
        }

        return sprintf('Interval : %s - %s', $this->start->format($format), $this->end->format($format));
    }

    public function originalConfig(): string
    {
        return $this->originalConfig;
    }
}
