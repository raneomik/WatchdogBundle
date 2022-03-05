<?php

namespace Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker;

class LegacyCheckerFactory
{
    private static bool $testMode = false;

    public static function create(bool $forceLegacy = false): LegacyChecker
    {
        if ($forceLegacy) {
            return new LegacyFaker($forceLegacy);
        }

        return self::$testMode
            ? new LegacyFaker()
            : new LegacyChecker()
        ;
    }

    public static function testMode(): void
    {
        self::$testMode = true;
    }
}
