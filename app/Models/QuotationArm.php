<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationArm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'arm_id',
        'sale_price',
        'line_total',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class);
    }

    // Business Logic Methods
    public function getLineTotalAttribute(): float
    {
        return $this->sale_price;
    }

    public function getSalePriceFromItem(): float
    {
        if ($this->arm && $this->arm->sale_price) {
            return $this->arm->sale_price;
        }
        return $this->sale_price ?? 0;
    }

    // Boot method to auto-calculate line_total
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->line_total = $model->sale_price;
        });
    }
}

