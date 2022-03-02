<?php

namespace Raneomik\WatchdogBundle\Subscriber;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WatchdogEventSubscriber implements EventSubscriberInterface
{
    private Watchdog $watchdog;
    private iterable $watchdogHandlers;

    public function __construct(Watchdog $watchdog, iterable $watchdogHandlers)
    {
        $this->watchdog = $watchdog;
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
        if (false === $this->watchdog->isWoofTime()) {
            return;
        }

        /** @var WatchdogHandlerInterface $handler */
        foreach ($this->watchdogHandlers as $handler) {
            $handler->processWoof($event->eventParams());
        }
    }
}