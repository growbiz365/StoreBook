<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturnArm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_return_id',
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
    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class);
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
        if ($this->arm && $this->arm->sale_price) {
            return $this->arm->sale_price;
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

