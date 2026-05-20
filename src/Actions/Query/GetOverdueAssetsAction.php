<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Query;

use Illuminate\Database\Eloquent\Collection;
use Obelaw\Ium\Eam\Events\AssetOverdue;
use Obelaw\Ium\Eam\Models\Asset;

final class GetOverdueAssetsAction
{
    public static function run(): Collection
    {
        $overdue = Asset::whereNotNull('holder_id')
            ->whereIn('status', ['assigned'])
            ->whereHas('transitions', function ($query) {
                $query->whereNull('returned_at')
                    ->whereNotNull('expected_return_at')
                    ->where('expected_return_at', '<', now());
            })
            ->get();

        foreach ($overdue as $asset) {
            event(new AssetOverdue($asset));
        }

        return $overdue;
    }
}
