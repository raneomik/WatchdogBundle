<?php

namespace Raneomik\WatchdogBundle\Handler;

interface WatchdogHandlerInterface
{
    public function processWoof(array $parameters = []): void;
}
