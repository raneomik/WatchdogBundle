<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;
use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;
use Raneomik\WatchdogBundle\Exception\NotSupportedConfigurationException;

abstract class AbstractWatchdogUnit implements WatchdogUnitInterface
{
    protected \DateTimeInterface $dateConfig;

    public static function createFromConfig(string $dateConfig): self
    {
        $self = new static();

        $self->dateConfig = new \DateTime($dateConfig);

        return $self;
    }

    public static function create(array $data): ?self
    {
        $invalidConfigKey = null;
        $key = null;
        $value = null;

        try {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'compound':
                        return CompoundUnit::createFromCompoundConfig($value, true);
                    case 'date_time':
                        return DateTimeUnit::createFromConfig($value);
                    case 'date':
                        return DateUnit::createFromConfig($value);
                    case 'time':
                        return TimeUnit::createFromConfig($value);
                    case 'hour':
                        return TimeUnit::createFromConfig($value)
                            ->setMatchHourOnly(true)
                        ;
                    case 'relative':
                        return RelativeDateTimeUnit::createFromConfig($value);
                    default:
                        $invalidConfigKey = $key;
                }
            }

            if (key_exists('start', $data) || key_exists('end', $data)) {
                if (false === key_exists('start', $data)) {
                    throw new MalformedConfigurationValueException('missing "start" data for interval');
                }

                if (false === key_exists('end', $data)) {
                    throw new MalformedConfigurationValueException('missing "end" data for interval');
                }

                return IntervalUnit::createFromIntervalConfig($data['start'], $data['end']);
            }
        } catch (IllogicConfigurationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new MalformedConfigurationValueException(sprintf('%s : %s', $key, $value));
        }

        throw new NotSupportedConfigurationException($invalidConfigKey);
    }

    protected function currentDateTime(): \DateTime
    {
        return new \DateTime();
    }
}
