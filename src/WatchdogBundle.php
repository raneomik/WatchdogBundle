<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle;

use Raneomik\WatchdogBundle\DependencyInjection\Compiler\WatchdogCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class WatchdogBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WatchdogCompilerPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
