<?php

namespace Raneomik\WatchdogBundle\Test\Integration\Stubs;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class Bundle extends BaseBundle
{
    public function getContainerExtension(): BundleExtension
    {
        return new BundleExtension();
    }
}
