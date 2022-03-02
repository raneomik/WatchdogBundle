<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;
use Raneomik\WatchdogBundle\Exception\NotSupportedConfigurationException;

class WatchdogUnit implements WatchdogUnitInterface
{
    protected \DateTimeInterface $dateConfig;

    public function __construct(string $stringDateConfig)
    {
        try {
            $this->dateConfig = new \DateTime($stringDateConfig);
        } catch (\Exception $e) {
            throw new MalformedConfigurationValueException(sprintf('DateTime string %s', $stringDateConfig));
        }
    }

    private static function processIntervalConfig(array $data): WatchdogUnitInterface
    {
        if (false === key_exists('start', $data)) {
            throw new MalformedConfigurationValueException('Missing "start" data for interval');
        }

        if (false === key_exists('end', $data)) {
            throw new MalformedConfigurationValueException('Missing "end" data for interval');
        }

        return new Interval($data['start'], $data['end']);
    }

    private static function processCommonConfig(array $data): WatchdogUnitInterface
    {
        $invalidConfigKey = '<not-supported>';
        $invalidConfigValue = '<invalid>';

        foreach ($data as $key => $value) {
            switch ($key) {
                case self::COMPOUND: return new Compound($value, true);
                case self::DATE_TIME: return new DateTime($value);
                case self::DATE: return new Date($value);
                case self::TIME: return new Time($value);
                case self::HOUR: return new Hour($value);
                case self::RELATIVE: return new RelativeDateTime($value);
                default:
                    $invalidConfigKey = $key;
                    $invalidConfigValue = $value;
            }
        }

        throw new NotSupportedConfigurationException(sprintf('%s : %s', $invalidConfigKey, $invalidConfigValue));
    }

    public static function create(array $data): WatchdogUnitInterface
    {
        if (key_exists('start', $data) || key_exists('end', $data)) {
            return self::processIntervalConfig($data);
        }

        return self::processCommonConfig($data);
    }

    public function isMatching(): bool
    {
        return false;
    }
}
