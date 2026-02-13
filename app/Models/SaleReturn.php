<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralItemStockLedger;
use App\Models\ArmsStockLedger;
use App\Models\ArmHistory;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Models\GeneralBatch;
use App\Models\SaleReturnAuditLog;
use App\Models\PartyLedger;

class SaleReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'party_id',
        'return_type',
        'bank_id',
        'original_sale_invoice_id',
        'return_date',
        'subtotal',
        'shipping_charges',
        'total_amount',
        'status',
        'reason',
        'created_by',
        'posted_by',
        'cancelled_by',
        'deleted_by',
        'name_of_customer',
        'father_name',
        'contact',
        'address',
        'cnic',
        'licence_no',
        'licence_issue_date',
        'licence_valid_upto',
        'licence_issued_by',
        're_reg_no',
        'dc',
        'Date',
        // Party License Details
        'party_license_no',
        'party_license_issue_date',
        'party_license_valid_upto',
        'party_license_issued_by',
        'party_re_reg_no',
        'party_dc',
        'party_dc_date',
    ];

    protected $casts = [
        'return_date' => 'date',
        'subtotal' => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'licence_issue_date' => 'date',
        'licence_valid_upto' => 'date',
        'Date' => 'date',
        // Party License Details
        'party_license_issue_date' => 'date',
        'party_license_valid_upto' => 'date',
        'party_dc_date' => 'date',
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

    public function originalSaleInvoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class, 'original_sale_invoice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function generalLines(): HasMany
    {
        return $this->hasMany(SaleReturnGeneralItem::class);
    }

    public function armLines(): HasMany
    {
        return $this->hasMany(SaleReturnArm::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(SaleReturnAuditLog::class);
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

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }

    public function scopeByCustomer($query, $partyId)
    {
        return $query->where('party_id', $partyId);
    }

    // Business Logic Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBePosted(): bool
    {
        return $this->isDraft() && !$this->isCancelled() && ($this->generalLines()->count() > 0 || $this->armLines()->count() > 0);
    }

    public function canBeCancelled(): bool
    {
        return $this->isPosted() && !$this->isCancelled();
    }

    public function canBeEdited(): bool
    {
        return ($this->isDraft() || $this->isPosted()) && !$this->isCancelled();
    }

    public function canBeEditedAfterPosting(): bool
    {
        return $this->isPosted() && !$this->isCancelled();
    }

    public function calculateTotals(): void
    {
        $subtotal = 0;

        // Calculate general items
        foreach ($this->generalLines as $line) {
            $subtotal += $line->quantity * $line->return_price;
        }

        // Calculate arms
        foreach ($this->armLines as $line) {
            $subtotal += $line->return_price;
        }

        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal + $this->shipping_charges;
    }

    public function getReturnNumberAttribute(): string
    {
        return 'SR-' . $this->id;
    }

    public function getFormattedReturnDateAttribute(): string
    {
        return $this->return_date->format('d M Y');
    }

    // Enhanced Edit and Delete Methods
    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['draft', 'posted', 'cancelled']) && !$this->trashed();
    }

    public function isDeleted(): bool
    {
        return $this->trashed();
    }

    /**
     * Enhanced edit method that properly handles stock reversal and reapplication
     */
    public function performEnhancedEdit(array $newGeneralLines, array $newArmLines, array $returnData): void
    {
        $wasPosted = $this->isPosted();

        if ($wasPosted) {
            // Step 1: Check if there are meaningful changes
            $hasChanges = $this->hasMeaningfulChanges($newGeneralLines, $newArmLines, $returnData);

            if ($hasChanges) {
                // Step 2: Store original values for comparison
                $originalTotal = $this->total_amount;
                $originalReturnType = $this->return_type;
                $originalBankId = $this->bank_id;

                // Step 3: Reverse original stock and journal entries
                $this->reverseInventoryImpacts();
                $this->reverseJournalEntriesForEdit();

                // Step 4: Update return data
                $this->update($returnData);

                // Step 5: Delete existing lines
                $this->generalLines()->delete();
                $this->armLines()->delete();

                // Step 6: Create new lines
                if (!empty($newGeneralLines)) {
                    foreach ($newGeneralLines as $line) {
                        $this->generalLines()->create([
                            'general_item_id' => $line['general_item_id'],
                            'quantity' => $line['qty'],
                            'return_price' => $line['sale_price'],
                        ]);
                    }
                }

                if (!empty($newArmLines)) {
                    foreach ($newArmLines as $line) {
                        $this->armLines()->create([
                            'arm_id' => $line['arm_id'],
                            'return_price' => $line['sale_price'],
                        ]);
                    }
                }

                // Step 7: Refresh relationships and recalculate totals
                $this->load(['generalLines', 'armLines']);
                $this->calculateTotals();
                $this->save();

                // Step 8: Create new stock ledger entries for the new quantities
                $this->createStockLedgerEntriesForEdit();

                // Step 9: Check if final result is different from original
                $finalTotal = $this->total_amount;
                $finalReturnType = $this->return_type;
                $finalBankId = $this->bank_id;

                $isFinalResultDifferent = (
                    $originalTotal != $finalTotal ||
                    $originalReturnType != $finalReturnType ||
                    $originalBankId != $finalBankId
                );

                // Step 9: Status is already posted, no need to call postSaleReturn() again
                // The stock ledger entries and journal entries were already created in Step 8
            } else {
                // No meaningful changes - just update the return data without affecting stock/journal
                $this->update($returnData);
                $this->save();
            }
        } else {
            // Not posted - just update normally
            $this->update($returnData);

            // Delete existing lines
            $this->generalLines()->delete();
            $this->armLines()->delete();

            // Create new lines
            if (!empty($newGeneralLines)) {
                foreach ($newGeneralLines as $line) {
                    $this->generalLines()->create([
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'return_price' => $line['sale_price'],
                    ]);
                }
            }

            if (!empty($newArmLines)) {
                foreach ($newArmLines as $line) {
                    $this->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'return_price' => $line['sale_price'],
                    ]);
                }
            }

            // Refresh relationships and recalculate totals
            $this->load(['generalLines', 'armLines']);
            $this->calculateTotals();
            $this->save();
        }

        // Step 8: Create audit log
        SaleReturnAuditLog::create([
            'sale_return_id' => $this->id,
            'action' => 'enhanced_edit',
            'old_values' => null, // We'll store this in the controller
            'new_values' => $this->toArray(),
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Check if there are meaningful changes that require stock/journal reversal
     */
    private function hasMeaningfulChanges(array $newGeneralLines, array $newArmLines, array $returnData): bool
    {
        // Check if return data has meaningful changes (excluding status changes)
        $returnFields = ['party_id', 'return_type', 'bank_id', 'return_date', 'shipping_charges', 'reason'];
        foreach ($returnFields as $field) {
            if (isset($returnData[$field]) && $this->$field != $returnData[$field]) {
                return true;
            }
        }

        // Check if general lines have changed
        $currentGeneralLines = $this->generalLines->map(function ($line) {
            return [
                'general_item_id' => $line->general_item_id,
                'quantity' => $line->quantity,
                'return_price' => $line->return_price,
            ];
        })->toArray();

        $newGeneralLinesNormalized = array_map(function ($line) {
            return [
                'general_item_id' => $line['general_item_id'],
                'quantity' => $line['qty'],
                'return_price' => $line['return_price'],
            ];
        }, $newGeneralLines);

        if ($currentGeneralLines !== $newGeneralLinesNormalized) {
            return true;
        }

        // Check if arm lines have changed
        $currentArmLines = $this->armLines->map(function ($line) {
            return [
                'arm_id' => $line->arm_id,
                'return_price' => $line->return_price,
            ];
        })->toArray();

        if ($currentArmLines !== $newArmLines) {
            return true;
        }

        return false;
    }

    /**
     * Enhanced delete method that properly handles stock reversal
     */
    public function performSoftDelete(): void
    {
        if ($this->isPosted()) {
            // Step 1: Reverse stock impacts
            $this->reverseInventoryImpacts();

            // Step 2: Reverse journal entries
            $this->reverseJournalEntries();
        }

        // Step 3: Soft delete child records
        $this->generalLines()->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id()
        ]);

        $this->armLines()->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id()
        ]);

        $this->auditLogs()->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id()
        ]);

        // Step 4: Soft delete the main return
        $this->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id()
        ]);

        // Step 5: Create audit log for deletion
        SaleReturnAuditLog::create([
            'sale_return_id' => $this->id,
            'action' => 'soft_deleted',
            'old_values' => $this->toArray(),
            'new_values' => ['deleted_at' => now(), 'deleted_by' => auth()->id()],
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Reverse inventory impacts for both general items and arms
     */
    public function reverseInventoryImpacts(): void
    {
        $businessId = $this->business_id;
        $userId = auth()->id();

        // Load fresh relationships to ensure we have the current data
        $this->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm']);

        // Reverse general item stock ledger entries
        foreach ($this->generalLines as $line) {
            if ($line->batch) {
                // Create reversal stock ledger entry
                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $line->general_item_id,
                    'batch_id' => $line->batch_id,
                    'transaction_type' => 'reversal',
                    'transaction_date' => now(),
                    'quantity' => -$line->quantity, // Negative quantity to reverse the return
                    'quantity_out' => $line->quantity,
                    'balance_quantity' => $line->batch->qty_remaining - $line->quantity,
                    'unit_cost' => $line->batch->unit_cost,
                    'total_cost' => -($line->quantity * $line->batch->unit_cost),
                    'reference_id' => $this->id,
                    'reference_no' => $this->return_number . '-REV',
                    'remarks' => 'Return reversal for ' . ($this->party->name ?? 'Customer'),
                    'created_by' => $userId,
                ]);

                // Reduce batch remaining quantity (reverse the original return)
                $line->batch->decrement('qty_remaining', $line->quantity);
            }
        }

        // Reverse arms stock ledger entries
        foreach ($this->armLines as $line) {
            // Get the arm model to ensure we have fresh data
            $arm = Arm::find($line->arm_id);

            if (!$arm) {
                \Log::warning('Arm not found during reversal', ['arm_id' => $line->arm_id]);
                continue;
            }

            // Create reversal arms stock ledger entry
            ArmsStockLedger::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'transaction_date' => now(),
                'transaction_type' => 'reversal',
                'quantity_out' => 1,
                'balance' => 0, // Reverse arm back to sold
                'reference_id' => $this->return_number . '-REV',
                'remarks' => 'Return reversal for ' . ($this->party->name ?? 'Customer'),
            ]);

            // Reverse arm status back to sold
            $arm->update([
                'status' => 'sold',
                'sold_date' => $this->return_date,
            ]);

            // Create arm history entry for reversal
            ArmHistory::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'action' => 'cancel',
                'old_values' => ['status' => 'available', 'sold_date' => null],
                'new_values' => ['status' => 'sold', 'sold_date' => $this->return_date],
                'transaction_date' => now(),
                'price' => $line->return_price,
                'remarks' => 'Return reversal for ' . ($this->party->name ?? 'Customer'),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Create stock ledger entries for sale return edit (without changing status)
     */
    private function createStockLedgerEntriesForEdit(): void
    {
        $businessId = $this->business_id;
        $userId = auth()->id();

        // Load fresh relationships to ensure we have the current data
        $this->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm']);

        // Create stock ledger entries for general items (restore inventory)
        foreach ($this->generalLines as $line) {
            // Find the batch that was originally consumed (if available)
            $batch = GeneralBatch::where('item_id', $line->general_item_id)
                ->where('qty_remaining', '>=', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$batch) {
                \Log::warning('No batch found for general item in sale return edit', [
                    'sale_return_id' => $this->id,
                    'general_item_id' => $line->general_item_id,
                    'quantity' => $line->quantity
                ]);
                continue;
            }

            // Create stock ledger entry (positive quantity to restore stock)
            GeneralItemStockLedger::create([
                'business_id' => $businessId,
                'general_item_id' => $line->general_item_id,
                'batch_id' => $batch->id,
                'transaction_type' => 'return',
                'transaction_date' => $this->return_date,
                'quantity' => $line->quantity, // Positive quantity to restore stock
                'quantity_in' => $line->quantity, // Explicitly set quantity_in for balance sheet calculation
                'quantity_out' => 0,
                'balance_quantity' => $batch->qty_remaining + $line->quantity,
                'unit_cost' => $batch->unit_cost,
                'total_cost' => $line->quantity * $batch->unit_cost,
                'reference_id' => $this->id,
                'reference_no' => $this->return_number,
                'remarks' => 'Return from ' . ($this->party->name ?? 'Customer'),
                'created_by' => $userId,
            ]);

            // Restore batch remaining quantity
            $batch->increment('qty_remaining', $line->quantity);

            // Update line with batch information
            $line->update(['batch_id' => $batch->id]);
        }

        // Create stock ledger entries for arms (restore arm status)
        foreach ($this->armLines as $line) {
            // Get the arm model to ensure we have fresh data
            $arm = Arm::find($line->arm_id);

            if (!$arm) {
                \Log::warning('Arm not found during sale return edit', ['arm_id' => $line->arm_id]);
                continue;
            }

            // Create stock ledger entry (restore arm to available)
            ArmsStockLedger::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'transaction_date' => $this->return_date,
                'transaction_type' => 'return',
                'quantity_in' => 1, // Restore arm to inventory
                'quantity_out' => 0,
                'balance' => 1, // Restore arm to available
                'reference_id' => $this->return_number,
                'remarks' => 'Return from ' . ($this->party->name ?? 'Customer'),
            ]);

            // Restore arm status to available
            $arm->update([
                'status' => 'available',
                'sold_date' => null,
            ]);

            // Create arm history entry
            ArmHistory::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'action' => 'edit',
                'old_values' => ['status' => 'sold', 'sold_date' => $this->return_date],
                'new_values' => ['status' => 'available', 'sold_date' => null],
                'transaction_date' => $this->return_date,
                'price' => $line->return_price,
                'remarks' => 'Return from ' . ($this->party->name ?? 'Customer'),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Create journal entries
        $this->createJournalEntries();
    }

    /**
     * Reverse journal entries for sale return edits (delete existing entries)
     */
    public function reverseJournalEntriesForEdit(): void
    {
        // Delete existing journal entries for edits
        JournalEntry::where('business_id', $this->business_id)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleReturn')
            ->delete();

        // Delete existing party ledger entries for edits
        PartyLedger::where('business_id', $this->business_id)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'Sale Return')
            ->delete();

        // Delete existing bank ledger entries for edits
        \App\Models\BankLedger::where('business_id', $this->business_id)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleReturn')
            ->delete();
    }

    /**
     * Reverse journal entries for the sale return (for cancellations)
     */
    public function reverseJournalEntries(): void
    {
        $businessId = $this->business_id;
        $userId = auth()->id();

        // Get party's chart of account for credit returns (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($this->return_type === 'credit' && $this->party_id) {
            $party = \App\Models\Party::find($this->party_id);
            if ($party && $party->chart_of_account_id) {
                $partyAccountId = $party->chart_of_account_id;
            }
            
            if (!$partyAccountId) {
                \Log::error('Party chart of account not found for sale return reversal', [
                    'sale_return_id' => $this->id,
                    'party_id' => $this->party_id
                ]);
                throw new \Exception('Party chart of account is required for credit sale return reversal.');
            }
        }

        // CRITICAL FIX: Group orWhere clauses to respect business_id filter
        $salesRevenueId = ChartOfAccount::where('business_id', $businessId)
            ->where(function($query) {
                $query->where('name', 'like', '%Sales%')
                      ->orWhere('name', 'like', '%Revenue%')
                      ->orWhere('name', 'like', '%Income%');
            })
            ->value('id');

        $cogsId = ChartOfAccount::where('business_id', $businessId)
            ->where(function($query) {
                $query->where('name', 'like', '%Cost of Goods%')
                      ->orWhere('name', 'like', '%COGS%');
            })
            ->value('id');

        $inventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Inventory%')
            ->value('id');

        if (!$salesRevenueId || !$cogsId || !$inventoryId) {
            \Log::warning('Missing required chart of accounts for sale return reversal', [
                'sale_return_id' => $this->id,
            ]);
            return;
        }

        // Reverse Entry 1: Debit Party Account (for credit returns) / Debit Bank (for cash returns)
        if ($this->return_type === 'credit') {
            // Credit return reversal - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit sale return reversal.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => $this->total_amount, // Debit to reverse the original credit
                'credit_amount' => 0,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleReturnCancellation',
                'comments' => 'Sale Return Cancellation ' . $this->return_number,
                'user_id' => $userId,
                'date_added' => now(),
            ]);
        } else {
            // Cash return cancellation - credit bank account (opposite of original debit)
            if ($this->bank_id) {
                $bank = \App\Models\Bank::find($this->bank_id);
                if ($bank && $bank->chart_of_account_id) {
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $bank->chart_of_account_id,
                        'debit_amount' => 0,
                        'credit_amount' => $this->total_amount, // Credit to reverse the original debit
                        'voucher_id' => $this->id,
                        'voucher_type' => 'SaleReturnCancellation',
                        'comments' => 'Sale Return Cancellation ' . $this->return_number,
                        'user_id' => $userId,
                        'date_added' => now(),
                    ]);

                    // Create bank ledger cancellation entry (opposite of original)
                    \App\Models\BankLedger::create([
                        'business_id' => $businessId,
                        'bank_id' => $this->bank_id,
                        'voucher_id' => $this->id,
                        'voucher_type' => 'SaleReturnCancellation',
                        'date' => now(),
                        'user_id' => $userId,
                        'withdrawal_amount' => 0,
                        'deposit_amount' => $this->total_amount, // Money coming back in (reverse the refund)
                    ]);
                }
            }
        }

        // Reverse Entry 2: Credit Sales Revenue (opposite of original debit)
        // Original sale return: Debit Income (reduces income)
        // Cancellation: Credit Income (restores income)
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $salesRevenueId,
            'debit_amount' => 0,
            'credit_amount' => $this->total_amount, // Credit to reverse the original debit
            'voucher_id' => $this->id,
            'voucher_type' => 'SaleReturnCancellation',
            'comments' => 'Sale Return Cancellation ' . $this->return_number,
            'user_id' => $userId,
            'date_added' => now(),
        ]);

        // Reverse party ledger entry for credit returns (reverse credit -> debit)
        if ($this->return_type === 'credit' && $this->party_id) {
            PartyLedger::create([
                'business_id' => $this->business_id,
                'party_id' => $this->party_id,
                'voucher_id' => $this->id,
                'voucher_type' => 'Sale Return Cancellation',
                'date_added' => now(),
                'user_id' => $userId,
                'debit_amount' => $this->total_amount,
                'credit_amount' => 0,
            ]);
        }

        // Get the exact COGS amount from the original sale return journal entries
        // This ensures we reverse the exact amount that was posted, not a recalculated value
        $totalCogs = JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleReturn')
            ->where('account_head', $cogsId)
            ->where('credit_amount', '>', 0) // Sale return credits COGS
            ->sum('credit_amount');

        // Reverse COGS and Inventory entries (opposite of original)
        if ($totalCogs > 0) {
            // Debit COGS (opposite of original credit)
            // Original sale return: Credit COGS (reduces expense)
            // Cancellation: Debit COGS (restores expense)
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $cogsId,
                'debit_amount' => $totalCogs, // Debit to reverse the original credit
                'credit_amount' => 0,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleReturnCancellation',
                'comments' => 'COGS Cancellation',
                'user_id' => $userId,
                'date_added' => now(),
            ]);

            // Credit Inventory (opposite of original debit)
            // Original sale return: Debit Inventory (increases asset)
            // Cancellation: Credit Inventory (reduces asset)
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $inventoryId,
                'debit_amount' => 0,
                'credit_amount' => $totalCogs, // Credit to reverse the original debit
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleReturnCancellation',
                'comments' => 'Inventory Cancellation',
                'user_id' => $userId,
                'date_added' => now(),
            ]);
        }
    }

    /**
     * Post sale return and create all related entries (reused from controller)
     */
    private function postSaleReturn(): void
    {
        try {
            $businessId = $this->business_id;
            $userId = auth()->id();

            // Update status
            $this->update([
                'status' => 'posted',
                'posted_by' => $userId
            ]);

            // Load fresh relationships to ensure we have the current data
            $this->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm']);

            // Create stock ledger entries for general items (restore inventory)
            foreach ($this->generalLines as $line) {
                // Find the batch that was originally consumed (if available)
                $batch = GeneralBatch::where('item_id', $line->general_item_id)
                    ->where('qty_remaining', '>=', 0)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$batch) {
                    \Log::warning('No batch found for general item in sale return', [
                        'sale_return_id' => $this->id,
                        'general_item_id' => $line->general_item_id,
                        'quantity' => $line->quantity
                    ]);
                    continue;
                }

                // Create stock ledger entry (positive quantity to restore stock)
                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $line->general_item_id,
                    'batch_id' => $batch->id,
                    'transaction_type' => 'return',
                    'transaction_date' => $this->return_date,
                    'quantity' => $line->quantity, // Positive quantity to restore stock
                    'quantity_in' => $line->quantity, // Explicitly set quantity_in for balance sheet calculation
                    'quantity_out' => 0,
                    'balance_quantity' => $batch->qty_remaining + $line->quantity,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $line->quantity * $batch->unit_cost,
                    'reference_id' => $this->id,
                    'reference_no' => $this->return_number,
                    'remarks' => 'Return from ' . ($this->party->name ?? 'Customer'),
                    'created_by' => $userId,
                ]);

                // Restore batch remaining quantity
                $batch->increment('qty_remaining', $line->quantity);

                // Update line with batch information
                $line->update(['batch_id' => $batch->id]);
            }

            // Create stock ledger entries for arms (restore arm status)
            foreach ($this->armLines as $line) {
                // Store old values for history
                $oldValues = $line->arm->toArray();

                // Create arms stock ledger entry
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $line->arm_id,
                    'transaction_date' => $this->return_date,
                    'transaction_type' => 'return',
                    'quantity_in' => 1, // Restore arm to inventory
                    'quantity_out' => 0,
                    'balance' => 1, // Restore arm to available
                    'reference_id' => $this->return_number,
                    'remarks' => 'Return from ' . ($this->party->name ?? 'Customer'),
                ]);

                // Restore arm status
                $line->arm->update([
                    'status' => 'available',
                    'sold_date' => null,
                ]);

                // Create arm history entry
                ArmHistory::create([
                    'business_id' => $businessId,
                    'arm_id' => $line->arm_id,
                    'action' => 'return',
                    'old_values' => $oldValues,
                    'new_values' => $line->arm->fresh()->toArray(),
                    'transaction_date' => $this->return_date,
                    'price' => $line->return_price,
                    'remarks' => 'Return from ' . ($this->party->name ?? 'Customer'),
                    'user_id' => $userId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            // Create party ledger entry for credit returns (reduce receivable -> credit)
            if ($this->return_type === 'credit' && $this->party_id) {
                PartyLedger::create([
                    'business_id' => $this->business_id,
                    'party_id' => $this->party_id,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'Sale Return',
                    'date_added' => $this->return_date,
                    'user_id' => $userId,
                    'debit_amount' => 0,
                    'credit_amount' => $this->total_amount,
                ]);
            }

            // Create journal entries
            $this->createJournalEntries();

            // Create audit log
            SaleReturnAuditLog::create([
                'sale_return_id' => $this->id,
                'action' => 'posted',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'posted'],
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error posting sale return: ' . $e->getMessage(), [
                'sale_return_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }

    /**
     * Create journal entries for the sale return (reused from controller)
     */
    private function createJournalEntries(): void
    {
        $businessId = $this->business_id;
        $userId = auth()->id();

        // Get party's chart of account for credit returns (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($this->return_type === 'credit' && $this->party_id) {
            $party = \App\Models\Party::find($this->party_id);
            if ($party) {
                // If party doesn't have a chart of account, create one
                if (!$party->chart_of_account_id) {
                    $partyAccount = ChartOfAccount::createPartyAccount($party->name, $businessId);
                    $party->update(['chart_of_account_id' => $partyAccount->id]);
                    $party->refresh();
                }
                $partyAccountId = $party->chart_of_account_id;

                if (!$partyAccountId) {
                    throw new \Exception('Failed to create or retrieve party chart of account for credit sale return.');
                }
            } else {
                throw new \Exception('Party not found for credit sale return.');
            }
        }

        // CRITICAL FIX: Group orWhere clauses to respect business_id filter
        // Use same lookup logic as SaleInvoice to ensure we use the same account
        $salesRevenueId = ChartOfAccount::where('business_id', $businessId)
            ->where(function($query) {
                $query->where('name', 'like', '%Sales%')
                      ->orWhere('name', 'like', '%Revenue%')
                      ->orWhere('name', 'like', '%Income%');
            })
            ->orderBy('code') // Ensure consistent account selection (same as SaleInvoice)
            ->value('id');

        $cogsId = ChartOfAccount::where('business_id', $businessId)
            ->where(function($query) {
                $query->where('name', 'like', '%Cost of Goods%')
                      ->orWhere('name', 'like', '%COGS%');
            })
            ->orderBy('code') // Ensure consistent account selection
            ->value('id');

        $inventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Inventory%')
            ->orderBy('code') // Ensure consistent account selection
            ->value('id');

        // If any required account is missing, skip journal entries
        if (!$salesRevenueId || !$cogsId || !$inventoryId) {
            \Log::warning('Missing required chart of accounts for sale return posting', [
                'sale_return_id' => $this->id,
                'sales_revenue_id' => $salesRevenueId,
                'cogs_id' => $cogsId,
                'inventory_id' => $inventoryId
            ]);
            return; // Skip journal entries but continue with other posting operations
        }


        // Entry 1: Credit Party Account (for credit returns) / Credit Bank (for cash returns)
        if ($this->return_type === 'credit') {
            // Credit return - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit sale returns.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => 0,
                'credit_amount' => $this->total_amount, // Credit to reduce party receivable
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleReturn',
                'comments' => 'Sale Return ' . $this->return_number,
                'user_id' => $userId,
                'date_added' => $this->return_date,
            ]);
        } else {
            // Cash return - debit bank account (customer pays us back)
            if ($this->bank_id) {
                $bank = \App\Models\Bank::find($this->bank_id);
                if ($bank && $bank->chart_of_account_id) {
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $bank->chart_of_account_id,
                        'debit_amount' => $this->total_amount,
                        'credit_amount' => 0,
                        'voucher_id' => $this->id,
                        'voucher_type' => 'SaleReturn',
                        'comments' => 'Sale Return ' . $this->return_number,
                        'user_id' => $userId,
                        'date_added' => $this->return_date,
                    ]);

                    // Create bank ledger entry for cash return
                    \App\Models\BankLedger::create([
                        'business_id' => $businessId,
                        'bank_id' => $this->bank_id,
                        'voucher_id' => $this->id,
                        'voucher_type' => 'SaleReturn',
                        'date' => $this->return_date,
                        'user_id' => $userId,
                        'withdrawal_amount' => $this->total_amount, // Money going out (customer refund)
                        'deposit_amount' => 0,
                    ]);
                }
            }
        }

        // Entry 2: Debit Sales Revenue (reduce revenue)
        // Income accounts: Debit = Decrease, Credit = Increase
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $salesRevenueId,
            'debit_amount' => $this->total_amount, // Debit to reduce income
            'credit_amount' => 0,
            'voucher_id' => $this->id,
            'voucher_type' => 'SaleReturn',
            'comments' => 'Sale Return ' . $this->return_number,
            'user_id' => $userId,
            'date_added' => $this->return_date,
        ]);

        // Create party ledger entry for credit sale returns (reduce receivable -> credit)
        if ($this->return_type === 'credit' && $this->party_id) {
            PartyLedger::create([
                'business_id' => $this->business_id,
                'party_id' => $this->party_id,
                'voucher_id' => $this->id,
                'voucher_type' => 'Sale Return',
                'date_added' => $this->return_date,
                'user_id' => $userId,
                'debit_amount' => 0,
                'credit_amount' => $this->total_amount,
            ]);
        }

        // Calculate total COGS for all items (general items + arms)
        $totalCogs = 0;

        // Calculate COGS for general items
        // IMPORTANT: Use FIFO costs from original sale invoice stock ledger entries
        foreach ($this->generalLines as $line) {
            $cogsAmount = 0;
            
            // Method 1: Try to find the original sale invoice stock ledger entries
            if ($this->original_sale_invoice_id) {
                $originalSaleInvoice = \App\Models\SaleInvoice::find($this->original_sale_invoice_id);
                if ($originalSaleInvoice) {
                    // Find all stock ledger entries from the original sale for this item
                    // These entries contain the exact FIFO costs used in the original sale
                    $originalStockEntries = \App\Models\GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                        ->where('reference_no', $originalSaleInvoice->invoice_number)
                        ->where('transaction_type', 'sale')
                        ->where('quantity', '<', 0) // Sale entries have negative quantity
                        ->orderBy('id', 'asc') // FIFO order (oldest first) - same as original sale
                        ->get();
                    
                    if ($originalStockEntries->isNotEmpty()) {
                        // Calculate total original sale quantity and cost for this item
                        $originalTotalQty = 0;
                        $originalTotalCost = 0;
                        foreach ($originalStockEntries as $entry) {
                            $originalTotalQty += abs($entry->quantity);
                            $originalTotalCost += abs($entry->total_cost);
                        }
                        
                        // Calculate proportional COGS based on return quantity
                        if ($originalTotalQty > 0) {
                            $unitCost = $originalTotalCost / $originalTotalQty;
                            $cogsAmount = $line->quantity * $unitCost;
                        }
                    }
                }
            }
            
            // Method 2: If no original sale invoice, try to find stock ledger entries by matching item and party/date
            if ($cogsAmount == 0) {
                // Find recent sale stock ledger entries for this item (within reasonable date range)
                $recentStockEntries = \App\Models\GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                    ->where('transaction_type', 'sale')
                    ->where('quantity', '<', 0)
                    ->where('transaction_date', '>=', \Carbon\Carbon::parse($this->return_date)->subDays(90)) // Within 90 days
                    ->orderBy('id', 'asc') // FIFO order
                    ->limit(10) // Limit to recent entries
                    ->get();
                
                if ($recentStockEntries->isNotEmpty()) {
                    $totalQty = 0;
                    $totalCost = 0;
                    foreach ($recentStockEntries as $entry) {
                        $totalQty += abs($entry->quantity);
                        $totalCost += abs($entry->total_cost);
                    }
                    
                    if ($totalQty > 0) {
                        $unitCost = $totalCost / $totalQty;
                        $cogsAmount = $line->quantity * $unitCost;
                    }
                }
            }
            
            // Fallback: If still no cost found, use the batch from the line
            if ($cogsAmount == 0) {
                // Load batch relationship if not loaded
                if (!$line->relationLoaded('batch')) {
                    $line->load('batch');
                }
                
                $batch = $line->batch;
                if (!$batch) {
                    $batch = \App\Models\GeneralBatch::where('item_id', $line->general_item_id)
                        ->where('qty_remaining', '>=', 0)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    // Update line with batch if found
                    if ($batch) {
                        $line->update(['batch_id' => $batch->id]);
                        $line->load('batch');
            }
        }

                if ($batch) {
                    $cogsAmount = $line->quantity * $batch->unit_cost;
                }
            }
            
            $totalCogs += $cogsAmount;
        }

        // Calculate COGS for arms
        foreach ($this->armLines as $line) {
            $cogsAmount = $line->arm->purchase_price ?? 0;
            $totalCogs += $cogsAmount;
        }

        // Create single summarized COGS and Inventory journal entries
        if ($totalCogs > 0) {
                // Credit COGS (reduce COGS)
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $cogsId,
                    'debit_amount' => 0,
                'credit_amount' => $totalCogs,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'SaleReturn',
                'comments' => 'COGS Reversal',
                    'user_id' => $userId,
                    'date_added' => $this->return_date,
                ]);

            // Debit Inventory (restore inventory)
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $inventoryId,
                'debit_amount' => $totalCogs,
                    'credit_amount' => 0,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'SaleReturn',
                'comments' => 'Inventory Restoration',
                    'user_id' => $userId,
                    'date_added' => $this->return_date,
                ]);
        }
    }


}
