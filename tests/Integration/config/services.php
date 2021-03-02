<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Raneomik\WatchdogBundle\Test\Integration\Stubs\StubHandler;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set(StubHandler::class)
        ->autoconfigure()
    ;
};
