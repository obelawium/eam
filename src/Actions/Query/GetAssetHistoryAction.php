<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Query;

use Illuminate\Database\Eloquent\Collection;
use Obelaw\Ium\Eam\Models\AssetTransition;

final class GetAssetHistoryAction
{
    public static function run(int $assetId): Collection
    {
        return AssetTransition::where('asset_id', $assetId)
            ->orderByDesc('transitioned_at')
            ->get();
    }
}
