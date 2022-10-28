<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker;

class LegacyCheckerFactory
{
    public static function create(bool $forceLegacy = false): LegacyChecker
    {
        return $forceLegacy
            ? new LegacyFaker($forceLegacy)
            : new LegacyChecker()
        ;
    }
}
