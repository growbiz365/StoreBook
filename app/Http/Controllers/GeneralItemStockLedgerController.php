<?php

namespace App\Http\Controllers;

use App\Models\GeneralItem;
use App\Models\GeneralItemStockLedger;
use App\Models\GeneralBatch;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneralItemStockLedgerController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')->with('error', 'No active business selected.');
        }

        // Get filter parameters
        $itemId = $request->get('item_id');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type');
        $perPage = $request->get('per_page', 25);

        // Get all general items for filter dropdown
        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Build the query for stock ledger
        $query = GeneralItemStockLedger::with(['item'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($itemId) {
            $query->where('general_item_id', $itemId);
        }

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        if ($transactionType) {
            $query->where('transaction_type', $transactionType);
        }

        // Get paginated results - ORDER BY OPENING FIRST, THEN CHRONOLOGICAL ORDER for correct calculation
        // This ensures transactions are processed in the correct sequence for FIFO calculations
        $ledgerEntries = $query->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        // Calculate running balance correctly (chronological order)
        $runningBalance = 0;
        $ledgerEntries->getCollection()->transform(function ($entry) use (&$runningBalance) {
            // Stock in transactions: opening, purchase
            if (in_array($entry->transaction_type, ['opening', 'purchase'])) {
                $runningBalance += $entry->quantity;
            } 
            // Stock out transactions: issue, sale
            else if (in_array($entry->transaction_type, ['issue', 'sale'])) {
                $runningBalance -= abs($entry->quantity); // Use abs() since sale quantities are negative
            }
            // Other transactions: adjustment, reversal, edit
            else {
                if ($entry->quantity > 0) {
                    $runningBalance += $entry->quantity;
                } else {
                    $runningBalance += $entry->quantity; // quantity is already negative
                }
            }
            
            // Set the running balance for this entry
            $entry->running_balance = $runningBalance;
            return $entry;
        });

        // Keep entries in ascending order (oldest first) for correct display

        // Get summary statistics
        $summary = $this->getSummary($businessId, $itemId, $dateFrom, $dateTo);

        // Get business information
        $business = \App\Models\Business::find($businessId);

        return view('general_items.stock-ledger', compact(
            'ledgerEntries',
            'generalItems',
            'itemId',
            'dateFrom',
            'dateTo',
            'transactionType',
            'perPage',
            'summary',
            'business'
        ));
    }

    private function getSummary($businessId, $itemId = null, $dateFrom = null, $dateTo = null)
    {
        $query = GeneralItemStockLedger::where('business_id', $businessId);

        if ($itemId) {
            $query->where('general_item_id', $itemId);
        }

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        $summary = $query->selectRaw('
            SUM(CASE WHEN transaction_type IN ("opening", "purchase") THEN quantity ELSE 0 END) as total_in,
            SUM(CASE WHEN transaction_type IN ("issue", "sale") THEN quantity ELSE 0 END) as total_out,
            COUNT(*) as total_transactions
        ')->first();

        // Get current stock levels from actual batch remaining quantities
        if ($itemId) {
            // For specific item, get from batches
            $currentStock = \App\Models\GeneralBatch::where('business_id', $businessId)
                ->where('item_id', $itemId)
                ->where('status', 'active')
                ->sum('qty_remaining');
        } else {
            // For all items, get from batches
            $currentStock = \App\Models\GeneralBatch::where('business_id', $businessId)
                ->where('status', 'active')
                ->sum('qty_remaining');
        }

        return [
            'total_in' => $summary->total_in ?? 0,
            'total_out' => $summary->total_out ?? 0,
            'total_transactions' => $summary->total_transactions ?? 0,
            'current_stock' => $currentStock ?? 0,
            'net_movement' => ($summary->total_in ?? 0) - ($summary->total_out ?? 0)
        ];
    }

    /**
     * Inventory Valuation Summary Report
     */
    public function inventoryValuationSummary(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')->with('error', 'No active business selected.');
        }

        // Get filter parameters
        $itemId = $request->get('item_id');
        $itemTypeId = $request->get('item_type_id');
        $stockFilter = $request->get('stock_filter', 'all'); // all, greater_than_zero, equal_to_zero
        $asOnDate = $request->get('as_on_date', Carbon::now()->format('Y-m-d'));

        // Get all general items for filter dropdown
        $generalItems = GeneralItem::with('itemType')
            ->where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Get all item types for filter dropdown
        $itemTypes = \App\Models\ItemType::where('business_id', $businessId)
            ->where('status', true)
            ->orderBy('item_type')
            ->get();

        // Build the query for inventory valuation
        $query = GeneralItem::with(['itemType', 'batches' => function($q) use ($asOnDate) {
            $q->where('status', 'active')
              ->where('received_date', '<=', $asOnDate);
        }])
        ->where('business_id', $businessId);

        // Apply filters
        if ($itemId) {
            $query->where('id', $itemId);
        }

        if ($itemTypeId) {
            $query->where('item_type_id', $itemTypeId);
        }

        // Get items with their current stock and valuation using FIFO
        $items = $query->get()->map(function ($item) use ($asOnDate, $businessId) {
            // Calculate current stock from batches
            $currentStock = $item->batches->sum('qty_remaining');
            
            // Calculate inventory asset value using FIFO
            $inventoryAssetValue = $this->calculateFIFOValue($businessId, $item->id, $asOnDate);
            
            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'item_type' => $item->itemType ? $item->itemType->item_type : 'N/A',
                'cost_price' => $item->cost_price,
                'current_stock' => $currentStock,
                'inventory_asset_value' => $inventoryAssetValue,
            ];
        });

        // Apply stock filter
        if ($stockFilter === 'greater_than_zero') {
            $items = $items->filter(function ($item) {
                return $item['current_stock'] > 0;
            });
        } elseif ($stockFilter === 'equal_to_zero') {
            $items = $items->filter(function ($item) {
                return $item['current_stock'] == 0;
            });
        }

        // Group by item type for display
        $groupedItems = $items->groupBy('item_type');

        // Calculate totals
        $totalStock = $items->sum('current_stock');
        $totalValue = $items->sum('inventory_asset_value');

        // Get business info
        $business = \App\Models\Business::find($businessId);

        return view('general_items.inventory_valuation_summary', compact(
            'groupedItems',
            'generalItems',
            'itemTypes',
            'itemId',
            'itemTypeId',
            'stockFilter',
            'asOnDate',
            'totalStock',
            'totalValue',
            'business'
        ));
    }

    /**
     * Detailed Inventory Valuation Report for a specific item
     */
    public function detailedInventoryValuation(Request $request, $itemId)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')->with('error', 'No active business selected.');
        }

        // Get filter parameters
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get the specific item
        $item = GeneralItem::with('itemType')
            ->where('business_id', $businessId)
            ->findOrFail($itemId);

        // Get all items for filter dropdown
        $allItems = GeneralItem::with('itemType')
            ->where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Get stock ledger entries for this item within date range
        $ledgerEntries = GeneralItemStockLedger::with(['batch'])
            ->where('business_id', $businessId)
            ->where('general_item_id', $itemId)
            ->whereDate('transaction_date', '>=', $dateFrom)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate running stock and valuation using FIFO based on stock ledger entries
        $transactions = [];
        $runningStock = 0;
        $runningValue = 0;
        $batchQueue = []; // FIFO queue for batches

        // Get all stock ledger entries for this item up to the date range for proper FIFO calculation
        $allLedgerEntries = GeneralItemStockLedger::with(['batch'])
            ->where('business_id', $businessId)
            ->where('general_item_id', $itemId)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate initial stock and value from entries before date range
        $initialStock = $allLedgerEntries
            ->where('transaction_date', '<', $dateFrom)
            ->sum('quantity');

        $runningStock = $initialStock;

        // Build FIFO queue from stock ledger entries before date range
        // Process all entries chronologically to build accurate FIFO queue (includes stock_adjustment)
        $entriesBeforeDateRange = $allLedgerEntries
            ->where('transaction_date', '<', $dateFrom)
            ->sortBy(function($entry) {
                return $entry->transaction_date->format('Y-m-d') . '-' . str_pad($entry->id, 10, '0', STR_PAD_LEFT);
            })
            ->values();

        foreach ($entriesBeforeDateRange as $entry) {
            $transactionType = $entry->transaction_type;
            $quantity = $entry->quantity;
            $unitCost = $entry->unit_cost ?? 0;

            if (in_array($transactionType, ['opening', 'purchase'])) {
                // Stock-in entry - add to FIFO queue
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'LEDGER-' . $entry->id,
                    'unit_cost' => $unitCost,
                    'qty_remaining' => $quantity,
                    'received_date' => $entry->transaction_date,
                ];
            } elseif ($transactionType === 'stock_adjustment' && $quantity > 0) {
                // Stock adjustment addition - add to FIFO queue
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'STK-ADJ-' . $entry->id,
                    'unit_cost' => $unitCost,
                    'qty_remaining' => $quantity,
                    'received_date' => $entry->transaction_date,
                ];
            } elseif (in_array($transactionType, ['sale', 'issue']) || ($transactionType === 'stock_adjustment' && $quantity < 0)) {
                // Stock-out entry - consume from FIFO queue
                $outQty = abs($quantity);
                $remainingOutQty = $outQty;
                
                foreach ($batchQueue as $index => $batch) {
                    if ($remainingOutQty <= 0) break;
                    
                    $usedQty = min($batch['qty_remaining'], $remainingOutQty);
                    $remainingOutQty -= $usedQty;
                    $batchQueue[$index]['qty_remaining'] -= $usedQty;
                }
            } elseif ($transactionType === 'return' && $quantity > 0) {
                // Return - add back to FIFO queue
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'RET-' . $entry->id,
                    'unit_cost' => $unitCost,
                    'qty_remaining' => $quantity,
                    'received_date' => $entry->transaction_date,
                ];
            }
        }

        // Calculate initial value using FIFO from ledger entries
        $tempStock = $initialStock;
        $initialValue = 0;
        foreach ($batchQueue as $batch) {
            if ($tempStock <= 0) break;
            $usedQty = min($batch['qty_remaining'], $tempStock);
            $initialValue += $usedQty * $batch['unit_cost'];
            $tempStock -= $usedQty;
        }

        $runningValue = $initialValue;

        // Process each transaction in the date range
        foreach ($ledgerEntries as $entry) {
            $transactionType = $entry->transaction_type;
            $quantity = $entry->quantity;
            $unitCost = $entry->unit_cost ?? 0;
            $totalCost = $entry->total_cost ?? 0;

            // Handle different transaction types
            if ($transactionType === 'opening') {
                $runningStock += $quantity;
                $runningValue += $totalCost;
                
                // Add to FIFO queue
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'OPEN-' . $entry->id,
                    'unit_cost' => $unitCost,
                    'qty_remaining' => $quantity,
                    'received_date' => $entry->transaction_date,
                ];
            } elseif ($transactionType === 'purchase') {
                $runningStock += $quantity;
                $runningValue += $totalCost;
                
                // Add to FIFO queue
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'PUR-' . $entry->id,
                    'unit_cost' => $unitCost,
                    'qty_remaining' => $quantity,
                    'received_date' => $entry->transaction_date,
                ];
            } elseif (in_array($transactionType, ['sale', 'issue'])) {
                $runningStock += $quantity; // quantity is negative for sales
                $saleQty = abs($quantity);
                
                // Calculate value using FIFO from current batch queue
                $saleValue = 0;
                $remainingSaleQty = $saleQty;
                
                foreach ($batchQueue as $index => $batch) {
                    if ($remainingSaleQty <= 0) break;
                    
                    $usedQty = min($batch['qty_remaining'], $remainingSaleQty);
                    $saleValue += $usedQty * $batch['unit_cost'];
                    $remainingSaleQty -= $usedQty;
                    
                    // Update batch remaining quantity
                    $batchQueue[$index]['qty_remaining'] -= $usedQty;
                }
                
                $runningValue -= $saleValue;
            } elseif ($transactionType === 'return') {
                $this->handleReturnTransaction($entry, $quantity, $unitCost, $totalCost, $runningStock, $runningValue, $batchQueue);
            } elseif ($transactionType === 'reversal') {
                // Check if this is a purchase return reversal by looking at the reference
                $isPurchaseReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'PR-') && str_contains($entry->reference_no, '-REV');
                $isSaleReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'SR-');
                
                if ($isPurchaseReturnReversal && $quantity > 0) {
                    // Purchase return reversal - use special handling
                    $this->handlePurchaseReturnReversal($entry, $quantity, $unitCost, $totalCost, $runningStock, $runningValue, $batchQueue);
                    
                    // Recalculate running value based on current FIFO queue state
                    $recalculatedValue = 0;
                    $tempStock = $runningStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $recalculatedValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                    $runningValue = $recalculatedValue;
                    
                    // Don't skip - let it continue to add to transactions array
                } elseif ($isSaleReturnReversal && $quantity > 0) {
                    // Sale return reversal - use special handling
                    $this->handleSaleReturnReversal($entry, $quantity, $unitCost, $totalCost, $runningStock, $runningValue, $batchQueue);
                    
                    // Recalculate running value based on current FIFO queue state
                    $recalculatedValue = 0;
                    $tempStock = $runningStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $recalculatedValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                    $runningValue = $recalculatedValue;
                    
                    // Don't skip - let it continue to add to transactions array
                } else {
                
                $runningStock += $quantity; // quantity can be positive or negative for reversals
                
                if ($quantity > 0) {
                    // Positive reversal - adding stock back to FIFO queue (sale reversal)
                    // For sale reversals, we need to restore stock to the correct batch based on unit cost
                    // to maintain FIFO order (oldest stock first)
                    $batchFound = false;
                    
                    // First, try to find a batch with the same unit cost that has remaining capacity
                    for ($index = 0; $index < count($batchQueue); $index++) {
                        if ($batchQueue[$index]['unit_cost'] == $unitCost) {
                            // Restore quantity to the batch with same unit cost
                            $batchQueue[$index]['qty_remaining'] += $quantity;
                            $batchFound = true;
                            break;
                        }
                    }
                    
                    // If still not found, add as new entry
                    if (!$batchFound) {
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'batch_code' => $entry->batch ? $entry->batch->batch_code : 'REV-' . $entry->id,
                        'unit_cost' => $unitCost,
                        'qty_remaining' => $quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                    }
                    
                    // Add the value back to running value
                    $runningValue += $totalCost;
                } else {
                    // Negative reversal - reducing stock from FIFO queue (purchase reversal)
                    $reversalQty = abs($quantity);
                    $remainingReversalQty = $reversalQty;
                    $reversalValue = 0; // Track the actual value being removed
                    
                    // For purchase reversals, try to find and remove from the specific batch first
                    $batchFound = false;
                    if ($entry->batch_id) {
                        for ($index = 0; $index < count($batchQueue); $index++) {
                            if ($batchQueue[$index]['batch_id'] == $entry->batch_id && $batchQueue[$index]['unit_cost'] == $unitCost) {
                                $usedQty = min($batchQueue[$index]['qty_remaining'], $remainingReversalQty);
                                $remainingReversalQty -= $usedQty;
                                
                                // Calculate the value being removed from this batch
                                $reversalValue += $usedQty * $batchQueue[$index]['unit_cost'];
                                
                                // Update batch remaining quantity
                                $batchQueue[$index]['qty_remaining'] -= $usedQty;
                                $batchFound = true;
                                
                                if ($remainingReversalQty <= 0) break;
                            }
                        }
                    }
                    
                    // If specific batch not found or still have remaining quantity, use LIFO
                    if (!$batchFound || $remainingReversalQty > 0) {
                        // Process in reverse order (most recent first) for remaining quantity
                        for ($index = count($batchQueue) - 1; $index >= 0; $index--) {
                            if ($remainingReversalQty <= 0) break;
                            
                            $usedQty = min($batchQueue[$index]['qty_remaining'], $remainingReversalQty);
                            $remainingReversalQty -= $usedQty;
                            
                            // Calculate the value being removed from this batch
                            $reversalValue += $usedQty * $batchQueue[$index]['unit_cost'];
                            
                            // Update batch remaining quantity
                            $batchQueue[$index]['qty_remaining'] -= $usedQty;
                        }
                    }
                    
                    // Reduce the value from running value based on actual FIFO cost
                    $runningValue -= $reversalValue;
                }
                } // Close the else block
            } elseif ($transactionType === 'adjustment') {
                $runningStock += $quantity;
                
                if ($quantity == 0 && $unitCost > 0) {
                    // For price adjustments (zero quantity), recalculate running value
                    $runningValue = 0;
                    $tempStock = $runningStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $runningValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                } else {
                    $runningValue += $totalCost;
                }
                
                if ($quantity > 0) {
                    // For positive quantity adjustments, add to FIFO queue
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'batch_code' => $entry->batch ? $entry->batch->batch_code : 'ADJ-' . $entry->id,
                        'unit_cost' => $unitCost,
                        'qty_remaining' => $quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                } elseif ($quantity == 0 && $unitCost > 0) {
                    // For zero quantity adjustments (price changes), update existing batch in FIFO queue
                    foreach ($batchQueue as $index => $batch) {
                        if ($batch['batch_id'] == $entry->batch_id) {
                            // Update the unit cost for this batch
                            $batchQueue[$index]['unit_cost'] = $unitCost;
                            break;
                        }
                    }
                }
            } elseif ($transactionType === 'stock_adjustment') {
                // Handle stock adjustments (from Stock Adjustment module)
                $runningStock += $quantity;
                
                if ($quantity > 0) {
                    // Stock addition - add to FIFO queue
                    $runningValue += $totalCost;
                    
                    // Add to FIFO queue
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'batch_code' => $entry->batch ? $entry->batch->batch_code : 'STK-ADJ-' . $entry->id,
                        'unit_cost' => $unitCost,
                        'qty_remaining' => $quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                } else {
                    // Stock subtraction - consume from FIFO queue using FIFO
                    $subtractionQty = abs($quantity);
                    $remainingSubtractionQty = $subtractionQty;
                    $subtractionValue = 0;
                    
                    foreach ($batchQueue as $index => $batch) {
                        if ($remainingSubtractionQty <= 0) break;
                        
                        $usedQty = min($batch['qty_remaining'], $remainingSubtractionQty);
                        $subtractionValue += $usedQty * $batch['unit_cost'];
                        $remainingSubtractionQty -= $usedQty;
                        
                        // Update batch remaining quantity
                        $batchQueue[$index]['qty_remaining'] -= $usedQty;
                    }
                    
                    $runningValue -= $subtractionValue;
                }
            }

            // For detailed report, calculate FIFO value with correction for final accuracy
            $currentFifoValue = 0;
            $tempStock = $runningStock;
            foreach ($batchQueue as $batch) {
                if ($tempStock <= 0) break;
                $usedQty = min($batch['qty_remaining'], $tempStock);
                $currentFifoValue += $usedQty * $batch['unit_cost'];
                $tempStock -= $usedQty;
            }
            
            // For the final transaction, ensure consistency with actual batch values
            if ($entry->id === $allLedgerEntries->last()->id) {
                $actualBatchValue = $this->calculateFIFOValue($businessId, $itemId, $entry->transaction_date->format('Y-m-d'));
                $runningValue = $actualBatchValue;
            } else {
                $runningValue = $currentFifoValue;
            }

            // Format transaction details
            $transactionDetails = $this->formatTransactionDetails($entry, $transactionType);
            
            // Generate transaction link URL
            $transactionLink = $this->generateTransactionLink($entry, $transactionType);
            
            $transactions[] = [
                'date' => $entry->transaction_date,
                'transaction_details' => $transactionDetails,
                'transaction_link' => $transactionLink,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'stock_on_hand' => $runningStock,
                'inventory_asset_value' => $runningValue,
            ];
        }

        // Get business info
        $business = \App\Models\Business::find($businessId);

        return view('general_items.detailed_inventory_valuation', compact(
            'item',
            'allItems',
            'transactions',
            'dateFrom',
            'dateTo',
            'business'
        ));
    }

    /**
     * Export Detailed Inventory Valuation Report to CSV
     */
    public function exportDetailedInventoryValuation(Request $request, $itemId)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->back()->with('error', 'No active business selected.');
        }

        // Get filter parameters
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get the specific item
        $item = GeneralItem::with('itemType')
            ->where('business_id', $businessId)
            ->findOrFail($itemId);

        // Get stock ledger entries for this item within date range
        $ledgerEntries = GeneralItemStockLedger::with(['batch'])
            ->where('business_id', $businessId)
            ->where('general_item_id', $itemId)
            ->whereDate('transaction_date', '>=', $dateFrom)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate running stock and valuation using FIFO based on stock ledger entries
        $transactions = [];
        $runningStock = 0;
        $runningValue = 0;
        $batchQueue = [];

        // Get all stock ledger entries for this item up to the date range for proper FIFO calculation
        $allLedgerEntries = GeneralItemStockLedger::with(['batch'])
            ->where('business_id', $businessId)
            ->where('general_item_id', $itemId)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate initial stock and value from entries before date range
        $initialStock = $allLedgerEntries
            ->where('transaction_date', '<', $dateFrom)
            ->sum('quantity');

        $runningStock = $initialStock;

        // Build FIFO queue from stock ledger entries (not current batch quantities)
        foreach ($allLedgerEntries as $entry) {
            if ($entry->transaction_date < $dateFrom && in_array($entry->transaction_type, ['opening', 'purchase'])) {
                // This is a stock-in entry before our date range
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'LEDGER-' . $entry->id,
                    'unit_cost' => $entry->unit_cost ?? 0,
                    'qty_remaining' => $entry->quantity, // Use the actual ledger quantity
                    'received_date' => $entry->transaction_date,
                ];
            }
        }

        // Calculate initial value using FIFO from ledger entries
        $tempStock = $initialStock;
        $initialValue = 0;
        foreach ($batchQueue as $batch) {
            if ($tempStock <= 0) break;
            $usedQty = min($batch['qty_remaining'], $tempStock);
            $initialValue += $usedQty * $batch['unit_cost'];
            $tempStock -= $usedQty;
        }

        $runningValue = $initialValue;

        // Process each transaction in the date range
        foreach ($ledgerEntries as $entry) {
            $transactionType = $entry->transaction_type;
            $quantity = $entry->quantity;
            $unitCost = $entry->unit_cost ?? 0;
            $totalCost = $entry->total_cost ?? 0;

            // Handle different transaction types
            if ($transactionType === 'opening') {
                $runningStock += $quantity;
                $runningValue += $totalCost;
                
                // Add to FIFO queue
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'OPEN-' . $entry->id,
                    'unit_cost' => $unitCost,
                    'qty_remaining' => $quantity,
                    'received_date' => $entry->transaction_date,
                ];
            } elseif ($transactionType === 'purchase') {
                $runningStock += $quantity;
                $runningValue += $totalCost;
                
                // Add to FIFO queue
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'PUR-' . $entry->id,
                    'unit_cost' => $unitCost,
                    'qty_remaining' => $quantity,
                    'received_date' => $entry->transaction_date,
                ];
            } elseif (in_array($transactionType, ['sale', 'issue'])) {
                $runningStock += $quantity; // quantity is negative for sales
                $saleQty = abs($quantity);
                
                // Calculate value using FIFO from current batch queue
                $saleValue = 0;
                $remainingSaleQty = $saleQty;
                
                foreach ($batchQueue as $index => $batch) {
                    if ($remainingSaleQty <= 0) break;
                    
                    $usedQty = min($batch['qty_remaining'], $remainingSaleQty);
                    $saleValue += $usedQty * $batch['unit_cost'];
                    $remainingSaleQty -= $usedQty;
                    
                    // Update batch remaining quantity
                    $batchQueue[$index]['qty_remaining'] -= $usedQty;
                }
                
                $runningValue -= $saleValue;
            } elseif ($transactionType === 'return') {
                $this->handleReturnTransaction($entry, $quantity, $unitCost, $totalCost, $runningStock, $runningValue, $batchQueue);
            } elseif ($transactionType === 'reversal') {
                // Check if this is a purchase return reversal by looking at the reference
                $isPurchaseReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'PR-') && str_contains($entry->reference_no, '-REV');
                $isSaleReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'SR-');
                
                if ($isPurchaseReturnReversal && $quantity > 0) {
                    // Purchase return reversal - use special handling
                    $this->handlePurchaseReturnReversal($entry, $quantity, $unitCost, $totalCost, $runningStock, $runningValue, $batchQueue);
                    
                    // Recalculate running value based on current FIFO queue state
                    $recalculatedValue = 0;
                    $tempStock = $runningStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $recalculatedValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                    $runningValue = $recalculatedValue;
                    
                    // Don't skip - let it continue to add to transactions array
                } elseif ($isSaleReturnReversal && $quantity > 0) {
                    // Sale return reversal - use special handling
                    $this->handleSaleReturnReversal($entry, $quantity, $unitCost, $totalCost, $runningStock, $runningValue, $batchQueue);
                    
                    // Recalculate running value based on current FIFO queue state
                    $recalculatedValue = 0;
                    $tempStock = $runningStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $recalculatedValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                    $runningValue = $recalculatedValue;
                    
                    // Don't skip - let it continue to add to transactions array
                } else {
                
                $runningStock += $quantity; // quantity can be positive or negative for reversals
                
                if ($quantity > 0) {
                    // Positive reversal - adding stock back to FIFO queue (sale reversal)
                    // For sale reversals, we need to restore stock to the correct batch based on unit cost
                    // to maintain FIFO order (oldest stock first)
                    $batchFound = false;
                    
                    // First, try to find a batch with the same unit cost that has remaining capacity
                    for ($index = 0; $index < count($batchQueue); $index++) {
                        if ($batchQueue[$index]['unit_cost'] == $unitCost) {
                            // Restore quantity to the batch with same unit cost
                            $batchQueue[$index]['qty_remaining'] += $quantity;
                            $batchFound = true;
                            break;
                        }
                    }
                    
                    // If still not found, add as new entry
                    if (!$batchFound) {
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'batch_code' => $entry->batch ? $entry->batch->batch_code : 'REV-' . $entry->id,
                        'unit_cost' => $unitCost,
                        'qty_remaining' => $quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                    }
                    
                    // Add the value back to running value
                    $runningValue += $totalCost;
                } else {
                    // Negative reversal - reducing stock from FIFO queue (purchase reversal)
                    $reversalQty = abs($quantity);
                    $remainingReversalQty = $reversalQty;
                    $reversalValue = 0; // Track the actual value being removed
                    
                    // For purchase reversals, try to find and remove from the specific batch first
                    $batchFound = false;
                    if ($entry->batch_id) {
                        for ($index = 0; $index < count($batchQueue); $index++) {
                            if ($batchQueue[$index]['batch_id'] == $entry->batch_id && $batchQueue[$index]['unit_cost'] == $unitCost) {
                                $usedQty = min($batchQueue[$index]['qty_remaining'], $remainingReversalQty);
                                $remainingReversalQty -= $usedQty;
                                
                                // Calculate the value being removed from this batch
                                $reversalValue += $usedQty * $batchQueue[$index]['unit_cost'];
                                
                                // Update batch remaining quantity
                                $batchQueue[$index]['qty_remaining'] -= $usedQty;
                                $batchFound = true;
                                
                                if ($remainingReversalQty <= 0) break;
                            }
                        }
                    }
                    
                    // If specific batch not found or still have remaining quantity, use LIFO
                    if (!$batchFound || $remainingReversalQty > 0) {
                        // Process in reverse order (most recent first) for remaining quantity
                        for ($index = count($batchQueue) - 1; $index >= 0; $index--) {
                            if ($remainingReversalQty <= 0) break;
                            
                            $usedQty = min($batchQueue[$index]['qty_remaining'], $remainingReversalQty);
                            $remainingReversalQty -= $usedQty;
                            
                            // Calculate the value being removed from this batch
                            $reversalValue += $usedQty * $batchQueue[$index]['unit_cost'];
                            
                            // Update batch remaining quantity
                            $batchQueue[$index]['qty_remaining'] -= $usedQty;
                        }
                    }
                    
                    // Reduce the value from running value based on actual FIFO cost
                    $runningValue -= $reversalValue;
                }
                } // Close the else block
            } elseif ($transactionType === 'adjustment') {
                $runningStock += $quantity;
                
                if ($quantity == 0 && $unitCost > 0) {
                    // For price adjustments (zero quantity), recalculate running value
                    $runningValue = 0;
                    $tempStock = $runningStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $runningValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                } else {
                    $runningValue += $totalCost;
                }
                
                if ($quantity > 0) {
                    // For positive quantity adjustments, add to FIFO queue
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'batch_code' => $entry->batch ? $entry->batch->batch_code : 'ADJ-' . $entry->id,
                        'unit_cost' => $unitCost,
                        'qty_remaining' => $quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                } elseif ($quantity == 0 && $unitCost > 0) {
                    // For zero quantity adjustments (price changes), update existing batch in FIFO queue
                    foreach ($batchQueue as $index => $batch) {
                        if ($batch['batch_id'] == $entry->batch_id) {
                            // Update the unit cost for this batch
                            $batchQueue[$index]['unit_cost'] = $unitCost;
                            break;
                        }
                    }
                }
            }

            // For detailed report, calculate FIFO value with correction for final accuracy
            $currentFifoValue = 0;
            $tempStock = $runningStock;
            foreach ($batchQueue as $batch) {
                if ($tempStock <= 0) break;
                $usedQty = min($batch['qty_remaining'], $tempStock);
                $currentFifoValue += $usedQty * $batch['unit_cost'];
                $tempStock -= $usedQty;
            }
            
            // For the final transaction, ensure consistency with actual batch values
            if ($entry->id === $allLedgerEntries->last()->id) {
                $actualBatchValue = $this->calculateFIFOValue($businessId, $itemId, $entry->transaction_date->format('Y-m-d'));
                $runningValue = $actualBatchValue;
            } else {
                $runningValue = $currentFifoValue;
            }

            $transactionDetails = $this->formatTransactionDetails($entry, $transactionType);
            
            $transactions[] = [
                'date' => $entry->transaction_date,
                'transaction_details' => $transactionDetails,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'stock_on_hand' => $runningStock,
                'inventory_asset_value' => $runningValue,
            ];
        }

        // Generate CSV content
        $csvContent = "Inventory Valuation for {$item->item_name}\n";
        $csvContent .= "Generated on: " . now()->format('d-m-Y H:i:s') . "\n";
        $csvContent .= "From: " . Carbon::parse($dateFrom)->format('d M Y') . " To: " . Carbon::parse($dateTo)->format('d M Y') . "\n\n";
        
        $csvContent .= "Date,Transaction Details,Quantity,Unit Cost,Stock On Hand,Inventory Asset Value\n";
        
        foreach ($transactions as $transaction) {
            $csvContent .= Carbon::parse($transaction['date'])->format('d M Y') . "," . 
                          '"' . $transaction['transaction_details'] . '",' . 
                          $transaction['quantity'] . "," . 
                          $transaction['unit_cost'] . "," . 
                          $transaction['stock_on_hand'] . "," . 
                          $transaction['inventory_asset_value'] . "\n";
        }

        // Set headers for CSV download
        $filename = 'inventory_valuation_' . str_replace(' ', '_', $item->item_name) . '_' . Carbon::parse($dateFrom)->format('Y_m_d') . '_to_' . Carbon::parse($dateTo)->format('Y_m_d') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Build FIFO queue with proper cost tracking for edited transactions
     */
    private function buildFIFOQueue($allLedgerEntries, $dateFrom)
    {
        $batchQueue = [];
        $batchCosts = []; // Track the final cost for each batch
        $batchQuantities = []; // Track the final quantity for each batch
        
        // First pass: collect all stock-in entries and their initial costs
        foreach ($allLedgerEntries as $entry) {
            if ($entry->transaction_date < $dateFrom && in_array($entry->transaction_type, ['opening', 'purchase'])) {
                $batchId = $entry->batch_id ?: 'entry_' . $entry->id;
                $batchQuantities[$batchId] = $entry->quantity;
                $batchCosts[$batchId] = $entry->unit_cost ?? 0;
            }
        }
        
        // Second pass: apply any adjustments to get final costs
        foreach ($allLedgerEntries as $entry) {
            if ($entry->transaction_date < $dateFrom && $entry->transaction_type === 'adjustment' && $entry->quantity == 0) {
                // This is a cost adjustment (0 quantity but different unit cost)
                $batchId = $entry->batch_id ?: 'entry_' . $entry->id;
                if (isset($batchCosts[$batchId])) {
                    $batchCosts[$batchId] = $entry->unit_cost ?? $batchCosts[$batchId];
                }
            }
        }
        
        // Build the final FIFO queue
        foreach ($allLedgerEntries as $entry) {
            if ($entry->transaction_date < $dateFrom && in_array($entry->transaction_type, ['opening', 'purchase'])) {
                $batchId = $entry->batch_id ?: 'entry_' . $entry->id;
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'batch_code' => $entry->batch ? $entry->batch->batch_code : 'LEDGER-' . $entry->id,
                    'unit_cost' => $batchCosts[$batchId] ?? $entry->unit_cost ?? 0,
                    'qty_remaining' => $batchQuantities[$batchId] ?? $entry->quantity,
                    'received_date' => $entry->transaction_date,
                    'entry_id' => $entry->id,
                ];
            }
        }
        
        return $batchQueue;
    }

    /**
     * Calculate FIFO value for an item as of a specific date
     */
    public function calculateFIFOValue($businessId, $itemId, $asOnDate)
    {
        // Get all stock ledger entries for this item up to the date
        $allLedgerEntries = GeneralItemStockLedger::with(['batch'])
            ->where('business_id', $businessId)
            ->where('general_item_id', $itemId)
            ->whereDate('transaction_date', '<=', $asOnDate)
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate current stock
        $currentStock = $allLedgerEntries->sum('quantity');

        // Build FIFO queue from stock ledger entries
        $batchQueue = [];
        foreach ($allLedgerEntries as $entry) {
            if (in_array($entry->transaction_type, ['opening', 'purchase'])) {
                // This is a stock-in entry
                $batchQueue[] = [
                    'batch_id' => $entry->batch_id,
                    'unit_cost' => $entry->unit_cost ?? 0,
                    'qty_remaining' => $entry->quantity,
                    'received_date' => $entry->transaction_date,
                ];
            } elseif (in_array($entry->transaction_type, ['sale', 'issue'])) {
                // This is a stock-out entry, consume from FIFO queue
                $saleQty = abs($entry->quantity);
                $remainingSaleQty = $saleQty;
                
                foreach ($batchQueue as $index => $batch) {
                    if ($remainingSaleQty <= 0) break;
                    
                    $usedQty = min($batch['qty_remaining'], $remainingSaleQty);
                    $remainingSaleQty -= $usedQty;
                    
                    // Update batch remaining quantity
                    $batchQueue[$index]['qty_remaining'] -= $usedQty;
                }
            } elseif ($entry->transaction_type === 'return') {
                // Handle return transaction using the new method
                $this->handleReturnTransaction($entry, $entry->quantity, $entry->unit_cost ?? 0, $entry->total_cost ?? 0, $currentStock, $fifoValue, $batchQueue, false);
            } elseif ($entry->transaction_type === 'reversal') {
                // Check if this is a purchase return reversal by looking at the reference
                $isPurchaseReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'PR-') && str_contains($entry->reference_no, '-REV');
                $isSaleReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'SR-');
                
                if ($isPurchaseReturnReversal && $entry->quantity > 0) {
                    // Purchase return reversal - use special handling
                    $this->handlePurchaseReturnReversal($entry, $entry->quantity, $entry->unit_cost ?? 0, $entry->total_cost ?? 0, $currentStock, $fifoValue, $batchQueue, false);
                    
                    // Recalculate FIFO value based on current FIFO queue state
                    $fifoValue = 0;
                    $tempStock = $currentStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $fifoValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                } elseif ($isSaleReturnReversal && $entry->quantity > 0) {
                    // Sale return reversal - use special handling
                    $this->handleSaleReturnReversal($entry, $entry->quantity, $entry->unit_cost ?? 0, $entry->total_cost ?? 0, $currentStock, $fifoValue, $batchQueue, false);
                    
                    // Recalculate FIFO value based on current FIFO queue state
                    $fifoValue = 0;
                    $tempStock = $currentStock;
                    foreach ($batchQueue as $batch) {
                        if ($tempStock <= 0) break;
                        $usedQty = min($batch['qty_remaining'], $tempStock);
                        $fifoValue += $usedQty * $batch['unit_cost'];
                        $tempStock -= $usedQty;
                    }
                } else {
                // This is a reversal entry, handle both positive and negative reversals
                if ($entry->quantity > 0) {
                    // Positive reversal - adding stock back to FIFO queue (sale reversal)
                        // For sale reversals, restore to the exact batch that was originally consumed
                        $batchFound = false;
                        
                        // First, try to find the exact batch match
                        for ($index = 0; $index < count($batchQueue); $index++) {
                            if ($batchQueue[$index]['batch_id'] == $entry->batch_id && $batchQueue[$index]['unit_cost'] == ($entry->unit_cost ?? 0)) {
                                $batchQueue[$index]['qty_remaining'] += $entry->quantity;
                                $batchFound = true;
                                break;
                            }
                        }
                        
                        // If not found, add as new entry
                        if (!$batchFound) {
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'unit_cost' => $entry->unit_cost ?? 0,
                        'qty_remaining' => $entry->quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                        }
                } else {
                    // Negative reversal - reducing stock from FIFO queue (purchase reversal)
                    $reversalQty = abs($entry->quantity);
                    $remainingReversalQty = $reversalQty;
                    
                    // For purchase reversals, try to find and remove from the specific batch first
                    $batchFound = false;
                    if ($entry->batch_id) {
                        for ($index = 0; $index < count($batchQueue); $index++) {
                            if ($batchQueue[$index]['batch_id'] == $entry->batch_id && $batchQueue[$index]['unit_cost'] == ($entry->unit_cost ?? 0)) {
                                $usedQty = min($batchQueue[$index]['qty_remaining'], $remainingReversalQty);
                                $remainingReversalQty -= $usedQty;
                                
                                // Update batch remaining quantity
                                $batchQueue[$index]['qty_remaining'] -= $usedQty;
                                $batchFound = true;
                                
                                if ($remainingReversalQty <= 0) break;
                            }
                        }
                    }
                    
                    // If specific batch not found or still have remaining quantity, use LIFO
                    if (!$batchFound || $remainingReversalQty > 0) {
                        // Process in reverse order (most recent first) for remaining quantity
                        for ($index = count($batchQueue) - 1; $index >= 0; $index--) {
                            if ($remainingReversalQty <= 0) break;
                            
                            $usedQty = min($batchQueue[$index]['qty_remaining'], $remainingReversalQty);
                            $remainingReversalQty -= $usedQty;
                            
                            // Update batch remaining quantity
                            $batchQueue[$index]['qty_remaining'] -= $usedQty;
                            }
                        }
                    }
                }
            } elseif ($entry->transaction_type === 'adjustment') {
                if ($entry->quantity > 0) {
                    // Positive quantity adjustment
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'unit_cost' => $entry->unit_cost ?? 0,
                        'qty_remaining' => $entry->quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                } elseif ($entry->quantity == 0 && $entry->unit_cost > 0) {
                    // Price adjustment - update existing batch
                    foreach ($batchQueue as $index => $batch) {
                        if ($batch['batch_id'] == $entry->batch_id) {
                            $batchQueue[$index]['unit_cost'] = $entry->unit_cost;
                            break;
                        }
                    }
                }
            } elseif ($entry->transaction_type === 'stock_adjustment') {
                // Handle stock adjustments (from Stock Adjustment module)
                if ($entry->quantity > 0) {
                    // Stock addition - add to FIFO queue
                    $batchQueue[] = [
                        'batch_id' => $entry->batch_id,
                        'unit_cost' => $entry->unit_cost ?? 0,
                        'qty_remaining' => $entry->quantity,
                        'received_date' => $entry->transaction_date,
                    ];
                } else {
                    // Stock subtraction - consume from FIFO queue using FIFO
                    $subtractionQty = abs($entry->quantity);
                    $remainingSubtractionQty = $subtractionQty;
                    
                    foreach ($batchQueue as $index => $batch) {
                        if ($remainingSubtractionQty <= 0) break;
                        
                        $usedQty = min($batch['qty_remaining'], $remainingSubtractionQty);
                        $remainingSubtractionQty -= $usedQty;
                        
                        // Update batch remaining quantity
                        $batchQueue[$index]['qty_remaining'] -= $usedQty;
                    }
                }
            }
        }

        // Sort the FIFO queue to ensure proper FIFO order
        usort($batchQueue, function($a, $b) {
            // Sort by transaction date first
            $dateCompare = strcmp($a['received_date'], $b['received_date']);
            if ($dateCompare !== 0) {
                return $dateCompare;
            }
            
            // If dates are the same, sort by batch_id to maintain consistent order
            return $a['batch_id'] - $b['batch_id'];
        });

        // Calculate FIFO value based on actual remaining stock in FIFO queue
        $fifoValue = 0;
        $remainingStock = $currentStock;
        
        foreach ($batchQueue as $batch) {
            if ($remainingStock <= 0) break;
            $usedQty = min($batch['qty_remaining'], $remainingStock);
            $fifoValue += $usedQty * $batch['unit_cost'];
            $remainingStock -= $usedQty;
        }

        // If the FIFO calculation doesn't match the actual batch values, 
        // use the actual batch values as the final result
        $actualBatchValue = 0;
        $batches = \App\Models\GeneralBatch::where('item_id', $itemId)
            ->where('qty_remaining', '>', 0)
            ->get();
            
        foreach ($batches as $batch) {
            $actualBatchValue += $batch->qty_remaining * $batch->unit_cost;
        }
        
        // Use the actual batch value if it's different from FIFO calculation
        // This ensures consistency with the actual batch quantities
        if (abs($fifoValue - $actualBatchValue) > 0.01) {
            return $actualBatchValue;
        }

        return $fifoValue;
    }

    /**
     * Check if there are any reversal transactions up to a specific date
     */
    private function hasReversalsUpToDate($businessId, $itemId, $asOfDate): bool
    {
        return GeneralItemStockLedger::where('business_id', $businessId)
            ->where('general_item_id', $itemId)
            ->where('transaction_type', 'reversal')
            ->where('transaction_date', '<=', $asOfDate)
            ->exists();
    }

    /**
     * Check if there are any sale transactions up to a specific date
     */
    private function hasSalesUpToDate($businessId, $itemId, $asOfDate): bool
    {
        return GeneralItemStockLedger::where('business_id', $businessId)
            ->where('general_item_id', $itemId)
            ->where('transaction_type', 'sale')
            ->where('transaction_date', '<=', $asOfDate)
            ->exists();
    }

    /**
     * Handle return transaction logic for FIFO calculation
     */
    private function handleReturnTransaction($entry, $quantity, $unitCost, $totalCost, &$runningStock, &$value, &$batchQueue, $updateRunningStock = true)
    {
        // Determine if this is a sale return (positive quantity) or purchase return (negative quantity)
        if ($quantity > 0) {
            // Sale return - adding stock back to FIFO queue
            if ($updateRunningStock) {
            $runningStock += $quantity;
            }
            
            // Add back to FIFO queue at the return cost
            $batchQueue[] = [
                'batch_id' => $entry->batch_id,
                'batch_code' => $entry->batch ? $entry->batch->batch_code : 'RET-' . $entry->id,
                'unit_cost' => $unitCost,
                'qty_remaining' => $quantity,
                'received_date' => $entry->transaction_date,
            ];
            
            // Add the value back to running value
            $value += $totalCost;
        } else {
            // Purchase return - reducing stock from FIFO queue
            if ($updateRunningStock) {
            $runningStock += $quantity; // quantity is negative
            }
            $returnQty = abs($quantity);
            
            // For purchase returns, we should reduce from the most recent batch first (LIFO for returns)
            $remainingReturnQty = $returnQty;
            $returnValue = 0; // Track the actual value being removed
            
            // Process in reverse order (most recent first)
            for ($index = count($batchQueue) - 1; $index >= 0; $index--) {
                if ($remainingReturnQty <= 0) break;
                
                $usedQty = min($batchQueue[$index]['qty_remaining'], $remainingReturnQty);
                $remainingReturnQty -= $usedQty;
                
                // Calculate the value being removed from this batch
                $returnValue += $usedQty * $batchQueue[$index]['unit_cost'];
                
                // Update batch remaining quantity
                $batchQueue[$index]['qty_remaining'] -= $usedQty;
            }
            
            // Reduce the value from running value using actual FIFO cost
            $value -= $returnValue;
            
            // Recalculate FIFO value based on current FIFO queue state
            $recalculatedValue = 0;
            $tempStock = $runningStock;
            foreach ($batchQueue as $batch) {
                if ($tempStock <= 0) break;
                $usedQty = min($batch['qty_remaining'], $tempStock);
                $recalculatedValue += $usedQty * $batch['unit_cost'];
                $tempStock -= $usedQty;
            }
            $value = $recalculatedValue;
        }
    }

    /**
     * Handle purchase return reversal transactions
     */
    private function handlePurchaseReturnReversal($entry, $quantity, $unitCost, $totalCost, &$runningStock, &$runningValue, &$batchQueue, $updateRunningStock = true)
    {
        if ($updateRunningStock) {
            $runningStock += $quantity; // quantity is positive for purchase return reversals
        }
        
        // For purchase return reversals, we need to restore stock to the ORIGINAL batch
        // that was the source of the stock, not the batch mentioned in the reversal entry
        
        // Find the opening stock batch (the original source) and restore to it
        $batchFound = false;
        for ($index = 0; $index < count($batchQueue); $index++) {
            // Look for the opening stock batch (lowest unit_cost, usually the first batch)
            if ($batchQueue[$index]['unit_cost'] < $unitCost) {
                // Restore quantity to the opening stock batch
                $batchQueue[$index]['qty_remaining'] += $quantity;
                $batchFound = true;
                break;
            }
        }
        
        // If no opening stock batch found, try to find by batch_id
        if (!$batchFound) {
            for ($index = 0; $index < count($batchQueue); $index++) {
                if ($batchQueue[$index]['batch_id'] == $entry->batch_id) {
                    // Restore quantity to the original batch
                    $batchQueue[$index]['qty_remaining'] += $quantity;
                    $batchFound = true;
                    break;
                }
            }
        }
        
        // If batch not found, add as new entry
        if (!$batchFound) {
            $batchQueue[] = [
                'batch_id' => $entry->batch_id,
                'batch_code' => $entry->batch ? $entry->batch->batch_code : 'REV-' . $entry->id,
                'unit_cost' => $unitCost,
                'qty_remaining' => $quantity,
                'received_date' => $entry->transaction_date,
            ];
        }
        
        
        // Don't add to running value here - let the FIFO calculation handle it
        // The running value will be recalculated based on the FIFO queue
    }

    /**
     * Handle sale return reversal transactions
     */
    private function handleSaleReturnReversal($entry, $quantity, $unitCost, $totalCost, &$runningStock, &$runningValue, &$batchQueue, $updateRunningStock = true)
    {
        if ($updateRunningStock) {
            $runningStock += $quantity; // quantity is positive for sale return reversals
        }
        
        // For sale return reversals, we need to restore stock to the ORIGINAL batch
        // that was the source of the stock, not the batch mentioned in the reversal entry
        
        // Find the batch with the same unit_cost as the reversal (this should be the original batch)
        $batchFound = false;
        for ($index = 0; $index < count($batchQueue); $index++) {
            if ($batchQueue[$index]['unit_cost'] == $unitCost) {
                // Restore quantity to this batch (should be the original batch)
                $batchQueue[$index]['qty_remaining'] += $quantity;
                $batchFound = true;
                break;
            }
        }
        
        // If not found by unit_cost, try to find by batch_id
        if (!$batchFound) {
            for ($index = 0; $index < count($batchQueue); $index++) {
                if ($batchQueue[$index]['batch_id'] == $entry->batch_id) {
                    // Restore quantity to the original batch
                    $batchQueue[$index]['qty_remaining'] += $quantity;
                    $batchFound = true;
                    break;
                }
            }
        }
        
        // If batch not found, add as new entry
        if (!$batchFound) {
            $batchQueue[] = [
                'batch_id' => $entry->batch_id,
                'batch_code' => $entry->batch ? $entry->batch->batch_code : 'REV-' . $entry->id,
                'unit_cost' => $unitCost,
                'qty_remaining' => $quantity,
                'received_date' => $entry->transaction_date,
            ];
        }
        
        // Don't add to running value here - let the FIFO calculation handle it
        // The running value will be recalculated based on the FIFO queue
    }

    /**
     * Format transaction details for display
     */
    private function formatTransactionDetails($entry, $transactionType)
    {
        switch ($transactionType) {
            case 'opening':
                return '*** Opening Stock ***';
            case 'purchase':
                $purchaseId = $entry->purchase_id;
                $partyName = '';
                if ($purchaseId) {
                    $purchase = \App\Models\Purchase::with('party')->find($purchaseId);
                    $partyName = $purchase && $purchase->party ? ' - ' . $purchase->party->name : '';
                }
                return "Purchase Stock # {$purchaseId}{$partyName}";
            case 'sale':
                $saleId = $entry->reference_id ?: $entry->id;
                $saleNumber = null;
                $partyName = '';
                if ($entry->reference_id) {
                    $sale = \App\Models\SaleInvoice::with('party')->find($entry->reference_id);
                    if ($sale) {
                        $saleNumber = $sale->invoice_number ?: $saleId;
                        $partyName = $sale->party ? ' - ' . $sale->party->name : '';
                }
                }
                $displayNumber = $saleNumber ?? ($entry->reference_no ?: $saleId);
                return "Sale Stock # {$displayNumber}{$partyName}";
            case 'issue':
                $issueId = $entry->reference_id ?: $entry->id;
                return "Issue Stock # {$issueId}";
            case 'adjustment':
                $adjustmentId = $entry->reference_id ?: $entry->id;
                return "Stock Adjustment # {$adjustmentId}";
            case 'stock_adjustment':
                $adjustmentId = $entry->reference_id ?: $entry->id;
                $type = $entry->quantity > 0 ? 'Addition' : 'Subtraction';
                return "Stock Adjustment ({$type}) # {$adjustmentId}";
            case 'reversal':
                $reversalId = $entry->reference_id ?: $entry->id;
                
                // Check if this is a stock adjustment reversal
                $isStockAdjustmentReversal = $entry->remarks && str_contains($entry->remarks, 'stock adjustment');
                
                // Check if this is a purchase return reversal by looking at the reference
                $isPurchaseReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'PR-') && str_contains($entry->reference_no, '-REV');
                $isSaleReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'SR-');
                
                if ($isStockAdjustmentReversal) {
                    return "Stock Adjustment Reversal # {$reversalId}";
                } elseif ($isPurchaseReturnReversal) {
                    return "Purchase Return Reversal # {$reversalId}";
                } elseif ($isSaleReturnReversal) {
                    return "Sale Return Reversal # {$reversalId}";
                } else {
                // Determine if it's a sale reversal or purchase reversal based on quantity
                if ($entry->quantity > 0) {
                    $saleNumber = null;
                    if ($entry->reference_id) {
                        $sale = \App\Models\SaleInvoice::find($entry->reference_id);
                        if ($sale) {
                            $saleNumber = $sale->invoice_number ?: $reversalId;
                        }
                    }
                    $displayNumber = $saleNumber ?? ($entry->reference_no ? str_replace('-REV', '', $entry->reference_no) : $reversalId);
                    return "Sale Reversal # {$displayNumber}";
                } else {
                    return "Purchase Reversal # {$reversalId}";
                    }
                }
            case 'return':
                $returnId = $entry->reference_id ?: $entry->id;
                $referenceNo = $entry->reference_no;
                $isPurchaseReturn = $referenceNo && str_contains($referenceNo, 'PR-');
                $isSaleReturn = $referenceNo && str_contains($referenceNo, 'SR-');
                $partyName = '';
                
                if ($isPurchaseReturn) {
                    $displayNumber = $referenceNo ?? $returnId;
                    return "Purchase Return # {$displayNumber}";
                }

                if ($isSaleReturn) {
                $displayNumber = $referenceNo ?? $returnId;
                $partyName = '';

                    if ($entry->reference_id) {
                    $saleReturn = \App\Models\SaleReturn::with('party')->find($entry->reference_id);
                    if ($saleReturn) {
                        $displayNumber = $saleReturn->return_number ?? $displayNumber;
                        $partyName = $saleReturn->party ? ' - ' . $saleReturn->party->name : '';
                        }
                    }

                    return "Sale Return # {$displayNumber}{$partyName}";
                }

            $displayNumber = $referenceNo ?? $returnId;

            if ($entry->reference_id && $isPurchaseReturn) {
                $purchaseReturn = \App\Models\PurchaseReturn::with('party')->find($entry->reference_id);
                if ($purchaseReturn) {
                    $displayNumber = $purchaseReturn->return_number ?? $displayNumber;
                    $partyName = $purchaseReturn->party ? ' - ' . $purchaseReturn->party->name : '';
                }
            }

            return "Purchase Return # {$displayNumber}{$partyName}";
            default:
                $defaultId = $entry->reference_id ?: $entry->id;
                return ucfirst($transactionType) . " # {$defaultId}";
        }
    }

    /**
     * Generate transaction link URL for clickable transactions
     */
    private function generateTransactionLink($entry, $transactionType)
    {
        try {
            switch ($transactionType) {
                case 'purchase':
                    if ($entry->purchase_id) {
                        return route('purchases.show', $entry->purchase_id);
                    }
                    break;
                    
                case 'sale':
                    if ($entry->reference_id) {
                        $sale = \App\Models\SaleInvoice::find($entry->reference_id);
                        if ($sale) {
                            return route('sale-invoices.show', $sale->id);
                        }
                    }
                    break;
                    
                case 'return':
                    $referenceNo = $entry->reference_no;
                    $isPurchaseReturn = $referenceNo && str_contains($referenceNo, 'PR-');
                    $isSaleReturn = $referenceNo && str_contains($referenceNo, 'SR-');
                    
                    if ($isPurchaseReturn && $entry->reference_id) {
                        $purchaseReturn = \App\Models\PurchaseReturn::find($entry->reference_id);
                        if ($purchaseReturn) {
                            return route('purchase-returns.show', $purchaseReturn->id);
                        }
                    }
                    
                    if ($isSaleReturn && $entry->reference_id) {
                        $saleReturn = \App\Models\SaleReturn::find($entry->reference_id);
                        if ($saleReturn) {
                            return route('sale-returns.show', $saleReturn->id);
                        }
                    }
                    break;
                    
                case 'reversal':
                    // Check if this is a purchase return reversal
                    $isPurchaseReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'PR-') && str_contains($entry->reference_no, '-REV');
                    $isSaleReturnReversal = $entry->reference_no && str_contains($entry->reference_no, 'SR-');
                    
                    if ($isPurchaseReturnReversal && $entry->reference_id) {
                        $purchaseReturn = \App\Models\PurchaseReturn::find($entry->reference_id);
                        if ($purchaseReturn) {
                            return route('purchase-returns.show', $purchaseReturn->id);
                        }
                    }
                    
                    if ($isSaleReturnReversal && $entry->reference_id) {
                        $saleReturn = \App\Models\SaleReturn::find($entry->reference_id);
                        if ($saleReturn) {
                            return route('sale-returns.show', $saleReturn->id);
                        }
                    }
                    
                    // Sale reversal (editing a sale invoice)
                    if ($entry->quantity > 0 && $entry->reference_id) {
                        $sale = \App\Models\SaleInvoice::find($entry->reference_id);
                        if ($sale) {
                            return route('sale-invoices.show', $sale->id);
                        }
                    }
                    break;
                    
                case 'adjustment':
                case 'stock_adjustment':
                    if ($entry->reference_id) {
                        return route('stock-adjustments.show', $entry->reference_id);
                    }
                    break;
                    
                case 'opening':
                case 'issue':
                default:
                    // No link for opening stock, issues, or unknown types
                    return null;
            }
        } catch (\Exception $e) {
            // If any error occurs, return null (no link)
            \Log::warning('Error generating transaction link', [
                'transaction_type' => $transactionType,
                'entry_id' => $entry->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
        
        return null;
    }

    /**
     * Export Inventory Valuation Summary Report to CSV
     */
    public function exportInventoryValuationSummary(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->back()->with('error', 'No active business selected.');
        }

        // Get filter parameters
        $itemId = $request->get('item_id');
        $itemTypeId = $request->get('item_type_id');
        $stockFilter = $request->get('stock_filter', 'all');
        $asOnDate = $request->get('as_on_date', Carbon::now()->format('Y-m-d'));

        // Build the query for inventory valuation
        $query = GeneralItem::with(['itemType', 'batches' => function($q) use ($asOnDate) {
            $q->where('status', 'active')
              ->where('received_date', '<=', $asOnDate);
        }])
        ->where('business_id', $businessId);

        // Apply filters
        if ($itemId) {
            $query->where('id', $itemId);
        }

        if ($itemTypeId) {
            $query->where('item_type_id', $itemTypeId);
        }

        // Get items with their current stock and valuation
        $items = $query->get()->map(function ($item) use ($asOnDate, $businessId) {
            // Calculate current stock from batches
            $currentStock = $item->batches->sum('qty_remaining');
            
            // Calculate inventory asset value using FIFO
            $inventoryAssetValue = $this->calculateFIFOValue($businessId, $item->id, $asOnDate);
            
            return [
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'item_type' => $item->itemType ? $item->itemType->item_type : 'N/A',
                'current_stock' => $currentStock,
                'inventory_asset_value' => $inventoryAssetValue,
            ];
        });

        // Apply stock filter
        if ($stockFilter === 'greater_than_zero') {
            $items = $items->filter(function ($item) {
                return $item['current_stock'] > 0;
            });
        } elseif ($stockFilter === 'equal_to_zero') {
            $items = $items->filter(function ($item) {
                return $item['current_stock'] == 0;
            });
        }

        // Group by item type for export
        $groupedItems = $items->groupBy('item_type');

        // Generate CSV content
        $csvContent = "Inventory Valuation Summary Report\n";
        $csvContent .= "Generated on: " . now()->format('d-m-Y H:i:s') . "\n";
        $csvContent .= "As On Date: " . Carbon::parse($asOnDate)->format('d M Y') . "\n\n";
        
        $csvContent .= "No,Item Name,Stock On Hand,Inventory Asset Value\n";
        
        $rowNumber = 1;
        foreach ($groupedItems as $itemType => $items) {
            $csvContent .= "\n" . strtoupper($itemType) . "\n";
            foreach ($items as $item) {
                $csvContent .= $rowNumber . "," . 
                              '"' . $item['item_name'] . '",' . 
                              $item['current_stock'] . "," . 
                              $item['inventory_asset_value'] . "\n";
                $rowNumber++;
            }
        }

        // Add totals
        $totalStock = $items->sum('current_stock');
        $totalValue = $items->sum('inventory_asset_value');
        $csvContent .= "\nTotal," . $totalStock . "," . $totalValue . "\n";

        // Set headers for CSV download
        $filename = 'inventory_valuation_summary_' . Carbon::parse($asOnDate)->format('Y_m_d') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function export(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->back()->with('error', 'No active business selected.');
        }

        // Get filter parameters (same as index)
        $itemId = $request->get('item_id');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type');

        // Build the query
        $query = GeneralItemStockLedger::with(['item'])
            ->where('business_id', $businessId);

        if ($itemId) {
            $query->where('general_item_id', $itemId);
        }

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        if ($transactionType) {
            $query->where('transaction_type', $transactionType);
        }

        $ledgerEntries = $query->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN transaction_type = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate running balance correctly (chronological order)
        $runningBalance = 0;
        $ledgerEntries->transform(function ($entry) use (&$runningBalance) {
            // Stock in transactions: opening, purchase
            if (in_array($entry->transaction_type, ['opening', 'purchase'])) {
                $runningBalance += $entry->quantity;
            } 
            // Stock out transactions: issue, sale
            else if (in_array($entry->transaction_type, ['issue', 'sale'])) {
                $runningBalance -= abs($entry->quantity); // Use abs() since sale quantities are negative
            }
            // Other transactions: adjustment, reversal, edit
            else {
                if ($entry->quantity > 0) {
                    $runningBalance += $entry->quantity;
                } else {
                    $runningBalance += $entry->quantity; // quantity is already negative
                }
            }
            
            // Set the running balance for this entry
            $entry->running_balance = $runningBalance;
            return $entry;
        });

        // Keep entries in ascending order (oldest first) for correct display

        // Generate CSV
        $filename = 'stock_ledger_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($ledgerEntries) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date',
                'Item Name',
                'Item Code',
                'Transaction Type',
                'Quantity',
                'Unit Cost',
                'Total Cost',
                'Reference',
                'Description',
                'Running Balance'
            ]);

            // CSV data
            foreach ($ledgerEntries as $entry) {
                fputcsv($file, [
                    $entry->transaction_date,
                    $entry->item->item_name ?? 'N/A',
                    $entry->item->item_code ?? 'N/A',
                    ucfirst($entry->transaction_type),
                    $entry->quantity,
                    $entry->unit_cost ?? 0,
                    ($entry->quantity * ($entry->unit_cost ?? 0)),
                    $entry->reference_no ?? 'N/A',
                    $entry->remarks ?? 'N/A',
                    $entry->running_balance
                ]);
            }

            fclose($file);
        };

        // Get business information for filename
        $business = \App\Models\Business::find($businessId);
        $businessName = $business ? $business->name : 'Business';
        
        $filename = $businessName . '_stock_ledger_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream($callback, 200, $headers);
    }
}
