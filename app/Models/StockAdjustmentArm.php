<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentArm extends Model
{
    protected $fillable = [
        'stock_adjustment_id',
        'arm_id',
        'reason',
        'price',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class, 'stock_adjustment_id');
    }

    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class, 'arm_id');
    }
}


