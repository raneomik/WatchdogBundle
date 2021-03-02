<?php

namespace Raneomik\WatchdogBundle\Test\Integration\Stubs;

use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

class BundleExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $phpLoader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../config'));
        $phpLoader->load('services.php');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // Annotation must be disabled since this bundle doesn't use Doctrine
        // The framework allows enabling/disabling them only since symfony 3.2 where
        // doctrine/annotations has been removed from required dependencies
        $annotationsEnabled = (int) Kernel::MAJOR_VERSION >= 3 && (int) Kernel::MINOR_VERSION >= 2;
        if (false === $annotationsEnabled) {
            return;
        }

        $container->prependExtensionConfig('framework', ['annotations' => ['enabled' => false]]);
    }
}
