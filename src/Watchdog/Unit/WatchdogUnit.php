<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;
use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;
use Raneomik\WatchdogBundle\Exception\NotSupportedConfigurationException;

class WatchdogUnit implements WatchdogUnitInterface
{
    protected \DateTimeInterface $dateConfig;

    public function __construct(string $dateConfig)
    {
        $this->dateConfig = new \DateTime($dateConfig);
    }

    public static function create(array $data): ?WatchdogUnitInterface
    {
        $invalidConfigKey = '<not-supported>';
        $invalidConfigValue = '<invalid>';

        try {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case self::COMPOUND:
                        return Compound::createFromCompoundConfig($value, true);
                    case self::DATE_TIME:
                        return new DateTime($value);
                    case self::DATE:
                        return new Date($value);
                    case self::TIME:
                        return new Time($value);
                    case self::HOUR:
                        return (new Time($value))
                            ->matchHourOnly()
                        ;
                    case self::RELATIVE:
                        return new RelativeDateTime($value);
                    default:
                        $invalidConfigKey = $key;
                        $invalidConfigValue = $value;
                }
            }

            if (key_exists('start', $data) || key_exists('end', $data)) {
                if (false === key_exists('start', $data)) {
                    throw new MalformedConfigurationValueException('missing "start" data for interval');
                }

                if (false === key_exists('end', $data)) {
                    throw new MalformedConfigurationValueException('missing "end" data for interval');
                }

                return Interval::createFromIntervalConfig($data['start'], $data['end']);
            }
        } catch (IllogicConfigurationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new MalformedConfigurationValueException(sprintf('%s : %s', $invalidConfigKey, $invalidConfigValue));
        }

        throw new NotSupportedConfigurationException($invalidConfigKey);
    }

    public function isMatching(): bool
    {
        return false;
    }
}
