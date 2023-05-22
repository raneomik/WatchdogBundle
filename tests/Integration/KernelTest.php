<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Tests\Integration;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\DummyHandler;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\Kernel as KernelStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\MultiwiredStub;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\SimplewiredStub;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class KernelTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir() . '/WatchdogBundle/');
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new KernelStub('test', true, $options['config'] ?? 'base');
    }

    public function testLoadedBaseConfig(): void
    {
        $this->assertCorrectBaseConfig();
    }

    public function testLoadedLegacyBaseConfig(): void
    {
        self::bootKernel([
            'config' => 'base_alt'
        ]);
        $this->assertCorrectBaseConfig();
    }

    private function assertCorrectBaseConfig(): void
    {
        /** @var array $config */
        $config = self::container()->getParameter('watchdog_config');
        /** @var WatchdogInterface $watchdog */
        $watchdog = self::container()->get(WatchdogInterface::class);

        /** @var SimplewiredStub $simpleWired */
        $simpleWired = self::container()->get(SimplewiredStub::class);

        $this->assertArrayHasKey('relative', $config['default'][0] ?? $config[0]);
        $this->assertArrayHasKey('compound', $config['default'][1] ?? $config[1]);

        $this->assertTrue($watchdog->isWoofTime());
        $this->assertSame($watchdog, $simpleWired->watchdog());
    }

    public function testLoadedMultiConfig(): void
    {
        self::bootKernel([
            'config' => 'multi'
        ]);
        /** @var MultiwiredStub $stub */
        $stub = self::container()->get(MultiwiredStub::class);

        $this->assertTrue($stub->testOne()->isWoofTime());
        $this->assertFalse($stub->testTwo()->isWoofTime());
    }

    public function testLoadedEmptyConfig(): void
    {
        self::bootKernel([
            'config' => 'empty'
        ]);
        /** @var array $config */
        $config = self::container()->getParameter('watchdog_config');
        /** @var WatchdogInterface $watchdog */
        $watchdog = self::container()->get(WatchdogInterface::class);

        $this->assertEmpty($config);

        $this->assertFalse($watchdog->isWoofTime());
    }

    public function testDispatchedSimpleEvent(): void
    {
        self::bootKernel([
            'config' => 'base'
        ]);

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::container()->get(EventDispatcherInterface::class);
        /** @var DummyHandler $testHandler */
        $testHandler = self::container()->get(DummyHandler::class);

        $this->assertEmpty($testHandler->handled);

        $dispatcher->dispatch(new WatchdogWoofCheckEvent([
            'handled' => true
        ]));

        $this->assertArrayHasKey('handled', $testHandler->handled);
        $this->assertTrue($testHandler->handled['handled']);
    }

    public function testDispatchedMultiEvents(): void
    {
        self::bootKernel([
            'config' => 'multi'
        ]);
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::container()->get(EventDispatcherInterface::class);
        /** @var DummyHandler $testHandler */
        $testHandler = self::container()->get(DummyHandler::class);

        $this->assertEmpty($testHandler->handled);

        $dispatcher->dispatch(new WatchdogWoofCheckEvent([
            'handledOne' => true
        ], 'test_one'));
        $this->assertArrayHasKey('handledOne', $testHandler->handled);
        $this->assertTrue($testHandler->handled['handledOne']);

        $dispatcher->dispatch(new WatchdogWoofCheckEvent([
            'handledTwo' => true
        ], 'test_two'));
        $this->assertArrayNotHasKey('handledTwo', $testHandler->handled);

        $dispatcher->dispatch(new WatchdogWoofCheckEvent([
            'handledOneMoreTime' => true
        ], 'test_one'));
        $this->assertArrayHasKey('handledOneMoreTime', $testHandler->handled);
        $this->assertTrue($testHandler->handled['handledOneMoreTime']);

        $dispatcher->dispatch(new WatchdogWoofCheckEvent([
            'handledAll' => true
        ]));
        $this->assertArrayHasKey('handledAll', $testHandler->handled);
        $this->assertTrue($testHandler->handled['handledAll']);
    }

    private static function container(): ContainerInterface
    {
        if (method_exists(KernelTestCase::class, 'getContainer')) {
            return self::getContainer();
        }

        if (false === self::$booted) {
            self::bootKernel();
        }

        /** @phpstan-ignore-next-line */
        return self::$container;
    }
}
