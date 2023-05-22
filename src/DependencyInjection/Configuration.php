<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('watchdog');

        $treeBuilder->getRootNode()
            ->fixXmlConfig('watchdog')
            ->variablePrototype()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
