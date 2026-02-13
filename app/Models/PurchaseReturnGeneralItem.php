<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturnGeneralItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_return_id',
        'general_item_id',
        'batch_id',
        'quantity',
        'return_price',
        'line_total',
        'deleted_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'return_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function generalItem(): BelongsTo
    {
        return $this->belongsTo(GeneralItem::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(GeneralBatch::class);
    }

    // Business Logic Methods
    public function calculateLineTotal(): float
    {
        return $this->quantity * $this->return_price;
    }

    public function getGrossTotalAttribute(): float
    {
        return $this->quantity * $this->return_price;
    }

    public function getReturnPriceFromItem(): float
    {
        if ($this->generalItem && $this->generalItem->cost_price) {
            return $this->generalItem->cost_price;
        }
        return $this->return_price ?? 0;
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
