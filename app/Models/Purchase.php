<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
use App\Models\Arm;
use App\Models\ArmsMake;
use App\Models\GeneralItemStockLedger;
use App\Models\ArmsStockLedger;
use App\Models\ArmHistory;

class Purchase extends Model
{
    protected $fillable = [
        'business_id',
        'party_id',
        'payment_type',
        'bank_id',
        'invoice_date',
        'subtotal',
        'shipping_charges',
        'total_amount',
        'status',
        'created_by',
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

    public function generalLines(): HasMany
    {
        return $this->hasMany(PurchaseGeneralLine::class);
    }

    public function armLines(): HasMany
    {
        return $this->hasMany(PurchaseArmLine::class);
    }

    public function armSerials(): HasManyThrough
    {
        return $this->hasManyThrough(PurchaseArmSerial::class, PurchaseArmLine::class);
    }

    public function generalBatches(): HasMany
    {
        return $this->hasMany(GeneralBatch::class);
    }

    public function arms(): HasMany
    {
        return $this->hasMany(Arm::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PurchaseAuditLog::class);
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
        return $this->isDraft() && ($this->generalLines()->count() > 0 || $this->armLines()->count() > 0);
    }

    public function canBeCancelled(): bool
    {
        // Allow cancelling of all posted purchases regardless of consumption or sales
        return $this->isPosted();
    }

    public function canBeEdited(): bool
    {
        // Allow editing of draft and posted purchases
        return in_array($this->status, ['draft', 'posted']);
    }

    public function canBeEditedAfterPosting(): bool
    {
        // Allow editing of all posted purchases regardless of consumption or sales
        return $this->isPosted();
    }

    public function isEditedAfterPosting(): bool
    {
        return $this->isPosted() && $this->updated_at > $this->created_at;
    }

    public function getEditRestrictions(): array
    {
        $restrictions = [];

        if ($this->isPosted()) {
                $restrictions[] = 'Warning: Editing posted purchases will adjust inventory automatically';
        }

        return $restrictions;
    }

    public function adjustInventoryForEdit(array $newGeneralLines, array $newArmLines): void
    {
        // Store pre-edit state for audit trail
        $this->storePreEditState();

        // Adjust general item batches
        $this->adjustGeneralItemBatches($newGeneralLines);

        // Adjust arm inventory
        $this->adjustArmInventory($newArmLines);
    }

    private function storePreEditState(): void
    {
        // Store current state in the audit log table
        $preEditState = [
            'general_lines' => $this->generalLines->toArray(),
            'arm_lines' => $this->armLines->toArray(),
            'general_batches' => $this->generalBatches->toArray(),
            'arms' => $this->arms->toArray(),
            'subtotal' => $this->subtotal,
            'total_amount' => $this->total_amount,
            'edited_at' => now(),
            'edited_by' => auth()->id(),
        ];

        // Create audit log entry
        $this->auditLogs()->create([
            'business_id' => $this->business_id,
            'user_id' => auth()->id(),
            'action' => 'edit_started',
            'description' => 'Purchase edit started - storing current state',
            'old_values' => $preEditState,
            'new_values' => null,
            'changes' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function adjustGeneralItemBatches(array $newGeneralLines): void
    {
        // Get current batches
        $currentBatches = $this->generalBatches;

        // Create a map of item_id to current batch
        $currentBatchMap = $currentBatches->keyBy('item_id');

        foreach ($newGeneralLines as $lineData) {
            $itemId = $lineData['general_item_id'];
            $newQty = $lineData['qty'];
            $newUnitPrice = $lineData['unit_price'];

            if ($currentBatchMap->has($itemId)) {
                $currentBatch = $currentBatchMap[$itemId];
                $oldQty = $currentBatch->qty_received;
                $oldUnitPrice = $currentBatch->unit_cost;

                if ($newQty != $oldQty || $newUnitPrice != $oldUnitPrice) {
                    // Adjust batch quantities and costs
                    $this->adjustGeneralBatch($currentBatch, $oldQty, $newQty, $oldUnitPrice, $newUnitPrice);
                }
            } else {
                // Create new batch for new item
                $this->createGeneralBatch($lineData);
            }
        }

        // Remove batches for items no longer in the purchase
        $newItemIds = collect($newGeneralLines)->pluck('general_item_id')->toArray();
        $currentBatches->whereNotIn('item_id', $newItemIds)->each(function ($batch) {
            $this->removeGeneralBatch($batch);
        });
    }

    private function adjustArmInventory(array $newArmLines): void
    {
        // Get all current active arms for this purchase, ordered by ID
        $currentArms = $this->arms()
            ->whereNotIn('status', ['decommissioned'])
            ->orderBy('id')
            ->get();
        
        // Collect all new serials with their line data
        $newSerialsData = [];
        foreach ($newArmLines as $lineData) {
            if (isset($lineData['serials']) && !empty($lineData['serials'])) {
                $serials = array_map('trim', explode(',', $lineData['serials']));
                $serials = array_filter($serials, function($s) { return !empty($s); });
                foreach ($serials as $serial) {
                    $newSerialsData[] = [
                        'serial' => $serial,
                        'line_data' => $lineData
                    ];
                }
            }
        }

        // Create maps for matching
        $currentArmsMap = $currentArms->keyBy('serial_no');
        $newSerialsMap = collect($newSerialsData)->keyBy('serial');
        
        $matchedArmIds = [];
        $unmatchedCurrentArms = collect();
        $unmatchedNewSerials = collect();

        // STEP 1: Match by exact serial number (handles unchanged serials)
        foreach ($currentArms as $arm) {
            if ($newSerialsMap->has($arm->serial_no)) {
                // Serial exists in both - update the arm with possibly changed attributes
                $serialData = $newSerialsMap->get($arm->serial_no);
                $this->updateArmWithSerial($arm, $arm->serial_no, $serialData['line_data']);
                $matchedArmIds[] = $arm->id;
            } else {
                // Serial no longer exists - mark as unmatched for potential reuse
                $unmatchedCurrentArms->push($arm);
            }
        }

        // STEP 2: Identify unmatched new serials
        foreach ($newSerialsData as $serialData) {
            if (!$currentArmsMap->has($serialData['serial'])) {
                $unmatchedNewSerials->push($serialData);
            }
        }

        // STEP 3: Match unmatched items by position (handles serial number changes)
        $unmatchedCurrentArms = $unmatchedCurrentArms->values(); // Re-index
        $unmatchedNewSerials = $unmatchedNewSerials->values(); // Re-index
        
        $minCount = min($unmatchedCurrentArms->count(), $unmatchedNewSerials->count());
        
        for ($i = 0; $i < $minCount; $i++) {
            $arm = $unmatchedCurrentArms[$i];
            $serialData = $unmatchedNewSerials[$i];
            
            // Update this arm with the new serial
            $this->updateArmWithSerial($arm, $serialData['serial'], $serialData['line_data']);
            $matchedArmIds[] = $arm->id;
        }

        // STEP 4: Handle remaining unmatched items
        
        // If more current arms than new serials - decommission extras
        for ($i = $minCount; $i < $unmatchedCurrentArms->count(); $i++) {
            $this->removeArm($unmatchedCurrentArms[$i]);
        }
        
        // If more new serials than current arms - create new arms
        for ($i = $minCount; $i < $unmatchedNewSerials->count(); $i++) {
            $serialData = $unmatchedNewSerials[$i];
            $this->createArm($serialData['serial'], $serialData['line_data']);
        }
    }

    private function adjustGeneralBatch($batch, $oldQty, $newQty, $oldUnitPrice, $newUnitPrice): void
    {
        // Check if any items from this batch have been sold
        $soldQty = GeneralItemStockLedger::where('general_item_id', $batch->item_id)
            ->where('batch_id', $batch->id)
            ->where('transaction_type', 'sale')
            ->where('quantity', '<', 0)
            ->sum('quantity');
        
        $soldQty = abs($soldQty); // Convert to positive
        
        // Calculate the net change
        $qtyChange = $newQty - $oldQty;
        $priceChange = $newUnitPrice - $oldUnitPrice;
        
        if ($soldQty > 0) {
            // Items have been sold - use smart adjustment approach
            $this->handlePurchaseEditWithSales($batch, $oldQty, $oldUnitPrice, $newQty, $newUnitPrice, $soldQty, $qtyChange, $priceChange);
        } else {
            // No items sold - use simple reversal approach
            $this->handlePurchaseEditWithoutSales($batch, $oldQty, $oldUnitPrice, $newQty, $newUnitPrice);
        }

        // Log the change
        $this->logChange(
            'batch_adjusted',
            "General item batch adjusted: {$batch->item->item_name} (sold items: {$soldQty})",
            ['old_qty' => $oldQty, 'old_unit_cost' => $oldUnitPrice],
            ['new_qty' => $newQty, 'new_unit_cost' => $newUnitPrice],
            [
                'quantity' => ['old' => $oldQty, 'new' => $newQty],
                'unit_cost' => ['old' => $oldUnitPrice, 'new' => $newUnitPrice]
            ]
        );
    }

    /**
     * Handle purchase edit when items have been sold (smart approach)
     */
    private function handlePurchaseEditWithSales($batch, $oldQty, $oldUnitPrice, $newQty, $newUnitPrice, $soldQty, $qtyChange, $priceChange): void
    {
        // 1. Update the batch with new values
        $batch->update([
            'qty_received' => $newQty,
            'qty_remaining' => $newQty - $soldQty, // Adjust remaining based on sales
            'unit_cost' => $newUnitPrice,
            'total_cost' => $newQty * $newUnitPrice,
        ]);

        // 2. Create adjustment entry for quantity change (if any)
        if ($qtyChange != 0) {
            GeneralItemStockLedger::create([
                'business_id' => $this->business_id,
                'general_item_id' => $batch->item_id,
                'batch_id' => $batch->id,
                'transaction_type' => 'adjustment',
                'transaction_date' => now(),
                'quantity' => $qtyChange,
                'quantity_in' => $qtyChange > 0 ? $qtyChange : 0,
                'quantity_out' => $qtyChange < 0 ? abs($qtyChange) : 0,
                'balance_quantity' => 0, // Will be recalculated
                'unit_cost' => $newUnitPrice,
                'total_cost' => $qtyChange * $newUnitPrice,
                'reference_id' => $this->id,
                'purchase_id' => $this->id,
                'remarks' => 'Purchase edit - quantity adjustment (sold items: ' . $soldQty . ')',
                'created_by' => auth()->id(),
            ]);
        }

        // 3. Create adjustment entry for price change (if any and if items were sold)
        if ($priceChange != 0 && $soldQty > 0) {
            $priceAdjustmentAmount = $soldQty * $priceChange;
            GeneralItemStockLedger::create([
                'business_id' => $this->business_id,
                'general_item_id' => $batch->item_id,
                'batch_id' => $batch->id,
                'transaction_type' => 'adjustment',
                'transaction_date' => now(),
                'quantity' => 0, // No quantity change, only price
                'quantity_in' => 0,
                'quantity_out' => 0,
                'balance_quantity' => 0,
                'unit_cost' => $priceChange,
                'total_cost' => $priceAdjustmentAmount,
                'reference_id' => $this->id,
                'purchase_id' => $this->id,
                'remarks' => 'Purchase edit - price adjustment for sold items (' . $soldQty . ' qty)',
                'created_by' => auth()->id(),
            ]);
        }

        // 4. Recalculate balances for this item
        GeneralItemStockLedger::recalculateBalances($batch->item_id);
    }

    /**
     * Handle purchase edit when no items have been sold (simple approach)
     */
    private function handlePurchaseEditWithoutSales($batch, $oldQty, $oldUnitPrice, $newQty, $newUnitPrice): void
    {
        // 1. Create reversal entry for the original purchase
        GeneralItemStockLedger::create([
            'business_id' => $this->business_id,
            'general_item_id' => $batch->item_id,
            'batch_id' => $batch->id,
            'transaction_type' => 'reversal',
            'transaction_date' => now(),
            'quantity' => -$oldQty, // Negative to reverse
            'quantity_in' => 0,
            'quantity_out' => $oldQty,
            'balance_quantity' => 0, // Will be recalculated
            'unit_cost' => $oldUnitPrice,
            'total_cost' => -($oldQty * $oldUnitPrice), // Negative total cost
            'reference_id' => $this->id,
            'purchase_id' => $this->id,
            'remarks' => 'Purchase edit - reversing original entry',
            'created_by' => auth()->id(),
        ]);

        // 2. Update the batch with new values
        $batch->update([
            'qty_received' => $newQty,
            'qty_remaining' => $newQty,
            'unit_cost' => $newUnitPrice,
            'total_cost' => $newQty * $newUnitPrice,
        ]);

        // 3. Create new purchase entry with updated values
        GeneralItemStockLedger::create([
            'business_id' => $this->business_id,
            'general_item_id' => $batch->item_id,
            'batch_id' => $batch->id,
            'transaction_type' => 'purchase',
            'transaction_date' => now(),
            'quantity' => $newQty,
            'quantity_in' => $newQty,
            'quantity_out' => 0,
            'balance_quantity' => $newQty,
            'unit_cost' => $newUnitPrice,
            'total_cost' => $newQty * $newUnitPrice,
            'reference_id' => $this->id,
            'purchase_id' => $this->id,
            'remarks' => 'Purchase edit - updated entry',
            'created_by' => auth()->id(),
        ]);

        // 4. Recalculate balances for this item
        GeneralItemStockLedger::recalculateBalances($batch->item_id);
    }

    private function createGeneralBatch($lineData): void
    {
        $batch = GeneralBatch::create([
            'business_id' => $this->business_id,
            'item_id' => $lineData['general_item_id'],
            'qty_received' => $lineData['qty'],
            'qty_remaining' => $lineData['qty'],
            'unit_cost' => $lineData['unit_price'],
            'total_cost' => $lineData['qty'] * $lineData['unit_price'],
            'received_date' => $this->invoice_date,
            'user_id' => auth()->id(),
            'purchase_id' => $this->id,
            'batch_code' => 'PUR-' . $this->id . '-' . uniqid(),
            'status' => 'active',
        ]);

        // Create stock ledger entry
        GeneralItemStockLedger::create([
            'business_id' => $this->business_id,
            'general_item_id' => $lineData['general_item_id'],
            'batch_id' => $batch->id,
            'transaction_type' => 'purchase',
            'transaction_date' => now(),
            'quantity' => $lineData['qty'],
            'quantity_in' => $lineData['qty'],
            'quantity_out' => 0,
            'balance_quantity' => $lineData['qty'],
            'unit_cost' => $lineData['unit_price'],
            'total_cost' => $lineData['qty'] * $lineData['unit_price'],
            'reference_id' => $this->id,
            'purchase_id' => $this->id,
            'remarks' => 'Purchase edit - new item added',
            'created_by' => auth()->id(),
        ]);

        // Log the change
        $this->logChange(
            'batch_created',
            "New general item batch created: {$batch->item->item_name}",
            null,
            ['qty' => $lineData['qty'], 'unit_cost' => $lineData['unit_price']],
            [
                'action' => 'new_batch_created',
                'item' => $batch->item->item_name,
                'quantity' => $lineData['qty'],
                'unit_cost' => $lineData['unit_price']
            ]
        );
    }

    private function removeGeneralBatch($batch): void
    {
        // Create stock ledger entry for removal
            GeneralItemStockLedger::create([
                'business_id' => $this->business_id,
                'general_item_id' => $batch->item_id,
                'batch_id' => $batch->id,
            'transaction_type' => 'adjustment',
                'transaction_date' => now(),
            'quantity' => -$batch->qty_remaining,
                'quantity_in' => 0,
            'quantity_out' => $batch->qty_remaining,
                'balance_quantity' => 0,
                'unit_cost' => $batch->unit_cost,
                'total_cost' => -$batch->total_cost,
                'reference_id' => $this->id,
                'purchase_id' => $this->id,
            'remarks' => 'Purchase edit - item removed',
            'created_by' => auth()->id(),
            ]);

        // Mark batch as removed
            $batch->update([
            'status' => 'deleted',
                'qty_remaining' => 0,
            ]);

        // Log the change
        $this->logChange(
            'batch_removed',
            "General item batch removed: {$batch->item->item_name}",
            ['qty' => $batch->qty_received, 'unit_cost' => $batch->unit_cost, 'status' => 'active'],
            ['qty' => 0, 'unit_cost' => $batch->unit_cost, 'status' => 'deleted'],
            [
                'action' => 'batch_removed',
                'item' => $batch->item->item_name,
                'quantity_removed' => $batch->qty_received
            ]
        );
    }

    private function updateArm($arm, $lineData): void
    {
        $oldValues = $arm->toArray();
        
        // Update arm details
        $arm->update([
            'arm_type_id' => $lineData['arm_type_id'] ?? $arm->arm_type_id,
            'arm_category_id' => $lineData['arm_category_id'] ?? $arm->arm_category_id,
            'make' => $lineData['arm_make_id'] ? ArmsMake::find($lineData['arm_make_id'])->arm_make : $arm->make,
            'arm_caliber_id' => $lineData['arm_caliber_id'] ?? $arm->arm_caliber_id,
            'arm_condition_id' => $lineData['arm_condition_id'] ?? $arm->arm_condition_id,
            'purchase_price' => $lineData['unit_price'] ?? $arm->purchase_price,
            'sale_price' => $lineData['sale_price'] ?? $arm->sale_price,
        ]);
        
        // Regenerate arm title based on current attributes
        $arm->refresh();
        $arm->update(['arm_title' => $arm->generateArmTitle()]);

        // Create arm history
            ArmHistory::create([
                'business_id' => $this->business_id,
                'arm_id' => $arm->id,
                'action' => 'edit',
                'old_values' => $oldValues, // Store as array, not JSON string
                'new_values' => $arm->fresh()->toArray(), // Store as array, not JSON string
                'transaction_date' => now(),
                'price' => $arm->purchase_price ?? 0,
                'remarks' => 'Purchase edit - arm details updated',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

        // Log the change in purchase audit log
        $this->logChange(
            'arm_updated',
            "Arm details updated: {$arm->serial_no}",
            $oldValues,
            $arm->toArray(),
            [
                'serial_no' => $arm->serial_no,
                'action' => 'arm_details_updated'
            ]
        );

        // Update stock ledger
        ArmsStockLedger::create([
                'business_id' => $this->business_id,
            'arm_id' => $arm->id,
            'transaction_date' => now(),
            'transaction_type' => 'adjustment',
            'quantity_in' => 0,
                'quantity_out' => 0,
            'balance' => 1,
                'reference_id' => $this->id,
                'purchase_id' => $this->id,
            'remarks' => 'Purchase edit - arm details updated',
        ]);
    }

    /**
     * Update arm including serial number (used when editing purchases by position)
     */
    private function updateArmWithSerial($arm, string $newSerial, $lineData): void
    {
        $oldValues = $arm->toArray();
        $serialChanged = $arm->serial_no !== $newSerial;
        
        // Build update data - only include serial_no if it actually changed
        $updateData = [
            'arm_type_id' => $lineData['arm_type_id'] ?? $arm->arm_type_id,
            'arm_category_id' => $lineData['arm_category_id'] ?? $arm->arm_category_id,
            'make' => $lineData['arm_make_id'] ? ArmsMake::find($lineData['arm_make_id'])->arm_make : $arm->make,
            'arm_caliber_id' => $lineData['arm_caliber_id'] ?? $arm->arm_caliber_id,
            'arm_condition_id' => $lineData['arm_condition_id'] ?? $arm->arm_condition_id,
            'purchase_price' => $lineData['unit_price'] ?? $arm->purchase_price,
            'sale_price' => $lineData['sale_price'] ?? $arm->sale_price,
        ];
        
        // Only update serial_no if it has changed (avoids potential unique constraint issues)
        if ($serialChanged) {
            $updateData['serial_no'] = $newSerial;
        }
        
        // Update arm details
        $arm->update($updateData);
        
        // Regenerate arm title based on current attributes
        $arm->refresh();
        $arm->update(['arm_title' => $arm->generateArmTitle()]);

        // Create arm history
        ArmHistory::create([
            'business_id' => $this->business_id,
            'arm_id' => $arm->id,
            'action' => 'edit',
            'old_values' => $oldValues, // Store as array, not JSON string
            'new_values' => $arm->fresh()->toArray(), // Store as array, not JSON string
            'transaction_date' => now(),
            'price' => $arm->purchase_price ?? 0,
            'remarks' => $serialChanged 
                ? "Purchase edit - serial number changed from {$oldValues['serial_no']} to {$newSerial}" 
                : 'Purchase edit - arm details updated',
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log the change in purchase audit log
        $remarks = $serialChanged 
            ? "Arm serial number changed from {$oldValues['serial_no']} to {$newSerial}" 
            : "Arm details updated: {$arm->serial_no}";
            
        $this->logChange(
            'arm_updated',
            $remarks,
            $oldValues,
            $arm->toArray(),
            [
                'old_serial_no' => $oldValues['serial_no'],
                'new_serial_no' => $newSerial,
                'serial_changed' => $serialChanged,
                'action' => 'arm_details_updated'
            ]
        );

        // Update stock ledger
        ArmsStockLedger::create([
            'business_id' => $this->business_id,
            'arm_id' => $arm->id,
            'transaction_date' => now(),
            'transaction_type' => 'adjustment',
            'quantity_in' => 0,
            'quantity_out' => 0,
            'balance' => 1,
            'reference_id' => $this->id,
            'purchase_id' => $this->id,
            'remarks' => $serialChanged 
                ? "Purchase edit - serial number changed from {$oldValues['serial_no']} to {$newSerial}" 
                : 'Purchase edit - arm details updated',
        ]);
    }

    private function createArm($serial, $lineData): Arm
    {
                $arm = Arm::create([
                    'business_id' => $this->business_id,
            'arm_type_id' => $lineData['arm_type_id'] ?? 1,
            'arm_category_id' => $lineData['arm_category_id'] ?? 1,
            'make' => $lineData['arm_make_id'] ? ArmsMake::find($lineData['arm_make_id'])->arm_make : 'Unknown',
            'arm_caliber_id' => $lineData['arm_caliber_id'] ?? 1,
            'arm_condition_id' => $lineData['arm_condition_id'] ?? 1,
            'serial_no' => $serial,
            'purchase_price' => $lineData['unit_price'] ?? 0,
            'sale_price' => $lineData['sale_price'] ?? 0,
                    'purchase_date' => $this->invoice_date,
                    'status' => 'available',
            'arm_title' => $lineData['description'] ?? 'Unknown Arm',
                    'purchase_id' => $this->id,
                ]);
                
                // Generate and set arm title based on arm attributes
                $arm->update(['arm_title' => $arm->generateArmTitle()]);

                // Create stock ledger entry
                ArmsStockLedger::create([
                    'business_id' => $this->business_id,
                    'arm_id' => $arm->id,
            'transaction_date' => now(),
            'transaction_type' => 'purchase',
                    'quantity_in' => 1,
                    'quantity_out' => 0,
                    'balance' => 1,
                    'reference_id' => $this->id,
                    'purchase_id' => $this->id,
            'remarks' => 'Purchase edit - new arm added',
                ]);

                // Create arm history
                ArmHistory::create([
                    'business_id' => $this->business_id,
                    'arm_id' => $arm->id,
                    'action' => 'purchase',
                    'old_values' => null,
                    'new_values' => json_encode($arm->toArray()),
                    'transaction_date' => $this->invoice_date,
                    'price' => $arm->purchase_price ?? 0,
                    'remarks' => 'Purchase - new arm added',
                    'user_id' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

        // Log the change in purchase audit log
        $this->logChange(
            'arm_created',
            "New arm created: {$arm->serial_no}",
            null,
            $arm->toArray(),
            [
                'serial_no' => $arm->serial_no,
                'action' => 'new_arm_created',
                'arm_title' => $arm->arm_title
            ]
        );
        
        return $arm;
    }

    private function removeArm($arm): void
    {
        // Create stock ledger entry for removal
        ArmsStockLedger::create([
            'business_id' => $this->business_id,
            'arm_id' => $arm->id,
            'transaction_date' => now(),
            'transaction_type' => 'adjustment',
            'quantity_in' => 0,
            'quantity_out' => 1,
            'balance' => 0,
            'reference_id' => $this->id,
            'purchase_id' => $this->id,
            'remarks' => 'Purchase edit - arm removed',
        ]);

        // Mark arm as decommissioned
        $arm->update(['status' => 'decommissioned']);

        // Create arm history
        ArmHistory::create([
            'business_id' => $this->business_id,
            'arm_id' => $arm->id,
            'action' => 'edit',
            'old_values' => ['status' => 'available'], // Store as array, not JSON string
            'new_values' => ['status' => 'decommissioned'], // Store as array, not JSON string
            'transaction_date' => now(),
            'price' => $arm->purchase_price ?? 0,
            'remarks' => 'Purchase edit - arm removed',
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log the change in purchase audit log
        $this->logChange(
            'arm_removed',
            "Arm removed: {$arm->serial_no}",
            ['status' => 'available'],
            ['status' => 'decommissioned'],
            [
                'serial_no' => $arm->serial_no,
                'action' => 'arm_removed'
            ]
        );
    }

    public function getEditHistory(): array
    {
        $history = [];

        // Get audit log entries
        $auditLogs = $this->auditLogs()->orderBy('created_at', 'desc')->get();

        foreach ($auditLogs as $log) {
            $history[] = [
                'action' => $log->action,
                'date' => $log->created_at,
                'description' => $log->description,
                'type' => $log->action === 'edit_started' ? 'warning' : 'adjustment',
                'details' => $log->changes,
                'user' => $log->user->name ?? 'Unknown User'
            ];
        }

        // Check if purchase was edited after posting
        if ($this->isEditedAfterPosting()) {
            $history[] = [
                'action' => 'edited_after_posting',
                'date' => $this->updated_at,
                'description' => 'Purchase was edited after being posted',
                'type' => 'warning',
                'user' => 'System'
            ];
        }

        // Get stock ledger entries for adjustments
        $adjustments = GeneralItemStockLedger::where('purchase_id', $this->id)
            ->whereIn('transaction_type', ['adjustment', 'purchase'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        foreach ($adjustments as $adjustment) {
            $history[] = [
                'action' => $adjustment->transaction_type,
                'date' => $adjustment->transaction_date,
                'description' => $adjustment->remarks,
                'type' => 'adjustment',
                'details' => [
                    'item' => $adjustment->generalItem->item_name ?? 'N/A',
                    'quantity' => $adjustment->quantity,
                    'unit_cost' => $adjustment->unit_cost
                ],
                'user' => $adjustment->createdBy->name ?? 'Unknown User'
            ];
        }

        // Get arm stock ledger entries for adjustments
        $armAdjustments = ArmsStockLedger::where('purchase_id', $this->id)
            ->whereIn('transaction_type', ['adjustment', 'purchase'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        foreach ($armAdjustments as $adjustment) {
            $history[] = [
                'action' => $adjustment->transaction_type,
                'date' => $adjustment->transaction_date,
                'description' => $adjustment->remarks,
                'type' => 'adjustment',
                'details' => [
                    'arm' => $adjustment->arm->serial_no ?? 'N/A',
                    'quantity' => $adjustment->quantity_in - $adjustment->quantity_out
                ],
                'user' => 'System'
            ];
        }

        // Sort by date descending
        usort($history, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return $history;
    }

    public function hasEditHistory(): bool
    {

        return $this->auditLogs()->exists() || 
               $this->isEditedAfterPosting() || 
               GeneralItemStockLedger::where('purchase_id', $this->id)
                   ->whereIn('transaction_type', ['adjustment', 'purchase'])
                   ->exists() ||
               ArmsStockLedger::where('purchase_id', $this->id)
                   ->whereIn('transaction_type', ['adjustment', 'purchase'])
                   ->exists();
    }

    public function logChange(string $action, string $description, array $oldValues = null, array $newValues = null, array $changes = null): void
    {
        $this->auditLogs()->create([
            'business_id' => $this->business_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Calculation Methods
    public function calculateSubtotal(): float
    {
        $generalTotal = $this->generalLines()->sum('line_total');
        $armTotal = $this->armLines()->sum(DB::raw('qty * unit_price'));
        
        return $generalTotal + $armTotal;
    }

    public function calculateTotalAmount(): float
    {
        $subtotal = $this->calculateSubtotal();
        return $subtotal + $this->shipping_charges;
    }

    public function getTotalLinesCount(): int
    {
        return $this->generalLines()->count() + $this->armLines()->count();
    }

    public function getGeneralItemsCount(): int
    {
        return $this->generalLines()->sum('qty');
    }

    public function getArmsCount(): int
    {
        return $this->armLines()->sum('qty');
    }

    // Allocation Methods
    public function allocateCharges(): array
    {
        $totalLines = $this->getTotalLinesCount();
        if ($totalLines === 0) {
            return [];
        }

        $allocations = [];
        $totalLineValue = $this->calculateSubtotal();

        if ($totalLineValue > 0) {
            $shippingRatio = $this->shipping_charges / $totalLineValue;

            // Allocate to general lines
            foreach ($this->generalLines as $line) {
                $lineValue = $line->qty * $line->unit_price;
                $allocations[$line->id] = [
                    'type' => 'general',
                    'allocated_shipping' => $lineValue * $shippingRatio,
                    'allocated_other' => 0,
                    'effective_unit_cost' => $line->unit_price + ($lineValue * $shippingRatio / $line->qty)
                ];
            }

            // Allocate to arm lines
            foreach ($this->armLines as $line) {
                $lineValue = $line->qty * $line->unit_price;
                $allocations[$line->id] = [
                    'type' => 'arm',
                    'allocated_shipping' => $lineValue * $shippingRatio,
                    'allocated_other' => 0,
                    'effective_unit_cost' => ($lineValue + 
                                            ($lineValue * $shippingRatio)) / $line->qty
                ];
            }
        }

        return $allocations;
    }

    // Status Methods
    public function markAsPosted(): bool
    {
        if (!$this->canBePosted()) {
            return false;
        }

        $this->status = 'posted';
        $this->subtotal = $this->calculateSubtotal();
        $this->total_amount = $this->calculateTotalAmount();
        
        return $this->save();
    }

    public function markAsCancelled(string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = 'cancelled';
        
        return $this->save();
    }

    // Validation Methods
    public function validateForPosting(): array
    {
        $errors = [];

        if ($this->payment_type === 'credit' && $this->party_id === null) {
            $errors[] = 'Vendor (party) is required for credit payments';
        }

        if ($this->payment_type === 'cash' && $this->bank_id === null) {
            $errors[] = 'Bank is required for cash payments';
        }

        if ($this->getTotalLinesCount() === 0) {
            $errors[] = 'Purchase must have at least one line item';
        }

        // Check for duplicate serial numbers in arm serials
        $duplicateSerials = $this->armSerials()
            ->select('serial_no')
            ->groupBy('serial_no')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('serial_no')
            ->toArray();

        if (!empty($duplicateSerials)) {
            $errors[] = 'Duplicate serial numbers found: ' . implode(', ', $duplicateSerials);
        }

        return $errors;
    }

    // Accessor Methods
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'posted' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return ucfirst($this->payment_type);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 2);
    }

    public function getFormattedInvoiceDateAttribute(): string
    {
        return $this->invoice_date->format('M d, Y');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }

    public function getFormattedShippingChargesAttribute(): string
    {
        return number_format($this->shipping_charges, 2);
    }


}

