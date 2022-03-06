<?php

namespace Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker;

class LegacyFaker extends LegacyChecker
{
    private bool $isLegacy;

    public function __construct(bool $isLegacy = false)
    {
        $this->isLegacy = $isLegacy;
    }

    public function isLegacy(): bool
    {
        return $this->isLegacy;
    }
}
