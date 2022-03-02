<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Raneomik\WatchdogBundle\Watchdog\Unit\Compound;
use Raneomik\WatchdogBundle\Watchdog\Unit\Date;
use Raneomik\WatchdogBundle\Watchdog\Unit\DateTime as DateTimeUnit;
use Raneomik\WatchdogBundle\Watchdog\Unit\Hour;
use Raneomik\WatchdogBundle\Watchdog\Unit\Interval;
use Raneomik\WatchdogBundle\Watchdog\Unit\RelativeDateTime;
use Raneomik\WatchdogBundle\Watchdog\Unit\Time;
use Raneomik\WatchdogBundle\Watchdog\Unit\WatchdogUnit;
use Raneomik\WatchdogBundle\Watchdog\Unit\WatchdogUnitInterface;

class WatchdogUnitTest extends TestCase
{
    public function matchingUnitProvider(): \Generator
    {
        $now = new \DateTime();
        $nowPlus2Minutes = new \DateTime('+2 minutes');
        $nowMinus2Minutes = new \DateTime('-2 minutes');

        yield [new Hour($hour = $now->format('H:i'))];
        yield [new Hour($nowPlus2Minutes->format('H:i'))];

        yield [new Time($now->format('H:i'))];

        yield [new Date($now->format('Y-m-d'))];
        yield [new DateTimeUnit($nowString = $now->format('Y-m-d H:i'))];

        yield [new Interval($nowString, $nowPlus2Minutes->format('Y-m-d H:i'))];
        yield [new Interval($nowMinus2Minutes->format('Y-m-d H:i'), $nowString)];
        yield [new Interval($nowMinus2Minutes->format('Y-m-d H:i'), $nowPlus2Minutes->format('Y-m-d H:i'))];

        yield [new RelativeDateTime('now')];

        yield [new Compound([
            [WatchdogUnitInterface::RELATIVE => 'now'],
            [WatchdogUnitInterface::TIME => $hour],
        ], true)];
        yield [new Compound($atLeastOneCompound = [
            [WatchdogUnitInterface::RELATIVE => 'tomorrow'],
            [WatchdogUnitInterface::TIME => $hour],
        ])];

        yield [WatchdogUnit::create([
            WatchdogUnitInterface::TIME => $hour,
            WatchdogUnitInterface::COMPOUND => $atLeastOneCompound,
        ])];
    }

    public function notMatchingUnitProvider(): \Generator
    {
        $notNow = new \DateTime('+1 day +1 hour');
        $nowPlus2Minutes = new \DateTime('+2 minutes');

        yield [new Hour($hour = $notNow->format('H:i'))];

        yield [new Time($notNow->format('H:i'))];
        yield [new Time($nowPlus2Minutes->format('H:i'))];

        yield [new Date($notNow->format('Y-m-d'))];
        yield [new DateTimeUnit($notNow->format('Y-m-d H:i'))];

        yield [new Interval($nowPlus2Minutes->format('Y-m-d H:i'), $notNow->format('Y-m-d H:i'))];

        yield [new RelativeDateTime('tomorrow')];

        yield [new Compound($compound = [
            [WatchdogUnitInterface::RELATIVE => 'now'],
            [WatchdogUnitInterface::TIME => $hour],
        ], true)];
        yield [new Compound($noneCompound = [
            [WatchdogUnitInterface::RELATIVE => 'tomorrow'],
            [WatchdogUnitInterface::TIME => $hour],
        ])];

        yield [WatchdogUnit::create([
            WatchdogUnitInterface::TIME => $hour,
            WatchdogUnitInterface::COMPOUND => $compound,
        ])];
        yield [WatchdogUnit::create([
            WatchdogUnitInterface::TIME => $hour,
            WatchdogUnitInterface::COMPOUND => $noneCompound,
        ])];
        yield [new WatchdogUnit('tomorrow')];
    }

    /**
     * @dataProvider matchingUnitProvider
     */
    public function testOkCases(WatchdogUnitInterface $watchDogUnit)
    {
        $this->assertTrue($watchDogUnit->isMatching());
    }

    /**
     * @dataProvider notMatchingUnitProvider
     */
    public function testKoCases(WatchdogUnitInterface $watchDogUnit)
    {
        $this->assertFalse($watchDogUnit->isMatching());
    }
}
