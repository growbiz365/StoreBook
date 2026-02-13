<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseGeneralLine extends Model
{
    protected $fillable = [
        'purchase_id',
        'line_no',
        'general_item_id',
        'description',
        'qty',
        'unit_price',
        'sale_price',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function generalItem(): BelongsTo
    {
        return $this->belongsTo(GeneralItem::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(GeneralBatch::class, 'id', 'purchase_line_id');
    }

    // Business Logic Methods
    public function calculateLineTotal(): float
    {
        return $this->qty * $this->unit_price;
    }

    public function getEffectiveUnitCost(): float
    {
        if ($this->qty <= 0) {
            return 0;
        }
        
        // For now, just return the unit_price to avoid allocation issues
        return $this->unit_price;
    }

    public function getGrossTotalAttribute(): float
    {
        return $this->qty * $this->unit_price;
    }

    public function getSalePriceFromItem(): float
    {
        if ($this->generalItem && $this->generalItem->sale_price) {
            return $this->generalItem->sale_price;
        }
        return $this->sale_price ?? 0;
    }

    public function getUnitPriceFromItem(): float
    {
        if ($this->generalItem && $this->generalItem->cost_price) {
            return $this->generalItem->cost_price;
        }
        return $this->unit_price ?? 0;
    }

    // Boot method to auto-calculate line_total
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($line) {
            $line->line_total = $line->calculateLineTotal();
        });
    }
}
