<?php

declare(strict_types=1);

namespace Raneomik\WatchdogBundle\DependencyInjection\SymfonyVersionChecker;

use Symfony\Component\HttpKernel\Kernel;

class LegacyChecker
{
    public function isLegacy(): bool
    {
        /* @phpstan-ignore-next-line */
        return 5 > Kernel::MAJOR_VERSION;
    }
}
