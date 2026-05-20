<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Query;

use Illuminate\Database\Eloquent\Collection;
use Obelaw\Ium\Eam\Models\Asset;

final class GetAssetsByHolderAction
{
    public static function run(string $holderType, int $holderId): Collection
    {
        return Asset::where('holder_type', $holderType)
            ->where('holder_id', $holderId)
            ->get();
    }
}
