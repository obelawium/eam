<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Asset;

use Obelaw\Ium\Eam\Data\AssetDTO;
use Obelaw\Ium\Eam\Exceptions\AssetNotFoundException;
use Obelaw\Ium\Eam\Models\Asset;
use Obelaw\Ium\Eam\Models\AssetAuditLog;

final class UpdateAssetAction
{
    public static function run(int $id, AssetDTO $dto): Asset
    {
        $asset = Asset::find($id);

        if ($asset === null) {
            throw new AssetNotFoundException("Asset with ID {$id} not found.");
        }

        $asset->update([
            'code' => $dto->code,
            'name' => $dto->name,
            'description' => $dto->description,
            'category_id' => $dto->category_id,
            'serial_number' => $dto->serial_number,
            'purchase_date' => $dto->purchase_date,
            'purchase_value' => $dto->purchase_value,
        ]);

        AssetAuditLog::create([
            'asset_id' => $id,
            'action' => 'updated',
            'performed_by' => 'system',
            'payload' => ['code' => $dto->code, 'name' => $dto->name],
        ]);

        return $asset->fresh();
    }
}
