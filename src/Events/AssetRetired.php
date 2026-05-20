<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Events;

use Obelaw\Ium\Eam\Models\Asset;

final class AssetRetired
{
    public function __construct(
        public readonly Asset $asset,
    ) {}
}
