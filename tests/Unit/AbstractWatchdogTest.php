<?php

namespace Raneomik\WatchdogBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Raneomik\WatchdogBundle\Watchdog\Unit\Model\WatchdogUnitInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractWatchdogTest extends TestCase
{
    /**
     * Map : [
     *      config,
     *      type,
     *      original config simple representation
     *      string representation,
     *      woof match config offset,
     * ]
     */
    public function woofMatchCasesProvider(): \Generator
    {
        $now = new \DateTime();
        $minus1Mins = new \DateTime('-1 minutes');
        $plus1Mins = new \DateTime('+1 minutes');

        yield [[['date_time' => $config = $now->format('Y-m-d H:i')]], 'date_time', $config, sprintf('DateTime : %s', $config), 0];
        yield [[['hour' => $config = $now->format('H:i')]], 'hour', $config, sprintf('Hour : %s', $config), 0];
        yield [[['time' => $config = $now->format('H:i')]], 'time', $config, sprintf('Time : %s', $config), 0];
        yield [[['date' => $config = $now->format('Y-m-d')]], 'date', $config, sprintf('Date : %s', $config), 0];
        yield [
            [[
            'start' => $startConfig = $minus1Mins->format('H:i'),
            'end' => $endConfig = $plus1Mins->format('H:i'),
            ]],
            'interval',
            sprintf('start : %s / end : %s', $startConfig, $endConfig),
            sprintf('Interval : %s - %s', $startConfig, $endConfig),
            0,
        ];
        yield [[[
            'start' => $startConfig = (new \DateTime('-1 day'))->format('Y-m-d'),
            'end' => $endConfig = (new \DateTime('+1 day'))->format('Y-m-d'),
            ]],
            'interval',
            sprintf('start : %s / end : %s', $startConfig, $endConfig),
            sprintf('Interval : %s - %s', $startConfig, $endConfig),
            0,
        ];
        yield [[['relative' => $config = 'today']], 'relative', $config, sprintf('Relative : %s', $config), 0];
        yield [[[
            'compound' => [
                ['relative' => 'today'],
                ['start' => $minus1Mins->format('H:i'), 'end' => $plus1Mins->format('H:i')],
                ['hour' => $now->format('H:i')],
                ['time' => $now->format('H:i')],
                ['date' => $now->format('Y-m-d')],
            ], ]],
            'compound', '', '', 0,
        ];

        // global config wth 1 ok rule
        yield [[
            ['relative' => 'tomorrow'],
            [   // ok
                'start' => $startConfig = $minus1Mins->format('H:i'),
                'end' => $endConfig = $plus1Mins->format('H:i'),
            ],
            ['hour' => (new \DateTime('+2 hours'))->format('H:i')],
            ['date' => (new \DateTime('+2 days'))->format('Y-m-d')],
        ],
            'interval',
            sprintf('start : %s / end : %s', $startConfig, $endConfig),
            sprintf('Interval : %s - %s', $startConfig, $endConfig),
            1,
        ];
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
                ['relative' => 'today'],
                ['start' => $plus1Hour->format('H:i'), 'end' => $plus2Hours->format('H:i')],
            ],
        ]]];
        yield [[
            ['relative' => 'tomorrow'],
            ['start' => $plus1Hour->format('H:i'), 'end' => $plus2Hours->format('H:i')],
            ['hour' => $plus1Hour->format('H:i')],
            ['date' => (new \DateTime('+2 days'))->format('Y-m-d')],
        ]];
    }

    public function compoundIntervalInYamlProvider(): \Generator
    {
        $nowMinus1Minute = (new \DateTime('-1 minutes'))->format('H:i');
        $nowPlus1Minute = (new \DateTime('+1 minutes'))->format('H:i');
        $nowPlus2Minutes = (new \DateTime('+2 minutes'))->format('H:i');

        $matchingYamlConfig = <<<YAML
---
- { start: '{plus1Minute}', end: '{plus2Minutes}' }
- compound:
    - relative: 'today'
    - { start: '{minus1Minute}', end: '{plus1Minute}' }
YAML;

        $yamlConfig = Yaml::parse(str_replace(
            ['{minus1Minute}', '{plus1Minute}', '{plus2Minutes}'],
            [$nowMinus1Minute, $nowPlus1Minute, $nowPlus2Minutes],
            $matchingYamlConfig
        ));

        $arrayConfig = [
            [
                WatchdogUnitInterface::START => $nowPlus1Minute,
                WatchdogUnitInterface::END => $nowPlus2Minutes,
            ],
            [
                WatchdogUnitInterface::COMPOUND => [
                    [WatchdogUnitInterface::RELATIVE => 'today'],
                    [
                        WatchdogUnitInterface::START => $nowMinus1Minute,
                        WatchdogUnitInterface::END => $nowPlus1Minute,
                    ],
                ],
            ],
        ];

        yield [$yamlConfig, $arrayConfig, true];

        $notMatchingYamlConfig = <<<YAML
---
- { start: '{plus1Minute}', end: '{plus2Minutes}' }
- compound:
    - relative: 'today'
    - { start: '{plus1Minute}', end: '{plus2Minutes}' }
YAML;

        $yamlConfig = Yaml::parse(str_replace(
            ['{minus1Minute}', '{plus1Minute}', '{plus2Minutes}'],
            [$nowMinus1Minute, $nowPlus1Minute, $nowPlus2Minutes],
            $notMatchingYamlConfig
        ));

        $arrayConfig = [
            [
                WatchdogUnitInterface::START => $nowPlus1Minute,
                WatchdogUnitInterface::END => $nowPlus2Minutes,
            ],
            [
                WatchdogUnitInterface::COMPOUND => [
                    [WatchdogUnitInterface::RELATIVE => 'today'],
                    [
                        WatchdogUnitInterface::START => $nowPlus1Minute,
                        WatchdogUnitInterface::END => $nowPlus2Minutes,
                    ],
                ],
            ],
        ];

        yield [$yamlConfig, $arrayConfig, false];
    }
}
