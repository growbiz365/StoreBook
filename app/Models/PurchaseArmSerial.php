<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseArmSerial extends Model
{
    protected $fillable = [
        'purchase_arm_line_id',
        'serial_no',
        'arm_title',
        'make_id',
        'caliber_id',
        'category_id',
        'purchase_price',
        'sale_price',
        'purchase_date',
        'extra',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'purchase_date' => 'date',
        'extra' => 'array',
    ];

    // Relationships
    public function purchaseArmLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseArmLine::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(ArmsMake::class);
    }

    public function caliber(): BelongsTo
    {
        return $this->belongsTo(ArmsCaliber::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArmsCategory::class);
    }

    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class, 'serial_no', 'serial_no');
    }

    // Business Logic Methods
    public function getEffectivePurchasePrice(): float
    {
        return $this->purchase_price ?? $this->purchaseArmLine->unit_price;
    }

    public function generateArmTitle(): string
    {
        if ($this->arm_title) {
            return $this->arm_title;
        }

        $parts = [];
        
        if ($this->make) {
            $parts[] = $this->make->make;
        }
        
        if ($this->caliber) {
            $parts[] = $this->caliber->arm_caliber;
        }
        
        if ($this->category) {
            $parts[] = $this->category->arm_category;
        }
        
        $parts[] = '(SN: ' . $this->serial_no . ')';
        
        return implode(' ', $parts);
    }

    public function isSerialExists(): bool
    {
        return Arm::where('serial_no', $this->serial_no)
                   ->where('business_id', $this->purchaseArmLine->purchase->business_id)
                   ->exists();
    }

    public function getFormattedPurchasePriceAttribute(): string
    {
        return number_format($this->purchase_price ?? 0, 2);
    }

    public function getFormattedPurchaseDateAttribute(): string
    {
        return $this->purchase_date ? $this->purchase_date->format('M d, Y') : 'N/A';
    }

    // Boot method to auto-generate arm_title
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($serial) {
            if (empty($serial->arm_title)) {
                $serial->arm_title = $serial->generateArmTitle();
            }
        });
    }
}
