<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Obelaw\Ium\Eam\Base\ModelBase;

class AssetAuditLog extends ModelBase
{
    protected $table = 'audit_log';

    protected $fillable = [
        'asset_id',
        'action',
        'performed_by',
        'payload',
        'notes',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
