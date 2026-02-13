<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Party;
use App\Models\Bank;
use App\Models\GeneralItem;
use App\Models\Arm;
use App\Models\SaleInvoice;
use App\Models\GeneralBatch;
use App\Models\GeneralItemStockLedger;
use App\Models\ArmsStockLedger;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Models\ArmHistory;
use App\Models\SaleInvoiceAuditLog;
use App\Models\PartyLedger;
use App\Models\BankLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    /**
     * Display a listing of quotations.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = Quotation::with(['party', 'bank', 'createdBy', 'generalLines', 'armLines', 'convertedToSale'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('party_id', $request->customer);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('quotation_date', [$request->date_from, $request->date_to]);
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

        if (in_array($sortBy, ['id', 'quotation_date', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $quotations = $query->paginate(15)->withQueryString();

        // Get customers for filter dropdown
        $customers = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        return view('quotations.index', compact('quotations', 'customers'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create()
    {
        $businessId = session('active_business');

        $customers = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->orderBy('account_name')
            ->get();

        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Add available stock to each general item
        foreach ($generalItems as $item) {
            $item->available_stock = GeneralItemStockLedger::getCurrentBalance($item->id);
        }

        // Arms data loading disabled - StoreBook is items-only
        // $arms = Arm::where('business_id', $businessId)
        //     ->where('status', 'available')
        //     ->orderBy('serial_no')
        //     ->get();

        // Empty collection for arms data to prevent errors in views
        $arms = collect();

        return view('quotations.create', compact('customers', 'banks', 'generalItems', 'arms'));
    }

    /**
     * Store a newly created quotation in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            DB::beginTransaction();

            // Validate main quotation data
            $validator = Validator::make($request->all(), [
                'party_id' => 'required|exists:parties,id',
                'payment_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:payment_type,cash|exists:banks,id',
                'quotation_date' => 'required|date',
                'valid_until' => 'required|date|after_or_equal:quotation_date',
                'shipping_charges' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',

                // General lines validation
                'general_lines' => 'nullable|array',
                'general_lines.*.general_item_id' => 'required_with:general_lines|exists:general_items,id',
                'general_lines.*.qty' => 'required_with:general_lines|numeric|min:0.01',
                'general_lines.*.sale_price' => 'required_with:general_lines|numeric|min:0',

                // Arm lines validation
                'arm_lines' => 'nullable|array',
                'arm_lines.*.sale_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.arm_id' => 'required_with:arm_lines|exists:arms,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Validate at least one line item
            if ((!$request->has('general_lines') || empty($request->general_lines)) && 
                (!$request->has('arm_lines') || empty($request->arm_lines))) {
                return back()->withErrors(['lines' => 'Please add at least one item or arm to the quotation.'])->withInput();
            }

            // Create quotation
            $quotation = Quotation::create([
                'business_id' => $businessId,
                'party_id' => $request->party_id,
                'payment_type' => $request->payment_type,
                'bank_id' => $request->bank_id,
                'quotation_date' => $request->quotation_date,
                'valid_until' => $request->valid_until,
                'shipping_charges' => $request->shipping_charges ?? 0,
                'status' => 'sent', // Default status
                'created_by' => $userId,
                'notes' => $request->notes,
            ]);

            // Create general lines
            if ($request->has('general_lines')) {
                foreach ($request->general_lines as $line) {
                    $quotation->generalLines()->create([
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'sale_price' => $line['sale_price'],
                    ]);
                }
            }

            // Create arm lines
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $line) {
                    $quotation->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'sale_price' => $line['sale_price'],
                    ]);
                }
            }

            // Calculate totals
            $quotation->calculateTotals();
            $quotation->save();

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating quotation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create quotation: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified quotation.
     */
    public function show(Quotation $quotation)
    {
        // Check if user has access to this quotation's business
        // Super Admin can access all businesses
        if (!auth()->user()->hasRole('Super Admin')) {
            if ($quotation->business_id !== session('active_business')) {
                abort(403, 'Unauthorized access to quotation.');
            }
        }

        $quotation->load(['party', 'bank', 'createdBy', 'rejectedBy', 'generalLines.generalItem', 'armLines.arm', 'convertedToSale']);

        return view('quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified quotation.
     */
    public function edit(Quotation $quotation)
    {
        // Check if user has access to this quotation's business
        // Super Admin can access all businesses
        if (!auth()->user()->hasRole('Super Admin')) {
            if ($quotation->business_id !== session('active_business')) {
                abort(403, 'Unauthorized access to quotation.');
            }
        }

        // Check if quotation can be edited
        if (!$quotation->canBeEdited()) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This quotation cannot be edited.');
        }

        $businessId = session('active_business');

        $customers = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1)
            ->orderBy('account_name')
            ->get();

        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Add available stock to each general item
        foreach ($generalItems as $item) {
            $item->available_stock = GeneralItemStockLedger::getCurrentBalance($item->id);
        }

        // Arms data loading disabled - StoreBook is items-only
        // $arms = Arm::where('business_id', $businessId)
        //     ->where('status', 'available')
        //     ->orderBy('serial_no')
        //     ->get();

        // Empty collection for arms data to prevent errors in views
        $arms = collect();

        $quotation->load(['generalLines.generalItem', 'armLines.arm']);

        return view('quotations.edit', compact('quotation', 'customers', 'banks', 'generalItems', 'arms'));
    }

    /**
     * Update the specified quotation in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Check if user has access to this quotation's business
        // Super Admin can access all businesses
        if (!auth()->user()->hasRole('Super Admin')) {
            if ($quotation->business_id !== session('active_business')) {
                abort(403, 'Unauthorized access to quotation.');
            }
        }

        // Check if quotation can be edited
        if (!$quotation->canBeEdited()) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This quotation cannot be edited.');
        }

        try {
            DB::beginTransaction();

            // Validate main quotation data
            $validator = Validator::make($request->all(), [
                'party_id' => 'required|exists:parties,id',
                'payment_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:payment_type,cash|exists:banks,id',
                'quotation_date' => 'required|date',
                'valid_until' => 'required|date|after_or_equal:quotation_date',
                'shipping_charges' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',

                // General lines validation
                'general_lines' => 'nullable|array',
                'general_lines.*.general_item_id' => 'required_with:general_lines|exists:general_items,id',
                'general_lines.*.qty' => 'required_with:general_lines|numeric|min:0.01',
                'general_lines.*.sale_price' => 'required_with:general_lines|numeric|min:0',

                // Arm lines validation
                'arm_lines' => 'nullable|array',
                'arm_lines.*.sale_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.arm_id' => 'required_with:arm_lines|exists:arms,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Validate at least one line item
            if ((!$request->has('general_lines') || empty($request->general_lines)) && 
                (!$request->has('arm_lines') || empty($request->arm_lines))) {
                return back()->withErrors(['lines' => 'Please add at least one item or arm to the quotation.'])->withInput();
            }

            // Update quotation
            $quotation->update([
                'party_id' => $request->party_id,
                'payment_type' => $request->payment_type,
                'bank_id' => $request->bank_id,
                'quotation_date' => $request->quotation_date,
                'valid_until' => $request->valid_until,
                'shipping_charges' => $request->shipping_charges ?? 0,
                'notes' => $request->notes,
            ]);

            // Delete existing lines
            $quotation->generalLines()->delete();
            $quotation->armLines()->delete();

            // Create general lines
            if ($request->has('general_lines')) {
                foreach ($request->general_lines as $line) {
                    $quotation->generalLines()->create([
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'sale_price' => $line['sale_price'],
                    ]);
                }
            }

            // Create arm lines
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $line) {
                    $quotation->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'sale_price' => $line['sale_price'],
                    ]);
                }
            }

            // Calculate totals
            $quotation->calculateTotals();
            $quotation->save();

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quotation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update quotation: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified quotation from storage.
     */
    public function destroy(Quotation $quotation)
    {
        // Check if user has access to this quotation's business
        // Super Admin can access all businesses
        if (!auth()->user()->hasRole('Super Admin')) {
            if ($quotation->business_id !== session('active_business')) {
                abort(403, 'Unauthorized access to quotation.');
            }
        }

        // Check if quotation can be deleted
        if (!$quotation->canBeDeleted()) {
            return redirect()->route('quotations.index')
                ->with('error', 'This quotation cannot be deleted because it has been converted to a sale invoice.');
        }

        try {
            DB::beginTransaction();

            // Soft delete line items
            $quotation->generalLines()->delete();
            $quotation->armLines()->delete();

            // Soft delete quotation
            $quotation->delete();

            DB::commit();

            return redirect()->route('quotations.index')
                ->with('success', 'Quotation deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quotation: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete quotation: ' . $e->getMessage());
        }
    }

    /**
     * Convert quotation to sale invoice (DIRECTLY POSTED).
     */
    public function convertToSale(Quotation $quotation)
    {
        // Check if user has access to this quotation's business
        // Super Admin can access all businesses
        if (!auth()->user()->hasRole('Super Admin')) {
            if ($quotation->business_id !== session('active_business')) {
                abort(403, 'Unauthorized access to quotation.');
            }
        }

        // Check if quotation can be converted
        if (!$quotation->canBeConverted()) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This quotation cannot be converted. It may be expired, rejected, or already converted.');
        }

        try {
            DB::beginTransaction();

            $businessId = $quotation->business_id;
            $userId = auth()->id();

            // Load quotation lines
            $quotation->load(['generalLines.generalItem', 'armLines.arm', 'party', 'bank']);

            // Create sale invoice with status 'posted'
            $saleInvoice = SaleInvoice::create([
                'business_id' => $businessId,
                'party_id' => $quotation->party_id,
                'quotation_id' => $quotation->id, // Link to quotation
                'sale_type' => $quotation->payment_type,
                'bank_id' => $quotation->bank_id,
                'invoice_date' => today(), // Use today's date for invoice
                'shipping_charges' => $quotation->shipping_charges,
                'subtotal' => $quotation->subtotal,
                'total_amount' => $quotation->total_amount,
                'status' => 'posted', // Directly posted
                'created_by' => $userId,
                'posted_by' => $userId,
            ]);

            // Create general line items
            foreach ($quotation->generalLines as $line) {
                $saleInvoice->generalLines()->create([
                    'general_item_id' => $line->general_item_id,
                    'quantity' => $line->quantity,
                    'sale_price' => $line->sale_price,
                ]);
            }

            // Create arm line items
            foreach ($quotation->armLines as $line) {
                $saleInvoice->armLines()->create([
                    'arm_id' => $line->arm_id,
                    'sale_price' => $line->sale_price,
                ]);
            }

            // Post the sale invoice (create stock ledger, journal entries, etc.)
            $this->postSaleInvoice($saleInvoice);

            // Update quotation status
            $quotation->update([
                'status' => 'converted',
                'converted_to_sale_id' => $saleInvoice->id,
            ]);

            DB::commit();

            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('success', 'Quotation converted to sale invoice successfully and posted.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting quotation to sale: ' . $e->getMessage());
            return back()->with('error', 'Failed to convert quotation to sale: ' . $e->getMessage());
        }
    }

    /**
     * Reject the specified quotation.
     */
    public function reject(Request $request, Quotation $quotation)
    {
        // Check if user has access to this quotation's business
        // Super Admin can access all businesses
        if (!auth()->user()->hasRole('Super Admin')) {
            if ($quotation->business_id !== session('active_business')) {
                abort(403, 'Unauthorized access to quotation.');
            }
        }

        // Check if quotation can be rejected
        if (!$quotation->canBeRejected()) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This quotation cannot be rejected.');
        }

        try {
            $quotation->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => auth()->id(),
                'rejected_reason' => $request->rejected_reason,
            ]);

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation rejected successfully.');

        } catch (\Exception $e) {
            Log::error('Error rejecting quotation: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject quotation: ' . $e->getMessage());
        }
    }

    /**
     * Expire the specified quotation.
     */
    public function expire(Quotation $quotation)
    {
        // Check if user has access to this quotation's business
        // Super Admin can access all businesses
        if (!auth()->user()->hasRole('Super Admin')) {
            if ($quotation->business_id !== session('active_business')) {
                abort(403, 'Unauthorized access to quotation.');
            }
        }

        // Check if quotation can be expired
        if (!$quotation->canBeExpired() && !$quotation->isSent()) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This quotation cannot be expired.');
        }

        try {
            $quotation->update([
                'status' => 'expired',
            ]);

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation marked as expired.');

        } catch (\Exception $e) {
            Log::error('Error expiring quotation: ' . $e->getMessage());
            return back()->with('error', 'Failed to expire quotation: ' . $e->getMessage());
        }
    }

    /**
     * Post sale invoice and create all related entries (copied from SaleInvoiceController).
     */
    private function postSaleInvoice(SaleInvoice $saleInvoice)
    {
        try {
            $businessId = $saleInvoice->business_id;
            $userId = auth()->id();

            // Load fresh relationships
            $saleInvoice->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm']);

            // Create stock ledger entries for general items
            foreach ($saleInvoice->generalLines as $line) {
                // Get available batches for FIFO consumption
                $batches = GeneralBatch::where('item_id', $line->general_item_id)
                    ->where('qty_remaining', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                if ($batches->isEmpty()) {
                    Log::warning('No available batches for general item in sale invoice', [
                        'sale_invoice_id' => $saleInvoice->id,
                        'general_item_id' => $line->general_item_id,
                        'quantity' => $line->quantity
                    ]);
                    continue;
                }

                $remainingQty = $line->quantity;
                $consumedBatches = [];

                foreach ($batches as $batch) {
                    if ($remainingQty <= 0) break;

                    $qtyToConsume = min($remainingQty, $batch->qty_remaining);

                    // Create stock ledger entry
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
                        'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer') . ' (from quotation)',
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
            foreach ($saleInvoice->armLines as $line) {
                $oldValues = $line->arm->toArray();
                $oldSalePrice = $line->arm->sale_price;

                // Create arms stock ledger entry
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $line->arm_id,
                    'transaction_date' => $saleInvoice->invoice_date,
                    'transaction_type' => 'sale',
                    'quantity_out' => 1,
                    'balance' => 0,
                    'reference_id' => $saleInvoice->invoice_number,
                    'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer') . ' (from quotation)',
                ]);

                // Update arm status and sale price
                $line->arm->update([
                    'status' => 'sold',
                    'sold_date' => $saleInvoice->invoice_date,
                    'sale_price' => $line->sale_price,
                ]);

                // Create arm history entry
                ArmHistory::create([
                    'business_id' => $businessId,
                    'arm_id' => $line->arm_id,
                    'action' => 'sale',
                    'old_values' => array_merge($oldValues, ['sale_price' => $oldSalePrice]),
                    'new_values' => $line->arm->fresh()->toArray(),
                    'transaction_date' => $saleInvoice->invoice_date,
                    'price' => $line->sale_price,
                    'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer') . ' (from quotation)',
                    'user_id' => $userId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            // Create party ledger entry for credit sales
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

            // Create bank ledger entry for cash sales
            if ($saleInvoice->sale_type === 'cash' && $saleInvoice->bank_id) {
                BankLedger::create([
                    'business_id' => $saleInvoice->business_id,
                    'bank_id' => $saleInvoice->bank_id,
                    'voucher_id' => $saleInvoice->id,
                    'voucher_type' => 'Sale Invoice',
                    'date' => $saleInvoice->invoice_date,
                    'user_id' => $userId,
                    'deposit_amount' => $saleInvoice->total_amount,
                    'withdrawal_amount' => 0,
                ]);
            }

            // Create journal entries
            $this->createJournalEntries($saleInvoice);

            // Create audit log
            SaleInvoiceAuditLog::create([
                'sale_invoice_id' => $saleInvoice->id,
                'action' => 'posted',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'posted'],
                'user_id' => $userId,
            ]);

        } catch (\Exception $e) {
            Log::error('Error posting sale invoice from quotation: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create journal entries for the sale invoice.
     */
    private function createJournalEntries(SaleInvoice $saleInvoice)
    {
        $businessId = $saleInvoice->business_id;
        $userId = auth()->id();

        // Load bank relationship if not already loaded
        if (!$saleInvoice->relationLoaded('bank')) {
            $saleInvoice->load('bank.chartOfAccount');
        }

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
                    throw new \Exception('Failed to create or retrieve party chart of account for quotation conversion.');
                }
            } else {
                throw new \Exception('Party not found for quotation conversion.');
            }
        }

        $salesRevenueId = ChartOfAccount::where('business_id', $businessId)
            ->where(function ($query) {
                $query->where('name', 'like', '%Sales%')
                    ->orWhere('name', 'like', '%Revenue%')
                    ->orWhere('name', 'like', '%Income%');
            })
            ->value('id');

        $cogsId = ChartOfAccount::where('business_id', $businessId)
            ->where(function ($query) {
                $query->where('name', 'like', '%Cost of Goods%')
                    ->orWhere('name', 'like', '%COGS%');
            })
            ->value('id');

        $inventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Inventory%')
            ->value('id');

        // If any required account is missing, skip journal entries
        if (!$salesRevenueId || !$cogsId || !$inventoryId) {
            Log::warning('Missing required chart of accounts for sale invoice posting');
            return;
        }

        // Entry 1: Debit Party Account (for credit sales) / Debit Bank (for cash sales) / Credit Sales Revenue
        if ($saleInvoice->sale_type === 'credit') {
            // Credit sale - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for quotation conversion to credit sale.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => $saleInvoice->total_amount,
                'credit_amount' => 0,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number . ' (from quotation)',
                'user_id' => $userId,
                'date_added' => $saleInvoice->invoice_date,
            ]);
        } else {
            // Cash sale - debit bank account
            if ($saleInvoice->bank && $saleInvoice->bank->chartOfAccount) {
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $saleInvoice->bank->chartOfAccount->id,
                    'debit_amount' => $saleInvoice->total_amount,
                    'credit_amount' => 0,
                    'voucher_id' => $saleInvoice->id,
                    'voucher_type' => 'SaleInvoice',
                    'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number . ' - ' . $saleInvoice->bank->account_name . ' (from quotation)',
                    'user_id' => $userId,
                    'date_added' => $saleInvoice->invoice_date,
                ]);
            }
        }

        // Entry 2: Credit Sales Revenue
        JournalEntry::create([
            'business_id' => $businessId,
            'account_head' => $salesRevenueId,
            'debit_amount' => 0,
            'credit_amount' => $saleInvoice->total_amount,
            'voucher_id' => $saleInvoice->id,
            'voucher_type' => 'SaleInvoice',
            'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number . ' (from quotation)',
            'user_id' => $userId,
            'date_added' => $saleInvoice->invoice_date,
        ]);

        // Calculate total COGS
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

        // COGS for arms
        foreach ($saleInvoice->armLines as $line) {
            $cogsAmount = $line->arm->purchase_price ?? 0;
            $totalCogs += $cogsAmount;
        }

        // Create COGS and Inventory journal entries
        if ($totalCogs > 0) {
            // Debit COGS
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $cogsId,
                'debit_amount' => $totalCogs,
                'credit_amount' => 0,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Cost of Goods Sold (from quotation)',
                'user_id' => $userId,
                'date_added' => $saleInvoice->invoice_date,
            ]);

            // Credit Inventory
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $inventoryId,
                'debit_amount' => 0,
                'credit_amount' => $totalCogs,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Inventory (from quotation)',
                'user_id' => $userId,
                'date_added' => $saleInvoice->invoice_date,
            ]);
        }
    }
}

