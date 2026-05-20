<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Managers;

use Illuminate\Database\Eloquent\Collection;
use Obelaw\Ium\Eam\Data\AssetCategoryDTO;
use Obelaw\Ium\Eam\Models\AssetCategory;

final class CategoryManager
{
    public function create(AssetCategoryDTO $dto): AssetCategory
    {
        return AssetCategory::create([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'parent_id' => $dto->parent_id,
        ]);
    }

    public function find(int $id): AssetCategory
    {
        return AssetCategory::findOrFail($id);
    }

    public function list(): Collection
    {
        return AssetCategory::all();
    }
}

