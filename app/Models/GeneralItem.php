<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\StockQuantity;

class GeneralItem extends Model
{
    public const KIND_GOODS = 'goods';

    public const KIND_SERVICE = 'service';

    /** @var float|null Memoized result for {@see getAvailableStockQuantity()} */
    private ?float $availableStockQuantityResolved = null;

    protected $fillable = [
        'item_name',
        'item_kind',
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
     * Available quantity from stock ledger (supports decimal qty).
     */
    public function getAvailableStockQuantity(): float
    {
        if ($this->isService()) {
            return 0.0;
        }

        if ($this->availableStockQuantityResolved !== null) {
            return $this->availableStockQuantityResolved;
        }

        $businessId = (int) ($this->business_id ?? session('active_business'));
        $balance = GeneralItemStockLedger::getStockBalance($this->id, $businessId);
        $this->availableStockQuantityResolved = StockQuantity::normalize($balance['balance']);

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

    public function scopeGoods(Builder $query): Builder
    {
        return $query->where('item_kind', self::KIND_GOODS);
    }

    public function scopeServices(Builder $query): Builder
    {
        return $query->where('item_kind', self::KIND_SERVICE);
    }

    public function isService(): bool
    {
        return ($this->item_kind ?? self::KIND_GOODS) === self::KIND_SERVICE;
    }

    public function isGoods(): bool
    {
        return ! $this->isService();
    }

    public function tracksInventory(): bool
    {
        return $this->isGoods();
    }

    public function getItemKindLabelAttribute(): string
    {
        return $this->isService() ? 'Service' : 'Goods';
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
            if ($item && $item->isService() && (float) ($line['qty'] ?? 0) <= 0) {
                $errors["general_lines.$i.qty"] = 'Service quantity must be greater than zero.';
            }
        }

        return $errors;
    }
}
