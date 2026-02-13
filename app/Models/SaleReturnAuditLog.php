<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturnAuditLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_return_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'deleted_by',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

