<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralItem;
use App\Models\ItemType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralBatch;
use App\Models\GeneralItemStockLedger;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Models\InventoryTransaction;

class GeneralItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = GeneralItem::with('itemType')->where('business_id', $businessId);

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', '%' . $search . '%')
                  ->orWhere('item_code', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'item_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['item_name', 'item_code', 'cost_price', 'sale_price'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('item_name', 'asc');
        }

        $generalItems = $query->paginate(15)->withQueryString();
        return view('general_items.index', compact('generalItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        $itemTypes = ItemType::where('business_id', $businessId)->where('status', true)->get();
        return view('general_items.create', compact('itemTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255',
            'item_type_id' => 'required|exists:item_types,id',
            'item_code' => 'nullable|string|max:255|unique:general_items,item_code,NULL,id,business_id,'.$businessId,
            'min_stock_limit' => 'nullable|integer|min:0',
            'carton_or_pack_size' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'opening_stock' => 'nullable|integer|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('general-items.create')->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $businessId) {
            // Auto-generate item_code if empty
            $itemCode = $request->item_code;
            if (empty($itemCode)) {
                $itemCode = 'ITM-' . strtoupper(Str::random(8));
            }

            // Calculate opening_total
            $openingStock = $request->opening_stock ?? 0;
            $costPrice = $request->cost_price;
            $openingTotal = $openingStock * $costPrice;

            $item = GeneralItem::create([
                'item_name' => $request->item_name,
                'item_type_id' => $request->item_type_id,
                'item_code' => $itemCode,
                'min_stock_limit' => $request->min_stock_limit,
                'carton_or_pack_size' => $request->carton_or_pack_size,
                'cost_price' => $costPrice,
                'opening_stock' => $openingStock,
                'opening_total' => $openingTotal,
                'sale_price' => $request->sale_price,
                'business_id' => $businessId,
            ]);

            // Create initial batch if opening stock provided
            if ($openingStock > 0) {
                $batch = GeneralBatch::create([
                    'business_id' => $businessId,
                    'item_id' => $item->id,
                    'qty_received' => $openingStock,
                    'qty_remaining' => $openingStock,
                    'unit_cost' => $costPrice,
                    'total_cost' => $openingTotal,
                    'received_date' => now()->toDateString(),
                    'user_id' => auth()->id(),
                ]);

                // Create stock ledger entry for opening stock
                GeneralItemStockLedger::createOpeningStockEntry([
                    'business_id' => $businessId,
                    'general_item_id' => $item->id,
                    'batch_id' => $batch->id,
                    'transaction_date' => now(),
                    'quantity' => $openingStock,
                    'unit_cost' => $costPrice,
                    'total_cost' => $openingTotal,
                    'reference_id' => 'OPEN-' . $item->id,
                    'remarks' => 'Opening stock for ' . $item->item_name,
                    'created_by' => auth()->id(),
                ]);

                // Inventory opening transaction
                InventoryTransaction::create([
                    'business_id' => $businessId,
                    'item_id' => $item->id,
                    'batch_id' => $batch->id,
                    'tx_type' => 'opening',
                    'qty' => $openingStock,
                    'unit_cost' => $costPrice,
                    'total_cost' => $openingTotal,
                    'date' => now()->toDateString(),
                    'user_id' => auth()->id(),
                ]);

                // Create journal entries for opening stock
                $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
                $openingEquityAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $inventoryAccountId,
                    'voucher_id' => $item->id,
                    'voucher_type' => 'General Item',
                    'date_added' => now()->toDateString(),
                    'user_id' => auth()->id(),
                    'debit_amount' => $openingTotal,
                    'credit_amount' => 0,
                    'comments' => 'Opening stock for item ' . $item->item_name,
                ]);

                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $openingEquityAccountId,
                    'voucher_id' => $item->id,
                    'voucher_type' => 'General Item',
                    'date_added' => now()->toDateString(),
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => $openingTotal,
                    'comments' => 'Opening stock equity for item ' . $item->item_name,
                ]);
            }
        });

        return redirect()->route('general-items.index')->with('success', 'General Item created successfully.');
    }

    /**
     * Get item data for API calls (used in purchase forms).
     */
    public function getItemData($id)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return response()->json(['error' => 'No active business session'], 400);
        }
        
        $item = GeneralItem::where('business_id', $businessId)
            ->where('id', $id)
            ->select(['id', 'item_name', 'item_code', 'cost_price', 'sale_price'])
            ->first();

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json($item);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $generalItem = GeneralItem::with('itemType')->where('business_id', $businessId)->findOrFail($id);
        return view('general_items.show', compact('generalItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $businessId = session('active_business');
        $generalItem = GeneralItem::where('business_id', $businessId)->findOrFail($id);
        $itemTypes = ItemType::where('business_id', $businessId)->where('status', true)->get();
        
        // Get opening stock information for display
        $openingStockInfo = $this->canEditOpeningStock($businessId, $generalItem->id);
        $openingStockAdjustmentInfo = $this->getOpeningStockAdjustmentInfo($businessId, $generalItem->id);
        
        return view('general_items.edit', compact('generalItem', 'itemTypes', 'openingStockInfo', 'openingStockAdjustmentInfo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $generalItem = GeneralItem::where('business_id', $businessId)->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255',
            'item_type_id' => 'required|exists:item_types,id',
            'item_code' => 'required|string|max:255|unique:general_items,item_code,'.$id.',id,business_id,'.$businessId,
            'min_stock_limit' => 'nullable|integer|min:0',
            'carton_or_pack_size' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'opening_stock' => 'nullable|integer|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('general-items.edit', $id)->withErrors($validator)->withInput();
        }

        // Get current and new opening stock values
        $currentOpeningStock = $generalItem->opening_stock ?? 0;
        $newOpeningStock = $request->opening_stock ?? 0;
        $currentCostPrice = $generalItem->cost_price;
        $newCostPrice = $request->cost_price;
        
        // Check if opening stock has changed
        $openingStockChanged = ($currentOpeningStock != $newOpeningStock) || ($currentCostPrice != $newCostPrice);
        
        if ($openingStockChanged) {
            // Find existing opening batch
            $openingBatch = GeneralBatch::where('business_id', $businessId)
                ->where('item_id', $generalItem->id)
                ->where('qty_received', '>', 0)
                ->first();
                
            if ($openingBatch) {
                $remainingQty = $openingBatch->qty_remaining;
                $soldQty = $openingBatch->qty_received - $remainingQty;
                
                // Always use adjustment method when stock has been sold/used
                if ($soldQty > 0) {
                    return $this->handleOpeningStockAdjustment(
                        $request, $id, $businessId, $generalItem, 
                        $openingBatch, $currentOpeningStock, $newOpeningStock, 
                        $currentCostPrice, $newCostPrice, $soldQty, $remainingQty
                    );
                } else {
                    // No sales - direct edit allowed
                    return $this->handleDirectOpeningStockEdit(
                        $request, $id, $businessId, $generalItem, 
                        $openingBatch, $currentOpeningStock, $newOpeningStock, 
                        $currentCostPrice, $newCostPrice
                    );
                }
            } else {
                // No existing opening batch - create new one
                return $this->handleDirectOpeningStockEdit(
                    $request, $id, $businessId, $generalItem, 
                    null, $currentOpeningStock, $newOpeningStock, 
                    $currentCostPrice, $newCostPrice
                );
            }
        } else {
            // No opening stock changes - just update other fields
            $generalItem->update([
                'item_name' => $request->item_name,
                'item_type_id' => $request->item_type_id,
                'item_code' => $request->item_code,
                'min_stock_limit' => $request->min_stock_limit,
                'carton_or_pack_size' => $request->carton_or_pack_size,
                'sale_price' => $request->sale_price,
            ]);
            
            return redirect()->route('general-items.index')->with('success', 'General Item updated successfully.');
        }

        DB::transaction(function () use ($request, $businessId, $generalItem, $openingStock, $costPrice, $openingTotal, $currentOpeningStock) {
            $generalItem->update([
                'item_name' => $request->item_name,
                'item_type_id' => $request->item_type_id,
                'item_code' => $request->item_code,
                'min_stock_limit' => $request->min_stock_limit,
                'carton_or_pack_size' => $request->carton_or_pack_size,
                'cost_price' => $costPrice,
                'opening_stock' => $openingStock,
                'opening_total' => $openingTotal,
                'sale_price' => $request->sale_price,
            ]);

            // Find existing opening batch
            $openingBatch = GeneralBatch::where('business_id', $businessId)
                ->where('item_id', $generalItem->id)
                ->where('qty_received', '>', 0)
                ->first();

            if ($openingStock > 0) {
                if ($openingBatch) {
                    // Update existing opening batch
                    $openingBatch->update([
                        'qty_received' => $openingStock,
                        'qty_remaining' => $openingStock, // Reset to full quantity for opening stock
                        'unit_cost' => $costPrice,
                        'total_cost' => $openingTotal,
                    ]);
                    $batchId = $openingBatch->id;
                } else {
                    // Create new opening batch
                    $openingBatch = GeneralBatch::create([
                        'business_id' => $businessId,
                        'item_id' => $generalItem->id,
                        'qty_received' => $openingStock,
                        'qty_remaining' => $openingStock,
                        'unit_cost' => $costPrice,
                        'total_cost' => $openingTotal,
                        'received_date' => now()->toDateString(),
                        'user_id' => auth()->id(),
                    ]);
                    $batchId = $openingBatch->id;
                }

                // Upsert inventory opening transaction for this batch
                $invTx = InventoryTransaction::where('business_id', $businessId)
                    ->where('item_id', $generalItem->id)
                    ->where('batch_id', $batchId)
                    ->where('tx_type', 'opening')
                    ->first();
                if ($invTx) {
                    $invTx->update([
                        'qty' => $openingStock,
                        'unit_cost' => $costPrice,
                        'total_cost' => $openingTotal,
                        'date' => now()->toDateString(),
                    ]);
                } else {
                    InventoryTransaction::create([
                        'business_id' => $businessId,
                        'item_id' => $generalItem->id,
                        'batch_id' => $batchId,
                        'tx_type' => 'opening',
                        'qty' => $openingStock,
                        'unit_cost' => $costPrice,
                        'total_cost' => $openingTotal,
                        'date' => now()->toDateString(),
                        'user_id' => auth()->id(),
                    ]);
                }

                $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
                $openingEquityAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

                $entries = JournalEntry::where('voucher_type', 'General Item')
                    ->where('voucher_id', $generalItem->id)
                    ->where('business_id', $businessId)
                    ->get();

                $debit = $entries->firstWhere('debit_amount', '>', 0);
                $credit = $entries->firstWhere('credit_amount', '>', 0);

                if ($debit) {
                    $debit->update([
                        'account_head' => $inventoryAccountId,
                        'date_added' => now()->toDateString(),
                        'debit_amount' => $openingTotal,
                        'credit_amount' => 0,
                        'comments' => 'Opening stock for item ' . $generalItem->item_name,
                    ]);
                } else {
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $inventoryAccountId,
                        'voucher_id' => $generalItem->id,
                        'voucher_type' => 'General Item',
                        'date_added' => now()->toDateString(),
                        'user_id' => auth()->id(),
                        'debit_amount' => $openingTotal,
                        'credit_amount' => 0,
                        'comments' => 'Opening stock for item ' . $generalItem->item_name,
                    ]);
                }

                if ($credit) {
                    $credit->update([
                        'account_head' => $openingEquityAccountId,
                        'date_added' => now()->toDateString(),
                        'debit_amount' => 0,
                        'credit_amount' => $openingTotal,
                        'comments' => 'Opening stock equity for item ' . $generalItem->item_name,
                    ]);
                } else {
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $openingEquityAccountId,
                        'voucher_id' => $generalItem->id,
                        'voucher_type' => 'General Item',
                        'date_added' => now()->toDateString(),
                        'user_id' => auth()->id(),
                        'debit_amount' => 0,
                        'credit_amount' => $openingTotal,
                        'comments' => 'Opening stock equity for item ' . $generalItem->item_name,
                    ]);
                }
            } else {
                // Remove opening stock completely
                if ($openingBatch) {
                    // Check if any sales occurred
                    $remainingQty = $openingBatch->qty_remaining;
                    $soldQty = $openingBatch->qty_received - $remainingQty;
                    
                    if ($soldQty > 0) {
                        return redirect()->route('general-items.edit', $id)
                            ->withErrors(['opening_stock' => "Cannot remove opening stock completely as {$soldQty} units have been sold. Please create adjustment entries instead."])
                            ->withInput();
                    }
                    
                    // If no sales occurred, safe to remove
                    $openingBatch->update([
                        'qty_received' => 0,
                        'qty_remaining' => 0,
                        'unit_cost' => 0,
                        'total_cost' => 0,
                    ]);

                    // Delete related journal entries
                    JournalEntry::where('voucher_type', 'General Item')
                        ->where('voucher_id', $generalItem->id)
                        ->where('business_id', $businessId)
                        ->delete();

                    // Delete related inventory transactions
                    InventoryTransaction::where('business_id', $businessId)
                        ->where('item_id', $generalItem->id)
                        ->where('batch_id', $openingBatch->id)
                        ->where('tx_type', 'opening')
                        ->delete();
                }
            }
        });

        return redirect()->route('general-items.index')->with('success', 'General Item updated successfully.');
    }

    /**
     * Check if opening stock can be edited directly
     * Now always allows editing regardless of consumption
     */
    private function canEditOpeningStock($businessId, $itemId)
    {
        $openingBatch = GeneralBatch::where('business_id', $businessId)
            ->where('item_id', $itemId)
            ->where('qty_received', '>', 0)
            ->first();
            
        if (!$openingBatch) {
            return ['can_edit' => true, 'message' => null];
        }
        
        $remainingQty = $openingBatch->qty_remaining;
        $soldQty = $openingBatch->qty_received - $remainingQty;
        
        // Always allow editing, but provide information about consumption
        return [
            'can_edit' => true, 
            'message' => $soldQty > 0 ? "Opening stock has {$soldQty} units sold. Editing will create adjustment entries automatically." : null,
            'sold_qty' => $soldQty,
            'remaining_qty' => $remainingQty
        ];
    }

    /**
     * Get opening stock adjustment information for display
     */
    private function getOpeningStockAdjustmentInfo($businessId, $itemId)
    {
        $openingBatch = GeneralBatch::where('business_id', $businessId)
            ->where('item_id', $itemId)
            ->where('qty_received', '>', 0)
            ->first();
            
        if (!$openingBatch) {
            return null;
        }
        
        $remainingQty = $openingBatch->qty_remaining;
        $soldQty = $openingBatch->qty_received - $remainingQty;
        
        return [
            'total_opening' => $openingBatch->qty_received,
            'remaining_qty' => $remainingQty,
            'sold_qty' => $soldQty,
            'unit_cost' => $openingBatch->unit_cost,
            'total_value' => $openingBatch->total_cost,
            'batch_id' => $openingBatch->id
        ];
    }

    /**
     * Handle direct opening stock editing when no sales have occurred
     */
    private function handleDirectOpeningStockEdit($request, $id, $businessId, $generalItem, $openingBatch, $currentOpeningStock, $newOpeningStock, $currentCostPrice, $newCostPrice)
    {
        $openingTotal = $newOpeningStock * $newCostPrice;
        
        DB::transaction(function () use ($request, $businessId, $generalItem, $openingBatch, $newOpeningStock, $newCostPrice, $openingTotal, $currentOpeningStock, $currentCostPrice) {
            // Update general item
            $generalItem->update([
                'item_name' => $request->item_name,
                'item_type_id' => $request->item_type_id,
                'item_code' => $request->item_code,
                'min_stock_limit' => $request->min_stock_limit,
                'carton_or_pack_size' => $request->carton_or_pack_size,
                'cost_price' => $newCostPrice,
                'opening_stock' => $newOpeningStock,
                'opening_total' => $openingTotal,
                'sale_price' => $request->sale_price,
            ]);

            if ($newOpeningStock > 0) {
                // Update or create stock ledger entry for opening stock
                $this->updateStockLedgerOpeningStock($generalItem->id, $newOpeningStock, $newCostPrice, $openingTotal);

                if ($openingBatch) {
                    // Update existing opening batch
                    $openingBatch->update([
                        'qty_received' => $newOpeningStock,
                        'qty_remaining' => $newOpeningStock,
                        'unit_cost' => $newCostPrice,
                        'total_cost' => $openingTotal,
                    ]);
                    $batchId = $openingBatch->id;
                } else {
                    // Create new opening batch
                    $openingBatch = GeneralBatch::create([
                        'business_id' => $businessId,
                        'item_id' => $generalItem->id,
                        'qty_received' => $newOpeningStock,
                        'qty_remaining' => $newOpeningStock,
                        'unit_cost' => $newCostPrice,
                        'total_cost' => $openingTotal,
                        'received_date' => now()->toDateString(),
                        'user_id' => auth()->id(),
                    ]);
                    $batchId = $openingBatch->id;
                }

                // Update or create inventory opening transaction
                $invTx = InventoryTransaction::where('business_id', $businessId)
                    ->where('item_id', $generalItem->id)
                    ->where('batch_id', $batchId)
                    ->where('tx_type', 'opening')
                    ->first();
                    
                if ($invTx) {
                    $invTx->update([
                        'qty' => $newOpeningStock,
                        'unit_cost' => $newCostPrice,
                        'total_cost' => $openingTotal,
                        'date' => now()->toDateString(),
                    ]);
                } else {
                    InventoryTransaction::create([
                        'business_id' => $businessId,
                        'item_id' => $generalItem->id,
                        'batch_id' => $batchId,
                        'tx_type' => 'opening',
                        'qty' => $newOpeningStock,
                        'unit_cost' => $newCostPrice,
                        'total_cost' => $openingTotal,
                        'date' => now()->toDateString(),
                        'user_id' => auth()->id(),
                    ]);
                }

                // Handle journal entries - reverse old and post new
                $this->handleJournalEntriesForDirectEdit($businessId, $generalItem, $openingTotal, $currentOpeningStock, $currentCostPrice);
            } else {
                // Remove opening stock completely
                if ($openingBatch) {
                    $openingBatch->update([
                        'qty_received' => 0,
                        'qty_remaining' => 0,
                        'unit_cost' => 0,
                        'total_cost' => 0,
                    ]);

                    // Delete related journal entries
                    JournalEntry::where('voucher_type', 'General Item')
                        ->where('voucher_id', $generalItem->id)
                        ->where('business_id', $businessId)
                        ->delete();

                    // Delete related inventory transactions
                    InventoryTransaction::where('business_id', $businessId)
                        ->where('item_id', $generalItem->id)
                        ->where('batch_id', $openingBatch->id)
                        ->where('tx_type', 'opening')
                        ->delete();
                }
            }
        });

        return redirect()->route('general-items.index')->with('success', 'General Item updated successfully.');
    }

    /**
     * Handle opening stock adjustment when sales have occurred
     */
    private function handleOpeningStockAdjustment($request, $id, $businessId, $generalItem, $openingBatch, $currentOpeningStock, $newOpeningStock, $currentCostPrice, $newCostPrice, $soldQty, $remainingQty)
    {
        // Calculate differences
        $qtyDifference = $newOpeningStock - $currentOpeningStock;
        $costDifference = $newCostPrice - $currentCostPrice;
        
        // Allow all adjustments - no validation restrictions
        // The system will handle adjustments automatically

        DB::transaction(function () use ($request, $businessId, $generalItem, $openingBatch, $newOpeningStock, $newCostPrice, $qtyDifference, $soldQty, $remainingQty) {
            // Update general item
            $generalItem->update([
                'item_name' => $request->item_name,
                'item_type_id' => $request->item_type_id,
                'item_code' => $request->item_code,
                'min_stock_limit' => $request->min_stock_limit,
                'carton_or_pack_size' => $request->carton_or_pack_size,
                'sale_price' => $request->sale_price,
            ]);

            if ($qtyDifference != 0) {
                // Create adjustment transaction
                $adjustmentQty = $qtyDifference;
                $adjustmentTotal = $adjustmentQty * $newCostPrice;
                
                // Create stock ledger adjustment entry
                GeneralItemStockLedger::createAdjustmentEntry([
                    'business_id' => $businessId,
                    'general_item_id' => $generalItem->id,
                    'batch_id' => $openingBatch->id,
                    'transaction_date' => now(),
                    'quantity' => $adjustmentQty,
                    'unit_cost' => $newCostPrice,
                    'total_cost' => abs($adjustmentTotal),
                    'reference_id' => 'ADJ-' . $generalItem->id,
                    'remarks' => 'Opening stock adjustment: ' . ($adjustmentQty > 0 ? '+' : '-') . abs($adjustmentQty) . ' units',
                    'created_by' => auth()->id(),
                ]);
                
                // Update batch remaining quantity (historical qty_received stays intact)
                $newRemainingQty = $remainingQty + $adjustmentQty;
                $openingBatch->update([
                    'qty_remaining' => $newRemainingQty,
                ]);

                // Create adjustment inventory transaction
                InventoryTransaction::create([
                    'business_id' => $businessId,
                    'item_id' => $generalItem->id,
                    'batch_id' => $openingBatch->id,
                    'tx_type' => 'adjustment',
                    'qty' => $adjustmentQty,
                    'unit_cost' => $newCostPrice,
                    'total_cost' => $adjustmentTotal,
                    'date' => now()->toDateString(),
                    'user_id' => auth()->id(),
                ]);

                // Create journal entry for adjustment
                $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
                $openingEquityAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

                if ($adjustmentQty > 0) {
                    // Adding stock
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $inventoryAccountId,
                        'voucher_id' => $generalItem->id,
                        'voucher_type' => 'General Item Adjustment',
                        'date_added' => now()->toDateString(),
                        'user_id' => auth()->id(),
                        'debit_amount' => $adjustmentTotal,
                        'credit_amount' => 0,
                        'comments' => 'Opening stock adjustment (addition) for item ' . $generalItem->item_name,
                    ]);

                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $openingEquityAccountId,
                        'voucher_id' => $generalItem->id,
                        'voucher_type' => 'General Item Adjustment',
                        'date_added' => now()->toDateString(),
                        'user_id' => auth()->id(),
                        'debit_amount' => 0,
                        'credit_amount' => $adjustmentTotal,
                        'comments' => 'Opening stock equity adjustment (addition) for item ' . $generalItem->item_name,
                    ]);
                } else {
                    // Reducing stock
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $openingEquityAccountId,
                        'voucher_id' => $generalItem->id,
                        'voucher_type' => 'General Item Adjustment',
                        'date_added' => now()->toDateString(),
                        'user_id' => auth()->id(),
                        'debit_amount' => abs($adjustmentTotal),
                        'credit_amount' => 0,
                        'comments' => 'Opening stock equity adjustment (reduction) for item ' . $generalItem->item_name,
                    ]);

                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $inventoryAccountId,
                        'voucher_id' => $generalItem->id,
                        'voucher_type' => 'General Item Adjustment',
                        'date_added' => now()->toDateString(),
                        'user_id' => auth()->id(),
                        'debit_amount' => 0,
                        'credit_amount' => abs($adjustmentTotal),
                        'comments' => 'Opening stock adjustment (reduction) for item ' . $generalItem->item_name,
                    ]);
                }
            }
        });

        return redirect()->route('general-items.index')->with('success', 'General Item updated successfully with opening stock adjustment.');
    }

    /**
     * Update stock ledger opening stock entry
     */
    private function updateStockLedgerOpeningStock($itemId, $newOpeningStock, $newCostPrice, $newOpeningTotal)
    {
        $businessId = session('active_business');
        
        // Find existing opening stock ledger entry
        $openingLedgerEntry = GeneralItemStockLedger::forItem($itemId)
            ->ofType('opening')
            ->first();

        if ($openingLedgerEntry) {
            // Get item name for remarks
            $item = GeneralItem::find($itemId);
            $itemName = $item ? $item->item_name : 'item';
            
            // Update existing entry
            $openingLedgerEntry->update([
                'quantity' => $newOpeningStock,
                'quantity_in' => $newOpeningStock,
                'quantity_out' => 0,
                'unit_cost' => $newCostPrice,
                'total_cost' => $newOpeningTotal,
                'remarks' => 'Opening stock updated for ' . $itemName,
            ]);

            // Recalculate balances for all entries from the beginning
            GeneralItemStockLedger::recalculateBalances($itemId);
            
            // Log the update for debugging
            \Log::info('Opening stock ledger updated', [
                'item_id' => $itemId,
                'new_opening_stock' => $newOpeningStock,
                'new_cost_price' => $newCostPrice,
                'new_total' => $newOpeningTotal,
                'ledger_entry_id' => $openingLedgerEntry->id
            ]);
        } else {
            // Get item name for remarks
            $item = GeneralItem::find($itemId);
            $itemName = $item ? $item->item_name : 'item';
            
            // Create new opening stock entry
            GeneralItemStockLedger::createOpeningStockEntry([
                'business_id' => $businessId,
                'general_item_id' => $itemId,
                'batch_id' => null, // Will be set by the calling method
                'transaction_date' => now(),
                'quantity' => $newOpeningStock,
                'unit_cost' => $newCostPrice,
                'total_cost' => $newOpeningTotal,
                'reference_id' => 'OPEN-' . $itemId,
                'remarks' => 'Opening stock for ' . $itemName,
                'created_by' => auth()->id(),
            ]);
        }
    }

    /**
     * Handle journal entries for direct opening stock editing
     * For direct editing (no sales occurred), we delete old entries and create new ones
     */
    private function handleJournalEntriesForDirectEdit($businessId, $generalItem, $newOpeningTotal, $oldOpeningStock, $oldCostPrice)
    {
        $oldOpeningTotal = $oldOpeningStock * $oldCostPrice;
        $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
        $openingEquityAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

        // Find existing journal entries for this item
        $entries = JournalEntry::where('voucher_type', 'General Item')
            ->where('voucher_id', $generalItem->id)
            ->where('business_id', $businessId)
            ->get();

        if ($entries->count() > 0) {
            // Delete existing journal entries for this item (since no sales occurred, we can safely do this)
            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        // Create new opening stock entries with updated amounts
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $inventoryAccountId,
            'voucher_id' => $generalItem->id,
            'voucher_type' => 'General Item',
            'date_added' => now()->toDateString(),
            'user_id' => auth()->id(),
            'debit_amount' => $newOpeningTotal,
            'credit_amount' => 0,
            'comments' => 'Opening stock for ' . $generalItem->item_name . ' (Updated: ' . number_format($oldOpeningStock, 2) . ' units @ ' . number_format($oldCostPrice, 2) . ' = ' . number_format($oldOpeningTotal, 2) . ' → ' . number_format($newOpeningTotal / $generalItem->cost_price, 2) . ' units @ ' . number_format($generalItem->cost_price, 2) . ' = ' . number_format($newOpeningTotal, 2) . ')',
        ]);

        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $openingEquityAccountId,
            'voucher_id' => $generalItem->id,
            'voucher_type' => 'General Item',
            'date_added' => now()->toDateString(),
            'user_id' => auth()->id(),
            'debit_amount' => 0,
            'credit_amount' => $newOpeningTotal,
            'comments' => 'Opening stock equity for ' . $generalItem->item_name . ' (Updated: ' . number_format($oldOpeningStock, 2) . ' units @ ' . number_format($oldCostPrice, 2) . ' = ' . number_format($oldOpeningTotal, 2) . ' → ' . number_format($newOpeningTotal / $generalItem->cost_price, 2) . ' units @ ' . number_format($generalItem->cost_price, 2) . ' = ' . number_format($newOpeningTotal, 2) . ')',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $businessId = session('active_business');
        $generalItem = GeneralItem::where('business_id', $businessId)->findOrFail($id);
        
        // Allow delete only if the item has no transactions beyond opening stock
        $hasNonOpeningLedger = GeneralItemStockLedger::forBusiness($businessId)
            ->forItem($generalItem->id)
            ->where(function ($query) {
                $query->whereNull('transaction_type')
                      ->orWhere('transaction_type', '!=', 'opening');
            })
            ->exists();

        $hasNonOpeningInventory = InventoryTransaction::where('business_id', $businessId)
            ->where('item_id', $generalItem->id)
            ->where(function ($query) {
                $query->whereNull('tx_type')
                      ->orWhere('tx_type', '!=', 'opening');
            })
            ->exists();

        if ($hasNonOpeningLedger || $hasNonOpeningInventory) {
            return redirect()->route('general-items.index')
                ->withErrors(['delete_error' => 'This item cannot be deleted.']);
        }
        
        DB::transaction(function () use ($generalItem, $businessId) {
            // Delete stock ledger entries for this item
            GeneralItemStockLedger::forBusiness($businessId)
                ->forItem($generalItem->id)
                ->delete();

            // Delete related inventory transactions
            InventoryTransaction::where('business_id', $businessId)
                ->where('item_id', $generalItem->id)
                ->delete();

            // Delete related batches
            GeneralBatch::where('business_id', $businessId)
                ->where('item_id', $generalItem->id)
                ->delete();

            // Delete related journal entries (opening and adjustment)
            JournalEntry::where('business_id', $businessId)
                ->where('voucher_id', $generalItem->id)
                ->whereIn('voucher_type', ['General Item', 'General Item Adjustment'])
                ->delete();
            
            // Delete the general item (this will cascade delete related records)
            $generalItem->delete();
        });
        
        return redirect()->route('general-items.index')->with('success', 'General Item deleted successfully.');
    }

    /**
     * Show the form for editing opening stock only.
     */
    public function editOpeningStock($id)
    {
        $businessId = session('active_business');
        $generalItem = GeneralItem::with('itemType')->where('business_id', $businessId)->findOrFail($id);
        
        return view('general_items.edit_opening_stock', compact('generalItem'));
    }

    /**
     * Update opening stock only.
     */
    public function updateOpeningStock(Request $request, $id)
    {
        $businessId = session('active_business');
        $generalItem = GeneralItem::where('business_id', $businessId)->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'opening_stock' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('general-items.edit-opening-stock', $id)->withErrors($validator)->withInput();
        }

        // Get current and new values
        $currentOpeningStock = $generalItem->opening_stock ?? 0;
        $newOpeningStock = $request->opening_stock;
        $currentCostPrice = $generalItem->cost_price;
        $newCostPrice = $request->cost_price;
        $newOpeningTotal = $newOpeningStock * $newCostPrice;
        
        // Check if values have changed
        $hasChanged = ($currentOpeningStock != $newOpeningStock) || ($currentCostPrice != $newCostPrice);
        
        if (!$hasChanged) {
            return redirect()->route('general-items.show', $id)->with('info', 'No changes were made to opening stock.');
        }

        DB::transaction(function () use ($generalItem, $businessId, $newOpeningStock, $newCostPrice, $newOpeningTotal, $currentOpeningStock, $currentCostPrice) {
            // Update the general item
            $generalItem->update([
                'opening_stock' => $newOpeningStock,
                'cost_price' => $newCostPrice,
                'opening_total' => $newOpeningTotal,
            ]);

            // Find existing opening batch
            $openingBatch = GeneralBatch::where('business_id', $businessId)
                ->where('item_id', $generalItem->id)
                ->where('qty_received', '>', 0)
                ->first();

            if ($openingBatch) {
                $remainingQty = $openingBatch->qty_remaining;
                $soldQty = $openingBatch->qty_received - $remainingQty;
                
                if ($soldQty > 0) {
                    // Stock has been sold - use adjustment method
                    $qtyDifference = $newOpeningStock - $currentOpeningStock;
                    
                    // Update batch with new values
                    $openingBatch->update([
                        'qty_received' => $newOpeningStock,
                        'qty_remaining' => $remainingQty + $qtyDifference, // Adjust remaining quantity
                        'unit_cost' => $newCostPrice,
                        'total_cost' => $newOpeningTotal,
                    ]);
                    
                    // Create adjustment entries only for quantity changes when stock has been sold
                    if ($qtyDifference != 0) {
                        $this->createOpeningStockAdjustmentEntries($businessId, $generalItem, $openingBatch, $qtyDifference, $newCostPrice);
                    }
                } else {
                    // No sales - update batch directly without creating adjustment entries
                    $openingBatch->update([
                        'qty_received' => $newOpeningStock,
                        'qty_remaining' => $newOpeningStock,
                        'unit_cost' => $newCostPrice,
                        'total_cost' => $newOpeningTotal,
                    ]);
                }
            } else {
                // Create new opening batch if none exists
                GeneralBatch::create([
                    'business_id' => $businessId,
                    'item_id' => $generalItem->id,
                    'qty_received' => $newOpeningStock,
                    'qty_remaining' => $newOpeningStock,
                    'unit_cost' => $newCostPrice,
                    'total_cost' => $newOpeningTotal,
                    'received_date' => now()->toDateString(),
                    'user_id' => auth()->id(),
                ]);
            }

            // Update or create stock ledger entry
            $this->updateStockLedgerOpeningStock($generalItem->id, $newOpeningStock, $newCostPrice, $newOpeningTotal);

            // Update journal entries
            $this->handleJournalEntriesForDirectEdit($businessId, $generalItem, $newOpeningTotal, $currentOpeningStock, $currentCostPrice);
        });

        return redirect()->route('general-items.show', $id)->with('success', 'Opening stock updated successfully.');
    }

    /**
     * Create adjustment entries for opening stock changes when items have been sold
     */
    private function createOpeningStockAdjustmentEntries($businessId, $generalItem, $openingBatch, $qtyDifference, $newCostPrice)
    {
        // Get current cost price from the batch to check if cost changed
        $currentCostPrice = $openingBatch->unit_cost;
        $costDifference = $newCostPrice - $currentCostPrice;
        
        // If no quantity change and no cost change, no adjustment needed
        if ($qtyDifference == 0 && $costDifference == 0) {
            return;
        }

        // Handle quantity adjustments
        if ($qtyDifference != 0) {
            $adjustmentTotal = $qtyDifference * $newCostPrice;
            
            // Create stock ledger adjustment entry
            GeneralItemStockLedger::createAdjustmentEntry([
                'business_id' => $businessId,
                'general_item_id' => $generalItem->id,
                'batch_id' => $openingBatch->id,
                'transaction_date' => now(),
                'quantity' => $qtyDifference,
                'unit_cost' => $newCostPrice,
                'total_cost' => abs($adjustmentTotal),
                'reference_id' => 'ADJ-' . $generalItem->id,
                'remarks' => 'Opening stock adjustment: ' . ($qtyDifference > 0 ? '+' : '-') . abs($qtyDifference) . ' units',
                'created_by' => auth()->id(),
            ]);

            // Create adjustment inventory transaction
            InventoryTransaction::create([
                'business_id' => $businessId,
                'item_id' => $generalItem->id,
                'batch_id' => $openingBatch->id,
                'tx_type' => 'adjustment',
                'qty' => $qtyDifference,
                'unit_cost' => $newCostPrice,
                'total_cost' => $adjustmentTotal,
                'date' => now()->toDateString(),
                'user_id' => auth()->id(),
            ]);
        }
        
        // Handle cost-only adjustments (when quantity doesn't change but cost does)
        if ($qtyDifference == 0 && $costDifference != 0) {
            // Create a cost adjustment entry with 0 quantity but new unit cost
            GeneralItemStockLedger::createAdjustmentEntry([
                'business_id' => $businessId,
                'general_item_id' => $generalItem->id,
                'batch_id' => $openingBatch->id,
                'transaction_date' => now(),
                'quantity' => 0, // No quantity change
                'unit_cost' => $newCostPrice,
                'total_cost' => 0, // No total cost change for cost-only adjustments
                'reference_id' => 'ADJ-' . $generalItem->id,
                'remarks' => 'Opening stock cost adjustment: ' . $currentCostPrice . ' → ' . $newCostPrice,
                'created_by' => auth()->id(),
            ]);

            // Create cost adjustment inventory transaction
            InventoryTransaction::create([
                'business_id' => $businessId,
                'item_id' => $generalItem->id,
                'batch_id' => $openingBatch->id,
                'tx_type' => 'adjustment',
                'qty' => 0, // No quantity change
                'unit_cost' => $newCostPrice,
                'total_cost' => 0, // No total cost change for cost-only adjustments
                'date' => now()->toDateString(),
                'user_id' => auth()->id(),
            ]);
        }

        // Create journal entries for adjustments
        $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
        $openingEquityAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

        if ($qtyDifference != 0) {
            // Quantity adjustment - create journal entries
            if ($qtyDifference > 0) {
                // Adding stock
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $inventoryAccountId,
                    'voucher_id' => $generalItem->id,
                    'voucher_type' => 'General Item Adjustment',
                    'date_added' => now()->toDateString(),
                    'user_id' => auth()->id(),
                    'debit_amount' => $adjustmentTotal,
                    'credit_amount' => 0,
                    'comments' => 'Opening stock adjustment (addition) for item ' . $generalItem->item_name,
                ]);

                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $openingEquityAccountId,
                    'voucher_id' => $generalItem->id,
                    'voucher_type' => 'General Item Adjustment',
                    'date_added' => now()->toDateString(),
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => $adjustmentTotal,
                    'comments' => 'Opening stock equity adjustment (addition) for item ' . $generalItem->item_name,
                ]);
            } else {
                // Reducing stock
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $openingEquityAccountId,
                    'voucher_id' => $generalItem->id,
                    'voucher_type' => 'General Item Adjustment',
                    'date_added' => now()->toDateString(),
                    'user_id' => auth()->id(),
                    'debit_amount' => abs($adjustmentTotal),
                    'credit_amount' => 0,
                    'comments' => 'Opening stock equity adjustment (reduction) for item ' . $generalItem->item_name,
                ]);

                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $inventoryAccountId,
                    'voucher_id' => $generalItem->id,
                    'voucher_type' => 'General Item Adjustment',
                    'date_added' => now()->toDateString(),
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => abs($adjustmentTotal),
                    'comments' => 'Opening stock adjustment (reduction) for item ' . $generalItem->item_name,
                ]);
            }
        }
        
        // For cost-only adjustments, no journal entries are needed as the total value doesn't change
        // The FIFO calculation will use the updated unit cost from the adjustment entry
    }
}
