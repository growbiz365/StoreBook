<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    protected $fillable = [
        'business_id',
        'adjustment_type',
        'adjustment_date',
        'description',
        'user_id',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    /**
     * Get the business that owns the stock adjustment.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the general item.
     */
    public function generalItem(): BelongsTo
    {
        return $this->belongsTo(GeneralItem::class);
    }

    /**
     * Get the arm when this adjustment is for an ARM.
     */
    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class);
    }

    /**
     * Get the user who created the adjustment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the stock ledger entries for this adjustment.
     */
    public function stockLedgerEntries(): HasMany
    {
        return $this->hasMany(GeneralItemStockLedger::class, 'reference_id');
    }

    public function itemLines(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class, 'stock_adjustment_id');
    }

    public function armLines(): HasMany
    {
        return $this->hasMany(StockAdjustmentArm::class, 'stock_adjustment_id');
    }

    /**
     * Scope to filter by business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope to filter by adjustment type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('adjustment_type', $type);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('adjustment_date', [$fromDate, $toDate]);
    }

    /**
     * Get adjustment type badge color.
     */
    public function getAdjustmentTypeBadgeColor(): string
    {
        return match($this->adjustment_type) {
            'addition' => 'green',
            'subtraction' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get adjustment type label.
     */
    public function getAdjustmentTypeLabel(): string
    {
        return match($this->adjustment_type) {
            'addition' => 'Addition',
            'subtraction' => 'Subtraction',
            default => 'Unknown'
        };
    }

    /**
     * Get formatted adjustment type with icon.
     */
    public function getFormattedAdjustmentType(): string
    {
        $icon = $this->adjustment_type === 'addition' ? '↗' : '↘';
        return $icon . ' ' . $this->getAdjustmentTypeLabel();
    }
}
