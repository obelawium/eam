<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Managers;

use Illuminate\Database\Eloquent\Collection;
use Obelaw\Ium\Eam\Actions\Asset\CreateAssetAction;
use Obelaw\Ium\Eam\Actions\Asset\RetireAssetAction;
use Obelaw\Ium\Eam\Actions\Asset\TransferAssetAction;
use Obelaw\Ium\Eam\Actions\Asset\UpdateAssetAction;
use Obelaw\Ium\Eam\Actions\Query\GetAssetAction;
use Obelaw\Ium\Eam\Actions\Query\GetAssetsByHolderAction;
use Obelaw\Ium\Eam\Actions\Query\GetAssetHistoryAction;
use Obelaw\Ium\Eam\Actions\Query\GetAvailableAssetsAction;
use Obelaw\Ium\Eam\Data\AssetDTO;
use Obelaw\Ium\Eam\Data\TransferAssetDTO;
use Obelaw\Ium\Eam\Models\Asset;
use Obelaw\Ium\Eam\Models\AssetTransition;

final class AssetManager
{
    public function create(AssetDTO $dto): Asset
    {
        return CreateAssetAction::run($dto);
    }

    public function update(int $id, AssetDTO $dto): Asset
    {
        return UpdateAssetAction::run($id, $dto);
    }

    public function find(int $id): Asset
    {
        return GetAssetAction::run($id);
    }

    public function list(): Collection
    {
        return Asset::all();
    }

    public function available(): Collection
    {
        return GetAvailableAssetsAction::run();
    }

    public function transferTo(TransferAssetDTO $dto): AssetTransition
    {
        return TransferAssetAction::run($dto);
    }

    public function history(int $assetId): Collection
    {
        return GetAssetHistoryAction::run($assetId);
    }

    public function retire(int $id, string $reason): Asset
    {
        return RetireAssetAction::run($id, $reason);
    }

    public function heldBy(string $holderType, int $holderId): Collection
    {
        return GetAssetsByHolderAction::run($holderType, $holderId);
    }
}

