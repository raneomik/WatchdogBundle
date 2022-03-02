<?php

namespace Raneomik\WatchdogBundle\Tests\Integration\Stubs;

use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;

class DummyHandler implements WatchdogHandlerInterface
{
    public array $handled = [];

    public function processWoof(array $parameters = []): void
    {
        $this->handled = $parameters;
    }
}
