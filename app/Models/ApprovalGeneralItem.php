<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalGeneralItem extends Model
{
    protected $fillable = [
        'approval_id',
        'general_item_id',
        'batch_id',
        'quantity',
        'sale_price',
        'line_total',
        'returned_quantity',
        'sold_quantity',
        'remaining_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'returned_quantity' => 'decimal:2',
        'sold_quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
    ];

    // Relationships
    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
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

    public function updateRemainingQuantity(): void
    {
        $this->remaining_quantity = $this->quantity - $this->returned_quantity - $this->sold_quantity;
        $this->save();
    }

    public function hasRemaining(): bool
    {
        return $this->remaining_quantity > 0;
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
