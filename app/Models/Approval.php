<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Approval extends Model
{
    protected $fillable = [
        'business_id',
        'party_id',
        'approval_date',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'approval_date' => 'date',
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function arms(): HasMany
    {
        return $this->hasMany(ApprovalArm::class);
    }

    public function generalItems(): HasMany
    {
        return $this->hasMany(ApprovalGeneralItem::class);
    }

    // Scopes
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Business Logic Methods
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function getApprovalNumberAttribute(): string
    {
        return 'APP-' . $this->id;
    }

    public function getTotalApprovedValueAttribute(): float
    {
        $total = 0;
        
        // Add general items total
        foreach ($this->generalItems as $item) {
            $total += $item->line_total;
        }
        
        // Add arms total
        foreach ($this->arms as $arm) {
            $total += $arm->sale_price;
        }
        
        return $total;
    }

    public function getTotalSoldValueAttribute(): float
    {
        $total = 0;
        
        // Add sold general items total
        foreach ($this->generalItems as $item) {
            $total += $item->sold_quantity * $item->sale_price;
        }
        
        // Add sold arms total
        foreach ($this->arms as $arm) {
            if ($arm->status === 'sold') {
                $total += $arm->sale_price;
            }
        }
        
        return $total;
    }

    public function getTotalReturnedValueAttribute(): float
    {
        $total = 0;
        
        // Add returned general items total
        foreach ($this->generalItems as $item) {
            $total += $item->returned_quantity * $item->sale_price;
        }
        
        // Add returned arms total
        foreach ($this->arms as $arm) {
            if ($arm->status === 'returned') {
                $total += $arm->sale_price;
            }
        }
        
        return $total;
    }

    public function getRemainingValueAttribute(): float
    {
        return $this->total_approved_value - $this->total_sold_value - $this->total_returned_value;
    }

    public function checkAndUpdateStatus(): void
    {
        $hasPendingItems = false;

        // Check general items
        foreach ($this->generalItems as $item) {
            if ($item->remaining_quantity > 0) {
                $hasPendingItems = true;
                break;
            }
        }

        // Check arms if no pending items found
        if (!$hasPendingItems) {
            foreach ($this->arms as $arm) {
                if ($arm->status === 'pending') {
                    $hasPendingItems = true;
                    break;
                }
            }
        }

        // Update status
        if (!$hasPendingItems) {
            $this->update(['status' => 'closed']);
        } else {
            $this->update(['status' => 'open']);
        }
    }
}
