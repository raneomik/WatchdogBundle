<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;

abstract class AbstractSimpleWatchdogUnit implements WatchdogUnitInterface
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
}
