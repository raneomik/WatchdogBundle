<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\Kernel;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\Kernel as KernelStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\MultiwiredStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\SimplewiredStub;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Raneomik\WatchdogBundle\WatchdogBundle;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class WatchdogDependencyInjectionTest extends TestCase
{
    public function legacyCasesProvider(): \Generator
    {
        yield [
            'fakeLegacy' => true
        ];
        yield [
            'fakeLegacy' => false
        ];
    }

    /**
     * @dataProvider legacyCasesProvider
     */
    public function testCompiledPassForProfiler(bool $fakeLegacy): void
    {
        $container = $this->createContainer($fakeLegacy);

        $bundle = new WatchdogBundle();
        $bundle->build($container);
        $container
            ->register(Watchdog::class)
            ->addArgument([
                [
                    'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                    'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                ],
                [
                    'relative' => 'now'
                ],
            ])
            ->setPublic(true)
            ->setAutowired(true)
            ->addTag(WatchdogExtension::SERVICE_TAG);
        $container
            ->register('other', Watchdog::class)
            ->addArgument([
                [
                    'relative' => 'now'
                ],
            ])
            ->setPublic(true)
            ->setAutowired(true)
            ->addTag(WatchdogExtension::SERVICE_TAG);
        $container->register('profiler', NullLogger::class);
        $container->compile();

        $this->assertFalse($container->hasDefinition(Watchdog::class));
    }

    /**
     * @dataProvider legacyCasesProvider
     */
    public function testLoadNewSimpleConfiguration(bool $fakeLegacy): void
    {
        $container = $this->createContainer($fakeLegacy, [
            'watchdog' => [
                [
                    'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                    'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                ],
            ],
        ]);

        /* @phpstan-ignore-next-line */
        if ($fakeLegacy || Kernel::IS_LEGACY) {
            $this->expectException(InvalidConfigurationException::class);
            $this->expectExceptionMessage('Your watchdog configuration needs to be set under "default"');
        }

        $this->assertCorrectBaseConfig($container);
    }

    /**
     * @dataProvider legacyCasesProvider
     */
    public function testLoadSimpleConfiguration(bool $isLegacy): void
    {
        $container = $this->createContainer($isLegacy, [
            'watchdog' => [
                'default' => [
                    [
                        'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                        'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                    ],
                ],
            ],
        ]);

        $this->assertCorrectBaseConfig($container);
    }

    /**
     * @dataProvider legacyCasesProvider
     */
    public function testLoadRedundantSimpleConfiguration(bool $isLegacy): void
    {
        $container = $this->createContainer($isLegacy, [
            'watchdog' => [
                'watchdog' => [
                    [
                        'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                        'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                    ],
                ],
            ],
        ]);

        $this->assertCorrectBaseConfig($container);
    }

    private function assertCorrectBaseConfig(ContainerBuilder $container): void
    {
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

    /**
     * @dataProvider legacyCasesProvider
     */
    public function testLoadMultiConfiguration(bool $fakeLegacy): void
    {
        $container = $this->createContainer($fakeLegacy, [
            'watchdog' => [
                'test_one' => [
                    [
                        'start' => (new \DateTime('-5mins'))->format('Y-m-d H:i'),
                        'end' => (new \DateTime('+5mins'))->format('Y-m-d H:i'),
                    ],
                    [
                        'relative' => 'now'
                    ],
                ],
                'test_two' => [
                    [
                        'relative' => 'tomorrow'
                    ],
                ],
            ],
        ]);

        $container
            ->register('test.autowired', MultiwiredStub::class)
            ->setPublic(true)
            ->setAutowired(true);

        $container->compile();

        /** @var Definition $testOneDef */
        $testOneDef = $container->getDefinition('test.autowired')
            ->getArgument(0);
        /** @var Definition $testTwoDef */
        $testTwoDef = $container->getDefinition('test.autowired')
            ->getArgument(1);

        if (false === KernelStub::IS_LEGACY) {
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

    private static function createContainer(bool $fakeLegacy, array $configs = []): ContainerBuilder
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

        $container->registerExtension(new WatchdogExtension($fakeLegacy));

        foreach ($configs as $extension => $config) {
            $container->loadFromExtension($extension, $config);
        }

        return $container;
    }
}
