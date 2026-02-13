<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'business_id',
        'item_id',
        'batch_id',
        'tx_type',
        'qty',
        'unit_cost',
        'total_cost',
        'date',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'qty' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(GeneralItem::class, 'item_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(GeneralBatch::class, 'batch_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


