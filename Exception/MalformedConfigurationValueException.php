<?php

namespace Raneomik\WatchdogBundle\Exception;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class MalformedConfigurationValueException extends InvalidConfigurationException
{
    public function __construct(string $malformed)
    {
        $message = sprintf('"%s" is malformed config. Please, see documentation for correct configuration.',
            $malformed
        );

        parent::__construct($message);
    }
}
