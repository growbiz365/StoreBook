<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalArm extends Model
{
    protected $fillable = [
        'approval_id',
        'arm_id',
        'sale_price',
        'status',
        'returned_date',
        'sold_date',
        'sale_invoice_id',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'returned_date' => 'date',
        'sold_date' => 'date',
    ];

    // Relationships
    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }

    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class);
    }

    public function saleInvoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class);
    }

    // Business Logic Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'returned' => 'blue',
            'sold' => 'green',
            default => 'gray'
        };
    }
}
