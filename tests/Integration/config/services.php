<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker\LegacyChecker;
use Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker\LegacyFaker;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\DummyHandler;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\MultiwiredStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\SimplewiredStub;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set(DummyHandler::class)
        ->public()
        ->autoconfigure()
        ->autowire()
    ;

    $services
        ->set(SimplewiredStub::class)
        ->public()
        ->autoconfigure()
        ->autowire()
    ;

    $services
        ->set(MultiwiredStub::class)
        ->public()
        ->autoconfigure()
        ->autowire()
    ;

    $services
        ->set(LegacyChecker::class, LegacyFaker::class)
        ->public()
        ->autoconfigure()
        ->autowire()
    ;
};
