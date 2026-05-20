<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Data;

final class AssetDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?int $category_id,
        public readonly ?string $serial_number,
        public readonly ?string $purchase_date,
        public readonly ?float $purchase_value,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,
            category_id: $data['category_id'] ?? null,
            serial_number: $data['serial_number'] ?? null,
            purchase_date: $data['purchase_date'] ?? null,
            purchase_value: isset($data['purchase_value']) ? (float) $data['purchase_value'] : null,
        );
    }
}
