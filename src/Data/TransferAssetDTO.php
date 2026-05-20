<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Data;

use Illuminate\Database\Eloquent\Model;

final class TransferAssetDTO
{
    public function __construct(
        public readonly int $asset_id,
        public readonly ?Model $toHolder,
        public readonly string $performed_by,
        public readonly ?string $condition,
        public readonly ?string $reason,
        public readonly ?string $notes,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            asset_id: (int) $data['asset_id'],
            toHolder: $data['toHolder'] ?? null,
            performed_by: (string) $data['performed_by'],
            condition: $data['condition'] ?? null,
            reason: $data['reason'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }
}
