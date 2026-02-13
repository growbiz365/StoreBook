<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseArmLine extends Model
{
    protected $fillable = [
        'purchase_id',
        'line_no',
        'description',
        'qty',
        'unit_price',
        'sale_price',
        'arm_type_id',
        'arm_make_id',
        'arm_caliber_id',
        'arm_category_id',
        'arm_condition_id',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // Relationships
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function armSerials(): HasMany
    {
        return $this->hasMany(PurchaseArmSerial::class);
    }

    public function armType(): BelongsTo
    {
        return $this->belongsTo(ArmsType::class, 'arm_type_id');
    }

    public function armMake(): BelongsTo
    {
        return $this->belongsTo(ArmsMake::class, 'arm_make_id');
    }

    public function armCaliber(): BelongsTo
    {
        return $this->belongsTo(ArmsCaliber::class, 'arm_caliber_id');
    }

    public function armCategory(): BelongsTo
    {
        return $this->belongsTo(ArmsCategory::class, 'arm_category_id');
    }

    public function armCondition(): BelongsTo
    {
        return $this->belongsTo(ArmsCondition::class, 'arm_condition_id');
    }

    // Business Logic Methods
    public function getLineTotalAttribute(): float
    {
        return $this->qty * $this->unit_price;
    }

    public function getSerialsCountAttribute(): int
    {
        return $this->armSerials()->count();
    }

    public function getAveragePurchasePriceAttribute(): float
    {
        $serials = $this->armSerials;
        if ($serials->isEmpty()) {
            return $this->unit_price;
        }

        $totalPrice = $serials->sum('purchase_price');
        return $totalPrice / $serials->count();
    }

    public function validateSerialsCount(): bool
    {
        return $this->armSerials()->count() === $this->qty;
    }

    public function getMissingSerialsCountAttribute(): int
    {
        return $this->qty - $this->armSerials()->count();
    }

    public function hasAllSerials(): bool
    {
        return $this->validateSerialsCount();
    }

    public function getSalePriceFromDatabase(): float
    {
        // This would typically come from a default sale price setting
        // For now, return a calculated sale price based on unit price
        return $this->unit_price * 1.25; // 25% markup as default
    }

    public function getUnitPriceFromDatabase(): float
    {
        // This would typically come from a default cost price setting
        return $this->unit_price ?? 0;
    }

    // Accessor Methods
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 2);
    }

    public function getFormattedLineTotalAttribute(): string
    {
        return number_format($this->line_total, 2);
    }
}
