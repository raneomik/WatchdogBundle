<?php

namespace Raneomik\WatchdogBundle\Test\Integration\Stubs;

use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;

class StubHandler implements WatchdogHandlerInterface
{
    public array $handled = [];

    public function processWoof(array $parameters = [])
    {
        $this->handled = $parameters;
    }
}
