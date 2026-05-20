<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Query;

use Obelaw\Ium\Eam\Exceptions\AssetNotFoundException;
use Obelaw\Ium\Eam\Models\Asset;

final class GetAssetAction
{
    public static function run(int $id): Asset
    {
        $asset = Asset::with(['category', 'holder'])->find($id);

        if ($asset === null) {
            throw new AssetNotFoundException("Asset with ID {$id} not found.");
        }

        return $asset;
    }
}
