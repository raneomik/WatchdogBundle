<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker;

class LegacyFaker extends LegacyChecker
{
    private bool $isLegacy;

    public function __construct(bool $forceLegacy = false)
    {
        $this->isLegacy = $forceLegacy;
    }

    public function isLegacy(): bool
    {
        return $this->isLegacy;
    }
}
