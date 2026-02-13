<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Arm extends Model
{
    protected $fillable = [
        'business_id',
        'arm_type_id',
        'arm_category_id',
        'make',
        'arm_caliber_id',
        'arm_condition_id',
        'serial_no',
        'purchase_price',
        'sale_price',
        'purchase_date',
        'status',
        'notes',
        'arm_title',
        'purchase_id',
        'purchase_arm_serial_id',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    /**
     * Get the business that owns the arm.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the arm type.
     */
    public function armType(): BelongsTo
    {
        return $this->belongsTo(ArmsType::class, 'arm_type_id');
    }

    /**
     * Get the arm category.
     */
    public function armCategory(): BelongsTo
    {
        return $this->belongsTo(ArmsCategory::class, 'arm_category_id');
    }

    /**
     * Get the arm caliber.
     */
    public function armCaliber(): BelongsTo
    {
        return $this->belongsTo(ArmsCaliber::class, 'arm_caliber_id');
    }

    /**
     * Get the arm condition.
     */
    public function armCondition(): BelongsTo
    {
        return $this->belongsTo(ArmsCondition::class, 'arm_condition_id');
    }

    /**
     * Get the arm make.
     */
    public function armMake(): BelongsTo
    {
        return $this->belongsTo(ArmsMake::class, 'make');
    }

    /**
     * Get the arm make name (handles both ID and name cases).
     */
    public function getMakeNameAttribute(): string
    {
        // If make is numeric, it's an ID - use the relationship
        if (is_numeric($this->make)) {
            return $this->armMake?->arm_make ?? 'N/A';
        }
        
        // If make is a string, it's already the make name
        return $this->make ?? 'N/A';
    }

    /**
     * Get the arm history records.
     */
    public function history(): HasMany
    {
        return $this->hasMany(ArmHistory::class);
    }

    /**
     * Get the stock ledger entries for this arm.
     */
    public function stockLedger(): HasMany
    {
        return $this->hasMany(ArmsStockLedger::class);
    }

    /**
     * Generate arm title automatically.
     */
    public function generateArmTitle(): string
    {
        $makeName = is_numeric($this->make)
            ? ($this->armMake?->arm_make ?? 'Unknown')
            : ($this->make ?? 'Unknown');

        $caliber = $this->armCaliber?->arm_caliber ?? 'Caliber';
        $type = $this->armType?->arm_type ?? 'Arm';
        $serial = $this->serial_no ?? 'N/A';

        return trim(sprintf('%s %s %s (SN: %s)', $makeName, $caliber, $type, $serial));
    }

    /**
     * Check if arm is available for sale/transfer.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'available' => 'green',
            'sold' => 'red',
            'under_repair' => 'yellow',
            'decommissioned' => 'gray',
            'pending_approval' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Scope to filter by business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter available arms.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to search arms.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('serial_no', 'like', '%' . $search . '%')
              ->orWhere('arm_title', 'like', '%' . $search . '%')
              ->orWhere('make', 'like', '%' . $search . '%')
              ->orWhere('notes', 'like', '%' . $search . '%');
        });
    }

    // Purchase relationships
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseArmSerial(): BelongsTo
    {
        return $this->belongsTo(PurchaseArmSerial::class);
    }

    // Business Logic Methods
    public function isPurchased(): bool
    {
        return $this->purchase_id !== null;
    }

    public function getPurchaseReferenceAttribute(): string
    {
        if ($this->purchase) {
            return 'PUR-' . $this->purchase_id;
        }
        return 'N/A';
    }
}
