<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class AbstractWatchdogTest extends TestCase
{
    public function woofMatchCasesProvider(): \Generator
    {
        $minus5Mins = new \DateTime('-5 minutes');
        $plus5Mins = new \DateTime('+5 minutes');

        yield [[['date_time' => (new \DateTime())->format('Y-m-d H:i')]]];
        yield [[['hour' => (new \DateTime())->format('H:i')]]];
        yield [[['time' => (new \DateTime())->format('H:i')]]];
        yield [[['date' => (new \DateTime())->format('Y-m-d')]]];
        yield [[['start' => $minus5Mins->format('H:i'), 'end' => $plus5Mins->format('H:i')]]];
        yield [[['start' => (new \DateTime('-1 day'))->format('Y-m-d'), 'end' => (new \DateTime('+1 day'))->format('Y-m-d')]]];
        yield [[['relative' => 'today']]];
        yield [[[
            'compound' => [
                ['relative' => 'today'],
                ['start' => $minus5Mins->format('H:i'), 'end' => $plus5Mins->format('H:i')],
            ],
        ]]];

        //global config wth 1 ok rule
        yield [[
            ['relative' => 'tomorrow'],
            ['start' => $minus5Mins->format('H:i'), 'end' => $plus5Mins->format('H:i')], //ok
            ['hour' => (new \DateTime('+2 hours'))->format('H:i')],
            ['date' => (new \DateTime('+2 days'))->format('Y-m-d')],
        ]];
    }

    public function notWoofMatchCasesProvider(): \Generator
    {
        $plus2Hours = new \DateTime('+1 hours');
        $plus3Hours = new \DateTime('+2 hours');

        yield [[['date_time' => $plus2Hours->format('Y-m-d H:i')]]];
        yield [[['hour' => $plus2Hours->format('H:i')]]];
        yield [[['time' => $plus3Hours->format('H:i')]]];
        yield [[['date' => (new \DateTime('+2 days'))->format('Y-m-d')]]];
        yield [[['start' => $plus2Hours->format('H:i'), 'end' => $plus3Hours->format('H:i')]]];
        yield [[['start' => (new \DateTime('+1 day'))->format('Y-m-d'), 'end' => (new \DateTime('+2 days'))->format('Y-m-d')]]];
        yield [[['relative' => 'tomorrow']]];
        yield [[[
            'compound' => [
                ['relative' => 'tomorrow'],
                ['start' => (new \DateTime())->format('H:i'), 'end' => $plus3Hours->format('H:i')],
            ],
        ]]];
        yield [[
            ['relative' => 'tomorrow'],
            ['start' => $plus2Hours->format('H:i'), 'end' => $plus3Hours->format('H:i')],
            ['hour' => $plus2Hours->format('H:i')],
            ['date' => (new \DateTime('+2 days'))->format('Y-m-d')],
        ]];
    }
}
