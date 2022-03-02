<?php

namespace Raneomik\WatchdogBundle\Watchdog\Unit\Model;

interface WatchdogUnitInterface
{
    public const COMPOUND = 'compound';
    public const DATE_TIME = 'date_time';
    public const DATE = 'date';
    public const TIME = 'time';
    public const HOUR = 'hour';
    public const START = 'start';
    public const END = 'end';
    public const RELATIVE = 'relative';

    public const UNIT_KEY_MAP = [
      self::COMPOUND,
      self::DATE_TIME,
      self::DATE,
      self::TIME,
      self::HOUR,
      self::START,
      self::END,
      self::RELATIVE,
    ];

    public function isMatching(): bool;
}
