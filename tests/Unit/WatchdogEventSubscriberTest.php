<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Subscriber\WatchdogEventSubscriber;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;

class WatchdogEventSubscriberTest extends AbstractWatchdogTest
{
    /**
     * @dataProvider woofMatchCasesProvider
     */
    public function testSubscriberOnWoof(array $timeRule): void
    {
        $handler = $this->createMock(WatchdogHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('processWoof')
        ;

        $this->subscribeAndDispatch($timeRule, $handler);
    }

    /**
     * @dataProvider notWoofMatchCasesProvider
     */
    public function testSubscriberOnNotWoof(array $timeRule): void
    {
        $handler = $this->createMock(WatchdogHandlerInterface::class);
        $handler
            ->expects($this->never())
            ->method('processWoof')
        ;

        $this->subscribeAndDispatch($timeRule, $handler);
    }

    public function subscribeAndDispatch(array $timeRule, WatchdogHandlerInterface $handler): void
    {
        $subscriber = new WatchdogEventSubscriber(
            new \ArrayIterator(['default' => new Watchdog($timeRule)]),
            new \ArrayIterator([$handler])
        );

        $this->assertArrayHasKey(WatchdogWoofCheckEvent::class, $events = $subscriber->getSubscribedEvents());
        $this->assertSame('onWoofCheck', $method = $events[WatchdogWoofCheckEvent::class]);

        $subscriber->$method(new WatchdogWoofCheckEvent());
    }

    public function testUnknownWatchdog(): void
    {
        $handler = $this->createMock(WatchdogHandlerInterface::class);
        $handler
            ->expects($this->never())
            ->method('processWoof')
        ;

        $subscriber = new WatchdogEventSubscriber(
            new \ArrayIterator(['known' => new Watchdog(['relative' => 'now'])]),
            new \ArrayIterator([$handler])
        );

        $this->assertArrayHasKey(WatchdogWoofCheckEvent::class, $events = $subscriber->getSubscribedEvents());
        $this->assertSame('onWoofCheck', $method = $events[WatchdogWoofCheckEvent::class]);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown "unknown" watchdog');
        $subscriber->$method(new WatchdogWoofCheckEvent([], 'unknown'));
    }
}
