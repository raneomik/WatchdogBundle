<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Handler;

interface WatchdogHandlerInterface
{
    public function processWoof(array $parameters = []): void;
}
