<?php

namespace Raneomik\WatchdogBundle\DependencyInjection\Compiler;

use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WatchdogCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $watchdogs = $container->findTaggedServiceIds(WatchdogExtension::SERVICE_TAG);

        if (1 === \count($watchdogs)) {
            return;
        }

        if (false === $container->hasDefinition('profiler')) {
            return;
        }

        $container->removeDefinition(Watchdog::class);
    }
}
