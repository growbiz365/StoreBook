<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseAuditLog extends Model
{
    protected $fillable = [
        'business_id',
        'purchase_id',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
    ];

    // Relationships
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    // Accessor Methods
    public function getFormattedActionAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M d, Y H:i:s');
    }
}
