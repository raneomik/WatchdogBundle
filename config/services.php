<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Subscriber\WatchdogSubscriber;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(Watchdog::class)
        ->args([param('watchdog')])
        ->public()
    ;

    $services
        ->instanceof(WatchdogHandlerInterface::class)
        ->tag(WatchdogExtension::HANDLER_SERVICE_TAG)
    ;

    $services->set(WatchdogSubscriber::class)
        ->args([
            service(Watchdog::class),
            tagged_iterator(WatchdogExtension::HANDLER_SERVICE_TAG),
        ])
        ->tag('kernel.event_subscriber')
    ;
};
