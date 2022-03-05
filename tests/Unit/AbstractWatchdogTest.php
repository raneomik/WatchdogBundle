<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class AbstractWatchdogTest extends TestCase
{
    public function woofMatchCasesProvider(): \Generator
    {
        $now = new \DateTime();
        $minus2Mins = new \DateTime('-2 minutes');
        $plus2Mins = new \DateTime('+2 minutes');

        yield [[['date_time' => $now->format('Y-m-d H:i')]]];
        yield [[['hour' => $now->format('H:i')]]];
        yield [[['time' => $now->format('H:i')]]];
        yield [[['date' => $now->format('Y-m-d')]]];
        yield [[['start' => $minus2Mins->format('H:i'), 'end' => $plus2Mins->format('H:i')]]];
        yield [[['start' => (new \DateTime('-1 day'))->format('Y-m-d'), 'end' => (new \DateTime('+1 day'))->format('Y-m-d')]]];
        yield [[['relative' => 'today']]];
        yield [[[
            'compound' => [
                ['relative' => 'today'],
                ['start' => $minus2Mins->format('H:i'), 'end' => $plus2Mins->format('H:i')],
                ['hour' => $now->format('H:i')],
                ['time' => $now->format('H:i')],
                ['date' => $now->format('Y-m-d')],
            ],
        ]]];

        //global config wth 1 ok rule
        yield [[
            ['relative' => 'tomorrow'],
            ['start' => $minus2Mins->format('H:i'), 'end' => $plus2Mins->format('H:i')], //ok
            ['hour' => (new \DateTime('+2 hours'))->format('H:i')],
            ['date' => (new \DateTime('+2 days'))->format('Y-m-d')],
        ]];
    }

    public function notWoofMatchCasesProvider(): \Generator
    {
        $now = new \DateTime();
        $plus1Hour = new \DateTime('+1 hours');
        $plus2Hours = new \DateTime('+2 hours');

        if (0 !== (int) ($plus2Hours->format('d') - $now->format('d'))) {
            $now->modify('+1 hours');
            $plus1Hour->modify('+1 hours');
            $plus2Hours->modify('+1 hours');
        }

        yield [[['date_time' => $plus1Hour->format('Y-m-d H:i')]]];
        yield [[['hour' => $plus1Hour->format('H:i')]]];
        yield [[['time' => $plus2Hours->format('H:i')]]];
        yield [[['date' => (new \DateTime('+2 days'))->format('Y-m-d')]]];
        yield [[['start' => $plus1Hour->format('H:i'), 'end' => $plus2Hours->format('H:i')]]];
        yield [[['start' => (new \DateTime('+1 day'))->format('Y-m-d'), 'end' => (new \DateTime('+2 days'))->format('Y-m-d')]]];
        yield [[['relative' => 'tomorrow']]];
        yield [[[
            'compound' => [
                ['relative' => 'tomorrow'],
                ['start' => $now->format('H:i'), 'end' => $plus2Hours->format('H:i')],
            ],
        ]]];
        yield [[
            ['relative' => 'tomorrow'],
            ['start' => $plus1Hour->format('H:i'), 'end' => $plus2Hours->format('H:i')],
            ['hour' => $plus1Hour->format('H:i')],
            ['date' => (new \DateTime('+2 days'))->format('Y-m-d')],
        ]];
    }
}
