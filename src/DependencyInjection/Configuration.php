<?php

namespace Raneomik\WatchdogBundle\DependencyInjection;

use Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker\LegacyChecker;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const LEGACY_CONFIG_ERROR_MESSAGE = 'The attribute "whatever" must be set for path "watchdog"';

    private LegacyChecker $symfonyVersionChecker;

    public function __construct(LegacyChecker $symfonyVersionChecker)
    {
        $this->symfonyVersionChecker = $symfonyVersionChecker;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('watchdog');

        /**
         * @psalm-suppress ReservedWord
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UnusedMethodCall
         * @psalm-suppress PossiblyUndefinedMethod
         */
        $this->configureLegacyPartIfNeeded($treeBuilder->getRootNode())
            ->fixXmlConfig('watchdog')
                ->variablePrototype()->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function configureLegacyPartIfNeeded(ArrayNodeDefinition $definition): ArrayNodeDefinition
    {
        if ($this->symfonyVersionChecker->isLegacy()) {
            $definition
                ->useAttributeAsKey('whatever') // some legacy magic
                ->arrayPrototype()
            ;
        }

        return $definition;
    }
}
