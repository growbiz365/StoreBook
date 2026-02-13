<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\GeneralItem;
use App\Models\GeneralItemStockLedger;
use App\Models\GeneralBatch;
use App\Models\Arm;
use App\Models\ArmsStockLedger;
use App\Models\ArmHistory;
use App\Models\StockAdjustmentItem;
use App\Models\StockAdjustmentArm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StockAdjustmentController extends Controller
{
    private function getLastItemBalance(int $businessId, int $itemId, ?int $excludeAdjustmentId = null): int
    {
        $query = GeneralItemStockLedger::where('business_id', $businessId)
            ->where('general_item_id', $itemId);
        
        if ($excludeAdjustmentId) {
            $query->where(function($q) use ($excludeAdjustmentId) {
                $q->where('reference_id', '!=', $excludeAdjustmentId)
                  ->orWhereNull('reference_id');
            });
        }
        
        return (int) ($query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->value('balance_quantity') ?? 0);
    }
    /**
     * Display a listing of stock adjustments
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');

        $parents = StockAdjustment::with(['itemLines.item', 'armLines.arm'])
            ->where('business_id', $businessId);

        if ($request->filled('adjustment_type') && $request->adjustment_type !== 'all') {
            $parents->where('adjustment_type', $request->adjustment_type);
        }
        if ($request->filled('date')) {
            $parents->whereDate('adjustment_date', $request->date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $parents->where(function($q) use ($search) {
                $q->whereHas('itemLines.item', function($iq) use ($search) {
                    $iq->where('item_name', 'like', "%$search%")
                       ->orWhere('item_code', 'like', "%$search%");
                })->orWhereHas('armLines.arm', function($aq) use ($search) {
                    $aq->where('arm_title', 'like', "%$search%")
                       ->orWhere('serial_no', 'like', "%$search%");
                });
            });
        }

        $parents->orderBy('adjustment_date', 'desc')->orderBy('created_at', 'desc');

        $paginated = $parents->paginate(20)->through(function($parent) {
            $itemsTotal = $parent->itemLines->sum('total_amount');
            $armsTotal = $parent->armLines->sum('price');
            return [
                'id' => $parent->id,
                'date' => $parent->adjustment_date,
                'type' => $parent->adjustment_type,
                'items_count' => $parent->itemLines->count(),
                'arms_count' => $parent->armLines->count(),
                'amount' => ($itemsTotal + $armsTotal),
            ];
        });

        return view('stock-adjustments.index', [
            'parents' => $paginated,
        ]);
    }

    /**
     * Show the form for creating a new stock adjustment
     */
    public function create()
    {
        $businessId = session('active_business');
        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Arms data loading disabled - StoreBook is items-only
        // $arms = Arm::forBusiness($businessId)
        //     ->available()
        //     ->orderBy('arm_title')
        //     ->get();

        // Empty collection for arms data to prevent errors in views
        $arms = collect();

        return view('stock-adjustments.create', compact('generalItems', 'arms'));
    }

    /**
     * Store a newly created stock adjustment
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');

        $validator = Validator::make($request->all(), [
            'adjustment_type' => 'required|in:addition,subtraction',
            'adjustment_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'items' => 'nullable|array',
            'items.*.general_item_id' => 'required|exists:general_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            // ARM specific (optional block)
            'arm_items' => 'nullable|array',
            'arm_items.*.arm_id' => 'required_with:arm_items|exists:arms,id',
            'arm_items.*.reason' => 'required_with:arm_items|in:damage,theft',
            'arm_items.*.price' => 'nullable|numeric|min:0',
            'arm_items.*.remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ensure at least one of items or arm_items is provided
        if (!(is_array($request->items) && count($request->items) > 0) && !(is_array($request->arm_items) && count($request->arm_items) > 0)) {
            return redirect()->back()
                ->withErrors(['items' => 'Please add at least one general item or one arm to adjust.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $businessId) {
            // Create parent adjustment row
            $parent = StockAdjustment::create([
                'business_id' => $businessId,
                'adjustment_type' => $request->adjustment_type,
                'adjustment_date' => $request->adjustment_date,
                'description' => $request->description,
                'user_id' => Auth::id(),
            ]);

            // Items
            if (is_array($request->items)) {
                foreach ($request->items as $item) {
                    StockAdjustmentItem::create([
                        'stock_adjustment_id' => $parent->id,
                        'general_item_id' => $item['general_item_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'total_amount' => $item['quantity'] * $item['unit_cost'],
                    ]);

                    if ($request->adjustment_type === 'addition') {
                        $batch = GeneralBatch::create([
                            'business_id' => $businessId,
                            'item_id' => $item['general_item_id'],
                            'qty_received' => $item['quantity'],
                            'qty_remaining' => $item['quantity'],
                            'unit_cost' => $item['unit_cost'],
                            'total_cost' => $item['quantity'] * $item['unit_cost'],
                            'received_date' => $request->adjustment_date,
                            'user_id' => Auth::id(),
                            'batch_code' => 'ADJ-' . $parent->id . '-' . uniqid(),
                            'status' => 'active',
                        ]);

                        GeneralItemStockLedger::create([
                            'business_id' => $businessId,
                            'general_item_id' => $item['general_item_id'],
                            'batch_id' => $batch->id,
                            'transaction_date' => $request->adjustment_date,
                            'transaction_type' => 'stock_adjustment',
                            'quantity' => $item['quantity'],
                            'quantity_in' => $item['quantity'],
                            'quantity_out' => 0,
                            'balance_quantity' => 0,
                            'unit_cost' => $item['unit_cost'],
                            'total_cost' => $item['quantity'] * $item['unit_cost'],
                            'reference_id' => $parent->id,
                            'remarks' => $request->description ?: 'Stock adjustment - Addition',
                            'created_by' => Auth::id(),
                        ]);
                    } else {
                        $fifoAllocation = GeneralItemStockLedger::getFIFOAllocation($item['general_item_id'], $item['quantity']);
                        foreach ($fifoAllocation as $allocation) {
                            GeneralItemStockLedger::create([
                                'business_id' => $businessId,
                                'general_item_id' => $item['general_item_id'],
                                'batch_id' => $allocation['batch_id'],
                                'transaction_date' => $request->adjustment_date,
                                'transaction_type' => 'stock_adjustment',
                                'quantity' => -$allocation['quantity'],
                                'quantity_in' => 0,
                                'quantity_out' => $allocation['quantity'],
                                'balance_quantity' => 0,
                                'unit_cost' => $allocation['unit_cost'],
                                'total_cost' => $allocation['total_cost'],
                                'reference_id' => $parent->id,
                                'remarks' => $request->description ?: 'Stock adjustment - Subtraction',
                                'created_by' => Auth::id(),
                            ]);
                            $batch = GeneralBatch::find($allocation['batch_id']);
                            $batch?->update(['qty_remaining' => $batch->qty_remaining - $allocation['quantity']]);
                        }
                    }
                }
            }

            // Arms
            if ($request->adjustment_type === 'subtraction' && is_array($request->arm_items)) {
                foreach ($request->arm_items as $armLine) {
                    $arm = Arm::forBusiness($businessId)->available()->find($armLine['arm_id']);
                    if (!$arm) continue;

                    StockAdjustmentArm::create([
                        'stock_adjustment_id' => $parent->id,
                        'arm_id' => $arm->id,
                        'reason' => $armLine['reason'],
                        'price' => $armLine['price'] ?? null,
                    ]);

                    ArmsStockLedger::create([
                        'business_id' => $businessId,
                        'arm_id' => $arm->id,
                        'transaction_date' => $request->adjustment_date,
                        'transaction_type' => $armLine['reason'],
                        'quantity_in' => 0,
                        'quantity_out' => 1,
                        'balance' => 0,
                        'reference_id' => $parent->id,
                        'remarks' => $request->description ?: ucfirst($armLine['reason']),
                    ]);

                    $oldValues = $arm->toArray();
                    $arm->update(['status' => 'decommissioned']);

                    ArmHistory::create([
                        'business_id' => $businessId,
                        'arm_id' => $arm->id,
                        'action' => 'decommission',
                        'old_values' => $oldValues,
                        'new_values' => $arm->fresh()->toArray(),
                        'transaction_date' => $request->adjustment_date,
                        'price' => $armLine['price'] ?? null,
                        'remarks' => ($request->description ?: ''),
                        'user_id' => \Auth::id(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock adjustment created successfully.');
    }

    /**
     * Display the specified stock adjustment
     */
    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['itemLines.item', 'armLines.arm', 'user']);
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    /**
     * Show the form for editing the specified stock adjustment
     */
    public function edit(StockAdjustment $stockAdjustment)
    {
        $businessId = session('active_business');
        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();
        
        // Arms data loading disabled - StoreBook is items-only
        // $arms = Arm::forBusiness($businessId)
        //     ->available()
        //     ->orderBy('arm_title')
        //     ->get();

        // Empty collection for arms data to prevent errors in views
        $arms = collect();

        return view('stock-adjustments.edit', compact('stockAdjustment', 'generalItems', 'arms'));
    }

    /**
     * Update the specified stock adjustment
     */
    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        $businessId = session('active_business');

        $validator = Validator::make($request->all(), [
            'adjustment_type' => 'required|in:addition,subtraction',
            'adjustment_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            // legacy single-item fields are optional for backward compatibility
            'general_item_id' => 'nullable|exists:general_items,id',
            'quantity' => 'nullable|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            // child lines (optional)
            'items' => 'nullable|array',
            'items.*.general_item_id' => 'required|exists:general_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'existing_items' => 'nullable|array',
            'existing_items.*.general_item_id' => 'nullable|exists:general_items,id',
            'existing_items.*.quantity' => 'nullable|numeric|min:0.01',
            'existing_items.*.unit_cost' => 'nullable|numeric|min:0',
            'existing_items.*._delete' => 'nullable|in:0,1',
            'arm_items' => 'nullable|array',
            'arm_items.*.arm_id' => 'required|exists:arms,id',
            'arm_items.*.reason' => 'required|in:damage,theft',
            'arm_items.*.price' => 'nullable|numeric|min:0',
            'existing_arms' => 'nullable|array',
            'existing_arms.*.arm_id' => 'nullable|exists:arms,id',
            'existing_arms.*.reason' => 'nullable|in:damage,theft',
            'existing_arms.*.price' => 'nullable|numeric|min:0',
            'existing_arms.*._delete' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $stockAdjustment, $businessId) {
            // Update parent fields
            $stockAdjustment->update([
                'adjustment_type' => $request->adjustment_type,
                'adjustment_date' => $request->adjustment_date,
                'description' => $request->description,
            ]);

            // Legacy single-item flow (only if fields are provided)
            if ($request->filled('general_item_id') && $request->filled('quantity') && $request->filled('unit_cost')) {
                $oldLedgerEntry = GeneralItemStockLedger::where('reference_id', $stockAdjustment->id)
                    ->where('transaction_type', 'stock_adjustment')
                    ->first();
                if ($oldLedgerEntry) {
                    GeneralItemStockLedger::create([
                        'business_id' => $businessId,
                        'general_item_id' => $oldLedgerEntry->general_item_id,
                        'transaction_date' => $oldLedgerEntry->transaction_date,
                        'transaction_type' => 'reversal',
                        'quantity' => $oldLedgerEntry->quantity_out - $oldLedgerEntry->quantity_in,
                        'quantity_in' => $oldLedgerEntry->quantity_out,
                        'quantity_out' => $oldLedgerEntry->quantity_in,
                        'balance_quantity' => 0,
                        'unit_cost' => $oldLedgerEntry->unit_cost,
                        'total_cost' => ($oldLedgerEntry->quantity_out - $oldLedgerEntry->quantity_in) * $oldLedgerEntry->unit_cost,
                        'reference_id' => $stockAdjustment->id,
                        'remarks' => 'Reverse of stock adjustment #' . $stockAdjustment->id,
                        'created_by' => Auth::id(),
                    ]);
                }
                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $request->general_item_id,
                    'transaction_date' => $request->adjustment_date,
                    'transaction_type' => 'stock_adjustment',
                    'quantity' => $request->adjustment_type === 'addition' ? $request->quantity : -$request->quantity,
                    'quantity_in' => $request->adjustment_type === 'addition' ? $request->quantity : 0,
                    'quantity_out' => $request->adjustment_type === 'subtraction' ? $request->quantity : 0,
                    'balance_quantity' => 0,
                    'unit_cost' => $request->unit_cost,
                    'total_cost' => $request->quantity * $request->unit_cost,
                    'reference_id' => $stockAdjustment->id,
                    'remarks' => $request->description ?: 'Stock adjustment - ' . ucfirst($request->adjustment_type),
                    'created_by' => Auth::id(),
                ]);
            }

            // Handle any additional items to add during edit
            if (is_array($request->items)) {
                foreach ($request->items as $item) {
                    $newAdjustment = StockAdjustment::create([
                        'business_id' => $businessId,
                        'general_item_id' => $item['general_item_id'],
                        'adjustment_type' => $request->adjustment_type,
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'total_amount' => $item['quantity'] * $item['unit_cost'],
                        'adjustment_date' => $request->adjustment_date,
                        'description' => $request->description,
                        'user_id' => Auth::id(),
                    ]);

                    if ($request->adjustment_type === 'addition') {
                        // Create a new batch for additions
                        $batch = GeneralBatch::create([
                            'business_id' => $businessId,
                            'item_id' => $item['general_item_id'],
                            'qty_received' => $item['quantity'],
                            'qty_remaining' => $item['quantity'],
                            'unit_cost' => $item['unit_cost'],
                            'total_cost' => $item['quantity'] * $item['unit_cost'],
                            'received_date' => $request->adjustment_date,
                            'user_id' => Auth::id(),
                            'batch_code' => 'ADJ-' . $newAdjustment->id . '-' . uniqid(),
                            'status' => 'active',
                        ]);

                        GeneralItemStockLedger::create([
                            'business_id' => $businessId,
                            'general_item_id' => $item['general_item_id'],
                            'batch_id' => $batch->id,
                            'transaction_date' => $request->adjustment_date,
                            'transaction_type' => 'stock_adjustment',
                            'quantity' => $item['quantity'],
                            'quantity_in' => $item['quantity'],
                            'quantity_out' => 0,
                            'balance_quantity' => 0,
                            'unit_cost' => $item['unit_cost'],
                            'total_cost' => $item['quantity'] * $item['unit_cost'],
                            'reference_id' => $newAdjustment->id,
                            'remarks' => $request->description ?: 'Stock adjustment - Addition',
                            'created_by' => Auth::id(),
                        ]);
                    } else {
                        // Subtraction via FIFO
                        $fifoAllocation = GeneralItemStockLedger::getFIFOAllocation($item['general_item_id'], $item['quantity']);
                        foreach ($fifoAllocation as $allocation) {
                            GeneralItemStockLedger::create([
                                'business_id' => $businessId,
                                'general_item_id' => $item['general_item_id'],
                                'batch_id' => $allocation['batch_id'],
                                'transaction_date' => $request->adjustment_date,
                                'transaction_type' => 'stock_adjustment',
                                'quantity' => -$allocation['quantity'],
                                'quantity_in' => 0,
                                'quantity_out' => $allocation['quantity'],
                                'balance_quantity' => 0,
                                'unit_cost' => $allocation['unit_cost'],
                                'total_cost' => $allocation['total_cost'],
                                'reference_id' => $newAdjustment->id,
                                'remarks' => $request->description ?: 'Stock adjustment - Subtraction',
                                'created_by' => Auth::id(),
                            ]);

                            // Update batch remaining quantity
                            $batch = GeneralBatch::find($allocation['batch_id']);
                            if ($batch) {
                                $batch->update(['qty_remaining' => $batch->qty_remaining - $allocation['quantity']]);
                            }
                        }
                    }
                }
            }

            // Update or delete existing item lines
            if (is_array($request->existing_items)) {
                foreach ($request->existing_items as $lineId => $payload) {
                    $line = StockAdjustmentItem::where('stock_adjustment_id', $stockAdjustment->id)->find($lineId);
                    if (!$line) { continue; }
                    if (isset($payload['_delete']) && (string)$payload['_delete'] === '1') {
                        $line->delete();
                        continue;
                    }
                    $line->update([
                        'general_item_id' => $payload['general_item_id'] ?? $line->general_item_id,
                        'quantity' => isset($payload['quantity']) ? (float)$payload['quantity'] : $line->quantity,
                        'unit_cost' => isset($payload['unit_cost']) ? (float)$payload['unit_cost'] : $line->unit_cost,
                        'total_amount' => (isset($payload['quantity']) && isset($payload['unit_cost']))
                            ? ((float)$payload['quantity'] * (float)$payload['unit_cost'])
                            : $line->total_amount,
                    ]);
                }
            }

            // Update or delete existing arm lines
            if (is_array($request->existing_arms)) {
                foreach ($request->existing_arms as $lineId => $payload) {
                    $line = StockAdjustmentArm::where('stock_adjustment_id', $stockAdjustment->id)->find($lineId);
                    if (!$line) { continue; }
                    if (isset($payload['_delete']) && (string)$payload['_delete'] === '1') {
                        $line->delete();
                        continue;
                    }
                    $line->update([
                        'arm_id' => $payload['arm_id'] ?? $line->arm_id,
                        'reason' => $payload['reason'] ?? $line->reason,
                        'price' => isset($payload['price']) ? (float)$payload['price'] : $line->price,
                    ]);
                }
            }

            // Add more arm lines in edit (same as create subtraction arm flow without status/ledger changes here)
            if ($request->adjustment_type === 'subtraction' && is_array($request->arm_items)) {
                foreach ($request->arm_items as $armLine) {
                    if (!isset($armLine['arm_id'])) { continue; }
                    StockAdjustmentArm::create([
                        'stock_adjustment_id' => $stockAdjustment->id,
                        'arm_id' => $armLine['arm_id'],
                        'reason' => $armLine['reason'] ?? 'damage',
                        'price' => $armLine['price'] ?? null,
                    ]);
                }
            }

            // Rebuild ledgers for this adjustment
            // NOTE: Per requirement, do not create reversal entries for items on edit; only arm side will be adjusted below.

            // Rebuild item ledgers without reversal entries in history (hard update)
            // a) Remove previous item ledgers for this adjustment and fix batches
            $oldItemLedgers = GeneralItemStockLedger::where('reference_id', $stockAdjustment->id)
                ->where('transaction_type', 'stock_adjustment')
                ->get();
            foreach ($oldItemLedgers as $old) {
                if ($old->batch_id) {
                    $batch = GeneralBatch::find($old->batch_id);
                    if ($batch) {
                        if ($old->quantity_in > 0) {
                            // If batch was created by this adjustment, remove the batch entirely; otherwise, reduce remaining
                            if (is_string($batch->batch_code) && str_starts_with($batch->batch_code, 'ADJ-' . $stockAdjustment->id . '-')) {
                                $batch->delete();
                            } else {
                                $batch->update(['qty_remaining' => max(0, $batch->qty_remaining - $old->quantity_in)]);
                            }
                        }
                        if ($old->quantity_out > 0) {
                            // Previously subtracted from stock -> roll back by increasing remaining
                            $batch->update(['qty_remaining' => $batch->qty_remaining + $old->quantity_out]);
                        }
                    }
                }
                $old->delete();
            }

            // b) Re-apply item ledgers from current children according to adjustment type
            $currentItems = StockAdjustmentItem::where('stock_adjustment_id', $stockAdjustment->id)->get();
            $runningItemBalance = [];
            $itemsToRecalculate = [];
            foreach ($currentItems as $line) {
                $itemId = $line->general_item_id;
                if (!array_key_exists($itemId, $runningItemBalance)) {
                    $runningItemBalance[$itemId] = $this->getLastItemBalance($businessId, $itemId, $stockAdjustment->id);
                    $itemsToRecalculate[] = $itemId;
                }
                if ($stockAdjustment->adjustment_type === 'addition') {
                    $batch = GeneralBatch::create([
                        'business_id' => $businessId,
                        'item_id' => $itemId,
                        'qty_received' => $line->quantity,
                        'qty_remaining' => $line->quantity,
                        'unit_cost' => $line->unit_cost,
                        'total_cost' => $line->total_amount,
                        'received_date' => $stockAdjustment->adjustment_date,
                        'user_id' => Auth::id(),
                        'batch_code' => 'ADJ-' . $stockAdjustment->id . '-' . uniqid(),
                        'status' => 'active',
                    ]);
                    GeneralItemStockLedger::create([
                        'business_id' => $businessId,
                        'general_item_id' => $itemId,
                        'batch_id' => $batch->id,
                        'transaction_date' => $stockAdjustment->adjustment_date,
                        'transaction_type' => 'stock_adjustment',
                        'quantity' => $line->quantity,
                        'quantity_in' => $line->quantity,
                        'quantity_out' => 0,
                        'balance_quantity' => $runningItemBalance[$itemId] + (int)$line->quantity,
                        'unit_cost' => $line->unit_cost,
                        'total_cost' => $line->total_amount,
                        'reference_id' => $stockAdjustment->id,
                        'remarks' => $stockAdjustment->description ?: 'Stock adjustment - Addition',
                        'created_by' => Auth::id(),
                    ]);
                    $runningItemBalance[$itemId] += (int)$line->quantity;
                } else {
                    $fifoAllocation = GeneralItemStockLedger::getFIFOAllocation($itemId, $line->quantity);
                    foreach ($fifoAllocation as $allocation) {
                        GeneralItemStockLedger::create([
                            'business_id' => $businessId,
                            'general_item_id' => $itemId,
                            'batch_id' => $allocation['batch_id'],
                            'transaction_date' => $stockAdjustment->adjustment_date,
                            'transaction_type' => 'stock_adjustment',
                            'quantity' => -$allocation['quantity'],
                            'quantity_in' => 0,
                            'quantity_out' => $allocation['quantity'],
                            'balance_quantity' => max(0, $runningItemBalance[$itemId] - (int)$allocation['quantity']),
                            'unit_cost' => $allocation['unit_cost'],
                            'total_cost' => $allocation['total_cost'],
                            'reference_id' => $stockAdjustment->id,
                            'remarks' => $stockAdjustment->description ?: 'Stock adjustment - Subtraction',
                            'created_by' => Auth::id(),
                        ]);
                        $batch = GeneralBatch::find($allocation['batch_id']);
                        if ($batch) { $batch->update(['qty_remaining' => max(0, $batch->qty_remaining - $allocation['quantity'])]); }
                        $runningItemBalance[$itemId] = max(0, $runningItemBalance[$itemId] - (int)$allocation['quantity']);
                    }
                }
            }

            // Recalculate balances for all affected items to ensure consistency
            foreach (array_unique($itemsToRecalculate) as $itemId) {
                // Get balance right before the adjustment date (exclude this adjustment)
                $balanceBefore = GeneralItemStockLedger::where('business_id', $businessId)
                    ->where('general_item_id', $itemId)
                    ->where(function($q) use ($stockAdjustment) {
                        $q->whereDate('transaction_date', '<', $stockAdjustment->adjustment_date)
                          ->orWhere(function($q2) use ($stockAdjustment) {
                              $q2->whereDate('transaction_date', '=', $stockAdjustment->adjustment_date)
                                 ->where(function($q3) use ($stockAdjustment) {
                                     $q3->where('reference_id', '!=', $stockAdjustment->id)
                                        ->orWhereNull('reference_id');
                                 });
                          });
                    })
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->value('balance_quantity') ?? 0;
                
                // Find all entries for this item on or after the adjustment date
                $entriesToRecalc = GeneralItemStockLedger::where('business_id', $businessId)
                    ->where('general_item_id', $itemId)
                    ->whereDate('transaction_date', '>=', $stockAdjustment->adjustment_date)
                    ->orderBy('transaction_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
                
                $runningBalance = (float)$balanceBefore;
                foreach ($entriesToRecalc as $entry) {
                    $quantityIn = (float) ($entry->quantity_in ?? 0);
                    $quantityOut = (float) ($entry->quantity_out ?? 0);
                    $runningBalance += $quantityIn - $quantityOut;
                    $entry->update(['balance_quantity' => $runningBalance]);
                }
            }

            // 1) Reverse old arm ledgers and restore arms
            $oldArmLedgers = ArmsStockLedger::where('reference_id', $stockAdjustment->id)
                ->whereIn('transaction_type', ['damage', 'theft', 'adjustment'])
                ->get();
            foreach ($oldArmLedgers as $old) {
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $old->arm_id,
                    'transaction_date' => $stockAdjustment->adjustment_date,
                    'transaction_type' => 'reversal',
                    'quantity_in' => 1,
                    'quantity_out' => 0,
                    'balance' => 1,
                    'reference_id' => $stockAdjustment->id,
                    'remarks' => 'Edit reversal for stock adjustment #' . $stockAdjustment->id,
                ]);
                // Restore arm status
                $arm = Arm::find($old->arm_id);
                if ($arm) {
                    $oldValues = $arm->toArray();
                    $arm->update(['status' => 'available']);
                    ArmHistory::create([
                        'business_id' => $businessId,
                        'arm_id' => $arm->id,
                        'action' => 'edit',
                        'old_values' => $oldValues,
                        'new_values' => $arm->fresh()->toArray(),
                        'transaction_date' => $stockAdjustment->adjustment_date,
                        'price' => null,
                        'remarks' => 'ARM restored due to stock adjustment edit',
                        'user_id' => Auth::id(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }

            // 2) Arm re-apply from current children

            $currentArms = StockAdjustmentArm::where('stock_adjustment_id', $stockAdjustment->id)->get();
            foreach ($currentArms as $line) {
                if ($stockAdjustment->adjustment_type !== 'subtraction') { continue; }
                $arm = Arm::forBusiness($businessId)->find($line->arm_id);
                if (!$arm) { continue; }
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $arm->id,
                    'transaction_date' => $stockAdjustment->adjustment_date,
                    'transaction_type' => $line->reason,
                    'quantity_in' => 0,
                    'quantity_out' => 1,
                    'balance' => 0,
                    'reference_id' => $stockAdjustment->id,
                    'remarks' => $stockAdjustment->description ?: ucfirst($line->reason),
                ]);
                $oldValues = $arm->toArray();
                $arm->update(['status' => 'decommissioned']);
                ArmHistory::create([
                    'business_id' => $businessId,
                    'arm_id' => $arm->id,
                    'action' => 'decommission',
                    'old_values' => $oldValues,
                    'new_values' => $arm->fresh()->toArray(),
                    'transaction_date' => $stockAdjustment->adjustment_date,
                    'price' => $line->price ?? null,
                    'remarks' => $stockAdjustment->description ?: '',
                    'user_id' => Auth::id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock adjustment updated successfully.');
    }

    /**
     * Remove the specified stock adjustment
     */
    public function destroy(StockAdjustment $stockAdjustment)
    {
        $businessId = session('active_business');

        DB::transaction(function () use ($stockAdjustment, $businessId) {
            // Load relationships
            $stockAdjustment->load(['itemLines', 'armLines']);
            
            // 1. Reverse item ledgers and fix batches
            $itemLedgers = GeneralItemStockLedger::where('reference_id', $stockAdjustment->id)
                ->where('transaction_type', 'stock_adjustment')
                ->get();
            
            $itemsToRecalculate = [];
            
            foreach ($itemLedgers as $ledger) {
                // Create reversal entry
            GeneralItemStockLedger::create([
                'business_id' => $businessId,
                    'general_item_id' => $ledger->general_item_id,
                    'batch_id' => $ledger->batch_id,
                'transaction_date' => $stockAdjustment->adjustment_date,
                'transaction_type' => 'reversal',
                    'quantity' => -$ledger->quantity,
                    'quantity_in' => $ledger->quantity_out,
                    'quantity_out' => $ledger->quantity_in,
                    'balance_quantity' => 0,
                    'unit_cost' => $ledger->unit_cost,
                    'total_cost' => -$ledger->total_cost,
                'reference_id' => $stockAdjustment->id,
                'remarks' => 'Reverse of deleted stock adjustment #' . $stockAdjustment->id,
                'created_by' => Auth::id(),
            ]);

                // Fix batch quantities
                if ($ledger->batch_id) {
                    $batch = GeneralBatch::find($ledger->batch_id);
                    if ($batch) {
                        if ($ledger->quantity_in > 0) {
                            // Was an addition - remove the batch if it was created by this adjustment
                            if (str_starts_with($batch->batch_code, 'ADJ-' . $stockAdjustment->id . '-')) {
                                $batch->delete();
                            } else {
                                $batch->update(['qty_remaining' => max(0, $batch->qty_remaining - $ledger->quantity_in)]);
                            }
                        }
                        if ($ledger->quantity_out > 0) {
                            // Was a subtraction - restore the quantity
                            $batch->update(['qty_remaining' => $batch->qty_remaining + $ledger->quantity_out]);
                        }
                    }
                }
                
                $itemsToRecalculate[] = $ledger->general_item_id;
            }
            
            // Recalculate balances for affected items
            foreach (array_unique($itemsToRecalculate) as $itemId) {
                GeneralItemStockLedger::recalculateBalances($itemId);
            }
            
            // 2. Reverse arm ledgers and restore arm status
            $armLedgers = ArmsStockLedger::where('reference_id', $stockAdjustment->id)
                ->whereIn('transaction_type', ['damage', 'theft', 'adjustment'])
                ->get();
            
            foreach ($armLedgers as $ledger) {
                // Create reversal entry
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $ledger->arm_id,
                    'transaction_date' => $stockAdjustment->adjustment_date,
                    'transaction_type' => 'reversal',
                    'quantity_in' => 1,
                    'quantity_out' => 0,
                    'balance' => 1,
                    'reference_id' => $stockAdjustment->id,
                    'remarks' => 'Reverse of deleted stock adjustment #' . $stockAdjustment->id,
                ]);
                
                // Restore arm to available status
                $arm = Arm::find($ledger->arm_id);
                if ($arm && $arm->status === 'decommissioned') {
                    $oldValues = $arm->toArray();
                    $arm->update(['status' => 'available']);
                    
                    ArmHistory::create([
                        'business_id' => $businessId,
                        'arm_id' => $arm->id,
                        'action' => 'edit',
                        'old_values' => $oldValues,
                        'new_values' => $arm->fresh()->toArray(),
                        'transaction_date' => now(),
                        'price' => null,
                        'remarks' => 'Restored due to stock adjustment deletion',
                        'user_id' => Auth::id(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
            
            // 3. Delete child records
            $stockAdjustment->itemLines()->delete();
            $stockAdjustment->armLines()->delete();
            
            // 4. Delete the parent adjustment
            $stockAdjustment->delete();
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock adjustment deleted successfully.');
    }
}
