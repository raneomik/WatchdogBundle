<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;
use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;
use Raneomik\WatchdogBundle\Exception\NotSupportedConfigurationException;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class WatchdogTest extends AbstractWatchdogTest
{
    public function testNoEventCase()
    {
        $watchDog = new Watchdog(['dates' => []]);
        $this->assertFalse($watchDog->isWoofTime());
    }

    /**
     * @dataProvider woofMatchCasesProvider
     */
    public function testOkCases(array $timeRule)
    {
        $watchDog = new Watchdog(['dates' => $timeRule]);
        $this->assertTrue($watchDog->isWoofTime());
    }

    /**
     * @dataProvider notWoofMatchCasesProvider
     */
    public function testKoCases(array $timeRule)
    {
        $watchDog = new Watchdog(['dates' => $timeRule]);
        $this->assertFalse($watchDog->isWoofTime());
    }

    public function testInvalidConfigurationExceptionCase()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Expected at least an empty array at "dates" key');
        new Watchdog();
    }

    public function testNotSupportedConfigExceptionCase()
    {
        $this->expectException(NotSupportedConfigurationException::class);
        $this->expectExceptionMessage('test : 12345" is not a supported config');
        new Watchdog(['dates' => [['test' => '12345']]]);
    }

    public function testMalformedDataExceptionCase()
    {
        $this->expectException(MalformedConfigurationValueException::class);
        $this->expectExceptionMessage('"DateTime string 12345" is malformed config');
        new Watchdog(['dates' => [['date' => '12345'], ['hour' => '12345']]]);
    }

    public function testIllogicDataExceptionCase()
    {
        $this->expectException(IllogicConfigurationException::class);
        $this->expectExceptionMessage('start time cannot occur after end time');
        new Watchdog(['dates' => [['start' => '11:00', 'end' => '10:00']]]);
    }

    public function testIllogicIntervalDataExceptionCase()
    {
        $this->expectException(IllogicConfigurationException::class);
        $this->expectExceptionMessage('start and end times must differ');
        new Watchdog(['dates' => [['start' => '10:00', 'end' => '10:00']]]);
    }

    public function testMalformedStartIntervalDataExceptionCase()
    {
        $this->expectException(MalformedConfigurationValueException::class);
        $this->expectExceptionMessage('Missing "start" data for interval');
        new Watchdog(['dates' => [['end' => '10:00']]]);
    }

    public function testMalformedEndIntervalDataExceptionCase()
    {
        $this->expectException(MalformedConfigurationValueException::class);
        $this->expectExceptionMessage('Missing "end" data for interval');
        new Watchdog(['dates' => [['start' => '10:00']]]);
    }
}
