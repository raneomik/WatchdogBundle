<?php

namespace Raneomik\WatchdogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('watchdog');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('dates')
                    ->variablePrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
