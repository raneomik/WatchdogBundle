<?php

namespace Raneomik\WatchdogBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\Kernel;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\MultiwiredStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\SimplewiredStub;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;

class WatchdogDependencyInjectionTest extends TestCase
{
    public function testLoadSimpleConfiguration(): void
    {
        $container = $this->createContainer([
            'watchdog' => [
                [
                    'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                    'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                ],
            ],
        ]);

        $container
            ->register('test.autowired', SimplewiredStub::class)
            ->setPublic(true)
            ->setAutowired(true);

        $container->compile();

        /** @var SimplewiredStub $stub */
        $stub = $container->get('test.autowired');
        $this->assertInstanceOf(WatchdogInterface::class, $watchdog = $stub->watchdog());

        $this->assertTrue($watchdog->isWoofTime());
    }

    public function testLoadMultiConfiguration(): void
    {
        $container = $this->createContainer([
            'watchdog' => [
                'test_one' => [
                    [
                        'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                        'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                    ],
                    'relative' => 'now',
                ],
                'test_two' => [
                    'relative' => 'tomorrow',
                ],
            ],
        ]);

        $container
            ->register('test.autowired', MultiwiredStub::class)
            ->setPublic(true)
            ->setAutowired(true);

        $container->compile();

        /** @var Definition $testOneDef */
        $testOneDef = $container->getDefinition('test.autowired')->getArgument(0);
        /** @var Definition $testTwoDef */
        $testTwoDef = $container->getDefinition('test.autowired')->getArgument(1);


        if (SymfonyKernel::MAJOR_VERSION >= 5) {
            $this->assertContains('test_one', $testOneDef->getTag(WatchdogExtension::SERVICE_TAG)[0]);
            $this->assertContains('test_two', $testTwoDef->getTag(WatchdogExtension::SERVICE_TAG)[0]);
        }

        /** @var MultiwiredStub $stub */
        $stub = $container->get('test.autowired');
        $this->assertInstanceOf(WatchdogInterface::class, $watchdogOne = $stub->testOne());
        $this->assertInstanceOf(WatchdogInterface::class, $watchdogTwo = $stub->testTwo());

        $this->assertTrue($watchdogOne->isWoofTime());
        $this->assertFalse($watchdogTwo->isWoofTime());
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
