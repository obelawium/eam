<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Asset;

use Obelaw\Ium\Eam\Data\AssetDTO;
use Obelaw\Ium\Eam\Events\AssetCreated;
use Obelaw\Ium\Eam\Models\Asset;
use Obelaw\Ium\Eam\Models\AssetAuditLog;

final class CreateAssetAction
{
    public static function run(AssetDTO $dto): Asset
    {
        $asset = Asset::create([
            'code' => $dto->code,
            'name' => $dto->name,
            'description' => $dto->description,
            'category_id' => $dto->category_id,
            'status' => 'available',
            'serial_number' => $dto->serial_number,
            'purchase_date' => $dto->purchase_date,
            'purchase_value' => $dto->purchase_value,
        ]);

        AssetAuditLog::create([
            'asset_id' => $asset->id,
            'action' => 'created',
            'performed_by' => 'system',
            'payload' => ['code' => $dto->code, 'name' => $dto->name],
        ]);

        event(new AssetCreated($asset));

        return $asset;
    }
}
