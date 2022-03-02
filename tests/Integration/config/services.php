<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Raneomik\WatchdogBundle\Tests\Integration\Stubs\DummyHandler;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set(DummyHandler::class)
        ->autoconfigure()
    ;
};
