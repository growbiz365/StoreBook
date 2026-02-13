<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArmsStockLedger extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'arms_stock_ledger';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'business_id',
        'arm_id',
        'transaction_date',
        'transaction_type',
        'quantity_in',
        'quantity_out',
        'balance',
        'reference_id',
        'remarks',
        'purchase_id',
        'purchase_arm_serial_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'transaction_date' => 'date',
        'quantity_in' => 'integer',
        'quantity_out' => 'integer',
        'balance' => 'integer',
    ];

    /**
     * Transaction types constants
     */
    const TRANSACTION_TYPES = [
        'opening_stock' => 'Opening Stock',
        'purchase' => 'Purchase',
        'sale' => 'Sale',
        'adjustment' => 'Adjustment',
        'transfer' => 'Transfer',
        'return' => 'Return',
        'damage' => 'Damage',
        'theft' => 'Theft',
        'other' => 'Other',
    ];

    /**
     * Get the business that owns the stock ledger entry.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the arm that owns the stock ledger entry.
     */
    public function arm(): BelongsTo
    {
        return $this->belongsTo(Arm::class);
    }

    /**
     * Scope a query to only include entries for a specific business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope a query to only include entries for a specific arm.
     */
    public function scopeForArm($query, $armId)
    {
        return $query->where('arm_id', $armId);
    }

    /**
     * Scope a query to only include entries of a specific transaction type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope a query to only include entries within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Get the transaction type display name.
     */
    public function getTransactionTypeDisplayName(): string
    {
        return self::TRANSACTION_TYPES[$this->transaction_type] ?? ucfirst(str_replace('_', ' ', $this->transaction_type));
    }

    /**
     * Get the transaction type badge color.
     */
    public function getTransactionTypeBadgeColor(): string
    {
        return match($this->transaction_type) {
            'opening_stock' => 'green',
            'purchase' => 'blue',
            'sale' => 'red',
            'adjustment' => 'yellow',
            'transfer' => 'purple',
            'return' => 'indigo',
            'damage' => 'red',
            'theft' => 'red',
            'other' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get the current balance for a specific arm.
     */
    public static function getCurrentBalance(int $armId, int $businessId): int
    {
        $latestEntry = self::forBusiness($businessId)
            ->forArm($armId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $latestEntry ? $latestEntry->balance : 0;
    }

    /**
     * Create a new stock ledger entry and update the balance.
     */
    public static function createEntry(array $data): self
    {
        // Get the current balance for this arm
        $currentBalance = self::getCurrentBalance($data['arm_id'], $data['business_id']);
        
        // Calculate new balance
        $quantityIn = $data['quantity_in'] ?? 0;
        $quantityOut = $data['quantity_out'] ?? 0;
        $newBalance = $currentBalance + $quantityIn - $quantityOut;
        
        // Create the ledger entry
        return self::create([
            ...$data,
            'balance' => $newBalance,
        ]);
    }

    /**
     * Update the balance for all subsequent entries after a specific entry.
     */
    public static function recalculateBalances(int $armId, int $businessId, ?int $fromEntryId = null): void
    {
        $query = self::forBusiness($businessId)
            ->forArm($armId)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        if ($fromEntryId) {
            $query->where('id', '>=', $fromEntryId);
        }

        $entries = $query->get();
        $runningBalance = $fromEntryId ? 
            self::getCurrentBalance($armId, $businessId) - $entries->sum('quantity_in') + $entries->sum('quantity_out') :
            0;

        foreach ($entries as $entry) {
            $quantityIn = $entry->quantity_in ?? 0;
            $quantityOut = $entry->quantity_out ?? 0;
            $runningBalance += $quantityIn - $quantityOut;
            
            $entry->update(['balance' => $runningBalance]);
        }
    }
}
