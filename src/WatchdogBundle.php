<?php

namespace Raneomik\WatchdogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WatchdogBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
