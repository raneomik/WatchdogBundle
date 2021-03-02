<?php

namespace Raneomik\WatchdogBundle\Test\Unit;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;
use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;
use Raneomik\WatchdogBundle\Exception\NotSupportedConfigurationException;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;

class WatchdogTest extends AbstractWatchdogTest
{
    public function testNoEventCase()
    {
        $watchDog = new Watchdog([]);
        $this->assertFalse($watchDog->isWoofTime());
    }

    /**
     * @dataProvider WoofMatchCasesProvider
     */
    public function testOkCases(array $timeRule)
    {
        $watchDog = new Watchdog(['dates' => $timeRule]);
        $this->assertTrue($watchDog->isWoofTime());
    }

    /**
     * @dataProvider NotWoofMatchCasesProvider
     */
    public function testKoCases(array $timeRule)
    {
        $watchDog = new Watchdog(['dates' => $timeRule]);
        $this->assertFalse($watchDog->isWoofTime());
    }

    public function testNotSupportedConfigExceptionCase()
    {
        $this->expectException(NotSupportedConfigurationException::class);
        new Watchdog(['dates' => [['test' => '12345']]]);
    }

    public function testMalformedDataExceptionCase()
    {
        $this->expectException(MalformedConfigurationValueException::class);
        new Watchdog(['dates' => [['date' => '12345'], ['hour' => '12345']]]);
    }

    public function testIllogicDataExceptionCase()
    {
        $this->expectException(IllogicConfigurationException::class);
        new Watchdog(['dates' => [['start' => '11:00', 'end' => '10:00']]]);
    }

    public function testMalformedStartIntervalDataExceptionCase()
    {
        $this->expectException(MalformedConfigurationValueException::class);
        new Watchdog(['dates' => [['end' => '10:00']]]);
    }

    public function testMalformedEndIntervalDataExceptionCase()
    {
        $this->expectException(MalformedConfigurationValueException::class);
        new Watchdog(['dates' => [['start' => '10:00']]]);
    }
}
