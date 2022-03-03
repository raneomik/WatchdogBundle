<?php

namespace Raneomik\WatchdogBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\AutowiredStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\Kernel;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class WatchdogDependencyInjectionTest extends TestCase
{
    public function testLoadConfiguration(): void
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

        /** @var AutowiredStub $stub */
        $stub = $container->get('test.autowired');
        $this->assertInstanceOf(Watchdog::class, $watchdog = $stub->watchdog());
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

        $container->set('kernel', new Kernel('test', false));

        $container->registerExtension(new WatchdogExtension());

        foreach ($configs as $extension => $config) {
            $container->loadFromExtension($extension, $config);
        }

        return $container;
    }
}
