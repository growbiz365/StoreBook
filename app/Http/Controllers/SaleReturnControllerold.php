<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
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
use App\Models\SaleReturnAuditLog;
use App\Models\PartyLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SaleReturnController extends Controller
{
    /**
     * Display a listing of sale returns.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = SaleReturn::with(['party', 'bank', 'createdBy', 'generalLines', 'armLines'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('party_id', $request->customer);
        }

        if ($request->filled('return_type')) {
            $query->where('return_type', $request->return_type);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('return_date', [$request->date_from, $request->date_to]);
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

        if (in_array($sortBy, ['id', 'return_date', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $saleReturns = $query->paginate(15)->withQueryString();

        // Get customers for filter dropdown
        $customers = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        return view('sale_returns.index', compact('saleReturns', 'customers'));
    }

    /**
     * Show the form for creating a new sale return.
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

        // Show all general items for sale returns
        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Add current stock to each item (for display purposes)
        foreach ($generalItems as $item) {
            $item->available_stock = GeneralItemStockLedger::getCurrentBalance($item->id);
        }

        // Arms data loading disabled - StoreBook is items-only
        // $arms = Arm::where('business_id', $businessId)
        //     ->where('status', 'sold') // Only show sold arms for return
        //     ->orderBy('serial_no')
        //     ->get();

        // Empty collection for arms data to prevent errors in views
        $arms = collect();

        // Get recent sale invoices for reference
        $saleInvoices = SaleInvoice::where('business_id', $businessId)
            ->where('status', 'posted')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('sale_returns.create', compact('customers', 'banks', 'generalItems', 'arms', 'saleInvoices'));
    }

    /**
     * Store a newly created sale return in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            DB::beginTransaction();

            Log::info('Sale return creation request', [
                'request_data' => $request->all(),
                'has_general_lines' => $request->has('general_lines'),
                'has_arm_lines' => $request->has('arm_lines'),
                'general_lines_count' => $request->has('general_lines') ? count($request->general_lines) : 0,
                'arm_lines_count' => $request->has('arm_lines') ? count($request->arm_lines) : 0,
            ]);

            // Validate main sale return data
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:return_type,credit|exists:parties,id',
                'return_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:return_type,cash|exists:banks,id',
                'original_sale_invoice_id' => 'nullable|exists:sale_invoices,id',
                'return_date' => 'required|date',
                'shipping_charges' => 'nullable|numeric|min:0',
                'reason' => 'nullable|string|max:1000',
                'action' => 'required|in:save,post',

                // Customer details validation (for cash returns)
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
                Log::error('Sale return validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                return back()->withErrors($validator)->withInput();
            }

        // Note: For sale returns, we don't validate against stock availability
        // since returns can be for any items regardless of current stock

            // Validate arm availability
            if ($request->has('arm_lines')) {
                $armValidationErrors = $this->validateArmAvailability($request->arm_lines, $businessId);
                if (!empty($armValidationErrors)) {
                    return back()->withErrors(['arms' => $armValidationErrors])->withInput();
                }
            }

            // Check if at least one line item is provided
            $hasGeneralLines = $request->has('general_lines') && !empty($request->general_lines);
            $hasArmLines = $request->has('arm_lines') && !empty($request->arm_lines);

            if (!$hasGeneralLines && !$hasArmLines) {
                return back()->withErrors(['general_lines' => 'At least one item (general item or arm) must be added to the return.'])->withInput();
            }

            // Create sale return
            $saleReturn = SaleReturn::create([
                'business_id' => $businessId,
                'party_id' => $request->party_id ?: null,
                'return_type' => $request->return_type,
                'bank_id' => $request->bank_id,
                'original_sale_invoice_id' => $request->original_sale_invoice_id,
                'return_date' => $request->return_date,
                'shipping_charges' => $request->shipping_charges ?? 0,
                'reason' => $request->reason,
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
                    $saleReturn->generalLines()->create([
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'return_price' => $line['sale_price'],
                    ]);
                }
            }

            // Create arm lines
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $line) {
                    $saleReturn->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'return_price' => $line['sale_price'],
                    ]);
                }
            }

            // Load relationships and calculate totals
            $saleReturn->load(['generalLines', 'armLines']);
            $saleReturn->calculateTotals();
            $saleReturn->save();

            // Create audit log
            SaleReturnAuditLog::create([
                'sale_return_id' => $saleReturn->id,
                'action' => 'created',
                'new_values' => $saleReturn->toArray(),
                'user_id' => $userId,
            ]);

            // If action is post, post the sale return
            if ($request->action === 'post') {
                $this->postSaleReturn($saleReturn);
            }

            DB::commit();

            // Clear any old input data from session
            $request->session()->forget('_old_input');

            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('success', 'Sale return created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale return creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to create sale return. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified sale return.
     */
    public function show(SaleReturn $saleReturn)
    {
        $saleReturn->load([
            'party',
            'bank',
            'createdBy',
            'generalLines.generalItem',
            'armLines.arm.armMake',
            'armLines.arm.armType',
            'armLines.arm.armCategory',
            'armLines.arm.armCondition',
            'armLines.arm.armCaliber',
            'originalSaleInvoice'
        ]);

        return view('sale_returns.show', compact('saleReturn'));
    }

    /**
     * Show the form for editing the specified sale return.
     */
    public function edit(SaleReturn $saleReturn)
    {
        if (!$saleReturn->canBeEdited()) {
            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('error', 'This sale return cannot be edited.');
        }

        $businessId = session('active_business');

        $customers = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->orderBy('account_name')
            ->get();

        // Load relationships with explicit eager loading
        $saleReturn->load([
            'generalLines.generalItem',
            'armLines.arm' => function ($query) {
                $query->with(['armMake', 'armType', 'armCategory', 'armCondition', 'armCaliber']);
            },
            'originalSaleInvoice.generalLines'
        ]);

        // Calculate correct available stock for edit form
        // For sale return edit: available stock = originally sold quantity
        if ($saleReturn->originalSaleInvoice) {
            foreach ($saleReturn->generalLines as $line) {
                $originalLine = $saleReturn->originalSaleInvoice->generalLines->where('general_item_id', $line->general_item_id)->first();
                if ($originalLine) {
                    $line->generalItem->available_stock = $originalLine->quantity;
                }
            }
        }

        // Ensure relationships are loaded and accessible
        $saleReturn->armLines->load('arm.armMake', 'arm.armType', 'arm.armCategory', 'arm.armCondition', 'arm.armCaliber');

        // Prepare arm lines data with explicit relationship data for JavaScript
        $armLinesData = $saleReturn->armLines->map(function ($line) {
            return [
                'id' => $line->id,
                'sale_return_id' => $line->sale_return_id,
                'arm_id' => $line->arm_id,
                'return_price' => $line->return_price,
                'line_total' => $line->line_total,
                'deleted_by' => $line->deleted_by,
                'deleted_at' => $line->deleted_at,
                'created_at' => $line->created_at,
                'updated_at' => $line->updated_at,
                'arm' => [
                    'id' => $line->arm->id,
                    'business_id' => $line->arm->business_id,
                    'arm_type_id' => $line->arm->arm_type_id,
                    'arm_category_id' => $line->arm->arm_category_id,
                    'make' => $line->arm->make,
                    'arm_caliber_id' => $line->arm->arm_caliber_id,
                    'arm_condition_id' => $line->arm->arm_condition_id,
                    'serial_no' => $line->arm->serial_no,
                    'purchase_price' => $line->arm->purchase_price,
                    'sale_price' => $line->arm->sale_price,
                    'purchase_date' => $line->arm->purchase_date,
                    'status' => $line->arm->status,
                    'notes' => $line->arm->notes,
                    'arm_title' => $line->arm->arm_title,
                    'purchase_id' => $line->arm->purchase_id,
                    'purchase_arm_serial_id' => $line->arm->purchase_arm_serial_id,
                    'armMake' => $line->arm->armMake ? [
                        'id' => $line->arm->armMake->id,
                        'arm_make' => $line->arm->armMake->arm_make,
                        'business_id' => $line->arm->armMake->business_id,
                        'status' => $line->arm->armMake->status,
                        'created_at' => $line->arm->armMake->created_at,
                        'updated_at' => $line->arm->armMake->updated_at,
                    ] : null,
                    'armType' => $line->arm->armType ? [
                        'id' => $line->arm->armType->id,
                        'arm_type' => $line->arm->armType->arm_type,
                        'business_id' => $line->arm->armType->business_id,
                        'status' => $line->arm->armType->status,
                        'created_at' => $line->arm->armType->created_at,
                        'updated_at' => $line->arm->armType->updated_at,
                    ] : null,
                    'armCaliber' => $line->arm->armCaliber ? [
                        'id' => $line->arm->armCaliber->id,
                        'arm_caliber' => $line->arm->armCaliber->arm_caliber,
                        'business_id' => $line->arm->armCaliber->business_id,
                        'status' => $line->arm->armCaliber->status,
                        'created_at' => $line->arm->armCaliber->created_at,
                        'updated_at' => $line->arm->armCaliber->updated_at,
                    ] : null,
                ]
            ];
        });

        return view('sale_returns.edit', compact('saleReturn', 'customers', 'banks', 'armLinesData'));
    }

    /**
     * Update the specified sale return in storage.
     */
    public function update(Request $request, SaleReturn $saleReturn)
    {
        if (!$saleReturn->canBeEdited()) {
            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('error', 'This sale return cannot be edited.');
        }

        $userId = auth()->id();
        $wasPosted = $saleReturn->isPosted();

        try {
            DB::beginTransaction();

            \Log::info('SaleReturn update initiated', [
                'sale_return_id' => $saleReturn->id,
                'was_posted' => $wasPosted,
                'party_id' => $request->party_id,
                'return_type' => $request->return_type,
                'bank_id' => $request->bank_id,
                'return_date' => $request->return_date,
                'general_lines_count' => is_array($request->input('general_lines')) ? count($request->input('general_lines')) : 0,
                'arm_lines_count' => is_array($request->input('arm_lines')) ? count($request->input('arm_lines')) : 0,
            ]);

            // Log a compact view of payload (avoid huge arrays)
            \Log::debug('SaleReturn update payload snapshot', [
                'keys' => array_keys($request->all()),
            ]);

            // Store old values for audit log
            $oldValues = $saleReturn->toArray();

            // Validate request
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:return_type,credit|exists:parties,id',
                'return_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:return_type,cash|exists:banks,id',
                'original_sale_invoice_id' => 'nullable|exists:sale_invoices,id',
                'return_date' => 'required|date|before_or_equal:today',
                'shipping_charges' => 'nullable|numeric|min:0',
                'reason' => 'nullable|string|max:1000',

                // Customer details validation (for cash returns)
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
                \Log::warning('SaleReturn update validation failed', [
                    'sale_return_id' => $saleReturn->id,
                    'errors' => $validator->errors()->toArray(),
                ]);
                return back()->withErrors($validator)->withInput();
            }

        // Note: For sale returns, we don't validate against stock availability
        // since returns can be for any items regardless of current stock

            // Validate arm availability
            if ($request->has('arm_lines')) {
                \Log::info('SaleReturn update validating arm availability', [
                    'sale_return_id' => $saleReturn->id,
                    'arm_lines_count' => is_array($request->arm_lines) ? count($request->arm_lines) : 0,
                ]);
                // Allow arms already on this sale return (they may now be available due to posting)
                $existingArmIds = $saleReturn->armLines()->pluck('arm_id')->toArray();
                $armValidationErrors = $this->validateArmAvailability($request->arm_lines, $saleReturn->business_id, $existingArmIds);
                if (!empty($armValidationErrors)) {
                    \Log::warning('SaleReturn update arm availability failed', [
                        'sale_return_id' => $saleReturn->id,
                        'errors' => $armValidationErrors,
                    ]);
                    return back()->withErrors(['arms' => $armValidationErrors])->withInput();
                }
            }

            // Prepare return data
            $returnData = [
                'party_id' => $request->party_id,
                'return_type' => $request->return_type,
                'bank_id' => $request->bank_id,
                'original_sale_invoice_id' => $request->original_sale_invoice_id,
                'return_date' => $request->return_date,
                'shipping_charges' => $request->shipping_charges ?? 0,
                'reason' => $request->reason,
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

            \Log::info('SaleReturn update prepared data', [
                'sale_return_id' => $saleReturn->id,
                'return_type' => $returnData['return_type'],
                'party_id' => $returnData['party_id'],
                'bank_id' => $returnData['bank_id'],
                'return_date' => $returnData['return_date'],
                'shipping_charges' => $returnData['shipping_charges'],
            ]);

            // Use enhanced edit method
            $saleReturn->performEnhancedEdit(
                $request->general_lines ?? [],
                $request->arm_lines ?? [],
                $returnData
            );

            // Update audit log with old values
            SaleReturnAuditLog::where('sale_return_id', $saleReturn->id)
                ->where('action', 'enhanced_edit')
                ->latest()
                ->first()
                ->update(['old_values' => $oldValues]);

            DB::commit();

            $message = $wasPosted
                ? 'Sale return updated successfully. Inventory has been adjusted and the return remains posted.'
                : 'Sale return updated successfully.';

            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale return update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update sale return. Please try again.'])->withInput();
        }
    }

    /**
     * Post the sale return.
     */
    public function post(SaleReturn $saleReturn)
    {
        if (!$saleReturn->canBePosted()) {
            return back()->with('error', 'This sale return cannot be posted.');
        }

        try {
            DB::beginTransaction();

            $this->postSaleReturn($saleReturn);

            DB::commit();

            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('success', 'Sale return posted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale return posting failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to post sale return. Please try again.');
        }
    }

    /**
     * Cancel the sale return.
     */
    public function cancel(SaleReturn $saleReturn)
    {
        if (!$saleReturn->canBeCancelled()) {
            return back()->with('error', 'This sale return cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Reverse inventory impacts
            $saleReturn->reverseInventoryImpacts();

            // Reverse journal entries
            $saleReturn->reverseJournalEntries();

            // Update status
            $saleReturn->update([
                'status' => 'cancelled',
                'cancelled_by' => auth()->id()
            ]);

            // Create audit log
            SaleReturnAuditLog::create([
                'sale_return_id' => $saleReturn->id,
                'action' => 'cancelled',
                'old_values' => ['status' => 'posted'],
                'new_values' => ['status' => 'cancelled'],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('success', 'Sale return cancelled successfully and inventory has been restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale return cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel sale return. Please try again.');
        }
    }

    /**
     * Show the audit log for the sale return.
     */
    public function auditLog(SaleReturn $saleReturn)
    {
        $auditLogs = $saleReturn->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sale_returns.audit-log', compact('saleReturn', 'auditLogs'));
    }

    /**
     * Soft delete the specified sale return.
     */
    public function destroy(SaleReturn $saleReturn)
    {
        if (!$saleReturn->canBeDeleted()) {
            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('error', 'This sale return cannot be deleted.');
        }

        try {
            DB::beginTransaction();

            // Use enhanced delete method
            $saleReturn->performSoftDelete();

            DB::commit();

            return redirect()->route('sale-returns.index')
                ->with('success', 'Sale return deleted successfully. All inventory has been restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale return deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete sale return. Please try again.']);
        }
    }

    /**
     * Restore a soft-deleted sale return.
     */
    public function restore($id)
    {
        $saleReturn = SaleReturn::withTrashed()->findOrFail($id);

        if (!$saleReturn->trashed()) {
            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('error', 'This sale return is not deleted.');
        }

        try {
            DB::beginTransaction();

            // Restore the return
            $saleReturn->restore();
            $saleReturn->update(['deleted_by' => null]);

            // Restore child records
            $saleReturn->generalLines()->withTrashed()->restore();
            $saleReturn->armLines()->withTrashed()->restore();
            $saleReturn->auditLogs()->withTrashed()->restore();

            // Update deleted_by to null for child records
            $saleReturn->generalLines()->update(['deleted_by' => null]);
            $saleReturn->armLines()->update(['deleted_by' => null]);
            $saleReturn->auditLogs()->update(['deleted_by' => null]);

            // Create audit log for restoration
            SaleReturnAuditLog::create([
                'sale_return_id' => $saleReturn->id,
                'action' => 'restored',
                'old_values' => ['deleted_at' => $saleReturn->deleted_at],
                'new_values' => ['deleted_at' => null, 'deleted_by' => null],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('success', 'Sale return restored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale return restoration failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to restore sale return. Please try again.']);
        }
    }

    /**
     * Permanently delete a soft-deleted sale return.
     */
    public function forceDelete($id)
    {
        $saleReturn = SaleReturn::withTrashed()->findOrFail($id);

        if (!$saleReturn->trashed()) {
            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('error', 'This sale return is not deleted.');
        }

        try {
            DB::beginTransaction();

            // Permanently delete child records first
            $saleReturn->generalLines()->withTrashed()->forceDelete();
            $saleReturn->armLines()->withTrashed()->forceDelete();
            $saleReturn->auditLogs()->withTrashed()->forceDelete();

            // Permanently delete the main return
            $saleReturn->forceDelete();

            DB::commit();

            return redirect()->route('sale-returns.index')
                ->with('success', 'Sale return permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale return permanent deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to permanently delete sale return. Please try again.']);
        }
    }

    /**
     * Post sale return and create all related entries.
     */
    private function postSaleReturn(SaleReturn $saleReturn)
    {
        try {
            $businessId = $saleReturn->business_id;
            $userId = auth()->id();

            // Update status
            $saleReturn->update([
                'status' => 'posted',
                'posted_by' => $userId
            ]);

            // Load fresh relationships to ensure we have the current data
            $saleReturn->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm']);

            // Create stock ledger entries for general items (restore inventory)
            foreach ($saleReturn->generalLines as $line) {
                // Find the batch that was originally consumed (if available)
                $batch = GeneralBatch::where('item_id', $line->general_item_id)
                    ->where('qty_remaining', '>=', 0)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$batch) {
                    \Log::warning('No batch found for general item in sale return', [
                        'sale_return_id' => $saleReturn->id,
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
                    'transaction_date' => $saleReturn->return_date,
                    'quantity' => $line->quantity, // Positive quantity to restore stock
                    'quantity_out' => 0,
                    'balance_quantity' => $batch->qty_remaining + $line->quantity,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $line->quantity * $batch->unit_cost,
                    'reference_id' => $saleReturn->id,
                    'reference_no' => $saleReturn->return_number,
                    'remarks' => 'Return from ' . ($saleReturn->party->name ?? 'Customer'),
                    'created_by' => $userId,
                ]);

                // Restore batch remaining quantity
                $batch->increment('qty_remaining', $line->quantity);

                // Update line with batch information
                $line->update(['batch_id' => $batch->id]);
            }

            // Create stock ledger entries for arms (restore arm status)
            foreach ($saleReturn->armLines as $line) {
                // Store old values for history
                $oldValues = $line->arm->toArray();

                // Create arms stock ledger entry
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $line->arm_id,
                    'transaction_date' => $saleReturn->return_date,
                    'transaction_type' => 'return',
                    'quantity_out' => 0,
                    'balance' => 1, // Restore arm to available
                    'reference_id' => $saleReturn->return_number,
                    'remarks' => 'Return from ' . ($saleReturn->party->name ?? 'Customer'),
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
                    'transaction_date' => $saleReturn->return_date,
                    'price' => $line->return_price,
                    'remarks' => 'Return from ' . ($saleReturn->party->name ?? 'Customer'),
                    'user_id' => $userId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            // Create party ledger entry for credit returns (reduce receivable -> credit)
            if ($saleReturn->return_type === 'credit' && $saleReturn->party_id) {
                PartyLedger::create([
                    'business_id' => $saleReturn->business_id,
                    'party_id' => $saleReturn->party_id,
                    'voucher_id' => $saleReturn->id,
                    'voucher_type' => 'Sale Return',
                    'date_added' => $saleReturn->return_date,
                    'user_id' => $userId,
                    'debit_amount' => 0,
                    'credit_amount' => $saleReturn->total_amount,
                ]);
            }

            // Create journal entries
            $this->createJournalEntries($saleReturn);

            // Create audit log
            SaleReturnAuditLog::create([
                'sale_return_id' => $saleReturn->id,
                'action' => 'posted',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'posted'],
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error posting sale return: ' . $e->getMessage(), [
                'sale_return_id' => $saleReturn->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }

    /**
     * Create journal entries for the sale return.
     */
    private function createJournalEntries(SaleReturn $saleReturn)
    {
        $businessId = $saleReturn->business_id;
        $userId = auth()->id();

        // Get party's chart of account for credit returns (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($saleReturn->return_type === 'credit' && $saleReturn->party_id) {
            $party = \App\Models\Party::find($saleReturn->party_id);
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
            \Log::warning('Missing required chart of accounts for sale return posting', [
                'sale_return_id' => $saleReturn->id,
                'sales_revenue_id' => $salesRevenueId,
                'cogs_id' => $cogsId,
                'inventory_id' => $inventoryId
            ]);
            return; // Skip journal entries but continue with other posting operations
        }

        // Entry 1: Credit Party Account (for credit returns) / Credit Bank (for cash returns)
        if ($saleReturn->return_type === 'credit') {
            // Credit return - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit sale returns.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => 0,
                'credit_amount' => $saleReturn->total_amount, // Credit to reduce party receivable
                'voucher_id' => $saleReturn->id,
                'voucher_type' => 'SaleReturn',
                'comments' => 'Sale Return ' . $saleReturn->return_number,
                'user_id' => $userId,
                'date_added' => $saleReturn->return_date,
            ]);
        } else {
            // Cash return - debit bank account (customer pays us back)
            if ($saleReturn->bank_id) {
                $bank = \App\Models\Bank::find($saleReturn->bank_id);
                if ($bank && $bank->chart_of_account_id) {
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $bank->chart_of_account_id,
                        'debit_amount' => $saleReturn->total_amount,
                        'credit_amount' => 0,
                        'voucher_id' => $saleReturn->id,
                        'voucher_type' => 'SaleReturn',
                        'comments' => 'Sale Return ' . $saleReturn->return_number,
                        'user_id' => $userId,
                        'date_added' => $saleReturn->return_date,
                    ]);

                    // Create bank ledger entry for cash return
                    \App\Models\BankLedger::create([
                        'business_id' => $businessId,
                        'bank_id' => $saleReturn->bank_id,
                        'voucher_id' => $saleReturn->id,
                        'voucher_type' => 'SaleReturn',
                        'date' => $saleReturn->return_date,
                        'user_id' => $userId,
                        'withdrawal_amount' => $saleReturn->total_amount, // Money going out (customer refund)
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
            'debit_amount' => $saleReturn->total_amount, // Debit to reduce income
            'credit_amount' => 0,
            'voucher_id' => $saleReturn->id,
            'voucher_type' => 'SaleReturn',
            'comments' => 'Sale Return ' . $saleReturn->return_number,
            'user_id' => $userId,
            'date_added' => $saleReturn->return_date,
        ]);

        // Calculate total COGS for all items (general items + arms)
        $totalCogs = 0;

        // Calculate COGS for general items
        // IMPORTANT: Use FIFO costs from original sale invoice stock ledger entries
        foreach ($saleReturn->generalLines as $line) {
            $cogsAmount = 0;
            
            // Method 1: Try to find the original sale invoice stock ledger entries
            if ($saleReturn->original_sale_invoice_id) {
                $originalSaleInvoice = \App\Models\SaleInvoice::find($saleReturn->original_sale_invoice_id);
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
                    ->where('transaction_date', '>=', \Carbon\Carbon::parse($saleReturn->return_date)->subDays(90)) // Within 90 days
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
        foreach ($saleReturn->armLines as $line) {
            $cogsAmount = $line->arm->purchase_price ?? 0;
            $totalCogs += $cogsAmount;
        }

        // Create single summarized COGS and Inventory journal entries
        if ($totalCogs > 0) {
            // Credit COGS (REVERSE the original debit from sale)
            // Original sale: Debit COGS, so reversal: Credit COGS
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $cogsId,
                'debit_amount' => 0,
                'credit_amount' => $totalCogs,
                    'voucher_id' => $saleReturn->id,
                    'voucher_type' => 'SaleReturn',
                'comments' => 'COGS Reversal',
                    'user_id' => $userId,
                    'date_added' => $saleReturn->return_date,
                ]);

            // Debit Inventory (REVERSE the original credit from sale)
            // Original sale: Credit Inventory, so reversal: Debit Inventory
                JournalEntry::create([
                    'business_id' => $businessId,
                    'account_head' => $inventoryId,
                'debit_amount' => $totalCogs,
                'credit_amount' => 0,
                    'voucher_id' => $saleReturn->id,
                    'voucher_type' => 'SaleReturn',
                'comments' => 'Inventory Restoration',
                    'user_id' => $userId,
                    'date_added' => $saleReturn->return_date,
                ]);
        }
    }

    /**
     * Validate stock availability for general items in sale returns.
     * For sale returns: check against original sale invoice quantities
     */
    private function validateStockAvailability(array $generalLines, int $businessId, ?SaleReturn $saleReturn = null): array
    {
        $errors = [];

        foreach ($generalLines as $index => $line) {
            $itemId = $line['general_item_id'];
            $requiredQty = (float) $line['qty'];

            // Get the original sale invoice to check how much was originally sold
            $originalSaleInvoice = null;
            if ($saleReturn && $saleReturn->originalSaleInvoice) {
                $originalSaleInvoice = $saleReturn->originalSaleInvoice;
            } elseif (request()->has('original_sale_invoice_id')) {
                $originalSaleInvoice = SaleInvoice::with('generalLines')->find(request('original_sale_invoice_id'));
            }

            if (!$originalSaleInvoice) {
                $errors[] = "Original sale invoice not found for validation.";
                continue;
            }

            // Find the original quantity sold for this item
            $originalLine = $originalSaleInvoice->generalLines->where('general_item_id', $itemId)->first();
            if (!$originalLine) {
                $item = GeneralItem::find($itemId);
                $itemName = $item ? $item->item_name : 'Unknown Item';
                $errors[] = "Item '{$itemName}' was not sold in the original sale invoice.";
                continue;
            }

            $originallySoldQty = $originalLine->quantity;

            // Check if we're editing an existing return
            if ($saleReturn && $saleReturn->exists) {
                // For edit: check against originally sold quantity
                $availableQty = $originallySoldQty;
            } else {
                // For create: check against originally sold quantity
                $availableQty = $originallySoldQty;
            }

            if ($requiredQty > $availableQty) {
                $item = GeneralItem::find($itemId);
                $itemName = $item ? $item->item_name : 'Unknown Item';
                
                $errors[] = "Cannot return more than originally sold for '{$itemName}'. Originally sold: {$originallySoldQty}, Trying to return: {$requiredQty}";
            }
        }

        return $errors;
    }

    /**
     * Validate arm availability.
     */
    private function validateArmAvailability(array $armLines, int $businessId, array $whitelistedArmIds = []): array
    {
        $errors = [];

        foreach ($armLines as $index => $line) {
            $armId = $line['arm_id'];
            
            $arm = Arm::where('business_id', $businessId)
                ->where('id', $armId)
                ->first();

            if (!$arm) {
                $errors[] = "Arm with ID {$armId} not found.";
                continue;
            }

            // If the arm is already on this sale return, allow it even if currently available
            if (!empty($whitelistedArmIds) && in_array($arm->id, $whitelistedArmIds, true)) {
                continue;
            }

            if ($arm->status !== 'sold') {
                $errors[] = "Arm '{$arm->arm_title}' (Serial: {$arm->serial_no}) cannot be returned. Only sold arms can be returned. Current status: " . ucfirst(str_replace('_', ' ', $arm->status));
            }
        }

        return $errors;
    }
}
