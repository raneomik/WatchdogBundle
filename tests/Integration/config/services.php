<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker\LegacyChecker;
use Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker\LegacyFaker;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\DummyHandler;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\MultiwiredStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\SimplewiredStub;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->defaults()
        ->public()
        ->autoconfigure()
        ->autowire()
    ;

    $services
        ->set(DummyHandler::class)
        ->set(SimplewiredStub::class)
        ->set(MultiwiredStub::class)
        ->set(LegacyChecker::class, LegacyFaker::class)
    ;
};
