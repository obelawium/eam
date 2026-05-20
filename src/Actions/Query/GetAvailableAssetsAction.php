<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Actions\Query;

use Illuminate\Database\Eloquent\Collection;
use Obelaw\Ium\Eam\Models\Asset;

final class GetAvailableAssetsAction
{
    public static function run(): Collection
    {
        return Asset::where('status', 'available')->get();
    }
}
