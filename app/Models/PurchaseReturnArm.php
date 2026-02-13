<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturnArm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_return_id',
        'arm_id',
        'return_price',
        'line_total',
        'deleted_by',
    ];

    protected $casts = [
        'return_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class);
    }

    // Business Logic Methods
    public function getLineTotalAttribute(): float
    {
        return $this->return_price;
    }

    public function getReturnPriceFromItem(): float
    {
        if ($this->arm && $this->arm->purchase_price) {
            return $this->arm->purchase_price;
        }
        return $this->return_price ?? 0;
    }

    // Boot method to auto-calculate line_total
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->line_total = $model->return_price;
        });
    }
}
