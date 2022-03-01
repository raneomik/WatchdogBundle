<?php

namespace Raneomik\WatchdogBundle\Exception;

use Raneomik\WatchdogBundle\Watchdog\Unit\WatchdogUnitInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class NotSupportedConfigurationException extends InvalidConfigurationException
{
    public function __construct(string $unsupportedValue)
    {
        $message = sprintf('"%s" is not a supported config. Please, provide one of the following : %s',
            $unsupportedValue,
            implode(', ', WatchdogUnitInterface::UNIT_KEY_MAP)
        );

        parent::__construct($message);
    }
}
