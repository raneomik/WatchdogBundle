<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Raneomik\WatchdogBundle\DataCollector\WatchdogDataCollector;
use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Subscriber\WatchdogEventSubscriber;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(Watchdog::class)
            ->public()
            ->arg('$watchdogParameters', '%watchdog_config%')
        ->tag(WatchdogExtension::SERVICE_TAG, ['id' => 'default'])

        ->alias(WatchdogInterface::class, Watchdog::class)
    ;

    $container->services()
        ->instanceof(WatchdogHandlerInterface::class)
        ->tag(WatchdogExtension::HANDLER_SERVICE_TAG)
    ;

    $container->services()
        ->set(WatchdogEventSubscriber::class)
            ->arg('$watchdogCollection', tagged_iterator(WatchdogExtension::SERVICE_TAG, 'id'))
            ->arg('$watchdogHandlers', tagged_iterator(WatchdogExtension::HANDLER_SERVICE_TAG))
        ->tag('kernel.event_subscriber')
    ;

    $container->services()
        ->set(WatchdogExtension::DATA_COLLECTOR_SERVICE_TAG, WatchdogDataCollector::class)
            ->arg('$watchdogCollection', tagged_iterator(WatchdogExtension::SERVICE_TAG, 'id'))
            ->arg('$watchdogHandlerCollection', tagged_iterator(WatchdogExtension::HANDLER_SERVICE_TAG))
        ->tag('data_collector', [
            'id' => WatchdogExtension::SERVICE_TAG,
            'template' => '@Watchdog/Collector/template.html.twig',
        ])
    ;
};
