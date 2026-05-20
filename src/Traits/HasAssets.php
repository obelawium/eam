<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Obelaw\Ium\Eam\Actions\Asset\TransferAssetAction;
use Obelaw\Ium\Eam\Data\TransferAssetDTO;
use Obelaw\Ium\Eam\Models\Asset;
use Obelaw\Ium\Eam\Models\AssetTransition;

trait HasAssets
{
    public function assets(): MorphMany
    {
        return $this->morphMany(Asset::class, 'holder');
    }

    public function assetTransitions(): MorphMany
    {
        return $this->morphMany(AssetTransition::class, 'to_holder');
    }

    public function receiveAsset(int $assetId, array $data = []): AssetTransition
    {
        return TransferAssetAction::run(
            TransferAssetDTO::from([
                'asset_id' => $assetId,
                'toHolder' => $this,
                ...$data,
            ])
        );
    }
}
