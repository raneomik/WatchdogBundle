<?php

namespace Raneomik\WatchdogBundle\DependencyInjection;

use Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker\LegacyChecker;
use Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker\LegacyCheckerFactory;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WatchdogExtension extends Extension
{
    public const SERVICE_TAG = 'raneomik_watchdog';
    public const HANDLER_SERVICE_TAG = 'raneomik_watchdog.handler';
    public const DATA_COLLECTOR_SERVICE_TAG = 'raneomik_watchdog.data_collector';

    private LegacyChecker $symfonyVersionChecker;

    public function __construct(bool $forceLegacyState = false)
    {
        $this->symfonyVersionChecker = LegacyCheckerFactory::create($forceLegacyState);
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        try {
            $configuration = $this->getConfiguration($configs, $container);
            /** @var array<string,array> $config */
            $config = $this->processConfiguration($configuration, $configs);

            $container->setParameter('watchdog_config', $config);

            $container->registerForAutoconfiguration(WatchdogHandlerInterface::class)
                ->addTag(self::HANDLER_SERVICE_TAG)
            ;

            $this->registerWatchdogConfiguration($config, $container);
        } catch (InvalidConfigurationException $exception) {
            if (str_contains($exception->getMessage(), Configuration::LEGACY_CONFIG_ERROR_MESSAGE)) {
                throw new InvalidConfigurationException('Your watchdog configuration needs to be set under "default" (or other) key');
            }
        }
    }

    private function registerWatchdogConfiguration(array $config, ContainerBuilder $container): void
    {
        if ($this->symfonyVersionChecker->isLegacy()) {
            $this->registerLegacyServiceDefinitions($container);
        } else {
            $this->registerServiceDefinitions($container);
        }

        if (\is_int(array_key_first($config))) {
            return;
        }

        /** @var array<string,array> $config */
        foreach ($config as $name => $scopeConfig) {
            $container
                ->register($name, Watchdog::class)
                ->addArgument($scopeConfig)
                ->addTag(self::SERVICE_TAG, ['id' => $name])
            ;

            $container->registerAliasForArgument($name, WatchdogInterface::class);
        }
    }

    private function registerLegacyServiceDefinitions(ContainerBuilder $container): void
    {
        /** @psalm-suppress UndefinedClass */
        (new XmlFileLoader(
            $container,
            new FileLocator(\dirname(__DIR__).'/../config')
        ))->load('watchdog.xml');
    }

    private function registerServiceDefinitions(ContainerBuilder $container): void
    {
        /** @psalm-suppress ReservedWord, ParseError */
        (new PhpFileLoader(
            $container,
            new FileLocator(\dirname(__DIR__).'/../config')
        ))->load('watchdog.php');
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration($this->symfonyVersionChecker);
    }
}
