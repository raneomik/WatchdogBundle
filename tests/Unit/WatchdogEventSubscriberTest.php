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
    public function testSubscriberOnWoof(array $timeRule)
    {
        $handler = $this->createMock(WatchdogHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('processWoof')
        ;

        $subscriber = new WatchdogEventSubscriber(new Watchdog(['dates' => $timeRule]), [$handler]);

        $this->assertArrayHasKey(WatchdogWoofCheckEvent::class, $events = $subscriber->getSubscribedEvents());
        $method = $events[WatchdogWoofCheckEvent::class];
        $subscriber->$method(new WatchdogWoofCheckEvent());
    }

    /**
     * @dataProvider notWoofMatchCasesProvider
     */
    public function testSubscriberOnNotWoof(array $timeRule)
    {
        $handler = $this->createMock(WatchdogHandlerInterface::class);
        $handler
            ->expects($this->never())
            ->method('processWoof')
        ;

        $subscriber = new WatchdogEventSubscriber(new Watchdog(['dates' => $timeRule]), [$handler]);

        $this->assertArrayHasKey(WatchdogWoofCheckEvent::class, $events = $subscriber->getSubscribedEvents());
        $method = $events[WatchdogWoofCheckEvent::class];
        $subscriber->$method(new WatchdogWoofCheckEvent());
    }
}
