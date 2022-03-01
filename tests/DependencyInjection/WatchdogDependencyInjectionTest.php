<?php

namespace Raneomik\WatchdogBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\AutowiredStub;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Kernel;

class WatchdogDependencyInjectionTest extends TestCase
{
    public function testLoadConfiguration()
    {
        $container = $this->createContainer([
            'watchdog' => [
                'dates' => [
                    [
                        'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                        'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                    ],
                ],
            ],
        ]);

        $container
            ->register('test.autowired', AutowiredStub::class)
            ->setPublic(true)
            ->setAutowired(true);

        $container->compile();

        $this->assertInstanceOf(Watchdog::class, $watchdog = $container->get('test.autowired')->watchdog());
        $this->assertTrue($watchdog->isWoofTime());
    }

    protected static function createContainer(array $configs = []): ContainerBuilder
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.build_dir' => __DIR__,
            'kernel.cache_dir' => __DIR__,
            'kernel.root_dir' => __DIR__,
            'kernel.project_dir' => __DIR__,
            'kernel.charset' => 'UTF-8',
            'kernel.environment' => 'test',
            'kernel.runtime_environment' => 'test',
            'kernel.debug' => false,
            'kernel.container_class' => 'AutowiringTestContainer',
        ]));

        $container->set('kernel', new class('test', false) extends Kernel {
            public function registerBundles(): iterable
            {
                return [];
            }

            public function registerContainerConfiguration(LoaderInterface $loader)
            {
            }
        }
        );

        $container->registerExtension(new WatchdogExtension());

        foreach ($configs as $extension => $config) {
            $container->loadFromExtension($extension, $config);
        }

        return $container;
    }
}
