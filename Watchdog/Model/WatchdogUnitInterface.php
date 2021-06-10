<?php

namespace Raneomik\WatchdogBundle\Watchdog\Model;

interface WatchdogUnitInterface
{
    public const UNIT_KEY_MAP = [
      'compound',
      'date_time',
      'date',
      'time',
      'hour',
      'start',
      'end',
      'relative',
    ];

    public function isMatching(): bool;
}
