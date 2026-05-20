<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Asset;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Obelaw\Ium\Eam\Data\TransferAssetDTO;
use Obelaw\Ium\Eam\Events\AssetTransferred;
use Obelaw\Ium\Eam\Exceptions\AssetNotFoundException;
use Obelaw\Ium\Eam\Exceptions\AssetAlreadyRetiredException;
use Obelaw\Ium\Eam\Exceptions\InvalidHolderException;
use Obelaw\Ium\Eam\Models\Asset;
use Obelaw\Ium\Eam\Models\AssetAuditLog;
use Obelaw\Ium\Eam\Models\AssetTransition;
use Obelaw\Ium\Eam\Traits\HasAssets;

final class TransferAssetAction
{
    public static function run(TransferAssetDTO $dto): AssetTransition
    {
        $asset = Asset::find($dto->asset_id);

        if ($asset === null) {
            throw new AssetNotFoundException("Asset with ID {$dto->asset_id} not found.");
        }

        if ($asset->status === 'retired') {
            throw new AssetAlreadyRetiredException("Asset with ID {$dto->asset_id} is retired and cannot be transferred.");
        }

        if ($dto->toHolder !== null) {
            $uses = array_keys(class_uses_recursive($dto->toHolder));

            if (!in_array(HasAssets::class, $uses, true)) {
                throw new InvalidHolderException(
                    'Class ' . get_class($dto->toHolder) . ' does not use the HasAssets trait.'
                );
            }
        }

        return DB::transaction(function () use ($asset, $dto): AssetTransition {
            $newStatus = self::resolveStatus($dto->toHolder);

            $transition = AssetTransition::create([
                'asset_id' => $dto->asset_id,
                'from_holder_type' => $asset->holder_type,
                'from_holder_id' => $asset->holder_id,
                'to_holder_type' => $dto->toHolder?->getMorphClass(),
                'to_holder_id' => $dto->toHolder?->getKey(),
                'performed_by' => $dto->performed_by,
                'condition' => $dto->condition,
                'reason' => $dto->reason,
                'notes' => $dto->notes,
                'transitioned_at' => now(),
            ]);

            $asset->update([
                'holder_type' => $dto->toHolder?->getMorphClass(),
                'holder_id' => $dto->toHolder?->getKey(),
                'status' => $newStatus,
            ]);

            AssetAuditLog::create([
                'asset_id' => $dto->asset_id,
                'action' => 'transferred',
                'performed_by' => $dto->performed_by,
                'payload' => [
                    'from' => ['type' => $asset->holder_type, 'id' => $asset->holder_id],
                    'to' => ['type' => $dto->toHolder?->getMorphClass(), 'id' => $dto->toHolder?->getKey()],
                    'status' => $newStatus,
                ],
                'notes' => $dto->notes,
            ]);

            event(new AssetTransferred($transition));

            return $transition;
        });
    }

    private static function resolveStatus(?Model $holder): string
    {
        if ($holder === null) {
            return 'available';
        }

        $shortName = class_basename($holder);
        $map = config('eam.holder_status_map', []);

        foreach ($map as $pattern => $status) {
            if (str_contains($shortName, $pattern)) {
                return $status;
            }
        }

        return 'available';
    }
}
