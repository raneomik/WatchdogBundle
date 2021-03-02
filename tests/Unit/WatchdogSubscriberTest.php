<?php

namespace Raneomik\WatchdogBundle\Test\Unit;

use Raneomik\WatchdogBundle\Event\WatchdogWoofEvent;
use Raneomik\WatchdogBundle\Subscriber\WatchdogSubscriber;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WatchdogSubscriberTest extends AbstractWatchdogTest
{
    /**
     * @dataProvider WoofMatchCasesProvider
     */
    public function testSubscriberOnWoof(array $timeRule)
    {
        $handler = $this->createMock(WatchdogHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('processWoof')
        ;

        $subscriber = new WatchdogSubscriber(new Watchdog(['dates' => $timeRule]), [$handler]);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($subscriber);
        $eventDispatcher->dispatch(
            new WatchdogWoofEvent()
        );
    }

    /**
     * @dataProvider NotWoofMatchCasesProvider
     */
    public function testSubscriberOnNotWoof(array $timeRule)
    {
        $handler = $this->createMock(WatchdogHandlerInterface::class);
        $handler
            ->expects($this->never())
            ->method('processWoof')
        ;

        $subscriber = new WatchdogSubscriber(new Watchdog(['dates' => $timeRule]), [$handler]);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($subscriber);
        $eventDispatcher->dispatch(
            new WatchdogWoofEvent()
        );
    }
}
