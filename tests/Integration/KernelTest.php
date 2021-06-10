<?php

namespace Raneomik\WatchdogBundle\Test\Integration;

use Psr\Container\ContainerInterface;
use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Test\Integration\Stubs\StubHandler;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;

class KernelTest extends TestCase
{
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
        $testHandler = self::container()->get(StubHandler::class);

        $dispatcher->dispatch(new WatchdogWoofCheckEvent(['handled' => true]));

        $this->assertArrayHasKey('handled', $testHandler->handled);
        $this->assertTrue($testHandler->handled['handled']);
    }

    private static function container(): ContainerInterface
    {
        if (Kernel::MAJOR_VERSION < 5) {
            return self::$container;
        }

        return self::getContainer();
    }
}
