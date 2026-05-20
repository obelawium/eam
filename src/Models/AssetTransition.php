<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Obelaw\Ium\Eam\Base\ModelBase;

class AssetTransition extends ModelBase
{
    protected $fillable = [
        'asset_id',
        'from_holder_type',
        'from_holder_id',
        'to_holder_type',
        'to_holder_id',
        'performed_by',
        'condition',
        'reason',
        'notes',
        'transitioned_at',
    ];

    protected $casts = [
        'transitioned_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function fromHolder(): MorphTo
    {
        return $this->morphTo('from_holder');
    }

    public function toHolder(): MorphTo
    {
        return $this->morphTo('to_holder');
    }
}
