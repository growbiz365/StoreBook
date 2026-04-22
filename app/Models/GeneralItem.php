<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralItem extends Model
{
    /** @var float|null Memoized result for {@see getAvailableStockQuantity()} */
    private ?float $availableStockQuantityResolved = null;

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
        'business_id',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'opening_total' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'min_stock_limit' => 'integer',
        'opening_stock' => 'integer',
        'is_active' => 'boolean',
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

    /**
     * Available quantity: sum of qty_remaining on active batches received on or before today.
     * Matches general items index listing and FIFO consumption rules.
     */
    public function getAvailableStockQuantity(): float
    {
        if ($this->availableStockQuantityResolved !== null) {
            return $this->availableStockQuantityResolved;
        }

        $asOnDate = now()->format('Y-m-d');

        if ($this->relationLoaded('batches')) {
            $this->availableStockQuantityResolved = round((float) $this->batches
                ->filter(function ($batch) use ($asOnDate) {
                    if (($batch->status ?? '') !== 'active') {
                        return false;
                    }
                    if (! $batch->received_date) {
                        return false;
                    }

                    return $batch->received_date->format('Y-m-d') <= $asOnDate;
                })
                ->sum('qty_remaining'));

            return $this->availableStockQuantityResolved;
        }

        $this->availableStockQuantityResolved = round((float) GeneralBatch::query()
            ->where('item_id', $this->id)
            ->where('status', 'active')
            ->where('received_date', '<=', $asOnDate)
            ->sum('qty_remaining'));

        return $this->availableStockQuantityResolved;
    }

    public function getStockStatusAttribute()
    {
        if ($this->min_stock_limit === null) {
            return 'neutral';
        }

        $available = $this->getAvailableStockQuantity();

        if ($available < $this->min_stock_limit) {
            return 'low';
        }
        if ($available == $this->min_stock_limit) {
            return 'warning';
        }

        return 'good';
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Active items, plus any item that appears on a posted sale (e.g. sale returns after deactivation).
     */
    public function scopeActiveOrHistoricallySold(Builder $query, int $businessId): Builder
    {
        $postedItemIds = SaleInvoiceGeneralItem::query()
            ->whereHas('saleInvoice', function ($q) use ($businessId) {
                $q->where('business_id', $businessId)->where('status', 'posted');
            })
            ->distinct()
            ->pluck('general_item_id');

        return $query->where(function ($q) use ($postedItemIds) {
            $q->where('is_active', true);
            if ($postedItemIds->isNotEmpty()) {
                $q->orWhereIn('id', $postedItemIds);
            }
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @param  array<int>  $allowedInactiveItemIds
     * @return array<string, string> field => message
     */
    public static function validateGeneralLinesAreSaleable(int $businessId, array $lines, array $allowedInactiveItemIds = []): array
    {
        $errors = [];
        foreach ($lines as $i => $line) {
            if (! is_array($line) || empty($line['general_item_id'])) {
                continue;
            }
            $itemId = (int) $line['general_item_id'];
            $item = static::where('business_id', $businessId)->find($itemId);
            if ($item && ! $item->is_active && ! in_array($itemId, $allowedInactiveItemIds, true)) {
                $errors["general_lines.$i.general_item_id"] = 'This item is inactive and cannot be sold.';
            }
        }

        return $errors;
    }
}
