<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralItemStockLedger extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'general_items_stock_ledger';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'business_id',
        'general_item_id',
        'batch_id',
        'transaction_type',
        'transaction_date',
        'quantity',
        'quantity_in',
        'quantity_out',
        'balance_quantity',
        'unit_cost',
        'total_cost',
        'reference_no',
        'reference_id',
        'remarks',
        'created_by',
        'purchase_id',
        'purchase_line_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity' => 'decimal:2',
        'quantity_in' => 'decimal:2',
        'quantity_out' => 'decimal:2',
        'balance_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Transaction types constants
     */
    const TRANSACTION_TYPES = [
        'opening' => 'Opening Stock',
        'purchase' => 'Purchase',
        'issue' => 'Issue',
        'sale' => 'Sale',
        'adjustment' => 'Adjustment',
        'reversal' => 'Reversal',
        'edit' => 'Edit',
        'stock_adjustment' => 'Stock Adjustment',
        'return' => 'Return',
    ];

    /**
     * Get the business that owns the stock ledger entry.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the general item that owns the stock ledger entry.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(GeneralItem::class, 'general_item_id');
    }

    /**
     * Get the batch that owns the stock ledger entry.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(GeneralBatch::class, 'batch_id');
    }

    /**
     * Get the user who created the stock ledger entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include entries for a specific business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope a query to only include entries for a specific item.
     */
    public function scopeForItem($query, $itemId)
    {
        return $query->where('general_item_id', $itemId);
    }

    /**
     * Scope a query to only include entries for a specific batch.
     */
    public function scopeForBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
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
            'opening' => 'green',
            'purchase' => 'blue',
            'issue' => 'orange',
            'sale' => 'red',
            'adjustment' => 'yellow',
            'reversal' => 'purple',
            'edit' => 'indigo',
            'stock_adjustment' => 'green',
            'return' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the current balance for a specific item.
     */
    public static function getCurrentBalance(int $itemId): float
    {
        $businessId = session('active_business');
        
        $latestEntry = self::forItem($itemId)
            ->where('business_id', $businessId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $latestEntry ? (float) $latestEntry->balance_quantity : 0.0;
    }

    /**
     * Create a new stock ledger entry and update the balance.
     */
    public static function createEntry(array $data): self
    {
        // Get the current balance for this item
        $currentBalance = self::getCurrentBalance($data['general_item_id']);
        
        // Calculate quantity_in and quantity_out from quantity
        $quantity = $data['quantity'] ?? 0;
        $quantityIn = $quantity > 0 ? $quantity : 0;
        $quantityOut = $quantity < 0 ? abs($quantity) : 0;
        
        // Calculate new balance
        $newBalance = $currentBalance + $quantityIn - $quantityOut;
        
        // Create the ledger entry
        return self::create([
            ...$data,
            'quantity_in' => $quantityIn,
            'quantity_out' => $quantityOut,
            'balance_quantity' => $newBalance,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Update the balance for all subsequent entries after a specific entry.
     */
    public static function recalculateBalances(int $itemId, ?int $fromEntryId = null): void
    {
        $query = self::forItem($itemId)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        if ($fromEntryId) {
            $query->where('id', '>=', $fromEntryId);
        }

        $entries = $query->get();
        
        // Start with 0 balance and recalculate from the beginning
        $runningBalance = 0;

        foreach ($entries as $entry) {
            $quantityIn = (float) ($entry->quantity_in ?? 0);
            $quantityOut = (float) ($entry->quantity_out ?? 0);
            $runningBalance += $quantityIn - $quantityOut;
            
            $entry->update(['balance_quantity' => $runningBalance]);
        }
        
        \Log::info('Recalculated balances for item', [
            'item_id' => $itemId,
            'final_balance' => $runningBalance,
            'entries_processed' => $entries->count()
        ]);
    }

    /**
     * Get stock balance per item using SQL aggregation.
     */
    public static function getStockBalancePerItem(): \Illuminate\Database\Eloquent\Collection
    {
        $businessId = session('active_business');
        
        return self::selectRaw('
                general_item_id,
                SUM(quantity_in) as total_in,
                SUM(quantity_out) as total_out,
                SUM(quantity_in) - SUM(quantity_out) as balance
            ')
            ->where('business_id', $businessId)
            ->groupBy('general_item_id')
            ->get();
    }

    /**
     * Get stock balance for a specific item.
     */
    public static function getStockBalance(int $itemId): array
{
    $businessId = session('active_business');

    $result = self::where('business_id', $businessId)
        ->where('general_item_id', $itemId)
        ->selectRaw('
            COALESCE(SUM(quantity),0) as total_quantity,
            COALESCE(SUM(quantity_in),0) as total_in,
            COALESCE(SUM(quantity_out),0) as total_out
        ')
        ->first();

    return [
        'total_in' => (float) $result->total_in,
        'total_out' => (float) $result->total_out,
        'balance' => (float) $result->total_quantity, // ✅ true stock
    ];
}


    /**
     * Get FIFO allocation for a specific item and quantity.
     */
    public static function getFIFOAllocation(int $itemId, float $requiredQty): array
    {
        $batches = GeneralBatch::where('item_id', $itemId)
            ->where('qty_remaining', '>', 0)
            ->orderBy('received_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $allocation = [];
        $remainingQty = $requiredQty;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;

            $allocatedQty = min($batch->qty_remaining, $remainingQty);
            
            $allocation[] = [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'quantity' => $allocatedQty,
                'unit_cost' => $batch->unit_cost,
                'total_cost' => $allocatedQty * $batch->unit_cost,
                'received_date' => $batch->received_date,
            ];

            $remainingQty -= $allocatedQty;
        }

        return $allocation;
    }

    /**
     * Create opening stock entry.
     */
    public static function createOpeningStockEntry(array $data): self
    {
        return self::createEntry([
            'business_id' => $data['business_id'],
            'general_item_id' => $data['general_item_id'],
            'batch_id' => $data['batch_id'],
            'transaction_type' => 'opening',
            'transaction_date' => $data['transaction_date'] ?? now(),
            'quantity' => $data['quantity'],
            'unit_cost' => $data['unit_cost'],
            'total_cost' => $data['total_cost'],
            'reference_id' => $data['reference_id'] ?? 'OPEN-' . $data['general_item_id'],
            'remarks' => $data['remarks'] ?? 'Opening stock entry',
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Create purchase entry.
     */
    public static function createPurchaseEntry(array $data): self
    {
        return self::createEntry([
            'business_id' => $data['business_id'],
            'general_item_id' => $data['general_item_id'],
            'batch_id' => $data['batch_id'],
            'transaction_type' => 'purchase',
            'transaction_date' => $data['transaction_date'] ?? now(),
            'quantity' => $data['quantity'],
            'unit_cost' => $data['unit_cost'],
            'total_cost' => $data['total_cost'],
            'reference_id' => $data['reference_id'],
            'remarks' => $data['remarks'] ?? 'Purchase entry',
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Create sale entry with FIFO allocation.
     */
    public static function createSaleEntry(array $data): array
    {
        $fifoAllocation = self::getFIFOAllocation($data['general_item_id'], $data['quantity']);
        $entries = [];

        foreach ($fifoAllocation as $allocation) {
            $entry = self::createEntry([
                'business_id' => $data['business_id'],
                'general_item_id' => $data['general_item_id'],
                'batch_id' => $allocation['batch_id'],
                'transaction_type' => 'sale',
                'transaction_date' => $data['transaction_date'] ?? now(),
                'quantity' => -$allocation['quantity'], // Negative for sale
                'unit_cost' => $allocation['unit_cost'],
                'total_cost' => $allocation['total_cost'],
                'reference_id' => $data['reference_id'],
                'remarks' => $data['remarks'] ?? 'Sale entry (FIFO)',
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            $entries[] = $entry;

            // Update batch remaining quantity
            $batch = GeneralBatch::find($allocation['batch_id']);
            $batch->update(['qty_remaining' => $batch->qty_remaining - $allocation['quantity']]);
        }

        return $entries;
    }

    /**
     * Create adjustment entry.
     */
    public static function createAdjustmentEntry(array $data): self
    {
        return self::createEntry([
            'business_id' => $data['business_id'],
            'general_item_id' => $data['general_item_id'],
            'batch_id' => $data['batch_id'] ?? null,
            'transaction_type' => 'adjustment',
            'transaction_date' => $data['transaction_date'] ?? now(),
            'quantity' => $data['quantity'],
            'unit_cost' => $data['unit_cost'],
            'total_cost' => $data['total_cost'],
            'reference_id' => $data['reference_id'],
            'remarks' => $data['remarks'] ?? 'Stock adjustment',
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }
}
