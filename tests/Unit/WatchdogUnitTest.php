<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Compound;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Date;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\DateTime as DateTimeUnit;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Hour;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Interval;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\RelativeDateTime;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\Time;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\WatchdogUnitInterface;
use Raneomik\WatchdogBundle\Watchdog\Unit\WatchdogUnitFactory;

class WatchdogUnitTest extends TestCase
{
    public function matchingUnitProvider(): \Generator
    {
        $now = new \DateTime();
        $nowPlus1Minutes = new \DateTime('+1 minutes');
        $nowMinus1Minutes = new \DateTime('-1 minutes');

        yield [new Hour($hour = $now->format('H:i'))];
        yield [new Hour($nowPlus1Minutes->format('H:i'))];

        yield [new Time($now->format('H:i'))];

        yield [new Date($date = $now->format('Y-m-d'))];
        yield [new DateTimeUnit($nowString = $now->format('Y-m-d H:i'))];

        yield [new Interval($nowString, $nowPlus1Minutes->format('Y-m-d H:i'))];
        yield [new Interval($nowMinus1Minutes->format('Y-m-d H:i'), $nowString)];
        yield [new Interval($nowMinus1Minutes->format('Y-m-d H:i'), $nowPlus1Minutes->format('Y-m-d H:i'))];

        yield [new RelativeDateTime('now')];

        yield [new Compound([
            [
                WatchdogUnitInterface::RELATIVE => 'today'
            ],
            [
                WatchdogUnitInterface::START => $nowMinus1Minutes->format('Y-m-d H:i'),
                WatchdogUnitInterface::END => $nowPlus1Minutes->format('Y-m-d H:i'),
            ],
        ], true)];
        yield [new Compound($atLeastOneCompound = [
            [
                WatchdogUnitInterface::RELATIVE => 'tomorrow'
            ],
            [
                WatchdogUnitInterface::TIME => $hour
            ],
        ])];

        yield [WatchdogUnitFactory::create([
            WatchdogUnitInterface::TIME => $hour,
            WatchdogUnitInterface::COMPOUND => $atLeastOneCompound,
        ])];

        yield [new Compound([
            WatchdogUnitInterface::RELATIVE => 'today',
            [
                WatchdogUnitInterface::START => $nowMinus1Minutes->format('Y-m-d H:i'),
                WatchdogUnitInterface::END => $nowPlus1Minutes->format('Y-m-d H:i'),
            ],
        ], true)];
    }

    public function notMatchingUnitProvider(): \Generator
    {
        $now = new \DateTime();
        $notNow = new \DateTime('+1 day +1 hour');
        $nowMinus2Minutes = new \DateTime('-2 minutes');
        $nowPlus2Minutes = new \DateTime('+2 minutes');

        if (0 !== (int) ($nowPlus2Minutes->format('d') - $now->format('d'))) {
            $now->modify('+1 hours');
            $nowPlus2Minutes->modify('+1 hours');
        }

        yield [new Hour($hour = $notNow->format('H:i'))];

        yield [new Time($notNow->format('H:i'))];
        yield [new Time($nowPlus2Minutes->format('H:i'))];

        yield [new Date($date = $notNow->format('Y-m-d'))];
        yield [new DateTimeUnit($dateTime = $notNow->format('Y-m-d H:i'))];

        yield [new Interval($nowPlus2Minutes->format('Y-m-d H:i'), $notNow->format('Y-m-d H:i'))];

        yield [new RelativeDateTime('tomorrow')];

        yield [
            new Compound($compound = [
                [
                    WatchdogUnitInterface::RELATIVE => 'now'
                ],
                [
                    WatchdogUnitInterface::TIME => $hour
                ],
                [
                    WatchdogUnitInterface::DATE => $date
                ],
                [
                    WatchdogUnitInterface::DATE_TIME => $dateTime
                ],
                [
                    WatchdogUnitInterface::START => $nowPlus2Minutes->format('Y-m-d H:i'),
                    WatchdogUnitInterface::END => $notNow->format('Y-m-d H:i'),
                ],
            ], true)];
        yield [new Compound($noneCompound = [
            [
                WatchdogUnitInterface::RELATIVE => 'tomorrow'
            ],
            [
                WatchdogUnitInterface::TIME => $hour
            ],
        ])];

        yield [WatchdogUnitFactory::create([
            WatchdogUnitInterface::TIME => $hour,
            WatchdogUnitInterface::COMPOUND => $compound,
        ])];
        yield [WatchdogUnitFactory::create([
            WatchdogUnitInterface::TIME => $hour,
            WatchdogUnitInterface::COMPOUND => $noneCompound,
        ])];
        yield [
            WatchdogUnitFactory::create([
                WatchdogUnitInterface::TIME => $hour,
                WatchdogUnitInterface::COMPOUND => new Compound([
                    [
                        WatchdogUnitInterface::RELATIVE => 'today'
                    ],
                    [
                        WatchdogUnitInterface::START => $nowMinus2Minutes->format('Y-m-d H:i'),
                        WatchdogUnitInterface::END => $nowPlus2Minutes->format('Y-m-d H:i'),
                    ],
                ], true),
            ])];

        yield [new Compound([
            WatchdogUnitInterface::RELATIVE => 'today',
            [
                WatchdogUnitInterface::START => $now->modify('+1 minute')->format('Y-m-d H:i'),
                WatchdogUnitInterface::END => $nowPlus2Minutes->format('Y-m-d H:i'),
            ],
        ], true)];
    }

    /**
     * @dataProvider matchingUnitProvider
     */
    public function testOkCases(WatchdogUnitInterface $watchDogUnit): void
    {
        $this->assertTrue($watchDogUnit->isMatching());
    }

    /**
     * @dataProvider notMatchingUnitProvider
     */
    public function testKoCases(WatchdogUnitInterface $watchDogUnit): void
    {
        $this->assertFalse($watchDogUnit->isMatching());
    }

    public function testComplexCompoundUnit(): void
    {
        $now = new \DateTime();
        $notNow = new \DateTime('-1 day -1 hour');

        $unit = new Compound([
            WatchdogUnitInterface::COMPOUND => [
                WatchdogUnitInterface::COMPOUND => [
                    WatchdogUnitInterface::COMPOUND => [
                        WatchdogUnitInterface::COMPOUND => [
                            WatchdogUnitInterface::COMPOUND => [
                                WatchdogUnitInterface::RELATIVE => 'now',
                                WatchdogUnitInterface::TIME => $now->format('H:i'),
                                WatchdogUnitInterface::DATE => $now->format('d-m-Y'),
                                WatchdogUnitInterface::DATE_TIME => $now->format('d-m-Y H:i'),
                                $interval = [
                                    WatchdogUnitInterface::START => (new \DateTime('-1 mins'))->format('d-m-Y H:i'),
                                    WatchdogUnitInterface::END => (new \DateTime('+1 mins'))->format('d-m-Y H:i'),
                                ],
                            ],
                            $interval,
                        ],
                        WatchdogUnitInterface::RELATIVE => 'now',
                    ],
                    WatchdogUnitInterface::DATE_TIME => $now->format('d-m-Y H:i'),
                ],
                WatchdogUnitInterface::DATE => $now->format('d-m-Y'),
            ],
            WatchdogUnitInterface::TIME => $notNow->format('H:i'),
        ]);

        $this->assertTrue($unit->isMatching());
    }
}
