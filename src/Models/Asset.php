<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Obelaw\Ium\Eam\Base\ModelBase;

class Asset extends ModelBase
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'status',
        'serial_number',
        'purchase_date',
        'purchase_value',
        'holder_type',
        'holder_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_value' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function holder(): MorphTo
    {
        return $this->morphTo();
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(AssetTransition::class, 'asset_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AssetAuditLog::class, 'asset_id');
    }
}

