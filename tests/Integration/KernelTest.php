<?php

namespace Raneomik\WatchdogBundle\Tests\Integration;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\DummyHandler;
use Raneomik\WatchdogBundle\Tests\Integration\Stubs\Kernel as KernelStub;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

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

    public function testLoadedBaseConfig()
    {
        self::bootKernel();
        $config = self::container()->getParameter('watchdog_config');

        $this->assertArrayHasKey('dates', $config);

        $this->assertArrayHasKey('relative', $config['dates'][0]);
        $this->assertArrayHasKey('compound', $config['dates'][1]);

        $this->assertTrue(self::container()->get(Watchdog::class)->isWoofTime());
    }

    public function testLoadedEmptyConfig()
    {
        self::bootKernel(['config' => 'empty']);
        $config = self::container()->getParameter('watchdog_config');

        $this->assertEmpty($config['dates']);

        $this->assertFalse(self::container()->get(Watchdog::class)->isWoofTime());
    }

    public function testDispatchedEvent()
    {
        self::bootKernel();
        $dispatcher = self::container()->get(EventDispatcherInterface::class);
        $testHandler = self::container()->get(DummyHandler::class);

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
