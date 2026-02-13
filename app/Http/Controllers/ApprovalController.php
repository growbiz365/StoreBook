<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\ApprovalArm;
use App\Models\ApprovalGeneralItem;
use App\Models\Party;
use App\Models\GeneralItem;
use App\Models\Arm;
use App\Models\ArmHistory;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceArm;
use App\Models\SaleInvoiceGeneralItem;
use App\Models\GeneralBatch;
use App\Models\GeneralItemStockLedger;
use App\Models\ArmsStockLedger;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Models\PartyLedger;
use App\Models\BankLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    /**
     * Display a listing of approvals.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = Approval::with(['party', 'createdBy', 'arms', 'generalItems'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        if ($request->filled('approval_date')) {
            $query->whereDate('approval_date', $request->approval_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('party', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'approval_date', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $approvals = $query->paginate(15)->withQueryString();

        // Get parties for filter dropdown
        $parties = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        return view('approvals.index', compact('approvals', 'parties'));
    }

    /**
     * Show the form for creating a new approval.
     */
    public function create()
    {
        $businessId = session('active_business');

        $parties = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Add available stock to each general item
        foreach ($generalItems as $item) {
            $item->available_stock = GeneralItemStockLedger::getCurrentBalance($item->id);
        }

        $arms = Arm::where('business_id', $businessId)
            ->where('status', 'available')
            ->orderBy('serial_no')
            ->get();

        return view('approvals.create', compact('parties', 'generalItems', 'arms'));
    }

    /**
     * Store a newly created approval in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'party_id' => 'required|exists:parties,id',
                'approval_date' => 'required|date',
                'notes' => 'nullable|string|max:1000',
                'general_lines' => 'nullable|array',
                'general_lines.*.general_item_id' => 'required_with:general_lines|exists:general_items,id',
                'general_lines.*.qty' => 'required_with:general_lines|numeric|min:0.01',
                'general_lines.*.sale_price' => 'required_with:general_lines|numeric|min:0',
                'arm_lines' => 'nullable|array',
                'arm_lines.*.arm_id' => 'required_with:arm_lines|exists:arms,id',
                'arm_lines.*.sale_price' => 'required_with:arm_lines|numeric|min:0',
            ], [
                'party_id.required' => 'Please select a party/customer.',
                'approval_date.required' => 'Approval date is required.',
                'general_lines.*.general_item_id.required_with' => 'Please select an item.',
                'general_lines.*.qty.required_with' => 'Quantity is required.',
                'general_lines.*.sale_price.required_with' => 'Sale price is required.',
                'arm_lines.*.arm_id.required_with' => 'Please select an arm.',
                'arm_lines.*.sale_price.required_with' => 'Sale price is required.',
            ]);

            // Validate that at least one line exists
            if (empty($request->general_lines) && empty($request->arm_lines)) {
                $validator->errors()->add('lines', 'Please add at least one item or arm.');
            }

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Validate arms are available
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $index => $line) {
                    if (!isset($line['arm_id'])) {
                        return back()->withErrors(['arm_lines' => "Arm line {$index} is missing arm_id."])->withInput();
                    }
                    $arm = Arm::find($line['arm_id']);
                    if (!$arm || $arm->status !== 'available') {
                        return back()->withErrors(['arm_lines' => 'One or more selected arms are not available.'])->withInput();
                    }
                }
            }

            // Create approval
            $approval = Approval::create([
                'business_id' => $businessId,
                'party_id' => $request->party_id,
                'approval_date' => $request->approval_date,
                'notes' => $request->notes,
                'status' => 'open',
                'created_by' => $userId,
            ]);

            // Create general item lines
            if ($request->has('general_lines')) {
                foreach ($request->general_lines as $index => $line) {
                    $lineTotal = $line['qty'] * $line['sale_price'];
                    ApprovalGeneralItem::create([
                        'approval_id' => $approval->id,
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'sale_price' => $line['sale_price'],
                        'line_total' => $lineTotal,
                        'remaining_quantity' => $line['qty'],
                        'returned_quantity' => 0,
                        'sold_quantity' => 0,
                    ]);
                }
            }

            // Create arm lines and update arm status
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $index => $line) {
                    $arm = Arm::find($line['arm_id']);
                    
                    if (!$arm) {
                        throw new \Exception("Arm with ID {$line['arm_id']} not found");
                    }
                    
                    // Create approval arm record
                    ApprovalArm::create([
                        'approval_id' => $approval->id,
                        'arm_id' => $line['arm_id'],
                        'sale_price' => $line['sale_price'],
                        'status' => 'pending',
                    ]);

                    // Update arm status to pending_approval
                    $oldValues = $arm->toArray();
                    $arm->update(['status' => 'pending_approval']);
                    $newValues = $arm->fresh()->toArray();

                    // Create arm history entry
                    ArmHistory::create([
                        'business_id' => $businessId,
                        'arm_id' => $arm->id,
                        'action' => 'approval',
                        'old_values' => $oldValues,
                        'new_values' => $newValues,
                        'transaction_date' => $request->approval_date,
                        'price' => $line['sale_price'],
                        'remarks' => 'Given on approval to ' . $approval->party->name,
                        'user_id' => $userId,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Approval created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to create approval: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified approval.
     */
    public function show(Approval $approval)
    {
        $approval->load(['party', 'createdBy', 'arms.arm', 'generalItems.generalItem', 'generalItems.batch']);
        
        return view('approvals.show', compact('approval'));
    }

    /**
     * Show the form for editing the specified approval.
     */
    public function edit(Approval $approval)
    {
        if ($approval->status === 'closed') {
            return redirect()->route('approvals.show', $approval)
                ->with('error', 'Closed approvals cannot be edited.');
        }

        $businessId = session('active_business');

        $parties = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Add available stock to each general item
        foreach ($generalItems as $item) {
            $item->available_stock = GeneralItemStockLedger::getCurrentBalance($item->id);
        }

        $arms = Arm::where('business_id', $businessId)
            ->whereIn('status', ['available', 'pending_approval'])
            ->orderBy('serial_no')
            ->get();

        $approval->load(['arms.arm', 'generalItems.generalItem']);

        return view('approvals.edit', compact('approval', 'parties', 'generalItems', 'arms'));
    }

    /**
     * Update the specified approval in storage.
     */
    public function update(Request $request, Approval $approval)
    {
        if ($approval->status === 'closed') {
            return redirect()->route('approvals.show', $approval)
                ->with('error', 'Closed approvals cannot be edited.');
        }

        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'party_id' => 'required|exists:parties,id',
                'approval_date' => 'required|date',
                'notes' => 'nullable|string|max:1000',
                'general_lines' => 'nullable|array',
                'general_lines.*.general_item_id' => 'required_with:general_lines|exists:general_items,id',
                'general_lines.*.qty' => 'required_with:general_lines|numeric|min:0.01',
                'general_lines.*.sale_price' => 'required_with:general_lines|numeric|min:0',
                'arm_lines' => 'nullable|array',
                'arm_lines.*.arm_id' => 'required_with:arm_lines|exists:arms,id',
                'arm_lines.*.sale_price' => 'required_with:arm_lines|numeric|min:0',
            ]);

            // Validate that at least one line exists
            if (empty($request->general_lines) && empty($request->arm_lines)) {
                $validator->errors()->add('lines', 'Please add at least one item or arm.');
            }

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Get existing arms to restore status if removed
            $existingArmIds = $approval->arms->pluck('arm_id')->toArray();

            // Update approval header
            $approval->update([
                'party_id' => $request->party_id,
                'approval_date' => $request->approval_date,
                'notes' => $request->notes,
            ]);

            // Delete existing lines
            $approval->generalItems()->delete();
            $approval->arms()->delete();

            // Restore status of removed arms
            foreach ($existingArmIds as $armId) {
                $arm = Arm::find($armId);
                if ($arm && $arm->status === 'pending_approval') {
                    $oldValues = $arm->toArray();
                    $arm->update(['status' => 'available']);
                    $newValues = $arm->fresh()->toArray();

                    ArmHistory::create([
                        'business_id' => $businessId,
                        'arm_id' => $arm->id,
                        'action' => 'return',
                        'old_values' => $oldValues,
                        'new_values' => $newValues,
                        'transaction_date' => now(),
                        'remarks' => 'Removed from approval during edit',
                        'user_id' => $userId,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
            }

            // Create new general item lines
            if ($request->has('general_lines')) {
                foreach ($request->general_lines as $line) {
                    $lineTotal = $line['qty'] * $line['sale_price'];
                    ApprovalGeneralItem::create([
                        'approval_id' => $approval->id,
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'sale_price' => $line['sale_price'],
                        'line_total' => $lineTotal,
                        'remaining_quantity' => $line['qty'],
                        'returned_quantity' => 0,
                        'sold_quantity' => 0,
                    ]);
                }
            }

            // Create new arm lines
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $line) {
                    $arm = Arm::find($line['arm_id']);
                    
                    // Only update status if not already pending_approval
                    if ($arm->status !== 'pending_approval') {
                        $oldValues = $arm->toArray();
                        $arm->update(['status' => 'pending_approval']);
                        $newValues = $arm->fresh()->toArray();

                        ArmHistory::create([
                            'business_id' => $businessId,
                            'arm_id' => $arm->id,
                            'action' => 'approval',
                            'old_values' => $oldValues,
                            'new_values' => $newValues,
                            'transaction_date' => $request->approval_date,
                            'price' => $line['sale_price'],
                            'remarks' => 'Given on approval to ' . $approval->party->name,
                            'user_id' => $userId,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ]);
                    }

                    ApprovalArm::create([
                        'approval_id' => $approval->id,
                        'arm_id' => $line['arm_id'],
                        'sale_price' => $line['sale_price'],
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Approval updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update approval. Please try again.'])->withInput();
        }
    }

    /**
     * Show the process approval view.
     */
    public function process(Approval $approval)
    {
        if ($approval->status === 'closed') {
            return redirect()->route('approvals.show', $approval)
                ->with('error', 'Closed approvals cannot be processed.');
        }

        $approval->load(['party', 'arms.arm', 'generalItems.generalItem']);

        return view('approvals.process', compact('approval'));
    }

    /**
     * Process the approval (return or sale).
     */
    public function processAction(Request $request, Approval $approval)
    {
        if ($approval->status === 'closed') {
            return redirect()->route('approvals.show', $approval)
                ->with('error', 'Closed approvals cannot be processed.');
        }

        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            $validator = Validator::make($request->all(), [
                'action_type' => 'required|in:return,sale',
                'selected_general_items' => 'nullable|array',
                'selected_general_items.*' => 'exists:approval_general_items,id',
                'selected_arms' => 'nullable|array',
                'selected_arms.*' => 'exists:approval_arms,id',
                'general_items' => 'nullable|array',
                'general_items.*.qty' => 'required_with:general_items|numeric|min:0.01',
                'general_items.*.sale_price' => 'required_with:general_items|numeric|min:0',
                'arms' => 'nullable|array',
                'arms.*.sale_price' => 'required_with:arms|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Validate that at least one item is selected
            $selectedGeneralItems = $request->selected_general_items ?? [];
            $selectedArms = $request->selected_arms ?? [];

            if (empty($selectedGeneralItems) && empty($selectedArms)) {
                return back()->withErrors(['selection' => 'Please select at least one item or arm to process.'])->withInput();
            }

            DB::beginTransaction();

            // Get quantities and prices from request
            $generalItemsData = $request->general_items ?? [];
            $armsData = $request->arms ?? [];

            if ($request->action_type === 'return') {
                $this->processReturn($approval, $selectedGeneralItems, $selectedArms, $generalItemsData, $userId, $request);
            } else {
                $this->processSale($approval, $selectedGeneralItems, $selectedArms, $generalItemsData, $armsData, $userId, $request);
            }

            // Check and update approval status
            $approval->refresh();
            $approval->checkAndUpdateStatus();

            DB::commit();

            $message = $request->action_type === 'return'
                ? 'Items/Arms returned successfully.'
                : 'Sale invoice generated successfully.';

            return redirect()->route('approvals.show', $approval)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval process failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to process approval. Please try again.'])->withInput();
        }
    }

    /**
     * Process return action.
     */
    private function processReturn($approval, $selectedGeneralItems, $selectedArms, $generalItemsData, $userId, $request)
    {
        // Process general items return
        foreach ($selectedGeneralItems as $itemId) {
            $item = ApprovalGeneralItem::find($itemId);
            if ($item && $item->approval_id === $approval->id && $item->remaining_quantity > 0) {
                // Get quantity from request, or return all remaining
                $qtyToReturn = $generalItemsData[$itemId]['qty'] ?? $item->remaining_quantity;
                
                // Validate quantity doesn't exceed remaining
                $qtyToReturn = min($qtyToReturn, $item->remaining_quantity);
                
                // Update approval item
                $item->returned_quantity += $qtyToReturn;
                $item->remaining_quantity -= $qtyToReturn;
                $item->save();
            }
        }

        // Process arms return
        foreach ($selectedArms as $armId) {
            $approvalArm = ApprovalArm::find($armId);
            if ($approvalArm && $approvalArm->approval_id === $approval->id && $approvalArm->status === 'pending') {
                $arm = $approvalArm->arm;
                
                // Update approval arm
                $approvalArm->update([
                    'status' => 'returned',
                    'returned_date' => now(),
                ]);

                // Update arm status
                $oldValues = $arm->toArray();
                $arm->update(['status' => 'available']);
                $newValues = $arm->fresh()->toArray();

                // Create arm history entry
                ArmHistory::create([
                    'business_id' => $approval->business_id,
                    'arm_id' => $arm->id,
                    'action' => 'return',
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'transaction_date' => now(),
                    'price' => $approvalArm->sale_price,
                    'remarks' => 'Returned from approval',
                    'user_id' => $userId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }
    }

    /**
     * Process sale action - generate sale invoice.
     */
    private function processSale($approval, $selectedGeneralItems, $selectedArms, $generalItemsData, $armsData, $userId, $request)
    {
        // Create sale invoice
        $saleInvoice = SaleInvoice::create([
            'business_id' => $approval->business_id,
            'party_id' => $approval->party_id,
            'approval_id' => $approval->id,
            'sale_type' => 'credit', // Default to credit, can be changed later
            'invoice_date' => now(),
            'subtotal' => 0,
            'shipping_charges' => 0,
            'total_amount' => 0,
            'status' => 'draft',
            'created_by' => $userId,
        ]);

        $subtotal = 0;

        // Process general items
        foreach ($selectedGeneralItems as $itemId) {
            $approvalItem = ApprovalGeneralItem::find($itemId);
            if ($approvalItem && $approvalItem->approval_id === $approval->id && $approvalItem->remaining_quantity > 0) {
                // Get quantity and price from request, or use defaults
                $qtyToSell = $generalItemsData[$itemId]['qty'] ?? $approvalItem->remaining_quantity;
                $salePrice = $generalItemsData[$itemId]['sale_price'] ?? $approvalItem->sale_price;
                
                // Validate quantity doesn't exceed remaining
                $qtyToSell = min($qtyToSell, $approvalItem->remaining_quantity);
                
                // Create sale invoice line
                SaleInvoiceGeneralItem::create([
                    'sale_invoice_id' => $saleInvoice->id,
                    'general_item_id' => $approvalItem->general_item_id,
                    'quantity' => $qtyToSell,
                    'sale_price' => $salePrice,
                ]);

                $subtotal += $qtyToSell * $salePrice;

                // Update approval item
                $approvalItem->sold_quantity += $qtyToSell;
                $approvalItem->remaining_quantity -= $qtyToSell;
                $approvalItem->save();
            }
        }

        // Process arms
        foreach ($selectedArms as $armId) {
            $approvalArm = ApprovalArm::find($armId);
            if ($approvalArm && $approvalArm->approval_id === $approval->id && $approvalArm->status === 'pending') {
                // Get price from request, or use default
                $salePrice = $armsData[$armId]['sale_price'] ?? $approvalArm->sale_price;
                
                // Create sale invoice line
                SaleInvoiceArm::create([
                    'sale_invoice_id' => $saleInvoice->id,
                    'arm_id' => $approvalArm->arm_id,
                    'sale_price' => $salePrice,
                ]);

                $subtotal += $salePrice;

                // Update approval arm
                $approvalArm->update([
                    'status' => 'sold',
                    'sold_date' => now(),
                    'sale_invoice_id' => $saleInvoice->id,
                ]);
            }
        }

        // Update sale invoice totals
        $saleInvoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal,
        ]);

        // Post the sale invoice (this will deduct stock, create accounting entries, etc.)
        $this->postSaleInvoice($saleInvoice);
    }

    /**
     * Post sale invoice (reused from SaleInvoiceController logic).
     */
    private function postSaleInvoice(SaleInvoice $saleInvoice)
    {
        $businessId = $saleInvoice->business_id;
        $userId = auth()->id();

        // Update status
        $saleInvoice->update([
            'status' => 'posted',
            'posted_by' => $userId
        ]);

        // Create stock ledger entries for general items
        foreach ($saleInvoice->generalLines as $line) {
            $batches = GeneralBatch::where('item_id', $line->general_item_id)
                ->where('qty_remaining', '>', 0)
                ->orderBy('created_at')
                ->get();

            if ($batches->isEmpty()) {
                continue;
            }

            $remainingQty = $line->quantity;

            foreach ($batches as $batch) {
                if ($remainingQty <= 0) break;

                $qtyToConsume = min($remainingQty, $batch->qty_remaining);

                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $line->general_item_id,
                    'batch_id' => $batch->id,
                    'transaction_type' => 'sale',
                    'transaction_date' => $saleInvoice->invoice_date,
                    'quantity' => -$qtyToConsume,
                    'quantity_out' => $qtyToConsume,
                    'balance_quantity' => $batch->qty_remaining - $qtyToConsume,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $qtyToConsume * $batch->unit_cost,
                    'reference_id' => $saleInvoice->id,
                    'reference_no' => $saleInvoice->invoice_number,
                    'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer'),
                    'created_by' => $userId,
                ]);

                $batch->update(['qty_remaining' => $batch->qty_remaining - $qtyToConsume]);
                $remainingQty -= $qtyToConsume;
            }

            GeneralItemStockLedger::recalculateBalances($line->general_item_id);
        }

        // Create stock ledger entries for arms
        foreach ($saleInvoice->armLines as $line) {
            $arm = $line->arm;
            $oldValues = $arm->toArray();
            $oldSalePrice = $arm->sale_price;

            // Create arms stock ledger entry
            ArmsStockLedger::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'transaction_date' => $saleInvoice->invoice_date,
                'transaction_type' => 'sale',
                'quantity_out' => 1,
                'balance' => 0,
                'reference_id' => $saleInvoice->invoice_number,
                'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer'),
            ]);

            // Update arm status
            $arm->update([
                'status' => 'sold',
                'sold_date' => $saleInvoice->invoice_date,
                'sale_price' => $line->sale_price,
            ]);

            // Create arm history entry
            ArmHistory::create([
                'business_id' => $businessId,
                'arm_id' => $arm->id,
                'action' => 'sale',
                'old_values' => array_merge($oldValues, ['sale_price' => $oldSalePrice]),
                'new_values' => $arm->fresh()->toArray(),
                'transaction_date' => $saleInvoice->invoice_date,
                'price' => $line->sale_price,
                'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer'),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Create party ledger entry
        if ($saleInvoice->sale_type === 'credit' && $saleInvoice->party_id) {
            PartyLedger::create([
                'business_id' => $saleInvoice->business_id,
                'party_id' => $saleInvoice->party_id,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'Sale Invoice',
                'date_added' => $saleInvoice->invoice_date,
                'user_id' => $userId,
                'debit_amount' => $saleInvoice->total_amount,
                'credit_amount' => 0,
            ]);
        }

        // Create journal entries
        $this->createJournalEntries($saleInvoice);
    }

    /**
     * Create journal entries for sale invoice.
     */
    private function createJournalEntries(SaleInvoice $saleInvoice)
    {
        $businessId = $saleInvoice->business_id;
        $userId = auth()->id();

        // Load relationships to ensure arm lines are available
        $saleInvoice->load(['generalLines', 'armLines.arm']);

        // Get party's chart of account for credit sales (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($saleInvoice->sale_type === 'credit' && $saleInvoice->party_id) {
            $party = \App\Models\Party::find($saleInvoice->party_id);
            if ($party) {
                // If party doesn't have a chart of account, create one
                if (!$party->chart_of_account_id) {
                    $partyAccount = ChartOfAccount::createPartyAccount($party->name, $businessId);
                    $party->update(['chart_of_account_id' => $partyAccount->id]);
                    $party->refresh();
                }
                $partyAccountId = $party->chart_of_account_id;
                
                if (!$partyAccountId) {
                    throw new \Exception('Failed to create or retrieve party chart of account for credit sale (approval).');
                }
            } else {
                throw new \Exception('Party not found for credit sale (approval).');
            }
        }

        $salesRevenueId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Sales%')
            ->orWhere('name', 'like', '%Revenue%')
            ->orWhere('name', 'like', '%Income%')
            ->value('id');

        $cogsId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Cost of Goods%')
            ->orWhere('name', 'like', '%COGS%')
            ->value('id');

        $inventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Inventory%')
            ->value('id');

        if (!$salesRevenueId || !$cogsId || !$inventoryId) {
            return;
        }

        // Entry 1: Debit Party Account (for credit sales) / Credit Sales Revenue
        if ($saleInvoice->sale_type === 'credit') {
            // Credit sale - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit sales (approval).');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => $saleInvoice->total_amount,
                'credit_amount' => 0,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number,
                'user_id' => $userId,
                'date_added' => $saleInvoice->invoice_date,
            ]);
        }

        // Entry 2: Credit Sales Revenue
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $salesRevenueId,
            'debit_amount' => 0,
            'credit_amount' => $saleInvoice->total_amount,
            'voucher_id' => $saleInvoice->id,
            'voucher_type' => 'SaleInvoice',
            'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number,
            'user_id' => $userId,
            'date_added' => $saleInvoice->invoice_date,
        ]);

        // Calculate COGS
        $totalCogs = 0;

        // COGS for general items
        foreach ($saleInvoice->generalLines as $line) {
            $stockEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                ->where('reference_no', $saleInvoice->invoice_number)
                ->where('transaction_type', 'sale')
                ->where('quantity', '<', 0)
                ->get();

            foreach ($stockEntries as $entry) {
                $totalCogs += abs($entry->total_cost);
            }
        }

        // COGS for arms - use the same inventory account
        foreach ($saleInvoice->armLines as $line) {
            $cogsAmount = $line->arm->purchase_price ?? 0;
            $totalCogs += $cogsAmount;
        }

        // Create COGS and Inventory entries
        if ($totalCogs > 0) {
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $cogsId,
                'debit_amount' => $totalCogs,
                'credit_amount' => 0,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Cost of Goods Sold',
                'user_id' => $userId,
                'date_added' => $saleInvoice->invoice_date,
            ]);

            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $inventoryId,
                'debit_amount' => 0,
                'credit_amount' => $totalCogs,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Inventory',
                'user_id' => $userId,
                'date_added' => $saleInvoice->invoice_date,
            ]);
        }
    }

    /**
     * Remove the specified approval from storage.
     */
    public function destroy(Approval $approval)
    {
        try {
            DB::beginTransaction();

            // Restore arm statuses
            foreach ($approval->arms as $approvalArm) {
                if ($approvalArm->status === 'pending') {
                    $arm = $approvalArm->arm;
                    if ($arm && $arm->status === 'pending_approval') {
                        $arm->update(['status' => 'available']);
                    }
                }
            }

            $approval->delete();

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Approval deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete approval. Please try again.']);
        }
    }

    /**
     * Display the approvals report.
     */
    public function report(Request $request)
    {
        $businessId = session('active_business');
        $business = \App\Models\Business::find($businessId);

        // Get filter values with defaults for current month
        $partyId = $request->get('party_id');
        $status = $request->get('status');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        // Build query
        $query = Approval::with(['party', 'createdBy', 'arms.arm', 'generalItems.generalItem'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($partyId) {
            $query->where('party_id', $partyId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($fromDate) {
            $query->whereDate('approval_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('approval_date', '<=', $toDate);
        }

        // Order by approval date descending
        $approvals = $query->orderBy('approval_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Calculate summary statistics
        $totalApprovals = $approvals->count();
        $totalApprovedValue = $approvals->sum('total_approved_value');
        $totalSoldValue = $approvals->sum('total_sold_value');
        $totalReturnedValue = $approvals->sum('total_returned_value');
        $totalRemainingValue = $approvals->sum('remaining_value');
        $openApprovals = $approvals->where('status', 'open')->count();
        $closedApprovals = $approvals->where('status', 'closed')->count();
        $pendingApprovals = $approvals->where('status', 'pending approval')->count();

        // Get parties for filter dropdown
        $parties = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        return view('approvals.report', compact(
            'approvals',
            'parties',
            'business',
            'partyId',
            'status',
            'fromDate',
            'toDate',
            'totalApprovals',
            'totalApprovedValue',
            'totalSoldValue',
            'totalReturnedValue',
            'totalRemainingValue',
            'openApprovals',
            'closedApprovals',
            'pendingApprovals'
        ));
    }
}
