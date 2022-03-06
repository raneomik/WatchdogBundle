<?php

namespace Raneomik\WatchdogBundle\Subscriber;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Traversable;

class WatchdogEventSubscriber implements EventSubscriberInterface
{
    /** @var array<string, WatchdogInterface> */
    private array $watchdogCollection;
    private iterable $watchdogHandlers;

    /**
     * @param Traversable<string, WatchdogInterface>     $watchdogCollection
     * @param Traversable<int, WatchdogHandlerInterface> $watchdogHandlers
     */
    public function __construct(Traversable $watchdogCollection, Traversable $watchdogHandlers)
    {
        $this->watchdogCollection = iterator_to_array($watchdogCollection);
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
        if ($concerned = $event->concernedWatchdogId()) {
            if (null === ($watchdog = $this->watchdogCollection[$concerned] ?? null)) {
                throw new \UnexpectedValueException(sprintf('Unknown "%s" watchdog', $concerned));
            }

            $this->watchdogCheck($watchdog, $event);

            return;
        }

        foreach ($this->watchdogCollection as $watchdog) {
            $this->watchdogCheck($watchdog, $event);
        }
    }

    private function watchdogCheck(WatchdogInterface $watchdog, WatchdogWoofCheckEvent $event): void
    {
        if (false === $watchdog->isWoofTime()) {
            return;
        }

        $this->triggerHandlers($event);
    }

    private function triggerHandlers(WatchdogWoofCheckEvent $event): void
    {
        /** @var WatchdogHandlerInterface $handler */
        foreach ($this->watchdogHandlers as $handler) {
            $handler->processWoof($event->eventParams());
        }
    }
}
