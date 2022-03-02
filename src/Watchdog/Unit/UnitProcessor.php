<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;
use Raneomik\WatchdogBundle\Exception\NotSupportedConfigurationException;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Compound;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Date;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\DateTime;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Hour;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Interval;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\RelativeDateTime;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Time;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\WatchdogUnitInterface;

class UnitProcessor
{
    public function process(array $data): WatchdogUnitInterface
    {
        if (\array_key_exists('start', $data) || \array_key_exists('end', $data)) {
            return $this->intervalUnit($data);
        }

        return $this->watchdogUnit($data);
    }

    private static function compoundUnit(array $data): Compound
    {
        return new Compound($data, true);
    }

    private static function intervalUnit(array $data): Interval
    {
        if (false === \is_string($start = $data['start'] ?? null)) {
            throw new MalformedConfigurationValueException('Missing "start" data for interval');
        }

        if (false === \is_string($end = $data['end'] ?? null)) {
            throw new MalformedConfigurationValueException('Missing "end" data for interval');
        }

        return new Interval($start, $end);
    }

    private static function commonUnit(string $key, string $value): ?WatchdogUnitInterface
    {
        if (WatchdogUnitInterface::RELATIVE === $key) {
            return new RelativeDateTime($value);
        }

        if (WatchdogUnitInterface::DATE_TIME === $key) {
            return new DateTime($value);
        }

        if (WatchdogUnitInterface::DATE === $key) {
            return new Date($value);
        }

        if (WatchdogUnitInterface::TIME === $key) {
            return new Time($value);
        }

        if (WatchdogUnitInterface::HOUR === $key) {
            return new Hour($value);
        }

        return null;
    }

    private function watchdogUnit(array $data): WatchdogUnitInterface
    {
        $invalidConfigKey = '<not-supported>';
        $invalidConfigValue = '<invalid>';

        /**
         * @var string       $key
         * @var string|array $value
         */
        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                return $this->compoundUnit($value);
            }

            if (null !== $unit = $this->commonUnit($key, $value)) {
                return $unit;
            }

            $invalidConfigKey = $key;
            $invalidConfigValue = $value;
        }

        throw new NotSupportedConfigurationException(sprintf('%s : %s', $invalidConfigKey, $invalidConfigValue));
    }
}
