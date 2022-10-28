<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Tests\Integration\Stubs;

use Psr\Log\NullLogger;
use Raneomik\WatchdogBundle\WatchdogBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    /* @phpstan-ignore-next-line */
    public const IS_LEGACY = 5 > BaseKernel::MAJOR_VERSION;

    private string $config;

    public function __construct(string $environment, bool $debug, string $config = 'base')
    {
        parent::__construct($environment, $debug);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        return [new FrameworkBundle(), new WatchdogBundle(), new Bundle(), ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/WatchdogBundle/cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/WatchdogBundle/logs';
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->register('logger', NullLogger::class);
    }

    /**
     * Loads the container configuration.
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(sprintf(__DIR__ . '/../config/%s_config.yml', $this->config));
    }
}
