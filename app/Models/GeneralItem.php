<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralItem extends Model
{
    protected $fillable = [
        'item_name',
        'item_type_id',
        'item_code',
        'min_stock_limit',
        'carton_or_pack_size',
        'cost_price',
        'opening_stock',
        'opening_total',
        'sale_price',
        'business_id'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'opening_total' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'min_stock_limit' => 'integer',
        'opening_stock' => 'integer'
    ];

    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(GeneralBatch::class, 'item_id');
    }

    public function stockLedger(): HasMany
    {
        return $this->hasMany(GeneralItemStockLedger::class, 'general_item_id');
    }

    public function getStockStatusAttribute()
    {
        if ($this->min_stock_limit === null) {
            return 'neutral';
        }

        if ($this->opening_stock < $this->min_stock_limit) {
            return 'low';
        } elseif ($this->opening_stock == $this->min_stock_limit) {
            return 'warning';
        } else {
            return 'good';
        }
    }

    public function getStockStatusColorAttribute()
    {
        switch ($this->stock_status) {
            case 'low':
                return 'text-red-600 bg-red-100';
            case 'warning':
                return 'text-yellow-600 bg-yellow-100';
            case 'good':
                return 'text-green-600 bg-green-100';
            default:
                return 'text-gray-600 bg-gray-100';
        }
    }

    public function getStockStatusIconAttribute()
    {
        switch ($this->stock_status) {
            case 'low':
                return '🔴';
            case 'warning':
                return '🟡';
            case 'good':
                return '🟢';
            default:
                return '⚪';
        }
    }
}
