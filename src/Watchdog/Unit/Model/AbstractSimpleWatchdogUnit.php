<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;

abstract class AbstractSimpleWatchdogUnit implements WatchdogUnitInterface
{
    protected string $originalConfig;

    protected \DateTimeInterface $dateConfig;

    public function __construct(string $stringDateConfig)
    {
        $this->originalConfig = $stringDateConfig;

        try {
            $this->dateConfig = new \DateTime($stringDateConfig);
        } catch (\Exception $e) {
            throw new MalformedConfigurationValueException(sprintf('DateTime string %s', $stringDateConfig));
        }
    }

    public function originalConfig(): string
    {
        return $this->originalConfig;
    }
}
