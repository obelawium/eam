<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Managers;

use Illuminate\Database\Eloquent\Collection;
use Obelaw\Ium\Eam\Actions\Query\GetAssetHistoryAction;
use Obelaw\Ium\Eam\Actions\Query\GetAssetsByHolderAction;
use Obelaw\Ium\Eam\Actions\Query\GetOverdueAssetsAction;
use Obelaw\Ium\Eam\Models\Asset;

final class ReportsManager
{
    public function overdue(): Collection
    {
        return GetOverdueAssetsAction::run();
    }

    public function byHolder(string $holderType, int $holderId): Collection
    {
        return GetAssetsByHolderAction::run($holderType, $holderId);
    }

    public function byCategory(int $categoryId): Collection
    {
        return Asset::where('category_id', $categoryId)->get();
    }

    public function byStatus(string $status): Collection
    {
        return Asset::where('status', $status)->get();
    }

    public function transitions(int $assetId): Collection
    {
        return GetAssetHistoryAction::run($assetId);
    }

    public function lostAndDamaged(): Collection
    {
        return Asset::whereIn('status', ['lost', 'maintenance'])->get();
    }
}
