<?php

namespace Raneomik\WatchdogBundle\Subscriber;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WatchdogEventSubscriber implements EventSubscriberInterface
{
    private iterable $watchdogCollection;
    private iterable $watchdogHandlers;

    public function __construct(iterable $watchdogCollection, iterable $watchdogHandlers)
    {
        $this->watchdogCollection = $watchdogCollection;
        $this->watchdogHandlers = $watchdogHandlers;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WatchdogWoofCheckEvent::class => 'onWoofCheck',
        ];
    }

    public function onWoofCheck(WatchdogWoofCheckEvent $event): void
    {
        /**
         * @var string            $id
         * @var WatchdogInterface $watchdog
         */
        foreach ($this->watchdogCollection as $id => $watchdog) {
            if (null !== ($concerned = $event->concernedWatchdog())
                && $id !== $concerned
            ) {
                continue;
            }

            if ($watchdog->isWoofTime()) {
                $this->triggerHandlers($event);
            }
        }
    }

    private function triggerHandlers(WatchdogWoofCheckEvent $event): void
    {
        /** @var WatchdogHandlerInterface $handler */
        foreach ($this->watchdogHandlers as $handler) {
            $handler->processWoof($event->eventParams());
        }
    }
}
