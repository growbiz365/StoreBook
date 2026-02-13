<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArmHistory extends Model
{
    protected $table = 'arms_history';

    protected $fillable = [
        'business_id',
        'arm_id',
        'action',
        'old_values',
        'new_values',
        'transaction_date',
        'price',
        'remarks',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'price' => 'decimal:2',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the business that owns the history record.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the arm associated with this history record.
     */
    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class);
    }



    /**
     * Get the user who created this history record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action badge color.
     */
    public function getActionBadgeColor(): string
    {
        return match($this->action) {
            'opening' => 'blue',
            'purchase' => 'green',
            'sale' => 'red',
            'transfer' => 'purple',
            'repair' => 'yellow',
            'decommission' => 'gray',
            'price_adjustment' => 'orange',
            'edit' => 'indigo',
            'cancel' => 'red',
            'delete' => 'red',
            'return' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get action display name.
     */
    public function getActionDisplayName(): string
    {
        return match($this->action) {
            'opening' => 'Opening Stock',
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'transfer' => 'Transfer',
            'repair' => 'Repair',
            'decommission' => 'Decommission',
            'price_adjustment' => 'Price Adjustment',
            'edit' => 'Edit',
            'cancel' => 'Cancel',
            'delete' => 'Delete',
            'return' => 'Return',
            default => ucfirst($this->action)
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
     * Scope to filter by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by arm.
     */
    public function scopeByArm($query, $armId)
    {
        return $query->where('arm_id', $armId);
    }

    /**
     * Scope to order by transaction date.
     */
    public function scopeOrderByDate($query, $direction = 'desc')
    {
        return $query->orderBy('transaction_date', $direction);
    }
}
