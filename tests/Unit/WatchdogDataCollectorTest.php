<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Raneomik\WatchdogBundle\DataCollector\WatchdogDataCollector;
use Raneomik\WatchdogBundle\DependencyInjection\WatchdogExtension;
use Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WatchdogDataCollectorTest extends TestCase
{
    public function testDataCollection(): void
    {
        $dataCollector = new WatchdogDataCollector(
            new \ArrayIterator($watchdogs = ['default' => new Watchdog([])]),
            new \ArrayIterator($handlers = [$this->createMock(WatchdogHandlerInterface::class)])
        );
        $this->assertEquals(WatchdogExtension::SERVICE_TAG, $dataCollector->getName());

        $dataCollector->collect(new Request(), new Response());

        $this->assertEquals($watchdogs, $dataCollector->getWatchdogs());
        $this->assertEquals($handlers, $dataCollector->getWatchdogHandlers());

        $dataCollector->reset();

        $this->assertEmpty($dataCollector->getWatchdogs());
        $this->assertEmpty($dataCollector->getWatchdogHandlers());
    }
}
