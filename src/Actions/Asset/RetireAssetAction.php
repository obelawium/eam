<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Asset;

use Illuminate\Support\Facades\DB;
use Obelaw\Ium\Eam\Events\AssetRetired;
use Obelaw\Ium\Eam\Exceptions\AssetNotFoundException;
use Obelaw\Ium\Eam\Exceptions\AssetAlreadyRetiredException;
use Obelaw\Ium\Eam\Models\Asset;
use Obelaw\Ium\Eam\Models\AssetAuditLog;

final class RetireAssetAction
{
    public static function run(int $id, string $reason): Asset
    {
        $asset = Asset::find($id);

        if ($asset === null) {
            throw new AssetNotFoundException("Asset with ID {$id} not found.");
        }

        if ($asset->status === 'retired') {
            throw new AssetAlreadyRetiredException("Asset with ID {$id} is already retired.");
        }

        return DB::transaction(function () use ($asset, $reason): Asset {
            $asset->update([
                'status' => 'retired',
                'holder_type' => null,
                'holder_id' => null,
            ]);

            AssetAuditLog::create([
                'asset_id' => $asset->id,
                'action' => 'retired',
                'performed_by' => 'system',
                'payload' => ['reason' => $reason],
            ]);

            event(new AssetRetired($asset));

            return $asset->fresh();
        });
    }
}
