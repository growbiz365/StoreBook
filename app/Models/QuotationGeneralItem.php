<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationGeneralItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'general_item_id',
        'quantity',
        'sale_price',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function generalItem(): BelongsTo
    {
        return $this->belongsTo(GeneralItem::class);
    }

    // Business Logic Methods
    public function calculateLineTotal(): float
    {
        return $this->quantity * $this->sale_price;
    }

    public function getGrossTotalAttribute(): float
    {
        return $this->quantity * $this->sale_price;
    }

    public function getSalePriceFromItem(): float
    {
        if ($this->generalItem && $this->generalItem->sale_price) {
            return $this->generalItem->sale_price;
        }
        return $this->sale_price ?? 0;
    }

    // Boot method to auto-calculate line_total
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->line_total = $model->calculateLineTotal();
        });
    }
}

