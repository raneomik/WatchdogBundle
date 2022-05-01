<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use Raneomik\WatchdogBundle\Exception\IllogicConfigurationException;
use Raneomik\WatchdogBundle\Exception\MalformedConfigurationValueException;
use Raneomik\WatchdogBundle\Exception\NotSupportedConfigurationException;
use Raneomik\WatchdogBundle\Watchdog\Watchdog;

class WatchdogTest extends AbstractWatchdogTest
{
    public function testNoEventCase(): void
    {
        $watchDog = new Watchdog([]);
        $this->assertFalse($watchDog->isWoofTime());
    }

    /**
     * @dataProvider woofMatchCasesProvider
     */
    public function testOkCases(array $timeRule): void
    {
        $watchDog = new Watchdog($timeRule);
        $this->assertTrue($watchDog->isWoofTime());
    }

    /**
     * @dataProvider notWoofMatchCasesProvider
     */
    public function testKoCases(array $timeRule): void
    {
        $watchDog = new Watchdog($timeRule);
        $this->assertFalse($watchDog->isWoofTime());
    }

    /**
     * @dataProvider diverseFormatsProvider
     */
    public function testYamlToArrayConfig(array $yamlConfig, array $arrayConfig, bool $watchdogWoof): void
    {
        $this->assertSame($arrayConfig, $yamlConfig);

        $yamlWatchdog = new Watchdog($yamlConfig);
        $this->assertSame($watchdogWoof, $yamlWatchdog->isWoofTime());
        $arrayWatchdog = new Watchdog($arrayConfig);
        $this->assertSame($watchdogWoof, $arrayWatchdog->isWoofTime());
    }

    public function testNotSupportedConfigExceptionCase(): void
    {
        $this->expectException(NotSupportedConfigurationException::class);
        $this->expectExceptionMessage('test : 12345" is not a supported config');
        new Watchdog([['test' => '12345']]);
    }

    public function testMalformedDataExceptionCase(): void
    {
        $this->expectException(MalformedConfigurationValueException::class);
        $this->expectExceptionMessage('"DateTime string 12345" is malformed config');
        new Watchdog([['date' => '12345'], ['hour' => '12345']]);
    }

    public function testIllogicDataExceptionCase(): void
    {
        $this->expectException(IllogicConfigurationException::class);
        $this->expectExceptionMessage('start time cannot occur after end time');
        new Watchdog([['start' => '11:00', 'end' => '10:00']]);
    }

    public function testIllogicIntervalDataExceptionCase(): void
    {
        $this->expectException(IllogicConfigurationException::class);
        $this->expectExceptionMessage('start and end times must differ');
        new Watchdog([['start' => '10:00', 'end' => '10:00']]);
    }

    public function testMalformedStartIntervalDataExceptionCase(): void
    {
        $this->expectException(MalformedConfigurationValueException::class);
        $this->expectExceptionMessage('Missing "start" data for interval');
        new Watchdog([['end' => '10:00']]);
    }

    public function testMalformedEndIntervalDataExceptionCase(): void
    {
        $this->expectException(MalformedConfigurationValueException::class);
        $this->expectExceptionMessage('Missing "end" data for interval');
        new Watchdog([['start' => '10:00']]);
    }
}
