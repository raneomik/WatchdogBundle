<?php

namespace Raneomik\WatchdogBundle\Tests\Integration;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\DummyHandler;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\Kernel as KernelStub;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class KernelTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().'/WatchdogBundle/');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::ensureKernelShutdown();
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
        self::bootKernel();
        /** @var array $config */
        $config = self::container()->getParameter('watchdog_config');
        /** @var Watchdog $watchdog */
        $watchdog = self::container()->get(Watchdog::class);

        $this->assertArrayHasKey('dates', $config);

        $this->assertArrayHasKey('relative', $config['dates'][0]);
        $this->assertArrayHasKey('compound', $config['dates'][1]);

        $this->assertTrue($watchdog->isWoofTime());
    }

    public function testLoadedEmptyConfig(): void
    {
        self::bootKernel(['config' => 'empty']);
        /** @var array $config */
        $config = self::container()->getParameter('watchdog_config');
        /** @var Watchdog $watchdog */
        $watchdog = self::container()->get(Watchdog::class);

        $this->assertEmpty($config['dates']);

        $this->assertFalse($watchdog->isWoofTime());
    }

    public function testDispatchedEvent(): void
    {
        self::bootKernel();
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::container()->get(EventDispatcherInterface::class);
        /** @var DummyHandler $testHandler */
        $testHandler = self::container()->get(DummyHandler::class);

        $this->assertEmpty($testHandler->handled);

        $dispatcher->dispatch(new WatchdogWoofCheckEvent(['handled' => true]));

        $this->assertArrayHasKey('handled', $testHandler->handled);
        $this->assertTrue($testHandler->handled['handled']);
    }

    private static function container(): ContainerInterface
    {
        /* @phpstan-ignore-next-line */
        if (Kernel::MAJOR_VERSION < 5) {
            return self::$container; /* @phpstan-ignore-line */
        }

        return self::getContainer();
    }
}
