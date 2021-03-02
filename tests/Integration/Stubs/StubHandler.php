<?php

namespace Raneomik\WatchdogBundle\Test\Integration\Stubs;

use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;

class StubHandler implements WatchdogHandlerInterface
{
    public $handled = [];

    public function processWoof(array $parameters = [])
    {
        $this->handled = $parameters;
    }
}
