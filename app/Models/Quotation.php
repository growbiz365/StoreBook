<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'party_id',
        'quotation_date',
        'valid_until',
        'payment_type',
        'bank_id',
        'subtotal',
        'shipping_charges',
        'total_amount',
        'status',
        'created_by',
        'converted_to_sale_id',
        'rejected_at',
        'rejected_by',
        'rejected_reason',
        'notes',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'rejected_at' => 'datetime',
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

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function convertedToSale(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class, 'converted_to_sale_id');
    }

    public function generalLines(): HasMany
    {
        return $this->hasMany(QuotationGeneralItem::class);
    }

    public function armLines(): HasMany
    {
        return $this->hasMany(QuotationArm::class);
    }

    // Scopes
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('quotation_date', [$startDate, $endDate]);
    }

    public function scopeByCustomer($query, $partyId)
    {
        return $query->where('party_id', $partyId);
    }

    // Status Check Methods
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->valid_until < today();
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    // Business Logic Methods
    public function canBeConverted(): bool
    {
        return $this->isSent() && 
               !$this->isExpired() && 
               !$this->isRejected() && 
               $this->valid_until >= today() &&
               ($this->generalLines()->count() > 0 || $this->armLines()->count() > 0);
    }

    public function canBeRejected(): bool
    {
        return $this->isSent() && !$this->isConverted();
    }

    public function canBeExpired(): bool
    {
        return $this->isSent() && $this->valid_until < today();
    }

    public function canBeEdited(): bool
    {
        return $this->isSent() && !$this->isConverted();
    }

    public function canBeDeleted(): bool
    {
        return !$this->isConverted();
    }

    // Calculation Methods
    public function calculateTotals(): void
    {
        $subtotal = 0;

        // Calculate general items
        foreach ($this->generalLines as $line) {
            $subtotal += $line->quantity * $line->sale_price;
        }

        // Calculate arms
        foreach ($this->armLines as $line) {
            $subtotal += $line->sale_price;
        }

        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal + $this->shipping_charges;
    }

    // Attribute Accessors
    public function getQuotationNumberAttribute(): string
    {
        return 'QT-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getFormattedQuotationDateAttribute(): string
    {
        return $this->quotation_date->format('d M Y');
    }

    public function getFormattedValidUntilAttribute(): string
    {
        return $this->valid_until->format('d M Y');
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }

    public function getFormattedShippingChargesAttribute(): string
    {
        return number_format($this->shipping_charges, 2);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'sent' => 'blue',
            'expired' => 'orange',
            'rejected' => 'red',
            'converted' => 'green',
            default => 'gray'
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return ucfirst($this->payment_type);
    }

    // Check if quotation is expiring soon (within 7 days)
    public function isExpiringSoon(): bool
    {
        if ($this->isSent()) {
            $daysUntilExpiry = today()->diffInDays($this->valid_until, false);
            return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 7;
        }
        return false;
    }

    public function getDaysUntilExpiry(): int
    {
        return today()->diffInDays($this->valid_until, false);
    }
}

