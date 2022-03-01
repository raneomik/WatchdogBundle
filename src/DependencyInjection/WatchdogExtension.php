<?php

namespace Raneomik\WatchdogBundle\DependencyInjection;

use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Subscriber\WatchdogEventSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WatchdogExtension extends Extension
{
    public const HANDLER_SERVICE_TAG = 'raneomik_watchdog.handler';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('watchdog_config', $config);

        $phpLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $phpLoader->load('services.yml');

        $container->registerForAutoconfiguration(WatchdogHandlerInterface::class)
            ->addTag(self::HANDLER_SERVICE_TAG)
        ;

        $container->registerForAutoconfiguration(WatchdogEventSubscriber::class)
            ->addTag('kernel.event_subscriber')
        ;
    }
}
