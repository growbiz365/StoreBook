<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleInvoiceGeneralItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_invoice_id',
        'general_item_id',
        'batch_id',
        'quantity',
        'sale_price',
        'line_total',
        'deleted_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function saleInvoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class);
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
