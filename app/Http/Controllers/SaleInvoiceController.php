<?php

namespace App\Http\Controllers;

use App\Models\SaleInvoice;
use App\Models\Party;
use App\Models\Bank;
use App\Models\GeneralItem;
use App\Models\Arm;
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

class SaleInvoiceController extends Controller
{
    /**
     * Display the sales dashboard.
     */
    public function dashboard()
    {
        $businessId = session('active_business');

        // Get sale statistics
        $saleStats = [
            'total_sales' => SaleInvoice::where('business_id', $businessId)->count(),
            'today_sales' => SaleInvoice::where('business_id', $businessId)->whereDate('created_at', today())->count(),
            'this_week_sales' => SaleInvoice::where('business_id', $businessId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_sales' => SaleInvoice::where('business_id', $businessId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        // Get value statistics
        $valueStats = [
            'total_value' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->sum('total_amount'),
            'this_month_value' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
            'highest_value' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->max('total_amount') ?? 0,
        ];

        // Get status statistics
        $statusStats = [
            'posted' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->count(),
            'draft' => SaleInvoice::where('business_id', $businessId)->where('status', 'draft')->count(),
            'cancelled' => SaleInvoice::where('business_id', $businessId)->where('status', 'cancelled')->count(),
        ];

        // Get payment type statistics
        $paymentTypeStats = [
            'cash' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->where('sale_type', 'cash')->count(),
            'credit' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->where('sale_type', 'credit')->count(),
        ];

        // Get recent activities
        $recentActivities = [
            'recent_sales' => SaleInvoice::where('business_id', $businessId)
                ->with(['party', 'bank'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return view('sale_invoices.dashboard', compact(
            'saleStats',
            'valueStats',
            'statusStats',
            'paymentTypeStats',
            'recentActivities'
        ));
    }

    /**
     * Display a listing of sale invoices.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = SaleInvoice::with(['party', 'bank', 'createdBy', 'generalLines', 'armLines', 'quotation'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('party_id', $request->customer);
        }

        if ($request->filled('sale_type')) {
            $query->where('sale_type', $request->sale_type);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('invoice_date', [$request->date_from, $request->date_to]);
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

        if (in_array($sortBy, ['id', 'invoice_date', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $saleInvoices = $query->paginate(15)->withQueryString();

        // Get customers for filter dropdown
        $customers = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        return view('sale_invoices.index', compact('saleInvoices', 'customers'));
    }

    /**
     * Show the form for creating a new sale invoice.
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

        $arms = Arm::where('business_id', $businessId)
            ->where('status', 'available')
            ->orderBy('serial_no')
            ->get();

        return view('sale_invoices.create', compact('customers', 'banks', 'generalItems', 'arms'));
    }

    /**
     * Store a newly created sale invoice in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            // Validate stock availability before processing
            if ($request->has('general_lines')) {
                $stockErrors = $this->validateStockAvailability($request->general_lines, $businessId);
                if (!empty($stockErrors)) {
                    return back()->withErrors(['stock' => $stockErrors])->withInput();
                }
            }
            
            DB::beginTransaction();

            // Validate main sale invoice data
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:sale_type,credit|exists:parties,id',
                'sale_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:sale_type,cash|exists:banks,id',
                'invoice_date' => 'required|date',
                'shipping_charges' => 'nullable|numeric|min:0',
                'action' => 'required|in:save,post',

                // Customer details validation (for cash sales)
                'name_of_customer' => 'nullable|string|max:255',
                'father_name' => 'nullable|string|max:255',
                'contact' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:1000',
                'cnic' => 'nullable|string|max:20',
                'licence_no' => 'nullable|string|max:255',
                'licence_issue_date' => 'nullable|date',
                'licence_valid_upto' => 'nullable|date',
                'licence_issued_by' => 'nullable|string|max:255',
                're_reg_no' => 'nullable|string|max:255',
                'dc' => 'nullable|string|max:255',
                'Date' => 'nullable|date',

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

            // Create sale invoice
            $saleInvoice = SaleInvoice::create([
                'business_id' => $businessId,
                'party_id' => $request->party_id ?: null, // Handle null for cash sales
                'sale_type' => $request->sale_type,
                'bank_id' => $request->bank_id,
                'invoice_date' => $request->invoice_date,
                'shipping_charges' => $request->shipping_charges ?? 0,
                'status' => 'draft',
                'created_by' => $userId,
                'name_of_customer' => $request->name_of_customer,
                'father_name' => $request->father_name,
                'contact' => $request->contact,
                'address' => $request->address,
                'cnic' => $request->cnic,
                'licence_no' => $request->licence_no,
                'licence_issue_date' => $request->licence_issue_date,
                'licence_valid_upto' => $request->licence_valid_upto,
                'licence_issued_by' => $request->licence_issued_by,
                're_reg_no' => $request->re_reg_no,
                'dc' => $request->dc,
                'Date' => $request->Date,
                // Party license details
                'party_license_no' => $request->party_license_no,
                'party_license_issue_date' => $request->party_license_issue_date,
                'party_license_valid_upto' => $request->party_license_valid_upto,
                'party_license_issued_by' => $request->party_license_issued_by,
                'party_re_reg_no' => $request->party_re_reg_no,
                'party_dc' => $request->party_dc,
                'party_dc_date' => $request->party_dc_date,
            ]);

            // Create general lines
            if ($request->has('general_lines')) {
                foreach ($request->general_lines as $line) {
                    $saleInvoice->generalLines()->create([
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'sale_price' => $line['sale_price'],
                    ]);
                }
            }

            // Create arm lines
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $line) {
                    $saleInvoice->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'sale_price' => $line['sale_price'],
                    ]);
                }
            }

            // Calculate totals
            $saleInvoice->calculateTotals();
            $saleInvoice->save();

            // Create audit log
            SaleInvoiceAuditLog::create([
                'sale_invoice_id' => $saleInvoice->id,
                'action' => 'created',
                'new_values' => $saleInvoice->toArray(),
                'user_id' => $userId,
            ]);

            // If action is post, post the sale invoice
            if ($request->action === 'post') {
                $this->postSaleInvoice($saleInvoice);
            }

            DB::commit();

            // Clear any old input data from session
            $request->session()->forget('_old_input');

            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('success', 'Sale invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create sale invoice. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified sale invoice.
     */
    public function show(SaleInvoice $saleInvoice)
    {
        $saleInvoice->load(['party', 'bank', 'createdBy', 'generalLines.generalItem', 'armLines.arm', 'quotation']);

        return view('sale_invoices.show', compact('saleInvoice'));
    }

    /**
     * Show the form for editing the specified sale invoice.
     */
    public function edit(SaleInvoice $saleInvoice)
    {
        if (!$saleInvoice->canBeEdited()) {
            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('error', 'This sale invoice cannot be edited.');
        }

        $businessId = session('active_business');

        $customers = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->orderBy('account_name')
            ->get();

        $saleInvoice->load(['generalLines.generalItem', 'armLines.arm']);

        // Calculate correct available stock for edit form
        // For edit: available stock = current stock + previously sold quantity from this invoice
        foreach ($saleInvoice->generalLines as $line) {
            $currentStock = GeneralItemStockLedger::getCurrentBalance($line->general_item_id);
            $previouslySoldQty = $line->quantity; // Quantity that was sold in this invoice
            $line->generalItem->available_stock = $currentStock + $previouslySoldQty;
        }

        return view('sale_invoices.edit', compact('saleInvoice', 'customers', 'banks'));
    }

    /**
     * Update the specified sale invoice in storage.
     */
    public function update(Request $request, SaleInvoice $saleInvoice)
    {
        if (!$saleInvoice->canBeEdited()) {
            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('error', 'This sale invoice cannot be edited.');
        }

        $userId = auth()->id();
        $wasPosted = $saleInvoice->isPosted();
        $businessId = session('active_business');

        try {
            // Validate stock availability before processing
            if ($request->has('general_lines')) {
                $stockErrors = $this->validateStockAvailabilityForEdit($request->general_lines, $businessId, $saleInvoice);
                if (!empty($stockErrors)) {
                    return back()->withErrors(['stock' => $stockErrors])->withInput();
                }
            }
            
            DB::beginTransaction();

            // Store old values for audit log
            $oldValues = $saleInvoice->toArray();

            // Validate request
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:sale_type,credit|exists:parties,id',
                'sale_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:sale_type,cash|exists:banks,id',
                'invoice_date' => 'required|date',
                'shipping_charges' => 'nullable|numeric|min:0',

                // Customer details validation (for cash sales)
                'name_of_customer' => 'nullable|string|max:255',
                'father_name' => 'nullable|string|max:255',
                'contact' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:1000',
                'cnic' => 'nullable|string|max:20',
                'licence_no' => 'nullable|string|max:255',
                'licence_issue_date' => 'nullable|date',
                'licence_valid_upto' => 'nullable|date',
                'licence_issued_by' => 'nullable|string|max:255',
                're_reg_no' => 'nullable|string|max:255',
                'dc' => 'nullable|string|max:255',
                'Date' => 'nullable|date',

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

            // Prepare invoice data
            $invoiceData = [
                'party_id' => $request->party_id,
                'sale_type' => $request->sale_type,
                'bank_id' => $request->bank_id,
                'invoice_date' => $request->invoice_date,
                'shipping_charges' => $request->shipping_charges ?? 0,
                'name_of_customer' => $request->name_of_customer,
                'father_name' => $request->father_name,
                'contact' => $request->contact,
                'address' => $request->address,
                'cnic' => $request->cnic,
                'licence_no' => $request->licence_no,
                'licence_issue_date' => $request->licence_issue_date,
                'licence_valid_upto' => $request->licence_valid_upto,
                'licence_issued_by' => $request->licence_issued_by,
                're_reg_no' => $request->re_reg_no,
                'dc' => $request->dc,
                'Date' => $request->Date,
                // Party license details
                'party_license_no' => $request->party_license_no,
                'party_license_issue_date' => $request->party_license_issue_date,
                'party_license_valid_upto' => $request->party_license_valid_upto,
                'party_license_issued_by' => $request->party_license_issued_by,
                'party_re_reg_no' => $request->party_re_reg_no,
                'party_dc' => $request->party_dc,
                'party_dc_date' => $request->party_dc_date,
            ];

            // Check if there are any actual changes
            $hasChanges = $this->hasChanges($saleInvoice, $request, $invoiceData);
            
            if (!$hasChanges) {
                DB::commit();
                return redirect()->route('sale-invoices.show', $saleInvoice)
                    ->with('info', 'No changes detected. Sale invoice remains unchanged.');
            }

            // Use enhanced edit method
            $saleInvoice->performEnhancedEdit(
                $request->general_lines ?? [],
                $request->arm_lines ?? [],
                $invoiceData
            );

            // Update audit log with old values
            $auditLog = SaleInvoiceAuditLog::where('sale_invoice_id', $saleInvoice->id)
                ->where('action', 'enhanced_edit')
                ->latest()
                ->first();
                
            if ($auditLog) {
                $auditLog->update(['old_values' => $oldValues]);
            }

            DB::commit();

            $message = $wasPosted
                ? 'Sale invoice updated successfully. Inventory has been adjusted and the invoice remains posted.'
                : 'Sale invoice updated successfully.';

            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice update failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Show more specific error messages for common issues
            $errorMessage = 'Failed to update sale invoice. ';
            if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                $errorMessage .= 'Database constraint violation. Please check your data.';
            } elseif (str_contains($e->getMessage(), 'SQLSTATE[42S22]')) {
                $errorMessage .= 'Database column not found. Please contact support.';
            } elseif (str_contains($e->getMessage(), 'SQLSTATE[42S02]')) {
                $errorMessage .= 'Database table not found. Please contact support.';
            } elseif (str_contains($e->getMessage(), 'Call to undefined method')) {
                $errorMessage .= 'System error. Please contact support.';
            } else {
                $errorMessage .= 'Error: ' . $e->getMessage();
            }
            
            return back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    /**
     * Post the sale invoice.
     */
    public function post(SaleInvoice $saleInvoice)
    {
        if (!$saleInvoice->canBePosted()) {
            return back()->with('error', 'This sale invoice cannot be posted.');
        }

        try {
            DB::beginTransaction();

            $this->postSaleInvoice($saleInvoice);

            DB::commit();

            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('success', 'Sale invoice posted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice posting failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to post sale invoice. Please try again.');
        }
    }

    /**
     * Cancel the sale invoice.
     */
    public function cancel(SaleInvoice $saleInvoice)
    {
        Log::info('=== SALE INVOICE CANCELLATION STARTED ===', [
            'sale_invoice_id' => $saleInvoice->id,
            'invoice_number' => $saleInvoice->invoice_number,
            'status' => $saleInvoice->status,
            'sale_type' => $saleInvoice->sale_type,
            'business_id' => $saleInvoice->business_id,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);

        // Reload to ensure we have fresh data
        $saleInvoice->refresh();

        if (!$saleInvoice->canBeCancelled()) {
            Log::warning('Sale invoice cannot be cancelled - canBeCancelled() returned false', [
                'sale_invoice_id' => $saleInvoice->id,
                'status' => $saleInvoice->status,
                'isPosted' => $saleInvoice->isPosted()
            ]);
            return back()->with('error', 'This sale invoice cannot be cancelled.');
        }

        // Additional check to prevent multiple cancellations
        if ($saleInvoice->status === 'cancelled') {
            Log::warning('Sale invoice already cancelled', [
                'sale_invoice_id' => $saleInvoice->id,
                'status' => $saleInvoice->status
            ]);
            return back()->with('error', 'This sale invoice has already been cancelled.');
        }

        try {
            Log::info('Starting database transaction', ['sale_invoice_id' => $saleInvoice->id]);
            DB::beginTransaction();

            // Load relationships before processing
            $saleInvoice->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm', 'party', 'bank']);
            
            Log::info('Loaded sale invoice relationships', [
                'sale_invoice_id' => $saleInvoice->id,
                'general_lines_count' => $saleInvoice->generalLines->count(),
                'arm_lines_count' => $saleInvoice->armLines->count(),
                'has_bank' => $saleInvoice->bank ? true : false,
                'bank_id' => $saleInvoice->bank_id
            ]);

            // Reverse inventory impacts for cancellation
            Log::info('Starting inventory reversal', ['sale_invoice_id' => $saleInvoice->id]);
            try {
                $this->reverseInventoryImpactsForCancellation($saleInvoice);
                Log::info('Inventory reversal completed successfully', ['sale_invoice_id' => $saleInvoice->id]);
            } catch (\Exception $e) {
                Log::error('Inventory reversal failed', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            // Reverse journal entries
            Log::info('Starting journal entries reversal', ['sale_invoice_id' => $saleInvoice->id]);
            try {
                $this->reverseJournalEntries($saleInvoice);
                Log::info('Journal entries reversal completed successfully', ['sale_invoice_id' => $saleInvoice->id]);
            } catch (\Exception $e) {
                Log::error('Journal entries reversal failed', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            // Reverse bank ledger entry for cash sales
            if ($saleInvoice->sale_type === 'cash' && $saleInvoice->bank_id) {
                Log::info('Creating bank ledger reversal entry', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'bank_id' => $saleInvoice->bank_id,
                    'total_amount' => $saleInvoice->total_amount
                ]);
                try {
                    BankLedger::create([
                        'business_id' => $saleInvoice->business_id,
                        'bank_id' => $saleInvoice->bank_id,
                        'voucher_id' => $saleInvoice->id,
                        'voucher_type' => 'Sale Invoice Cancellation',
                        'date' => now(),
                        'user_id' => auth()->id(),
                        'deposit_amount' => 0,
                        'withdrawal_amount' => $saleInvoice->total_amount,
                    ]);
                    Log::info('Bank ledger reversal entry created successfully', ['sale_invoice_id' => $saleInvoice->id]);
                } catch (\Exception $e) {
                    Log::error('Bank ledger reversal entry creation failed', [
                        'sale_invoice_id' => $saleInvoice->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                Log::info('Skipping bank ledger reversal', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'sale_type' => $saleInvoice->sale_type,
                    'bank_id' => $saleInvoice->bank_id
                ]);
            }

            // Update status
            Log::info('Updating sale invoice status to cancelled', ['sale_invoice_id' => $saleInvoice->id]);
            try {
                $saleInvoice->update([
                    'status' => 'cancelled',
                    'cancelled_by' => auth()->id()
                ]);
                Log::info('Sale invoice status updated successfully', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'new_status' => $saleInvoice->fresh()->status
                ]);
            } catch (\Exception $e) {
                Log::error('Sale invoice status update failed', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            // Create audit log
            Log::info('Creating audit log entry', ['sale_invoice_id' => $saleInvoice->id]);
            try {
                SaleInvoiceAuditLog::create([
                    'sale_invoice_id' => $saleInvoice->id,
                    'action' => 'cancelled',
                    'old_values' => ['status' => 'posted'],
                    'new_values' => ['status' => 'cancelled'],
                    'user_id' => auth()->id(),
                ]);
                Log::info('Audit log entry created successfully', ['sale_invoice_id' => $saleInvoice->id]);
            } catch (\Exception $e) {
                Log::error('Audit log entry creation failed', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            Log::info('Committing database transaction', ['sale_invoice_id' => $saleInvoice->id]);
            DB::commit();
            Log::info('=== SALE INVOICE CANCELLATION COMPLETED SUCCESSFULLY ===', [
                'sale_invoice_id' => $saleInvoice->id,
                'invoice_number' => $saleInvoice->invoice_number
            ]);

            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('success', 'Sale invoice cancelled successfully and inventory has been restored.');

        } catch (\Exception $e) {
            Log::error('=== SALE INVOICE CANCELLATION FAILED ===', [
                'sale_invoice_id' => $saleInvoice->id,
                'invoice_number' => $saleInvoice->invoice_number ?? 'N/A',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'previous_error' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
            ]);
            
            try {
                DB::rollBack();
                Log::info('Database transaction rolled back', ['sale_invoice_id' => $saleInvoice->id]);
            } catch (\Exception $rollbackException) {
                Log::error('Failed to rollback transaction', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'rollback_error' => $rollbackException->getMessage()
                ]);
            }
            
            return back()->with('error', 'Failed to cancel sale invoice: ' . $e->getMessage() . '. Please check the logs for details.');
        }
    }

    /**
     * Post sale invoice and create all related entries.
     */
    private function postSaleInvoice(SaleInvoice $saleInvoice)
    {
        try {
            $businessId = $saleInvoice->business_id;
            $userId = auth()->id();

            // Update status
            $saleInvoice->update([
                'status' => 'posted',
                'posted_by' => $userId
            ]);

            // Create stock ledger entries for general items
            foreach ($saleInvoice->generalLines as $line) {
                // Get available batches for FIFO consumption
                $batches = GeneralBatch::where('item_id', $line->general_item_id)
                    ->where('qty_remaining', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                if ($batches->isEmpty()) {
                    \Log::warning('No available batches for general item in sale invoice', [
                        'sale_invoice_id' => $saleInvoice->id,
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
                        'transaction_date' => $saleInvoice->invoice_date,
                        'quantity' => -$qtyToConsume,
                        'quantity_out' => $qtyToConsume,
                        'balance_quantity' => $batch->qty_remaining - $qtyToConsume,
                        'unit_cost' => $batch->unit_cost,
                        'total_cost' => $qtyToConsume * $batch->unit_cost,
                        'reference_no' => $saleInvoice->invoice_number,
                        'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer'),
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
                // Store old values for history (including original sale price before update)
                $oldValues = $line->arm->toArray();
                $oldSalePrice = $line->arm->sale_price;

                // Create arms stock ledger entry
                try {
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
                } catch (\Exception $e) {
                    \Log::error('Error creating arms stock ledger: ' . $e->getMessage(), [
                        'arm_id' => $line->arm_id,
                        'sale_invoice_id' => $saleInvoice->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }

                // Update arm status and sale price
                $line->arm->update([
                    'status' => 'sold',
                    'sold_date' => $saleInvoice->invoice_date,
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
                        'transaction_date' => $saleInvoice->invoice_date,
                        'price' => $line->sale_price,
                        'remarks' => 'Sale to ' . ($saleInvoice->party->name ?? 'Customer'),
                        'user_id' => $userId,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error creating arm history: ' . $e->getMessage(), [
                        'arm_id' => $line->arm_id,
                        'sale_invoice_id' => $saleInvoice->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            // Create party ledger entry for credit sales (party owes us -> debit)
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
                    'deposit_amount' => $saleInvoice->total_amount, // Money coming into bank
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
            \Log::error('Error posting sale invoice: ' . $e->getMessage(), [
                'sale_invoice_id' => $saleInvoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }

    /**
     * Create journal entries for the sale invoice.
     */
    private function createJournalEntries(SaleInvoice $saleInvoice)
    {
        $businessId = $saleInvoice->business_id;
        $userId = auth()->id();

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
                    throw new \Exception('Failed to create or retrieve party chart of account for credit sale.');
                }
            } else {
                throw new \Exception('Party not found for credit sale.');
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

        // If any required account is missing, skip journal entries
        if (!$salesRevenueId || !$cogsId || !$inventoryId) {
            \Log::warning('Missing required chart of accounts for sale invoice posting', [
                'sale_invoice_id' => $saleInvoice->id,
                'sales_revenue_id' => $salesRevenueId,
                'cogs_id' => $cogsId,
                'inventory_id' => $inventoryId
            ]);
            return; // Skip journal entries but continue with other posting operations
        }

        // Entry 1: Debit Party Account (for credit sales) / Debit Bank (for cash sales) / Credit Sales Revenue
        if ($saleInvoice->sale_type === 'credit') {
            // Credit sale - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit sales.');
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
                    'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number . ' - ' . $saleInvoice->bank->account_name,
                    'user_id' => $userId,
                    'date_added' => $saleInvoice->invoice_date,
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
                    'debit_amount' => $saleInvoice->total_amount,
                    'credit_amount' => 0,
                    'voucher_id' => $saleInvoice->id,
                    'voucher_type' => 'SaleInvoice',
                        'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number . ' - Cash',
                    'user_id' => $userId,
                    'date_added' => $saleInvoice->invoice_date,
                ]);
                }
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
            'comments' => 'Sale Invoice ' . $saleInvoice->invoice_number,
            'user_id' => $userId,
            'date_added' => $saleInvoice->invoice_date,
        ]);

        // Calculate total COGS
        $totalCogs = 0;

        // COGS for general items - calculate based on actual stock ledger entries
        foreach ($saleInvoice->generalLines as $line) {
            // Get the stock ledger entries for this line to calculate correct COGS
            $stockEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                ->where('reference_no', $saleInvoice->invoice_number)
                ->where('transaction_type', 'sale')
                ->where('quantity', '<', 0) // Only sale entries (negative quantity)
                ->get();

            // Get reversal entries for this item and invoice
            $reversalEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                ->where('reference_no', 'like', $saleInvoice->invoice_number . '%')
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
        foreach ($saleInvoice->armLines as $line) {
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
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Cost of Goods Sold',
                'user_id' => $userId,
                'date_added' => $saleInvoice->invoice_date,
            ]);

            // Credit Inventory (single entry for all items)
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
     * Show the audit log for the sale invoice.
     */
    public function auditLog(SaleInvoice $saleInvoice)
    {
        $auditLogs = $saleInvoice->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sale_invoices.audit-log', compact('saleInvoice', 'auditLogs'));
    }

    /**
     * Soft delete the specified sale invoice.
     */
    public function destroy(SaleInvoice $saleInvoice)
    {
        if (!$saleInvoice->canBeDeleted()) {
            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('error', 'This sale invoice cannot be deleted.');
        }

        try {
            DB::beginTransaction();

            // Use enhanced delete method
            $saleInvoice->performSoftDelete();

            DB::commit();

            return redirect()->route('sale-invoices.index')
                ->with('success', 'Sale invoice deleted successfully. All inventory has been restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete sale invoice. Please try again.']);
        }
    }

    /**
     * Restore a soft-deleted sale invoice.
     */
    public function restore($id)
    {
        $saleInvoice = SaleInvoice::withTrashed()->findOrFail($id);

        if (!$saleInvoice->trashed()) {
            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('error', 'This sale invoice is not deleted.');
        }

        try {
            DB::beginTransaction();

            // Restore the invoice
            $saleInvoice->restore();
            $saleInvoice->update(['deleted_by' => null]);

            // Restore child records
            $saleInvoice->generalLines()->withTrashed()->restore();
            $saleInvoice->armLines()->withTrashed()->restore();
            $saleInvoice->auditLogs()->withTrashed()->restore();

            // Update deleted_by to null for child records
            $saleInvoice->generalLines()->update(['deleted_by' => null]);
            $saleInvoice->armLines()->update(['deleted_by' => null]);
            $saleInvoice->auditLogs()->update(['deleted_by' => null]);

            // Create audit log for restoration
            SaleInvoiceAuditLog::create([
                'sale_invoice_id' => $saleInvoice->id,
                'action' => 'restored',
                'old_values' => ['deleted_at' => $saleInvoice->deleted_at],
                'new_values' => ['deleted_at' => null, 'deleted_by' => null],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('success', 'Sale invoice restored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice restoration failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to restore sale invoice. Please try again.']);
        }
    }

    /**
     * Permanently delete a soft-deleted sale invoice.
     */
    public function forceDelete($id)
    {
        $saleInvoice = SaleInvoice::withTrashed()->findOrFail($id);

        if (!$saleInvoice->trashed()) {
            return redirect()->route('sale-invoices.show', $saleInvoice)
                ->with('error', 'This sale invoice is not deleted.');
        }

        try {
            DB::beginTransaction();

            // Permanently delete child records first
            $saleInvoice->generalLines()->withTrashed()->forceDelete();
            $saleInvoice->armLines()->withTrashed()->forceDelete();
            $saleInvoice->auditLogs()->withTrashed()->forceDelete();

            // Permanently delete the main invoice
            $saleInvoice->forceDelete();

            DB::commit();

            return redirect()->route('sale-invoices.index')
                ->with('success', 'Sale invoice permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale invoice permanent deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to permanently delete sale invoice. Please try again.']);
        }
    }


    /**
     * Reverse journal entries for a sale invoice.
     */
    private function reverseJournalEntries(SaleInvoice $saleInvoice)
    {
        $businessId = $saleInvoice->business_id;
        $userId = auth()->id();

        Log::info('Starting journal entries reversal', [
            'sale_invoice_id' => $saleInvoice->id,
            'business_id' => $businessId,
            'sale_type' => $saleInvoice->sale_type
        ]);

        // Get party's chart of account for credit sales (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($saleInvoice->sale_type === 'credit' && $saleInvoice->party_id) {
            $party = \App\Models\Party::find($saleInvoice->party_id);
            if ($party && $party->chart_of_account_id) {
                $partyAccountId = $party->chart_of_account_id;
            }
            
            if (!$partyAccountId) {
                Log::error('Party chart of account not found for sale invoice reversal', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'party_id' => $saleInvoice->party_id
                ]);
                throw new \Exception('Party chart of account is required for credit sale reversal.');
            }
        }

        $salesRevenueId = ChartOfAccount::where('business_id', $businessId)
            ->where(function($q) {
                $q->where('name', 'like', '%Sales%')
                  ->orWhere('name', 'like', '%Revenue%')
                  ->orWhere('name', 'like', '%Income%');
            })
            ->value('id');

        $cogsId = ChartOfAccount::where('business_id', $businessId)
            ->where(function($q) {
                $q->where('name', 'like', '%Cost of Goods%')
                  ->orWhere('name', 'like', '%COGS%');
            })
            ->value('id');

        $inventoryId = ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'like', '%Inventory%')
            ->value('id');

        Log::info('Chart of accounts lookup results', [
            'sale_invoice_id' => $saleInvoice->id,
            'party_account_id' => $partyAccountId,
            'sales_revenue_id' => $salesRevenueId,
            'cogs_id' => $cogsId,
            'inventory_id' => $inventoryId
        ]);

        if (!$salesRevenueId || !$cogsId || !$inventoryId) {
            Log::error('Missing required chart of accounts for sale invoice reversal', [
                'sale_invoice_id' => $saleInvoice->id,
                'business_id' => $businessId,
                'sales_revenue_id' => $salesRevenueId,
                'cogs_id' => $cogsId,
                'inventory_id' => $inventoryId,
                'available_accounts' => ChartOfAccount::where('business_id', $businessId)
                    ->select('id', 'name', 'code', 'type')
                    ->get()
                    ->toArray()
            ]);
            throw new \Exception('Missing required chart of accounts. Please ensure Sales Revenue, COGS, and Inventory accounts exist.');
        }

        // Reverse Entry 1: Credit Party Account (for credit sales) / Debit Sales Revenue
        if ($saleInvoice->sale_type === 'credit') {
            // Credit sale reversal - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit sale reversal.');
            }
            
            Log::info('Creating credit sale reversal journal entry', [
                'sale_invoice_id' => $saleInvoice->id,
                'party_account_id' => $partyAccountId,
                'amount' => $saleInvoice->total_amount
            ]);
            try {
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $partyAccountId,
                    'debit_amount' => 0,
                    'credit_amount' => $saleInvoice->total_amount,
                    'voucher_id' => $saleInvoice->id,
                    'voucher_type' => 'SaleInvoice',
                    'comments' => 'Sale Invoice Reversal ' . $saleInvoice->invoice_number,
                    'user_id' => $userId,
                    'date_added' => now(),
                ]);
                Log::info('Credit sale reversal journal entry created', ['sale_invoice_id' => $saleInvoice->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create credit sale reversal journal entry', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            // Cash sale reversal - credit bank account
            if ($saleInvoice->bank && $saleInvoice->bank->chartOfAccount) {
                Log::info('Creating cash sale reversal journal entry (bank account)', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'bank_id' => $saleInvoice->bank_id,
                    'chart_of_account_id' => $saleInvoice->bank->chartOfAccount->id,
                    'amount' => $saleInvoice->total_amount
                ]);
                try {
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $saleInvoice->bank->chartOfAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => $saleInvoice->total_amount,
                        'voucher_id' => $saleInvoice->id,
                        'voucher_type' => 'SaleInvoice',
                        'comments' => 'Sale Invoice Reversal ' . $saleInvoice->invoice_number . ' - ' . $saleInvoice->bank->account_name,
                        'user_id' => $userId,
                        'date_added' => now(),
                    ]);
                    Log::info('Cash sale reversal journal entry created (bank account)', ['sale_invoice_id' => $saleInvoice->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to create cash sale reversal journal entry (bank account)', [
                        'sale_invoice_id' => $saleInvoice->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            } else {
                // Fallback to cash account if no bank selected
                Log::warning('No bank or chart of account found, trying cash account fallback', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'bank_id' => $saleInvoice->bank_id,
                    'has_bank' => $saleInvoice->bank ? true : false,
                    'has_chart_of_account' => $saleInvoice->bank && $saleInvoice->bank->chartOfAccount ? true : false
                ]);
                $cashAccountId = ChartOfAccount::where('business_id', $businessId)
                    ->where(function($q) {
                        $q->where('name', 'like', '%Cash%')
                          ->orWhere('name', 'like', '%Bank%');
                    })
                    ->value('id');

                if ($cashAccountId) {
                    Log::info('Creating cash sale reversal journal entry (cash account fallback)', [
                        'sale_invoice_id' => $saleInvoice->id,
                        'cash_account_id' => $cashAccountId,
                        'amount' => $saleInvoice->total_amount
                    ]);
                    try {
                        JournalEntry::create([
                            'business_id' => $businessId,
                            'account_head' => $cashAccountId,
                            'debit_amount' => 0,
                            'credit_amount' => $saleInvoice->total_amount,
                            'voucher_id' => $saleInvoice->id,
                            'voucher_type' => 'SaleInvoice',
                            'comments' => 'Sale Invoice Reversal ' . $saleInvoice->invoice_number . ' - Cash',
                            'user_id' => $userId,
                            'date_added' => now(),
                        ]);
                        Log::info('Cash sale reversal journal entry created (cash account fallback)', ['sale_invoice_id' => $saleInvoice->id]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create cash sale reversal journal entry (cash account fallback)', [
                            'sale_invoice_id' => $saleInvoice->id,
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }
                } else {
                    Log::error('No cash account found for fallback', [
                        'sale_invoice_id' => $saleInvoice->id,
                        'business_id' => $businessId
                    ]);
                    throw new \Exception('No bank account or cash account found for cash sale reversal.');
                }
            }
        }

        // Reverse Entry 2: Debit Sales Revenue
        Log::info('Creating sales revenue reversal journal entry', [
            'sale_invoice_id' => $saleInvoice->id,
            'sales_revenue_id' => $salesRevenueId,
            'amount' => $saleInvoice->total_amount
        ]);
        try {
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $salesRevenueId,
                'debit_amount' => $saleInvoice->total_amount,
                'credit_amount' => 0,
                'voucher_id' => $saleInvoice->id,
                'voucher_type' => 'SaleInvoice',
                'comments' => 'Sale Invoice Reversal ' . $saleInvoice->invoice_number,
                'user_id' => $userId,
                'date_added' => now(),
            ]);
            Log::info('Sales revenue reversal journal entry created', ['sale_invoice_id' => $saleInvoice->id]);
        } catch (\Exception $e) {
            Log::error('Failed to create sales revenue reversal journal entry', [
                'sale_invoice_id' => $saleInvoice->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Reverse party ledger entry for credit sales (reverse the debit -> credit)
        if ($saleInvoice->sale_type === 'credit' && $saleInvoice->party_id) {
            Log::info('Creating party ledger reversal entry', [
                'sale_invoice_id' => $saleInvoice->id,
                'party_id' => $saleInvoice->party_id,
                'amount' => $saleInvoice->total_amount
            ]);
            try {
                PartyLedger::create([
                    'business_id' => $saleInvoice->business_id,
                    'party_id' => $saleInvoice->party_id,
                    'voucher_id' => $saleInvoice->id,
                    'voucher_type' => 'Sale Invoice Reversal',
                    'date_added' => now(),
                    'user_id' => $userId,
                    'debit_amount' => 0,
                    'credit_amount' => $saleInvoice->total_amount,
                ]);
                Log::info('Party ledger reversal entry created', ['sale_invoice_id' => $saleInvoice->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create party ledger reversal entry', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        // Calculate total COGS for reversal
        // IMPORTANT: Get the CURRENT journal entry amounts, not from stock ledger entries
        // This ensures we reverse the correct amounts even after edits
        $totalCogs = 0;

        // Get the current COGS journal entry amount (after any edits)
        $cogsJournalEntry = JournalEntry::where('business_id', $businessId)
            ->where('voucher_id', $saleInvoice->id)
            ->where('voucher_type', 'SaleInvoice')
            ->where('comments', 'Cost of Goods Sold')
            ->where('debit_amount', '>', 0) // COGS is debited
            ->orderBy('id', 'desc') // Get the most recent entry (after edits)
            ->first();

        if ($cogsJournalEntry) {
            // Use the current journal entry amount (this is the correct amount after edits)
            $totalCogs = $cogsJournalEntry->debit_amount;
            Log::info('Found COGS journal entry for reversal', [
                'sale_invoice_id' => $saleInvoice->id,
                'cogs_amount' => $totalCogs,
                'journal_entry_id' => $cogsJournalEntry->id
            ]);
        } else {
            // Fallback: Calculate from current sale invoice lines if journal entry not found
            // This should rarely happen, but provides a safety net
            Log::warning('COGS journal entry not found, calculating from invoice lines', [
                'sale_invoice_id' => $saleInvoice->id
            ]);
            
            // COGS for general items - calculate from current invoice lines
            foreach ($saleInvoice->generalLines as $line) {
                if ($line->batch) {
                    $cogsAmount = $line->quantity * $line->batch->unit_cost;
                    $totalCogs += $cogsAmount;
            }
        }

            // COGS for arms
        foreach ($saleInvoice->armLines as $line) {
            $cogsAmount = $line->arm->purchase_price ?? 0;
            $totalCogs += $cogsAmount;
            }
        }

        // Create single summarized COGS and Inventory reversal journal entries
        if ($totalCogs > 0) {
            Log::info('Creating COGS and Inventory reversal journal entries', [
                'sale_invoice_id' => $saleInvoice->id,
                'total_cogs' => $totalCogs,
                'cogs_id' => $cogsId,
                'inventory_id' => $inventoryId
            ]);
            try {
                // Credit COGS (single entry for all items)
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $cogsId,
                    'debit_amount' => 0,
                    'credit_amount' => $totalCogs,
                    'voucher_id' => $saleInvoice->id,
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
                    'voucher_id' => $saleInvoice->id,
                    'voucher_type' => 'SaleInvoice',
                    'comments' => 'Inventory Reversal',
                    'user_id' => $userId,
                    'date_added' => now(),
                ]);
                Log::info('COGS and Inventory reversal journal entries created', ['sale_invoice_id' => $saleInvoice->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create COGS and Inventory reversal journal entries', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            Log::info('No COGS to reverse (total_cogs = 0)', ['sale_invoice_id' => $saleInvoice->id]);
        }

        Log::info('Journal entries reversal completed successfully', ['sale_invoice_id' => $saleInvoice->id]);
    }

    /**
     * Check if there are any actual changes in the sale invoice.
     */
    private function hasChanges(SaleInvoice $saleInvoice, Request $request, array $invoiceData): bool
    {
        // Check main invoice fields
        $currentData = [
            'party_id' => $saleInvoice->party_id,
            'sale_type' => $saleInvoice->sale_type,
            'bank_id' => $saleInvoice->bank_id,
            'invoice_date' => $saleInvoice->invoice_date->format('Y-m-d'),
            'shipping_charges' => $saleInvoice->shipping_charges,
            'name_of_customer' => $saleInvoice->name_of_customer,
            'father_name' => $saleInvoice->father_name,
            'contact' => $saleInvoice->contact,
            'address' => $saleInvoice->address,
            'cnic' => $saleInvoice->cnic,
            'licence_no' => $saleInvoice->licence_no,
            'licence_issue_date' => $saleInvoice->licence_issue_date?->format('Y-m-d'),
            'licence_valid_upto' => $saleInvoice->licence_valid_upto?->format('Y-m-d'),
            'licence_issued_by' => $saleInvoice->licence_issued_by,
            're_reg_no' => $saleInvoice->re_reg_no,
            'dc' => $saleInvoice->dc,
            'Date' => $saleInvoice->Date?->format('Y-m-d'),
        ];

        // Normalize invoice data for comparison
        $newData = [
            'party_id' => $invoiceData['party_id'],
            'sale_type' => $invoiceData['sale_type'],
            'bank_id' => $invoiceData['bank_id'],
            'invoice_date' => $invoiceData['invoice_date'],
            'shipping_charges' => $invoiceData['shipping_charges'],
            'name_of_customer' => $invoiceData['name_of_customer'],
            'father_name' => $invoiceData['father_name'],
            'contact' => $invoiceData['contact'],
            'address' => $invoiceData['address'],
            'cnic' => $invoiceData['cnic'],
            'licence_no' => $invoiceData['licence_no'],
            'licence_issue_date' => $invoiceData['licence_issue_date'],
            'licence_valid_upto' => $invoiceData['licence_valid_upto'],
            'licence_issued_by' => $invoiceData['licence_issued_by'],
            're_reg_no' => $invoiceData['re_reg_no'],
            'dc' => $invoiceData['dc'],
            'Date' => $invoiceData['Date'],
        ];

        // Check if main invoice data has changed
        foreach ($currentData as $key => $value) {
            if ($currentData[$key] != $newData[$key]) {
                return true;
            }
        }

        // Check general lines changes
        $currentGeneralLines = $saleInvoice->generalLines->map(function ($line) {
            return [
                'general_item_id' => $line->general_item_id,
                'quantity' => $line->quantity,
                'sale_price' => $line->sale_price,
            ];
        })->toArray();

        $newGeneralLines = $request->general_lines ?? [];
        
        // Normalize arrays for comparison
        usort($currentGeneralLines, function ($a, $b) {
            return $a['general_item_id'] <=> $b['general_item_id'];
        });
        usort($newGeneralLines, function ($a, $b) {
            return $a['general_item_id'] <=> $b['general_item_id'];
        });

        if (count($currentGeneralLines) !== count($newGeneralLines)) {
            return true;
        }

        foreach ($currentGeneralLines as $index => $currentLine) {
            if (!isset($newGeneralLines[$index])) {
                return true;
            }
            
            $newLine = $newGeneralLines[$index];
            if ($currentLine['general_item_id'] != $newLine['general_item_id'] ||
                $currentLine['quantity'] != $newLine['qty'] ||
                $currentLine['sale_price'] != $newLine['sale_price']) {
                return true;
            }
        }

        // Check arm lines changes
        $currentArmLines = $saleInvoice->armLines->map(function ($line) {
            return [
                'arm_id' => $line->arm_id,
                'sale_price' => $line->sale_price,
            ];
        })->toArray();

        $newArmLines = $request->arm_lines ?? [];
        
        // Normalize arrays for comparison
        usort($currentArmLines, function ($a, $b) {
            return $a['arm_id'] <=> $b['arm_id'];
        });
        usort($newArmLines, function ($a, $b) {
            return $a['arm_id'] <=> $b['arm_id'];
        });

        if (count($currentArmLines) !== count($newArmLines)) {
            return true;
        }

        foreach ($currentArmLines as $index => $currentLine) {
            if (!isset($newArmLines[$index])) {
                return true;
            }
            
            $newLine = $newArmLines[$index];
            if ($currentLine['arm_id'] != $newLine['arm_id'] ||
                $currentLine['sale_price'] != $newLine['sale_price']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate stock availability for new sale invoice.
     */
    private function validateStockAvailability(array $generalLines, int $businessId): array
    {
        $errors = [];

        foreach ($generalLines as $index => $line) {
            $itemId = $line['general_item_id'];
            $requiredQty = (float) $line['qty'];

            // Get current stock balance for this item
            $stockBalance = GeneralItemStockLedger::getStockBalance($itemId);
            $availableQty = $stockBalance['balance'];

            if ($availableQty < $requiredQty) {
                $item = GeneralItem::find($itemId);
                $itemName = $item ? $item->item_name : 'Unknown Item';
                
                $errors[] = "Insufficient stock for '{$itemName}'. Available: {$availableQty}, Required: {$requiredQty}";
            }
        }

        return $errors;
    }

    /**
     * Validate stock availability for sale invoice edit.
     * For edit: available stock = current stock + previously sold quantity from this invoice
     */
    private function validateStockAvailabilityForEdit(array $generalLines, int $businessId, SaleInvoice $saleInvoice): array
    {
        $errors = [];

        foreach ($generalLines as $index => $line) {
            $itemId = $line['general_item_id'];
            $requiredQty = (float) $line['qty'];

            // Get current stock balance for this item
            $stockBalance = GeneralItemStockLedger::getStockBalance($itemId);
            $currentStock = $stockBalance['balance'];

            // Find the original quantity sold in this invoice
            $originalLine = $saleInvoice->generalLines->where('general_item_id', $itemId)->first();
            $previouslySoldQty = $originalLine ? $originalLine->quantity : 0;

            // Calculate available stock for edit: current stock + previously sold quantity
            $availableQty = $currentStock + $previouslySoldQty;

            if ($availableQty < $requiredQty) {
                $item = GeneralItem::find($itemId);
                $itemName = $item ? $item->item_name : 'Unknown Item';
                
                $errors[] = "Insufficient stock for '{$itemName}'. Available: {$availableQty}, Required: {$requiredQty}";
            }
        }

        return $errors;
    }

    /**
     * Reverse inventory impacts specifically for sale invoice cancellation.
     * This method ensures all inventory is properly restored without any edit logic.
     */
    private function reverseInventoryImpactsForCancellation(SaleInvoice $saleInvoice): void
    {
        $businessId = $saleInvoice->business_id;
        $userId = auth()->id() ?? 1;

        // Load fresh relationships to ensure we have the current data
        $saleInvoice->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm', 'party']);

        Log::info('Starting inventory reversal for sale invoice cancellation', [
            'sale_invoice_id' => $saleInvoice->id,
            'invoice_number' => $saleInvoice->invoice_number,
            'general_lines_count' => $saleInvoice->generalLines->count(),
            'arm_lines_count' => $saleInvoice->armLines->count()
        ]);

        // Get ALL sale entries for this invoice once to avoid processing duplicates
        $allSaleEntries = GeneralItemStockLedger::where('reference_no', $saleInvoice->invoice_number)
            ->where('transaction_type', 'sale')
            ->where('quantity', '<', 0) // Only sale entries (negative quantity)
            ->orderBy('id')
            ->get();

        // Get ALL existing reversals for this invoice once
        $allExistingReversals = GeneralItemStockLedger::where('reference_no', 'like', $saleInvoice->invoice_number . '%')
            ->where('transaction_type', 'reversal')
            ->get();

        // Create a map of already reversed entries by sale entry ID
        // We'll track which sale entry IDs have been reversed by checking if a reversal exists
        // that matches the quantity, unit_cost, batch_id, and general_item_id
        $reversedSaleEntryIds = [];
        foreach ($allExistingReversals as $reversal) {
            // Find the corresponding sale entry that was reversed
            $matchingSaleEntry = $allSaleEntries->first(function($entry) use ($reversal) {
                return $entry->general_item_id == $reversal->general_item_id
                    && $entry->batch_id == $reversal->batch_id
                    && abs($entry->quantity) == $reversal->quantity
                    && $entry->unit_cost == $reversal->unit_cost;
            });
            
            if ($matchingSaleEntry) {
                $reversedSaleEntryIds[$matchingSaleEntry->id] = true;
            }
        }

        // Group sale entries by general_item_id to process each item once
        $saleEntriesByItem = $allSaleEntries->groupBy('general_item_id');

        // Reverse general item stock ledger entries - process each item only once
        foreach ($saleEntriesByItem as $generalItemId => $originalSaleEntries) {
            Log::info('Processing general item reversal', [
                'general_item_id' => $generalItemId,
                'sale_entries_count' => $originalSaleEntries->count(),
                'invoice_number' => $saleInvoice->invoice_number
            ]);

            if ($originalSaleEntries->isEmpty()) {
                Log::warning('No original sale entries found for reversal', [
                    'sale_invoice_id' => $saleInvoice->id,
                    'general_item_id' => $generalItemId,
                    'invoice_number' => $saleInvoice->invoice_number
                ]);
                continue;
            }

            // Reverse each original sale entry with its exact FIFO details
            // Only reverse entries that haven't been reversed yet
            foreach ($originalSaleEntries as $originalEntry) {
                // Skip if this entry has already been reversed
                if (isset($reversedSaleEntryIds[$originalEntry->id])) {
                    Log::info('Skipping reversal for already reversed entry', [
                        'sale_entry_id' => $originalEntry->id,
                        'quantity' => $originalEntry->quantity,
                        'unit_cost' => $originalEntry->unit_cost
                    ]);
                    continue;
                }

                // Create reversal stock ledger entry with exact FIFO details
                GeneralItemStockLedger::create([
                    'business_id' => $businessId,
                    'general_item_id' => $originalEntry->general_item_id,
                    'batch_id' => $originalEntry->batch_id,
                    'transaction_type' => 'reversal',
                    'transaction_date' => $saleInvoice->invoice_date,
                    'quantity' => abs($originalEntry->quantity), // Positive quantity to restore stock
                    'quantity_in' => abs($originalEntry->quantity),
                    'quantity_out' => 0,
                    'balance_quantity' => 0, // Will be recalculated by recalculateBalances
                    'unit_cost' => $originalEntry->unit_cost, // Use exact FIFO unit cost
                    'total_cost' => abs($originalEntry->total_cost), // Use exact FIFO total cost
                    'reference_no' => $saleInvoice->invoice_number . '-REV',
                    'remarks' => 'Sale cancellation for ' . ($saleInvoice->party->name ?? 'Customer'),
                    'created_by' => $userId,
                ]);

                // Mark this entry as reversed
                $reversedSaleEntryIds[$originalEntry->id] = true;

                // Restore batch remaining quantity
                $batch = GeneralBatch::find($originalEntry->batch_id);
                if ($batch) {
                    $batch->increment('qty_remaining', abs($originalEntry->quantity));
                    Log::info('Restored batch quantity', [
                        'batch_id' => $batch->id,
                        'restored_quantity' => abs($originalEntry->quantity),
                        'new_remaining' => $batch->fresh()->qty_remaining
                    ]);
                }
                
                Log::info('Created reversal for sale entry', [
                    'sale_entry_id' => $originalEntry->id,
                    'quantity' => $originalEntry->quantity,
                    'unit_cost' => $originalEntry->unit_cost,
                    'batch_id' => $originalEntry->batch_id
                ]);
            }
            
            // Recalculate balances for this item
            GeneralItemStockLedger::recalculateBalances($generalItemId);
            Log::info('Recalculated balances for general item', [
                'general_item_id' => $generalItemId
            ]);
        }

        // Reverse arms stock ledger entries
        foreach ($saleInvoice->armLines as $line) {
            Log::info('Processing arm reversal', [
                'arm_id' => $line->arm_id,
                'invoice_number' => $saleInvoice->invoice_number
            ]);

            // Get the arm model to ensure we have fresh data
            $arm = Arm::find($line->arm_id);

            if (!$arm) {
                Log::warning('Arm not found during reversal', ['arm_id' => $line->arm_id]);
                continue;
            }

            Log::info('Reversing arm sale', [
                'arm_id' => $arm->id,
                'serial_no' => $arm->serial_no,
                'current_status' => $arm->status,
                'sale_invoice_id' => $saleInvoice->id
            ]);

            // Create reversal arms stock ledger entry
            // When reversing a sale cancellation, we need to restore the arm (quantity_in = 1)
            // The original sale had quantity_out = 1, so reversal needs quantity_in = 1
            ArmsStockLedger::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'transaction_date' => now(),
                'transaction_type' => 'reversal',
                'quantity_in' => 1, // Restore arm (opposite of original quantity_out = 1)
                'quantity_out' => 0,
                'balance' => 1, // Restore arm to available
                'reference_id' => $saleInvoice->invoice_number . '-REV',
                'remarks' => 'Sale cancellation for ' . ($saleInvoice->party->name ?? 'Customer'),
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

            Log::info('Arm status restored', [
                'arm_id' => $arm->id,
                'serial_no' => $arm->serial_no,
                'new_status' => 'available',
                'sale_invoice_id' => $saleInvoice->id
            ]);

            // Create arm history entry for reversal
            ArmHistory::create([
                'business_id' => $businessId,
                'arm_id' => $line->arm_id,
                'action' => 'cancel',
                'old_values' => ['status' => 'sold', 'sold_date' => $saleInvoice->invoice_date, 'sale_price' => $line->sale_price],
                'new_values' => ['status' => 'available', 'sold_date' => null, 'sale_price' => $originalSalePrice],
                'transaction_date' => now(),
                'price' => $line->sale_price,
                'remarks' => 'Sale cancellation for ' . ($saleInvoice->party->name ?? 'Customer'),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        Log::info('Completed inventory reversal for sale invoice cancellation', [
            'sale_invoice_id' => $saleInvoice->id,
            'invoice_number' => $saleInvoice->invoice_number
        ]);
    }

    /**
     * Display profit and loss report based on sale invoices.
     */
    public function profitLossReport(Request $request)
    {
        $businessId = session('active_business');
        
        // Get filter values
        $invoiceNumber = $request->get('invoice_number');
        
        // Set default dates to current month if not provided
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        
        if (!$fromDate) {
            $fromDate = now()->startOfMonth()->format('Y-m-d');
        }
        
        if (!$toDate) {
            $toDate = now()->endOfMonth()->format('Y-m-d');
        }
        
        // Build query for posted sale invoices
        $query = SaleInvoice::with(['party', 'generalLines.generalItem', 'armLines.arm'])
            ->where('business_id', $businessId)
            ->where('status', 'posted');
        
        // Apply filters
        if ($invoiceNumber) {
            // Remove SI- prefix if present and extract numeric ID
            $invoiceId = preg_replace('/[^0-9]/', '', str_replace('SI-', '', $invoiceNumber));
            if ($invoiceId) {
                $query->where('id', $invoiceId);
            }
        }
        
        if ($fromDate) {
            $query->whereDate('invoice_date', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('invoice_date', '<=', $toDate);
        }
        
        // Order by date
        $saleInvoices = $query->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Build report data
        $reportData = [];
        $totalSales = 0;
        $totalCost = 0;
        $totalProfit = 0;
        $totalQuantity = 0;
        
        foreach ($saleInvoices as $invoice) {
            // Process general items
            foreach ($invoice->generalLines as $line) {
                // Calculate cost based on CURRENT stock ledger entries
                // Get the net quantity and cost from stock ledger for this invoice
                $stockEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                    ->where('reference_no', $invoice->invoice_number)
                    ->where('transaction_type', 'sale')
                    ->where('quantity', '<', 0)
                    ->get();
                
                // Get reversal entries
                $reversalEntries = GeneralItemStockLedger::where('general_item_id', $line->general_item_id)
                    ->where('reference_no', 'like', $invoice->invoice_number . '%')
                    ->where('transaction_type', 'reversal')
                    ->where('quantity', '>', 0)
                    ->get();
                
                // Calculate NET quantity and cost from ledger
                $netQuantitySold = 0;
                $netTotalCost = 0;
                
                // Sum up sale entries (negative quantities)
                foreach ($stockEntries as $entry) {
                    $netQuantitySold += abs($entry->quantity); // Convert to positive
                    $netTotalCost += abs($entry->total_cost);
                }
                
                // Subtract reversal entries (they restore inventory)
                foreach ($reversalEntries as $reversal) {
                    $netQuantitySold -= $reversal->quantity;
                    $netTotalCost -= $reversal->total_cost;
                }
                
                // Ensure values are non-negative
                $netQuantitySold = max(0, $netQuantitySold);
                $netTotalCost = max(0, $netTotalCost);
                
                // Calculate unit cost based on NET values from ledger
                $unitCost = $netQuantitySold > 0 ? ($netTotalCost / $netQuantitySold) : 0;
                
                // Calculate total cost based on CURRENT line quantity and unit cost
                // This ensures consistency: quantity × unit_cost = total_cost
                $lineTotalCost = $line->quantity * $unitCost;
                
                // Calculate sales
                $totalSalesAmount = $line->quantity * $line->sale_price;
                
                // Calculate profit/loss
                $profitLoss = $totalSalesAmount - $lineTotalCost;
                
                $reportData[] = [
                    'date' => $invoice->invoice_date,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'item_name' => $line->generalItem->item_name ?? 'N/A',
                    'item_type' => 'general',
                    'quantity' => $line->quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $lineTotalCost,
                    'sale_rate' => $line->sale_price,
                    'total_sales' => $totalSalesAmount,
                    'profit_loss' => $profitLoss,
                ];
                
                $totalSales += $totalSalesAmount;
                $totalCost += $lineTotalCost;
                $totalProfit += $profitLoss;
                $totalQuantity += $line->quantity;
            }
            
            // Process arms
            foreach ($invoice->armLines as $line) {
                // Get cost from arm purchase price
                $unitCost = $line->arm->purchase_price ?? 0;
                $totalCostAmount = $unitCost; // 1 unit per arm
                
                // Calculate sales
                $totalSalesAmount = $line->sale_price;
                
                // Calculate profit/loss
                $profitLoss = $totalSalesAmount - $totalCostAmount;
                
                $reportData[] = [
                    'date' => $invoice->invoice_date,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'item_name' => $line->arm->arm_title ?? 'N/A',
                    'item_type' => 'arm',
                    'quantity' => 1,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCostAmount,
                    'sale_rate' => $line->sale_price,
                    'total_sales' => $totalSalesAmount,
                    'profit_loss' => $profitLoss,
                ];
                
                $totalSales += $totalSalesAmount;
                $totalCost += $totalCostAmount;
                $totalProfit += $profitLoss;
                $totalQuantity += 1; // Arms are always quantity 1
            }
        }
        
        // Calculate weighted averages for display
        $avgUnitCost = $totalQuantity > 0 ? ($totalCost / $totalQuantity) : 0;
        $avgSaleRate = $totalQuantity > 0 ? ($totalSales / $totalQuantity) : 0;
        
        // Get business info
        $business = \App\Models\Business::find($businessId);
        
        return view('sale_invoices.profit-loss-report', compact(
            'reportData',
            'totalSales',
            'totalCost',
            'totalProfit',
            'totalQuantity',
            'avgUnitCost',
            'avgSaleRate',
            'business',
            'invoiceNumber',
            'fromDate',
            'toDate'
        ));
    }

}