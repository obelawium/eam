<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Events;

use Obelaw\Ium\Eam\Models\Asset;

final class AssetCreated
{
    public function __construct(
        public readonly Asset $asset,
    ) {}
}
