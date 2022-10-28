<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();

    $ecsConfig->sets([
        SetList::CLEAN_CODE,
        SetList::PSR_12,
        SetList::SPACES,
        SetList::NAMESPACES,
        SetList::STRICT,
        SetList::SYMPLIFY,
    ]);
};
