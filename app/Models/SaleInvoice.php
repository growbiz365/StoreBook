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
use App\Models\SaleInvoiceAuditLog;
use App\Models\PartyLedger;
use App\Models\BankLedger;

class SaleInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'party_id',
        'approval_id',
        'quotation_id',
        'sale_type',
        'bank_id',
        'invoice_date',
        'subtotal',
        'shipping_charges',
        'total_amount',
        'status',
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
        'invoice_date' => 'date',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function generalLines(): HasMany
    {
        return $this->hasMany(SaleInvoiceGeneralItem::class);
    }

    public function armLines(): HasMany
    {
        return $this->hasMany(SaleInvoiceArm::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(SaleInvoiceAuditLog::class);
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
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
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
        return $this->isDraft() && ($this->generalLines()->count() > 0 || $this->armLines()->count() > 0);
    }

    public function canBeCancelled(): bool
    {
        // Allow cancelling posted sales - inventory will be automatically reversed
        return $this->isPosted();
    }

    public function canBeEdited(): bool
    {
        // Allow editing of draft and posted sales
        return $this->isDraft() || $this->isPosted();
    }

    public function canBeEditedAfterPosting(): bool
    {
        // Always allow editing posted sales - inventory will be automatically adjusted
        return $this->isPosted();
    }

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

    public function getInvoiceNumberAttribute(): string
    {
        return 'SI-' . $this->id;
    }

    public function getFormattedInvoiceDateAttribute(): string
    {
        return $this->invoice_date->format('d M Y');
    }

    // Enhanced Edit and Delete Methods
    public function canBeDeleted(): bool
    {
        // Can delete draft, posted, or cancelled invoices
        return in_array($this->status, ['draft', 'posted', 'cancelled']) && !$this->trashed();
    }

    public function isDeleted(): bool
    {
        return $this->trashed();
    }

    /**
     * Enhanced edit method that properly handles stock reversal and reapplication
     */
    public function performEnhancedEdit(array $newGeneralLines, array $newArmLines, array $invoiceData): void
    {
        $wasPosted = $this->isPosted();

        if ($wasPosted) {
            // Step 1: Check if there are meaningful changes
            $hasChanges = $this->hasMeaningfulChanges($newGeneralLines, $newArmLines, $invoiceData);

            if ($hasChanges) {
                // Step 2: Store original values for comparison
                $originalTotal = $this->total_amount;
                $originalSaleType = $this->sale_type;
                $originalBankId = $this->bank_id;

                // Step 3: Reverse original stock and journal entries
                $this->reverseInventoryImpacts($newGeneralLines, $newArmLines);
                $this->reverseJournalEntriesForEdit();

                // Step 4: Update invoice data
                $this->update($invoiceData);

                // Step 5: Delete existing lines
                $this->generalLines()->delete();
                $this->armLines()->delete();

                // Step 6: Create new lines
                if (!empty($newGeneralLines)) {
                    foreach ($newGeneralLines as $line) {
                        $this->generalLines()->create([
                            'general_item_id' => $line['general_item_id'],
                            'quantity' => $line['qty'],
                            'sale_price' => $line['sale_price'],
                        ]);
                    }
                }

                if (!empty($newArmLines)) {
                    foreach ($newArmLines as $line) {
                        $this->armLines()->create([
                            'arm_id' => $line['arm_id'],
                            'sale_price' => $line['sale_price'],
                        ]);
                        
                        // Update the arm's sale price with the new sale price and create history entry
                        $arm = Arm::find($line['arm_id']);
                        if ($arm) {
                            $oldSalePrice = $arm->sale_price;
                            $arm->update(['sale_price' => $line['sale_price']]);
                            
                            // Create history entry if sale price changed
                            if ($oldSalePrice != $line['sale_price']) {
                                ArmHistory::create([
                                    'business_id' => $this->business_id,
                                    'arm_id' => $arm->id,
                                    'action' => 'edit',
                                    'old_values' => ['sale_price' => $oldSalePrice],
                                    'new_values' => ['sale_price' => $line['sale_price']],
                                    'transaction_date' => $this->invoice_date,
                                    'price' => $line['sale_price'],
                                    'remarks' => 'Sale price updated via sale invoice edit',
                                    'user_id' => auth()->id(),
                                    'ip_address' => request()->ip(),
                                    'user_agent' => request()->userAgent(),
                                ]);
                            }
                        }
                    }
                }

                // Step 7: Refresh relationships and recalculate totals
                $this->load(['generalLines', 'armLines']);
                $this->calculateTotals();
                $this->save();

                // Step 8: Check if final result is different from original
                $finalTotal = $this->total_amount;
                $finalSaleType = $this->sale_type;
                $finalBankId = $this->bank_id;

                $isFinalResultDifferent = (
                    $originalTotal != $finalTotal ||
                    $originalSaleType != $finalSaleType ||
                    $originalBankId != $finalBankId
                );

                if ($isFinalResultDifferent) {
                    // Step 9: Apply new stock deductions and journal entries
                    // Don't call postSaleInvoice() as it will create duplicate entries
                    // Instead, manually create the new stock ledger entries and journal entries
                    $this->createStockLedgerEntriesForEdit();
                    $this->createJournalEntries();
                } else {
                    // Step 9: Just update status to posted (no new journal entries needed)
                    $this->update(['status' => 'posted']);
                }
            } else {
                // No meaningful changes - just update the invoice data without affecting stock/journal
                $this->update($invoiceData);
                $this->save();
            }
        } else {
            // Not posted - just update normally
            $this->update($invoiceData);

            // Delete existing lines
            $this->generalLines()->delete();
            $this->armLines()->delete();

            // Create new lines
            if (!empty($newGeneralLines)) {
                foreach ($newGeneralLines as $line) {
                    $this->generalLines()->create([
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'sale_price' => $line['sale_price'],
                    ]);
                }
            }

            if (!empty($newArmLines)) {
                foreach ($newArmLines as $line) {
                    $this->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'sale_price' => $line['sale_price'],
                    ]);
                    
                    // Update the arm's sale price with the new sale price and create history entry
                    $arm = Arm::find($line['arm_id']);
                    if ($arm) {
                        $oldSalePrice = $arm->sale_price;
                        $arm->update(['sale_price' => $line['sale_price']]);
                        
                        // Create history entry if sale price changed
                        if ($oldSalePrice != $line['sale_price']) {
                            ArmHistory::create([
                                'business_id' => $this->business_id,
                                'arm_id' => $arm->id,
                                'action' => 'edit',
                                'old_values' => ['sale_price' => $oldSalePrice],
                                'new_values' => ['sale_price' => $line['sale_price']],
                                'transaction_date' => $this->invoice_date,
                                'price' => $line['sale_price'],
                                'remarks' => 'Sale price updated via sale invoice edit',
                                'user_id' => auth()->id(),
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                            ]);
                        }
                    }
                }
            }

            // Refresh relationships and recalculate totals
            $this->load(['generalLines', 'armLines']);
            $this->calculateTotals();
            $this->save();
        }

        // Step 8: Create audit log
        SaleInvoiceAuditLog::create([
            'sale_invoice_id' => $this->id,
            'action' => 'enhanced_edit',
            'old_values' => null, // We'll store this in the controller
            'new_values' => $this->toArray(),
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Check if there are meaningful changes that require stock/journal reversal
     */
    private function hasMeaningfulChanges(array $newGeneralLines, array $newArmLines, array $invoiceData): bool
    {
        // Check if invoice data has meaningful changes (excluding status changes)
        $invoiceFields = ['party_id', 'sale_type', 'bank_id', 'invoice_date', 'shipping_charges'];
        foreach ($invoiceFields as $field) {
            if (isset($invoiceData[$field]) && $this->$field != $invoiceData[$field]) {
                return true;
            }
        }

        // Check if general lines have changed
        $currentGeneralLines = $this->generalLines->map(function ($line) {
            return [
                'general_item_id' => $line->general_item_id,
                'quantity' => $line->quantity,
                'sale_price' => $line->sale_price,
            ];
        })->toArray();

        $newGeneralLinesNormalized = array_map(function ($line) {
            return [
                'general_item_id' => $line['general_item_id'],
                'quantity' => $line['qty'],
                'sale_price' => $line['sale_price'],
            ];
        }, $newGeneralLines);

        if ($currentGeneralLines !== $newGeneralLinesNormalized) {
            return true;
        }

        // Check if arm lines have changed
        $currentArmLines = $this->armLines->map(function ($line) {
            return [
                'arm_id' => $line->arm_id,
                'sale_price' => $line->sale_price,
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

        // Step 4: Soft delete the main invoice
        $this->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id()
        ]);

        // Step 5: Create audit log for deletion
        SaleInvoiceAuditLog::create([
            'sale_invoice_id' => $this->id,
            'action' => 'soft_deleted',
            'old_values' => $this->toArray(),
            'new_values' => ['deleted_at' => now(), 'deleted_by' => auth()->id()],
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Reverse inventory impacts for both general items and arms
     * For edits, only reverse items/arms that are being removed
     */
    public function reverseInventoryImpacts(array $newGeneralLines = [], array $newArmLines = []): void
    {
        // No global duplicate prevention - we'll check per item

        $businessId = $this->business_id;
        $userId = auth()->id() ?? 1; // Default to user ID 1 if not authenticated

        // Load fresh relationships to ensure we have the current data
        $this->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm', 'party']);

        // Reverse general item stock ledger entries
        foreach ($this->generalLines as $line) {
            // Get the actual stock ledger entries that were created for this sale line
            $originalSaleEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                ->where('reference_no', $this->invoice_number)
                ->where('transaction_type', 'sale')
                ->where('quantity', '<', 0) // Only sale entries (negative quantity)
                ->orderBy('id')
                ->get();

            if ($originalSaleEntries->isEmpty()) {
                \Log::warning('No original sale entries found for reversal', [
                    'sale_invoice_id' => $this->id,
                    'general_item_id' => $line->general_item_id,
                    'invoice_number' => $this->invoice_number
                ]);
                continue;
            }

            // Get existing reversals to avoid duplicates
            $existingReversals = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                ->where('reference_no', 'like', $this->invoice_number . '%')
                ->where('transaction_type', 'reversal')
                ->get();

            // Create a map of existing reversals by quantity and unit cost
            $existingReversalMap = [];
            foreach ($existingReversals as $reversal) {
                $key = number_format($reversal->quantity, 2) . '_' . number_format($reversal->unit_cost, 2);
                $existingReversalMap[$key] = ($existingReversalMap[$key] ?? 0) + 1;
            }

            // Check if all sale entries have been reversed by comparing individual entries
            $allReversed = true;
            foreach ($originalSaleEntries as $saleEntry) {
                $expectedReversalQty = abs($saleEntry->quantity);
                $expectedReversalCost = $saleEntry->unit_cost;
                $key = number_format($expectedReversalQty, 2) . '_' . number_format($expectedReversalCost, 2);
                
                $existingCount = $existingReversalMap[$key] ?? 0;
                if ($existingCount == 0) {
                    $allReversed = false;
                    break;
                }
            }
            
            if ($allReversed) {
                \Log::info('All sale entries for this item have already been reversed, skipping to prevent duplicates', [
                    'sale_invoice_id' => $this->id,
                    'general_item_id' => $line->general_item_id,
                    'invoice_number' => $this->invoice_number,
                    'sale_entries_count' => $originalSaleEntries->count(),
                    'existing_reversals_count' => $existingReversals->count()
                ]);
                continue;
            }
            
            \Log::info('Some sale entries need to be reversed, creating missing reversals', [
                'sale_invoice_id' => $this->id,
                'general_item_id' => $line->general_item_id,
                'invoice_number' => $this->invoice_number,
                'sale_entries_count' => $originalSaleEntries->count(),
                'existing_reversals_count' => $existingReversals->count()
            ]);

            // Reverse each original sale entry with its exact FIFO details
            foreach ($originalSaleEntries as $originalEntry) {
                $reversalKey = number_format(abs($originalEntry->quantity), 2) . '_' . number_format($originalEntry->unit_cost, 2);
                
                // Check if this specific reversal already exists
                if (isset($existingReversalMap[$reversalKey]) && $existingReversalMap[$reversalKey] > 0) {
                    $existingReversalMap[$reversalKey]--;
                    \Log::info('Skipping reversal for already reversed entry', [
                        'sale_entry_id' => $originalEntry->id,
                        'quantity' => $originalEntry->quantity,
                        'unit_cost' => $originalEntry->unit_cost
                    ]);
                    continue;
                }

                // Create reversal stock ledger entry with exact FIFO details
                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $line->general_item_id,
                    'batch_id' => $originalEntry->batch_id,
                    'transaction_type' => 'reversal',
                    'transaction_date' => $this->invoice_date, // Use original invoice date to maintain FIFO order
                    'quantity' => abs($originalEntry->quantity), // Positive quantity to restore stock
                    'quantity_in' => abs($originalEntry->quantity),
                    'quantity_out' => 0,
                    'balance_quantity' => 0, // Will be recalculated by recalculateBalances
                    'unit_cost' => $originalEntry->unit_cost, // Use exact FIFO unit cost
                    'total_cost' => abs($originalEntry->total_cost), // Use exact FIFO total cost
                    'reference_id' => $this->id,
                    'reference_no' => $this->invoice_number . '-REV',
                    'remarks' => 'Sale reversal for ' . ($this->party->name ?? 'Customer'),
                    'created_by' => $userId,
                ]);

                // Restore batch remaining quantity
                $batch = GeneralBatch::find($originalEntry->batch_id);
                if ($batch) {
                    $batch->increment('qty_remaining', abs($originalEntry->quantity));
                }
                
                \Log::info('Created reversal for missing entry', [
                    'sale_entry_id' => $originalEntry->id,
                    'quantity' => $originalEntry->quantity,
                    'unit_cost' => $originalEntry->unit_cost
                ]);
            }
            
            // Recalculate balances for this item
            GeneralItemStockLedger::recalculateBalances($line->general_item_id);
        }

        // Reverse arms stock ledger entries
        // For edits, only reverse arms that are not in the new arm lines
        $newArmIds = collect($newArmLines)->pluck('arm_id')->toArray();
        
        foreach ($this->armLines as $line) {
            // Skip if this arm is still in the new arm lines (not being removed)
            if (!empty($newArmLines) && in_array($line->arm_id, $newArmIds)) {
                \Log::info('Arm still in new sale lines, skipping reversal', [
                    'arm_id' => $line->arm_id,
                    'sale_invoice_id' => $this->id
                ]);
                continue;
            }
            
            // Get the arm model to ensure we have fresh data
            $arm = Arm::find($line->arm_id);

            if (!$arm) {
                \Log::warning('Arm not found during reversal', ['arm_id' => $line->arm_id]);
                continue;
            }

            \Log::info('Reversing arm sale (arm being removed)', [
                'arm_id' => $arm->id,
                'serial_no' => $arm->serial_no,
                'current_status' => $arm->status,
                'sale_invoice_id' => $this->id
            ]);

            // Create reversal arms stock ledger entry
            // When reversing a sale, we need to restore the arm (quantity_in = 1)
            // The original sale had quantity_out = 1, so reversal needs quantity_in = 1
            ArmsStockLedger::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'transaction_date' => now(),
                'transaction_type' => 'reversal',
                'quantity_in' => 1, // Restore arm (opposite of original quantity_out = 1)
                'quantity_out' => 0,
                'balance' => 1, // Restore arm to available
                'reference_id' => $this->invoice_number . '-REV',
                'remarks' => 'Sale reversal for ' . ($this->party->name ?? 'Customer'),
            ]);

            // Get the original sale price from the most recent 'sale' history entry
            $saleHistory = ArmHistory::where('arm_id', $arm->id)
                ->where('action', 'sale')
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Get original sale price from the sale history (before it was sold)
            $originalSalePrice = $arm->purchase_price; // Default to purchase price
            if ($saleHistory && isset($saleHistory->old_values['sale_price'])) {
                $originalSalePrice = $saleHistory->old_values['sale_price'];
            }
            
            // Restore arm status and original sale price
            $arm->update([
                'status' => 'available',
                'sold_date' => null,
                'sale_price' => $originalSalePrice, // Restore original sale price from history
            ]);

            \Log::info('Arm status restored', [
                'arm_id' => $arm->id,
                'serial_no' => $arm->serial_no,
                'new_status' => 'available',
                'sale_invoice_id' => $this->id
            ]);

            // Create arm history entry for reversal
            ArmHistory::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'action' => 'cancel',
                'old_values' => ['status' => 'sold', 'sold_date' => $this->invoice_date, 'sale_price' => $line->sale_price],
                'new_values' => ['status' => 'available', 'sold_date' => null, 'sale_price' => $originalSalePrice],
                'transaction_date' => now(),
                'price' => $line->sale_price,
                'remarks' => 'Sale reversal for ' . ($this->party->name ?? 'Customer'),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Reverse journal entries for sale invoice edits (delete existing entries)
     */
    private function reverseJournalEntriesForEdit(): void
    {
        $businessId = $this->business_id;
        
        // For edits, delete ALL existing journal entries for this voucher
        // We're recreating all entries anyway, so delete everything
        // (Cancellation entries have voucher_type = 'SaleInvoiceCancellation', so they won't be affected)
        // Get count before deletion for logging
        $entriesBeforeDeletion = JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleInvoice')
            ->count();
        
        // Delete ALL entries for this sale invoice - no filtering by comments
        // Since we're recreating all entries, we can safely delete everything
        JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleInvoice')
            ->delete();
        
        // Defensive check: Verify all entries are deleted
        $remainingCount = JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleInvoice')
            ->count();
        
        if ($remainingCount > 0) {
            \Log::error('CRITICAL: Journal entries still exist after deletion!', [
                'sale_invoice_id' => $this->id,
                'invoice_number' => $this->invoice_number,
                'remaining_count' => $remainingCount,
                'remaining_entries' => JournalEntry::where('business_id', $businessId)
                    ->where('voucher_id', $this->id)
                    ->where('voucher_type', 'SaleInvoice')
                    ->get()
                    ->map(function($entry) {
                        return [
                            'id' => $entry->id,
                            'account_head' => $entry->account_head,
                            'debit' => $entry->debit_amount,
                            'credit' => $entry->credit_amount,
                            'comments' => $entry->comments,
                            'date_added' => $entry->date_added
                        ];
                    })->toArray()
            ]);
            
            // Last resort: Try to delete again (shouldn't be necessary, but just in case)
            $forceDeleted = JournalEntry::where('business_id', $businessId)
                ->where('voucher_id', $this->id)
                ->where('voucher_type', 'SaleInvoice')
                ->delete();
            
            \Log::info('Force deleted remaining journal entries (second attempt)', [
                'sale_invoice_id' => $this->id,
                'force_deleted_count' => $forceDeleted
            ]);
        }

        // Delete existing party ledger entries
        PartyLedger::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'Sale Invoice')
            ->delete();

        // Delete existing bank ledger entries
        BankLedger::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'Sale Invoice')
            ->delete();
    }

    /**
     * Reverse journal entries for the sale invoice (for cancellations)
     */
    private function reverseJournalEntries(): void
    {
        $businessId = $this->business_id;
        $userId = auth()->id();
        
        // Load bank relationship if not already loaded
        if (!$this->relationLoaded('bank')) {
            $this->load('bank.chartOfAccount');
        }

        // Get party's chart of account for credit sales (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($this->sale_type === 'credit' && $this->party_id) {
            $party = \App\Models\Party::find($this->party_id);
            if ($party && $party->chart_of_account_id) {
                $partyAccountId = $party->chart_of_account_id;
            }
            
            if (!$partyAccountId) {
                \Log::error('Party chart of account not found for sale invoice reversal', [
                    'sale_invoice_id' => $this->id,
                    'party_id' => $this->party_id
                ]);
                throw new \Exception('Party chart of account is required for credit sale reversal.');
            }
        }

        // Get account IDs
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
            \Log::warning('Missing required chart of accounts for sale invoice reversal', [
                'sale_invoice_id' => $this->id,
            ]);
            return;
        }

        // Reverse Entry 1: Credit Party Account (for credit sales) / Debit Sales Revenue
        if ($this->sale_type === 'credit') {
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => 0,
                'credit_amount' => $this->total_amount,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Sale Invoice Reversal ' . $this->invoice_number,
                'user_id' => $userId,
                'date_added' => now(),
            ]);
        } else {
            // Cash sale reversal - credit bank account
            if ($this->bank && $this->bank->chartOfAccount) {
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $this->bank->chartOfAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $this->total_amount,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'SaleInvoice',
                    'comments' => 'Sale Invoice Reversal ' . $this->invoice_number . ' - ' . $this->bank->account_name,
                    'user_id' => $userId,
                    'date_added' => now(),
                ]);
            } else {
                // Fallback to cash account if no bank selected
                $cashAccountId = ChartOfAccount::where('business_id', $businessId)
                    ->where('name', 'like', '%Cash%')
                    ->orWhere('name', 'like', '%Bank%')
                ->value('id');

                if ($cashAccountId) {
                JournalEntry::create([
                    'business_id' => $businessId,
                        'account_head' => $cashAccountId,
                    'debit_amount' => 0,
                    'credit_amount' => $this->total_amount,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'SaleInvoice',
                        'comments' => 'Sale Invoice Reversal ' . $this->invoice_number . ' - Cash',
                    'user_id' => $userId,
                    'date_added' => now(),
                ]);
                }
            }
        }

        // Reverse Entry 2: Debit Sales Revenue
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $salesRevenueId,
            'debit_amount' => $this->total_amount,
            'credit_amount' => 0,
            'voucher_id' => $this->id,
            'voucher_type' => 'SaleInvoice',
            'comments' => 'Sale Invoice Reversal ' . $this->invoice_number,
            'user_id' => $userId,
            'date_added' => now(),
        ]);

        // Reverse party ledger entry for credit sales (reverse the debit -> credit)
        if ($this->sale_type === 'credit' && $this->party_id) {
            PartyLedger::create([
                'business_id' => $this->business_id,
                'party_id' => $this->party_id,
                'voucher_id' => $this->id,
                'voucher_type' => 'Sale Invoice Reversal',
                'date_added' => now(),
                'user_id' => $userId,
                'debit_amount' => 0,
                'credit_amount' => $this->total_amount,
            ]);
        }

        // Reverse bank ledger entry for cash sales
        if ($this->sale_type === 'cash' && $this->bank_id) {
            BankLedger::create([
                'business_id' => $this->business_id,
                'bank_id' => $this->bank_id,
                'voucher_id' => $this->id,
                'voucher_type' => 'Sale Invoice Reversal',
                'date' => now(),
                'user_id' => $userId,
                'withdrawal_amount' => $this->total_amount, // Reverse the deposit (money going out)
                'deposit_amount' => 0,
            ]);
        }

        // Calculate total COGS for reversal
        // IMPORTANT: Get the CURRENT journal entry amounts, not from stock ledger entries
        // This ensures we reverse the correct amounts even after edits
        $totalCogs = 0;

        // Get the current COGS journal entry amount (after any edits)
        $cogsJournalEntry = JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleInvoice')
            ->where('comments', 'Cost of Goods Sold')
            ->where('debit_amount', '>', 0) // COGS is debited
            ->orderBy('id', 'desc') // Get the most recent entry (after edits)
            ->first();

        if ($cogsJournalEntry) {
            // Use the current journal entry amount (this is the correct amount after edits)
            $totalCogs = $cogsJournalEntry->debit_amount;
            \Log::info('Found COGS journal entry for reversal', [
                'sale_invoice_id' => $this->id,
                'cogs_amount' => $totalCogs,
                'journal_entry_id' => $cogsJournalEntry->id
            ]);
        } else {
            // Fallback: Calculate from current sale invoice lines if journal entry not found
            // This should rarely happen, but provides a safety net
            \Log::warning('COGS journal entry not found, calculating from invoice lines', [
                'sale_invoice_id' => $this->id
            ]);
            
            // COGS for general items - calculate from current invoice lines
            foreach ($this->generalLines as $line) {
                if ($line->batch) {
                    $cogsAmount = $line->quantity * $line->batch->unit_cost;
                    $totalCogs += $cogsAmount;
            }
        }

            // COGS for arms
        foreach ($this->armLines as $line) {
            $cogsAmount = $line->arm->purchase_price ?? 0;
            $totalCogs += $cogsAmount;
            }
        }

        // Create single summarized COGS and Inventory reversal journal entries
        if ($totalCogs > 0) {
            // Credit COGS (single entry for all items)
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $cogsId,
                'debit_amount' => 0,
                'credit_amount' => $totalCogs,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Cost of Goods Sold Reversal',
                'user_id' => $userId,
                'date_added' => now(),
            ]);

            // Debit Inventory (single entry for all items)
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $inventoryId,
                'debit_amount' => $totalCogs,
                'credit_amount' => 0,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Inventory Reversal',
                'user_id' => $userId,
                'date_added' => now(),
            ]);
        }
    }

    /**
     * Post sale invoice and create all related entries (reused from controller)
     */
    private function postSaleInvoice(): void
    {
        try {
            $businessId = $this->business_id;
            $userId = auth()->id();

            // Load fresh relationships to ensure we have the current data
            $this->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm']);

            // Update status
            $this->update([
                'status' => 'posted',
                'posted_by' => $userId
            ]);

            // Create stock ledger entries for general items
            foreach ($this->generalLines as $line) {
                // Get available batches for FIFO consumption
                $batches = GeneralBatch::where('item_id', $line->general_item_id)
                    ->where('qty_remaining', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                if ($batches->isEmpty()) {
                    \Log::warning('No available batches for general item in sale invoice', [
                        'sale_invoice_id' => $this->id,
                        'general_item_id' => $line->general_item_id,
                        'quantity' => $line->quantity
                    ]);
                    continue; // Skip this line if no batches available
                }

                $remainingQty = $line->quantity;
                $consumedBatches = [];

                foreach ($batches as $batch) {
                    if ($remainingQty <= 0)
                        break;

                    $qtyToConsume = min($remainingQty, $batch->qty_remaining);

                    // Create stock ledger entry
                    GeneralItemStockLedger::create([
                        'business_id' => $businessId,
                        'general_item_id' => $line->general_item_id,
                        'batch_id' => $batch->id,
                        'transaction_type' => 'sale',
                        'transaction_date' => $this->invoice_date,
                        'quantity' => -$qtyToConsume,
                        'quantity_out' => $qtyToConsume,
                        'balance_quantity' => $batch->qty_remaining - $qtyToConsume,
                        'unit_cost' => $batch->unit_cost,
                        'total_cost' => $qtyToConsume * $batch->unit_cost,
                        'reference_id' => $this->id,
                        'reference_no' => $this->invoice_number,
                        'remarks' => 'Sale to ' . ($this->party->name ?? 'Customer'),
                        'created_by' => $userId,
                    ]);

                    // Update batch remaining quantity
                    $batch->update(['qty_remaining' => $batch->qty_remaining - $qtyToConsume]);

                    $consumedBatches[] = [
                        'batch_id' => $batch->id,
                        'quantity' => $qtyToConsume,
                        'unit_cost' => $batch->unit_cost,
                    ];

                    $remainingQty -= $qtyToConsume;
                }

                // Update line with batch information
                if (!empty($consumedBatches)) {
                    $line->update(['batch_id' => $consumedBatches[0]['batch_id']]);
                }
                
                // Recalculate balances for this item
                GeneralItemStockLedger::recalculateBalances($line->general_item_id);
            }

            // Create stock ledger entries for arms
            foreach ($this->armLines as $line) {
                // Store old values for history (including original sale price before update)
                $oldValues = $line->arm->toArray();
                $oldSalePrice = $line->arm->sale_price;

                // Create arms stock ledger entry
                try {
                    ArmsStockLedger::create([
                        'business_id' => $businessId,
                        'arm_id' => $line->arm_id,
                        'transaction_date' => $this->invoice_date,
                        'transaction_type' => 'sale',
                        'quantity_out' => 1,
                        'balance' => 0,
                        'reference_id' => $this->invoice_number,
                        'remarks' => 'Sale to ' . ($this->party->name ?? 'Customer'),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error creating arms stock ledger: ' . $e->getMessage(), [
                        'arm_id' => $line->arm_id,
                        'sale_invoice_id' => $this->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }

                // Update arm status and sale price
                $line->arm->update([
                    'status' => 'sold',
                    'sold_date' => $this->invoice_date,
                    'sale_price' => $line->sale_price, // Update the arm's sale price with actual sale price
                ]);

                // Create arm history entry
                try {
                    ArmHistory::create([
                        'business_id' => $businessId,
                        'arm_id' => $line->arm_id,
                        'action' => 'sale',
                        'old_values' => array_merge($oldValues, ['sale_price' => $oldSalePrice]), // Ensure old sale price is captured
                        'new_values' => $line->arm->fresh()->toArray(),
                        'transaction_date' => $this->invoice_date,
                        'price' => $line->sale_price,
                        'remarks' => 'Sale to ' . ($this->party->name ?? 'Customer'),
                        'user_id' => $userId,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error creating arm history: ' . $e->getMessage(), [
                        'arm_id' => $line->arm_id,
                        'sale_invoice_id' => $this->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            // Create party ledger entry for credit sales (party owes us -> debit)
            if ($this->sale_type === 'credit' && $this->party_id) {
                PartyLedger::create([
                    'business_id' => $this->business_id,
                    'party_id' => $this->party_id,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'Sale Invoice',
                    'date_added' => $this->invoice_date,
                    'user_id' => $userId,
                    'debit_amount' => $this->total_amount,
                    'credit_amount' => 0,
                ]);
            }

            // Create journal entries
            $this->createJournalEntries();

            // Create audit log
            SaleInvoiceAuditLog::create([
                'sale_invoice_id' => $this->id,
                'action' => 'posted',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'posted'],
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error posting sale invoice: ' . $e->getMessage(), [
                'sale_invoice_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }

    /**
     * Create stock ledger entries for edited sale invoice
     * This method creates entries for the entire new quantity since the reversal already restored the stock
     */
    private function createStockLedgerEntriesForEdit(): void
    {
        $businessId = $this->business_id;
        $userId = auth()->id();

        // Create stock ledger entries for general items
        foreach ($this->generalLines as $line) {
            // Get available batches for FIFO consumption
            $batches = GeneralBatch::where('item_id', $line->general_item_id)
                ->where('qty_remaining', '>', 0)
                ->orderBy('created_at')
                ->get();

            if ($batches->isEmpty()) {
                \Log::warning('No available batches for general item in sale invoice edit', [
                    'sale_invoice_id' => $this->id,
                    'general_item_id' => $line->general_item_id,
                    'quantity' => $line->quantity
                ]);
                continue;
            }

            $remainingQty = $line->quantity;

            foreach ($batches as $batch) {
                if ($remainingQty <= 0) break;

                $qtyToConsume = min($remainingQty, $batch->qty_remaining);

                // Create stock ledger entry
                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $line->general_item_id,
                    'batch_id' => $batch->id,
                    'transaction_type' => 'sale',
                    'transaction_date' => $this->invoice_date,
                    'quantity' => -$qtyToConsume,
                    'quantity_in' => 0,
                    'quantity_out' => $qtyToConsume,
                    'balance_quantity' => 0, // Will be recalculated by recalculateBalances
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $qtyToConsume * $batch->unit_cost,
                    'reference_id' => $this->id,
                    'reference_no' => $this->invoice_number,
                    'remarks' => 'Sale to ' . ($this->party->name ?? 'Customer'),
                    'created_by' => $userId,
                ]);

                // Update batch remaining quantity
                $batch->update(['qty_remaining' => $batch->qty_remaining - $qtyToConsume]);

                $remainingQty -= $qtyToConsume;
            }
            
            // Recalculate balances for this item after all entries are created
            GeneralItemStockLedger::recalculateBalances($line->general_item_id);
        }
    }

    /**
     * Create journal entries for the sale invoice (reused from controller)
     */
    private function createJournalEntries(): void
    {
        $businessId = $this->business_id;
        $userId = auth()->id();
        
        // Load bank relationship if not already loaded
        if (!$this->relationLoaded('bank')) {
            $this->load('bank.chartOfAccount');
        }

        // Check if journal entries already exist for this sale to prevent duplicates
        $existingEntries = JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $this->id)
            ->where('voucher_type', 'SaleInvoice')
            ->where('comments', 'not like', '%Reversal%')
            ->exists();

        if ($existingEntries) {
            \Log::warning('Journal entries already exist for this sale invoice, skipping creation', [
                'sale_invoice_id' => $this->id,
                'voucher_type' => 'SaleInvoice'
            ]);
            return;
        }

        // Get party's chart of account for credit sales (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($this->sale_type === 'credit' && $this->party_id) {
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
                    throw new \Exception('Failed to create or retrieve party chart of account for credit sale.');
                }
            } else {
                throw new \Exception('Party not found for credit sale.');
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

        // If any required account is missing, skip journal entries
        if (!$salesRevenueId || !$cogsId || !$inventoryId) {
            \Log::warning('Missing required chart of accounts for sale invoice posting', [
                'sale_invoice_id' => $this->id,
                'sales_revenue_id' => $salesRevenueId,
                'cogs_id' => $cogsId,
                'inventory_id' => $inventoryId
            ]);
            return; // Skip journal entries but continue with other posting operations
        }

        // Entry 1: Debit Party Account (for credit sales) / Debit Bank (for cash sales) / Credit Sales Revenue
        if ($this->sale_type === 'credit') {
            // Credit sale - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit sales.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => $this->total_amount,
                'credit_amount' => 0,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Sale Invoice ' . $this->invoice_number,
                'user_id' => $userId,
                'date_added' => $this->invoice_date,
            ]);
        } else {
            // Cash sale - debit bank account
            if ($this->bank && $this->bank->chartOfAccount) {
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $this->bank->chartOfAccount->id,
                    'debit_amount' => $this->total_amount,
                    'credit_amount' => 0,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'SaleInvoice',
                    'comments' => 'Sale Invoice ' . $this->invoice_number . ' - ' . $this->bank->account_name,
                    'user_id' => $userId,
                    'date_added' => $this->invoice_date,
                ]);
            } else {
                // Fallback to cash account if no bank selected
                $cashAccountId = ChartOfAccount::where('business_id', $businessId)
                    ->where('name', 'like', '%Cash%')
                    ->orWhere('name', 'like', '%Bank%')
                    ->value('id');

                if ($cashAccountId) {
                JournalEntry::create([
                    'business_id' => $businessId,
                        'account_head' => $cashAccountId,
                    'debit_amount' => $this->total_amount,
                    'credit_amount' => 0,
                    'voucher_id' => $this->id,
                    'voucher_type' => 'SaleInvoice',
                        'comments' => 'Sale Invoice ' . $this->invoice_number . ' - Cash',
                    'user_id' => $userId,
                    'date_added' => $this->invoice_date,
                ]);
                }
            }
        }

        // Entry 2: Credit Sales Revenue
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $salesRevenueId,
            'debit_amount' => 0,
            'credit_amount' => $this->total_amount,
            'voucher_id' => $this->id,
            'voucher_type' => 'SaleInvoice',
            'comments' => 'Sale Invoice ' . $this->invoice_number,
            'user_id' => $userId,
            'date_added' => $this->invoice_date,
        ]);

        // Create party ledger entry for credit sales (party owes us -> debit)
        if ($this->sale_type === 'credit' && $this->party_id) {
            PartyLedger::create([
                'business_id' => $this->business_id,
                'party_id' => $this->party_id,
                'voucher_id' => $this->id,
                'voucher_type' => 'Sale Invoice',
                'date_added' => $this->invoice_date,
                'user_id' => $userId,
                'debit_amount' => $this->total_amount,
                'credit_amount' => 0,
            ]);
        }

        // Create bank ledger entry for cash sales
        if ($this->sale_type === 'cash' && $this->bank_id) {
            BankLedger::create([
                'business_id' => $this->business_id,
                'bank_id' => $this->bank_id,
                'voucher_id' => $this->id,
                'voucher_type' => 'Sale Invoice',
                'date' => $this->invoice_date,
                'user_id' => $userId,
                'deposit_amount' => $this->total_amount, // Money coming into bank
                'withdrawal_amount' => 0,
            ]);
        }

        // Calculate total COGS
        $totalCogs = 0;

        // COGS for general items - calculate based on actual stock ledger entries
        foreach ($this->generalLines as $line) {
            // Get the stock ledger entries for this line to calculate correct COGS
            // For edits, we need to exclude entries that have been reversed
            $stockEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                ->where('reference_no', $this->invoice_number)
                ->where('transaction_type', 'sale')
                ->where('quantity', '<', 0) // Only sale entries (negative quantity)
                ->get();

            // Get reversal entries for this item and invoice
            $reversalEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                ->where('reference_no', 'like', $this->invoice_number . '%')
                ->where('transaction_type', 'reversal')
                ->where('quantity', '>', 0)
                ->get();

            // Calculate net COGS by subtracting reversals from sales
            $totalCogsForLine = 0;
            foreach ($stockEntries as $entry) {
                $totalCogsForLine += abs($entry->total_cost);
            }
            
            foreach ($reversalEntries as $reversal) {
                $totalCogsForLine -= $reversal->total_cost;
            }
            
            // Only add positive COGS
            $totalCogsForLine = max(0, $totalCogsForLine);
            $totalCogs += $totalCogsForLine;
        }

        // COGS for arms - use the same inventory account
        foreach ($this->armLines as $line) {
            $cogsAmount = $line->arm->purchase_price ?? 0;
            $totalCogs += $cogsAmount;
        }

        // Create single summarized COGS and Inventory journal entries
        if ($totalCogs > 0) {
            // Debit COGS (single entry for all items)
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $cogsId,
                'debit_amount' => $totalCogs,
                'credit_amount' => 0,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Cost of Goods Sold',
                'user_id' => $userId,
                'date_added' => $this->invoice_date,
            ]);

            // Credit Inventory (single entry for all items)
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $inventoryId,
                'debit_amount' => 0,
                'credit_amount' => $totalCogs,
                'voucher_id' => $this->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Inventory',
                'user_id' => $userId,
                'date_added' => $this->invoice_date,
            ]);
        }
    }
}
