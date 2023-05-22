<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\DependencyInjection;

use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WatchdogExtension extends Extension
{
    public const SERVICE_TAG = 'raneomik_watchdog';

    public const HANDLER_SERVICE_TAG = 'raneomik_watchdog.handler';

    public const DATA_COLLECTOR_SERVICE_TAG = 'raneomik_watchdog.data_collector';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        /** @var array<string,array> $config */
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('watchdog_config', $config);

        $container->registerForAutoconfiguration(WatchdogHandlerInterface::class)
            ->addTag(self::HANDLER_SERVICE_TAG);

        $this->registerWatchdogConfiguration($config, $container);
    }

    private function registerWatchdogConfiguration(array $config, ContainerBuilder $container): void
    {
        (new PhpFileLoader($container, new FileLocator(\dirname(__DIR__) . '/../config')))
            ->load('watchdog.php');

        if (\is_int(array_key_first($config))) {
            return;
        }

        /** @var array<string,array> $config */
        foreach ($config as $name => $scopeConfig) {
            $container
                ->register($name, Watchdog::class)
                ->addArgument($scopeConfig)
                ->addTag(self::SERVICE_TAG, [
                    'id' => $name
                ]);

            $container->registerAliasForArgument($name, WatchdogInterface::class);
        }
    }
}
