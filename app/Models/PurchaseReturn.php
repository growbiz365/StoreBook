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
use App\Models\PartyLedger;
use App\Models\PurchaseReturnAuditLog;

class PurchaseReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'party_id',
        'return_type',
        'bank_id',
        'original_purchase_id',
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

    public function originalPurchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'original_purchase_id');
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
        return $this->hasMany(PurchaseReturnGeneralItem::class);
    }

    public function armLines(): HasMany
    {
        return $this->hasMany(PurchaseReturnArm::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PurchaseReturnAuditLog::class);
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

    public function scopeByVendor($query, $partyId)
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
        return 'PR-' . $this->id;
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
                $originalPartyId = $this->party_id;
                
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
                            'return_price' => $line['unit_price'],
                        ]);
                    }
                }

                if (!empty($newArmLines)) {
                    foreach ($newArmLines as $line) {
                        $this->armLines()->create([
                            'arm_id' => $line['arm_id'],
                            'return_price' => $line['unit_price'],
                        ]);
                    }
                }

                // Step 7: Refresh relationships and recalculate totals
                $this->load(['generalLines', 'armLines']);
                $this->calculateTotals();
                $this->save();

                // Step 8: Check if final result is different from original
                $finalTotal = $this->total_amount;
                $finalReturnType = $this->return_type;
                $finalBankId = $this->bank_id;
                $finalPartyId = $this->party_id;
                
                $isFinalResultDifferent = (
                    $originalTotal != $finalTotal ||
                    $originalReturnType != $finalReturnType ||
                    $originalBankId != $finalBankId ||
                    $originalPartyId != $finalPartyId
                );
                
                if ($isFinalResultDifferent) {
                    // Step 9: Apply new stock additions and journal entries
                    $this->postPurchaseReturn();
                } else {
                    // Step 9: Just update status to posted (no new journal entries needed)
                    $this->update(['status' => 'posted']);
                }
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
                        'return_price' => $line['unit_price'],
                    ]);
                }
            }

            if (!empty($newArmLines)) {
                foreach ($newArmLines as $line) {
                    $this->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'return_price' => $line['unit_price'],
                    ]);
                }
            }

            // Refresh relationships and recalculate totals
            $this->load(['generalLines', 'armLines']);
            $this->calculateTotals();
            $this->save();
        }

        // Step 8: Create audit log
        PurchaseReturnAuditLog::create([
            'purchase_return_id' => $this->id,
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
        $currentGeneralLines = $this->generalLines->map(function($line) {
            return [
                'general_item_id' => $line->general_item_id,
                'quantity' => $line->quantity,
                'return_price' => $line->return_price,
            ];
        })->toArray();

        $newGeneralLinesNormalized = array_map(function($line) {
            return [
                'general_item_id' => $line['general_item_id'],
                'quantity' => $line['qty'],
                'return_price' => $line['unit_price'],
            ];
        }, $newGeneralLines);

        if ($currentGeneralLines !== $newGeneralLinesNormalized) {
            return true;
        }

        // Check if arm lines have changed
        $currentArmLines = $this->armLines->map(function($line) {
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
        PurchaseReturnAuditLog::create([
            'purchase_return_id' => $this->id,
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
                    'quantity' => $line->quantity, // Positive quantity to reverse the return
                    'quantity_out' => 0,
                    'balance_quantity' => $line->batch->qty_remaining + $line->quantity,
                    'unit_cost' => $line->batch->unit_cost,
                    'total_cost' => $line->quantity * $line->batch->unit_cost,
                    'reference_id' => $this->id,
                    'reference_no' => $this->return_number . '-REV',
                    'remarks' => 'Return reversal for ' . ($this->party->name ?? 'Vendor'),
                    'created_by' => $userId,
                ]);

                // Restore batch remaining quantity
                $line->batch->increment('qty_remaining', $line->quantity);
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
            // When cancelling purchase return, we need to restore the arm (quantity_in = 1)
            // The original purchase return had quantity_out = 1, so reversal needs quantity_in = 1
            ArmsStockLedger::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'transaction_date' => now(),
                'transaction_type' => 'reversal',
                'quantity_in' => 1, // Restore arm (opposite of original quantity_out = 1)
                'quantity_out' => 0,
                'balance' => 1, // Reverse arm back to available
                'reference_id' => $this->return_number . '-REV',
                'remarks' => 'Return reversal for ' . ($this->party->name ?? 'Vendor'),
            ]);

            // Reverse arm status back to available (from decommissioned)
            $arm->update([
                'status' => 'available',
            ]);

            // Create arm history entry for reversal
            ArmHistory::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'action' => 'edit',
                'old_values' => ['status' => 'decommissioned'],
                'new_values' => ['status' => 'available'],
                'transaction_date' => now(),
                'price' => $line->return_price,
                'remarks' => 'Return reversal for ' . ($this->party->name ?? 'Vendor'),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Reverse journal entries for purchase return edits (delete existing entries)
     */
    public function reverseJournalEntriesForEdit(): void
    {
        // Delete existing journal entries for edits
        JournalEntry::where('business_id', $this->business_id)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'PurchaseReturn')
            ->delete();

        // Delete existing party ledger entries for edits
        PartyLedger::where('business_id', $this->business_id)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'Purchase Return')
            ->delete();

        // Delete existing bank ledger entries for edits
        \App\Models\BankLedger::where('business_id', $this->business_id)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'PurchaseReturn')
            ->delete();
    }

    /**
     * Reverse journal entries for the purchase return (for cancellations)
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
                \Log::error('Party chart of account not found for purchase return reversal', [
                    'purchase_return_id' => $this->id,
                    'party_id' => $this->party_id
                ]);
                throw new \Exception('Party chart of account is required for credit purchase return reversal.');
            }
        }

        $inventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Inventory%')
            ->value('id');

        if (!$inventoryId) {
            \Log::warning('Missing required chart of accounts for purchase return reversal', [
                'purchase_return_id' => $this->id,
            ]);
            throw new \Exception('Inventory account not found. Please ensure the chart of accounts is properly set up.');
        }

        // Reverse Entry 1: Credit Party Account (for credit returns) / Credit Bank (for cash returns)
        if ($this->return_type === 'credit') {
            // Credit return reversal - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit purchase return reversal.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => 0,
                'credit_amount' => $this->total_amount, // Credit to reverse the original debit
                'voucher_id' => $this->id,
                'voucher_type' => 'PurchaseReturnCancellation',
                'comments' => 'Purchase Return Cancellation ' . $this->return_number,
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
                        'voucher_type' => 'PurchaseReturnCancellation',
                        'comments' => 'Purchase Return Cancellation ' . $this->return_number,
                    'user_id' => $userId,
                    'date_added' => now(),
                ]);

                    // Create bank ledger cancellation entry (opposite of original)
                    \App\Models\BankLedger::create([
                        'business_id' => $businessId,
                        'bank_id' => $this->bank_id,
                        'voucher_id' => $this->id,
                        'voucher_type' => 'PurchaseReturnCancellation',
                        'date' => now(),
                        'user_id' => $userId,
                        'withdrawal_amount' => $this->total_amount, // Money going out (reverse the refund)
                        'deposit_amount' => 0,
                    ]);
                }
            }
        }

        // Reverse Entry 2: Debit Inventory Asset (opposite of original credit)
        // Get the original inventory journal entry amount to ensure we reverse the correct amount
        $inventoryJournalEntry = JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'PurchaseReturn')
            ->where('account_head', $inventoryId)
            ->where('credit_amount', '>', 0) // Original credit entry
            ->orderBy('id', 'desc')
            ->first();

        if ($inventoryJournalEntry) {
            // Use the original journal entry amount (this is the correct amount after any edits)
            $inventoryAmount = $inventoryJournalEntry->credit_amount;
        } else {
            // Fallback: Use total_amount if journal entry not found (should rarely happen)
            $inventoryAmount = $this->total_amount;
        }

        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $inventoryId,
            'debit_amount' => $inventoryAmount, // Debit to reverse the original credit
            'credit_amount' => 0,
            'voucher_id' => $this->id,
            'voucher_type' => 'PurchaseReturnCancellation',
            'comments' => 'Purchase Return Cancellation ' . $this->return_number,
            'user_id' => $userId,
            'date_added' => now(),
        ]);

        // Reverse party ledger entry for credit returns (reverse debit -> credit)
        if ($this->return_type === 'credit' && $this->party_id) {
            PartyLedger::create([
                'business_id' => $this->business_id,
                'party_id' => $this->party_id,
                'voucher_id' => $this->id,
                'voucher_type' => 'Purchase Return Cancellation',
                'date_added' => now(),
                'user_id' => $userId,
                'debit_amount' => 0,
                'credit_amount' => $this->total_amount,
            ]);
        }

        // NOTE: Purchase returns do NOT involve COGS entries
        // COGS (Cost of Goods Sold) is only for SALES, not purchase returns
        // When cancelling a purchase return, we only reverse:
        // 1. Party/Bank account (already done above)
        // 2. Inventory account (already done above)
        // No COGS entries should be created for purchase return cancellations
    }

    /**
     * Post purchase return and create all related entries (reused from controller)
     */
    private function postPurchaseReturn(): void
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

            // Create stock ledger entries for general items (reduce inventory)
            foreach ($this->generalLines as $line) {
                // Find the batch that was originally received (if available)
                $batch = GeneralBatch::where('item_id', $line->general_item_id)
                    ->where('qty_remaining', '>=', $line->quantity)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$batch) {
                    \Log::warning('No batch found for general item in purchase return', [
                        'purchase_return_id' => $this->id,
                        'general_item_id' => $line->general_item_id,
                        'quantity' => $line->quantity
                    ]);
                    continue;
                }

                // Create stock ledger entry (negative quantity to reduce stock)
                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $line->general_item_id,
                    'batch_id' => $batch->id,
                    'transaction_type' => 'return',
                    'transaction_date' => $this->return_date,
                    'quantity' => -$line->quantity, // Negative quantity to reduce stock
                    'quantity_out' => $line->quantity,
                    'balance_quantity' => $batch->qty_remaining - $line->quantity,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $line->quantity * $batch->unit_cost,
                    'reference_id' => $this->id,
                    'reference_no' => $this->return_number,
                    'remarks' => 'Return to ' . ($this->party->name ?? 'Vendor'),
                    'created_by' => $userId,
                ]);

                // Reduce batch remaining quantity
                $batch->decrement('qty_remaining', $line->quantity);

                // Update line with batch information
                $line->update(['batch_id' => $batch->id]);
            }

            // Create stock ledger entries for arms (change arm status)
            foreach ($this->armLines as $line) {
                // Store old values for history
                $oldValues = $line->arm->toArray();
                
                // Create arms stock ledger entry
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $line->arm_id,
                    'transaction_date' => $this->return_date,
                    'transaction_type' => 'return',
                    'quantity_out' => 1,
                    'balance' => 0, // Remove arm from available
                    'reference_id' => $this->return_number,
                    'remarks' => 'Return to ' . ($this->party->name ?? 'Vendor'),
                ]);

                // Change arm status to decommissioned (since it's being returned to vendor)
                $line->arm->update([
                    'status' => 'decommissioned',
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
                    'remarks' => 'Return to ' . ($this->party->name ?? 'Vendor'),
                    'user_id' => $userId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            // Create party ledger entry for credit returns
            if ($this->return_type === 'credit' && $this->party_id) {
                PartyLedger::create([
                    'business_id' => $this->business_id,
                    'party_id' => $this->party_id,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'Purchase Return',
                    'date_added' => $this->return_date,
                    'user_id' => $userId,
                    'debit_amount' => $this->total_amount, // Increase what we owe vendor
                    'credit_amount' => 0,
                ]);
            }

            // Create journal entries
            $this->createJournalEntries();

            // Create audit log
            PurchaseReturnAuditLog::create([
                'purchase_return_id' => $this->id,
                'action' => 'posted',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'posted'],
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error posting purchase return: ' . $e->getMessage(), [
                'purchase_return_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }

    /**
     * Create journal entries for the purchase return (reused from controller)
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
                    throw new \Exception('Failed to create or retrieve party chart of account for credit purchase return.');
                }
            } else {
                throw new \Exception('Party not found for credit purchase return.');
            }
        }

        $purchaseExpenseId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Purchase%')
            ->orWhere('name', 'like', '%Expense%')
            ->value('id');

        $cogsId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Cost of Goods%')
            ->orWhere('name', 'like', '%COGS%')
            ->value('id');

        $inventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Inventory%')
            ->value('id');

        $armsInventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'Arms Inventory')
            ->value('id');

        // If any required account is missing, skip journal entries
        if (!$purchaseExpenseId || !$cogsId || !$inventoryId) {
            \Log::warning('Missing required chart of accounts for purchase return posting', [
                'purchase_return_id' => $this->id,
                'purchase_expense_id' => $purchaseExpenseId,
                'cogs_id' => $cogsId,
                'inventory_id' => $inventoryId,
                'arms_inventory_id' => $armsInventoryId
            ]);
            return; // Skip journal entries but continue with other posting operations
        }

        // Log if arms inventory account is missing but continue (arms COGS will be skipped)
        if (!$armsInventoryId) {
            \Log::warning('Arms Inventory account not found - arms COGS entries will be skipped', [
                'purchase_return_id' => $this->id,
                'arms_inventory_id' => $armsInventoryId
            ]);
        }

        // Entry 1: Debit Party Account (for credit returns) / Debit Bank (for cash returns)
        if ($this->return_type === 'credit') {
            // Credit return - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit purchase returns.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => $this->total_amount,
                'credit_amount' => 0,
                'voucher_id' => $this->id,
                'voucher_type' => 'PurchaseReturn',
                'comments' => 'Purchase Return ' . $this->return_number,
                'user_id' => $userId,
                'date_added' => $this->return_date,
            ]);
        } else {
            // Cash return - debit bank account (you receive money back)
            if ($this->bank_id) {
                $bank = \App\Models\Bank::find($this->bank_id);
                if ($bank && $bank->chart_of_account_id) {
                JournalEntry::create([
                    'business_id' => $businessId,
                        'account_head' => $bank->chart_of_account_id,
                        'debit_amount' => $this->total_amount,
                        'credit_amount' => 0,
                    'voucher_id' => $this->id,
                        'voucher_type' => 'PurchaseReturn',
                    'comments' => 'Purchase Return ' . $this->return_number,
                    'user_id' => $userId,
                    'date_added' => $this->return_date,
                ]);

                    // Create bank ledger entry for cash return
                    \App\Models\BankLedger::create([
                        'business_id' => $businessId,
                        'bank_id' => $this->bank_id,
                        'voucher_id' => $this->id,
                        'voucher_type' => 'PurchaseReturn',
                        'date' => $this->return_date,
                        'user_id' => $userId,
                        'withdrawal_amount' => 0,
                        'deposit_amount' => $this->total_amount, // Money coming in (vendor refund)
                    ]);
                }
            }
        }

        // Entry 2: Credit Inventory Asset (reduce inventory)
        // Use total_amount to match how purchases work (includes both general items and arms)
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $inventoryId,
            'debit_amount' => 0,
            'credit_amount' => $this->total_amount,
            'voucher_id' => $this->id,
            'voucher_type' => 'PurchaseReturn',
            'comments' => 'Purchase Return ' . $this->return_number,
            'user_id' => $userId,
            'date_added' => $this->return_date,
        ]);

        // Note: We don't need COGS reversal for purchase returns
        // The simple approach is: Debit Accounts Payable, Credit Inventory Asset
        // This correctly reverses the original purchase entry
    }
}
