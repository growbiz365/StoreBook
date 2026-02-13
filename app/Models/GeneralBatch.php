<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralBatch extends Model
{
    protected $fillable = [
        'business_id',
        'item_id',
        'qty_received',
        'qty_remaining',
        'unit_cost',
        'total_cost',
        'received_date',
        'user_id',
        'purchase_id',
        'purchase_line_id',
        'batch_code',
        'status',
    ];

    protected $casts = [
        'received_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'qty_received' => 'integer',
        'qty_remaining' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(GeneralItem::class, 'item_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseGeneralLine::class, 'purchase_line_id');
    }

    // Business Logic Methods
    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getConsumedQuantityAttribute(): int
    {
        return $this->qty_received - $this->qty_remaining;
    }

    public function getConsumptionPercentageAttribute(): float
    {
        if ($this->qty_received <= 0) {
            return 0;
        }
        return ($this->getConsumedQuantityAttribute() / $this->qty_received) * 100;
    }

    public function canBeReversed(): bool
    {
        return $this->isActive() && $this->qty_remaining === $this->qty_received;
    }
}


