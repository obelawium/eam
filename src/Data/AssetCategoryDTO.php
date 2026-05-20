<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Data;

final class AssetCategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?int $parent_id,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            parent_id: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
        );
    }
}
