<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Subscriber;

use Raneomik\WatchdogBundle\Event\WatchdogWoofCheckEvent;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WatchdogEventSubscriber implements EventSubscriberInterface
{
    /** @var array<string, WatchdogInterface> */
    private array $watchdogCollection;

    private iterable $watchdogHandlerCollection;

    /**
     * @param \Traversable<string, WatchdogInterface>     $watchdogCollection
     * @param \Traversable<int, WatchdogHandlerInterface> $watchdogHandlerCollection
     */
    public function __construct(\Traversable $watchdogCollection, \Traversable $watchdogHandlerCollection)
    {
        $this->watchdogCollection = iterator_to_array($watchdogCollection);
        $this->watchdogHandlerCollection = $watchdogHandlerCollection;
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
        foreach ($this->watchdogHandlerCollection as $handler) {
            $handler->processWoof($event->eventParams());
        }
    }
}
