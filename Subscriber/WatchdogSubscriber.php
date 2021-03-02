<?php

namespace Raneomik\WatchdogBundle\Subscriber;

use Raneomik\WatchdogBundle\Event\WatchdogWoofEvent;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WatchdogSubscriber implements EventSubscriberInterface
{
    private $watchdog;
    private $watchdogHandlers;

    public function __construct(Watchdog $watchdog, iterable $watchdogHandlers)
    {
        $this->watchdog = $watchdog;
        $this->watchdogHandlers = $watchdogHandlers;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WatchdogWoofEvent::class => 'onWoof',
        ];
    }

    public function onWoof(WatchdogWoofEvent $event)
    {
        if ($this->watchdog->isWoofTime()) {
            foreach ($this->watchdogHandlers as $handler) {
                $handler->processWoof($event->eventParams());
            }
        }
    }
}
