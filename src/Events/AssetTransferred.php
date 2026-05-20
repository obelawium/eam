<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Events;

use Obelaw\Ium\Eam\Models\AssetTransition;

final class AssetTransferred
{
    public function __construct(
        public readonly AssetTransition $transition,
    ) {}
}
