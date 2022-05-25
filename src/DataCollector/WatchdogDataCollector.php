<?php

namespace Raneomik\WatchdogBundle\DataCollector;

use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class WatchdogDataCollector extends DataCollector
{
    /** @var array<string, WatchdogInterface> */
    private array $watchdogCollection;
    /** @var array<int, WatchdogHandlerInterface> */
    private array $watchdogHandlerCollection;

    /**
     * @param \Traversable<string, WatchdogInterface>     $watchdogCollection
     * @param \Traversable<int, WatchdogHandlerInterface> $watchdogHandlerCollection
     */
    public function __construct(\Traversable $watchdogCollection, \Traversable $watchdogHandlerCollection)
    {
        $this->watchdogCollection = iterator_to_array($watchdogCollection);
        $this->watchdogHandlerCollection = iterator_to_array($watchdogHandlerCollection);
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data = [
            'watchdogs' => $this->watchdogCollection,
            'watchdogHandlers' => $this->watchdogHandlerCollection,
        ];
    }

    /** @psalm-suppress MixedInferredReturnType, MixedReturnStatement */
    public function getWatchdogs(): array
    {
        return $this->data['watchdogs'] ?? [];
    }

    /** @psalm-suppress MixedInferredReturnType, MixedReturnStatement */
    public function getWatchdogHandlers(): array
    {
        return $this->data['watchdogHandlers'] ?? [];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return WatchdogExtension::SERVICE_TAG;
    }
}
