<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\Purchase;
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
use App\Models\PurchaseReturnAuditLog;
use App\Models\PartyLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of purchase returns.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = PurchaseReturn::with(['party', 'bank', 'createdBy', 'generalLines', 'armLines'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor')) {
            $query->where('party_id', $request->vendor);
        }

        if ($request->filled('return_type')) {
            $query->where('return_type', $request->return_type);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('return_date', [$request->date_from, $request->date_to]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhereHas('party', function($q) use ($search) {
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

        $purchaseReturns = $query->paginate(15)->withQueryString();
        
        // Get vendors for filter dropdown
        $vendors = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();
        
        return view('purchase_returns.index', compact('purchaseReturns', 'vendors'));
    }

    /**
     * Show the form for creating a new purchase return.
     */
    public function create()
    {
        $businessId = session('active_business');
        
        $vendors = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->orderBy('account_name')
            ->get();

        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Arms data loading disabled - StoreBook is items-only
        // $arms = Arm::where('business_id', $businessId)
        //     ->where('status', 'available') // Only show available arms for return
        //     ->orderBy('serial_no')
        //     ->get();

        // Empty collection for arms data to prevent errors in views
        $arms = collect();

        // Get recent purchases for reference
        $purchases = Purchase::where('business_id', $businessId)
            ->where('status', 'posted')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('purchase_returns.create', compact('vendors', 'banks', 'generalItems', 'arms', 'purchases'));
    }

    /**
     * Store a newly created purchase return in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            DB::beginTransaction();

            Log::info('Purchase return creation request', [
                'request_data' => $request->all(),
                'has_general_lines' => $request->has('general_lines'),
                'has_arm_lines' => $request->has('arm_lines'),
                'general_lines_count' => $request->has('general_lines') ? count($request->general_lines) : 0,
                'arm_lines_count' => $request->has('arm_lines') ? count($request->arm_lines) : 0,
            ]);

            // Validate main purchase return data
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:return_type,credit|exists:parties,id',
                'return_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:return_type,cash|exists:banks,id',
                'original_purchase_id' => 'nullable|exists:purchases,id',
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
                'general_lines.*.unit_price' => 'required_with:general_lines|numeric|min:0',
                
                // Arm lines validation
                'arm_lines' => 'nullable|array',
                'arm_lines.*.unit_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.arm_id' => 'required_with:arm_lines|exists:arms,id',
            ]);

            if ($validator->fails()) {
                Log::error('Purchase return validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                return back()->withErrors($validator)->withInput();
            }

            // Validate stock availability for general items (for returns)
            if ($request->has('general_lines')) {
                $stockValidationErrors = $this->validateStockAvailability($request->general_lines, $businessId);
                if (!empty($stockValidationErrors)) {
                    return back()->withErrors(['stock' => $stockValidationErrors])->withInput();
                }
            }

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

            // Create purchase return
            $purchaseReturn = PurchaseReturn::create([
                'business_id' => $businessId,
                'party_id' => $request->party_id ?: null,
                'return_type' => $request->return_type,
                'bank_id' => $request->bank_id,
                'original_purchase_id' => $request->original_purchase_id,
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
                    $purchaseReturn->generalLines()->create([
                        'general_item_id' => $line['general_item_id'],
                        'quantity' => $line['qty'],
                        'return_price' => $line['unit_price'],
                    ]);
                }
            }

            // Create arm lines
            if ($request->has('arm_lines')) {
                foreach ($request->arm_lines as $line) {
                    $purchaseReturn->armLines()->create([
                        'arm_id' => $line['arm_id'],
                        'return_price' => $line['unit_price'],
                    ]);
                }
            }

            // Load relationships and calculate totals
            $purchaseReturn->load(['generalLines', 'armLines']);
            $purchaseReturn->calculateTotals();
            $purchaseReturn->save();

            // Create audit log
            PurchaseReturnAuditLog::create([
                'purchase_return_id' => $purchaseReturn->id,
                'action' => 'created',
                'new_values' => $purchaseReturn->toArray(),
                'user_id' => $userId,
            ]);

            // If action is post, post the purchase return
            if ($request->action === 'post') {
                $this->postPurchaseReturn($purchaseReturn);
            }

            DB::commit();

            // Clear any old input data from session
            $request->session()->forget('_old_input');

            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('success', 'Purchase return created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase return creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to create purchase return. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified purchase return.
     */
    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load([
            'party', 
            'bank', 
            'createdBy', 
            'generalLines.generalItem', 
            'armLines.arm.armMake', 
            'armLines.arm.armType', 
            'armLines.arm.armCategory', 
            'armLines.arm.armCondition', 
            'armLines.arm.armCaliber',
            'originalPurchase'
        ]);

        return view('purchase_returns.show', compact('purchaseReturn'));
    }

    /**
     * Show the form for editing the specified purchase return.
     */
    public function edit(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->canBeEdited()) {
            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('error', 'This purchase return cannot be edited.');
        }

        $businessId = session('active_business');
        
        $vendors = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->orderBy('account_name')
            ->get();

        // Load relationships with explicit eager loading
        $purchaseReturn->load([
            'generalLines.generalItem', 
            'armLines.arm' => function($query) {
                $query->with(['armMake', 'armType', 'armCategory', 'armCondition', 'armCaliber']);
            }
        ]);

        // Ensure relationships are loaded and accessible
        $purchaseReturn->armLines->load('arm.armMake', 'arm.armType', 'arm.armCategory', 'arm.armCondition', 'arm.armCaliber');

        // Prepare arm lines data with explicit relationship data for JavaScript
        $armLinesData = $purchaseReturn->armLines->map(function($line) {
            return [
                'id' => $line->id,
                'purchase_return_id' => $line->purchase_return_id,
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

        return view('purchase_returns.edit', compact('purchaseReturn', 'vendors', 'banks', 'armLinesData'));
    }

    /**
     * Update the specified purchase return in storage.
     */
    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->canBeEdited()) {
            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('error', 'This purchase return cannot be edited.');
        }

        $userId = auth()->id();
        $wasPosted = $purchaseReturn->isPosted();

        try {
            DB::beginTransaction();

            // Store old values for audit log
            $oldValues = $purchaseReturn->toArray();

            // Validate request
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:return_type,credit|exists:parties,id',
                'return_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:return_type,cash|exists:banks,id',
                'original_purchase_id' => 'nullable|exists:purchases,id',
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
                'general_lines.*.unit_price' => 'required_with:general_lines|numeric|min:0',
                
                // Arm lines validation
                'arm_lines' => 'nullable|array',
                'arm_lines.*.unit_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.arm_id' => 'required_with:arm_lines|exists:arms,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Prepare return data
            $returnData = [
                'party_id' => $request->party_id,
                'return_type' => $request->return_type,
                'bank_id' => $request->bank_id,
                'original_purchase_id' => $request->original_purchase_id,
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

            // Use enhanced edit method
            $purchaseReturn->performEnhancedEdit(
                $request->general_lines ?? [],
                $request->arm_lines ?? [],
                $returnData
            );

            // Update audit log with old values
            PurchaseReturnAuditLog::where('purchase_return_id', $purchaseReturn->id)
                ->where('action', 'enhanced_edit')
                ->latest()
                ->first()
                ->update(['old_values' => $oldValues]);

            DB::commit();

            $message = $wasPosted 
                ? 'Purchase return updated successfully. Inventory has been adjusted and the return remains posted.'
                : 'Purchase return updated successfully.';

            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase return update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update purchase return. Please try again.'])->withInput();
        }
    }

    /**
     * Post the purchase return.
     */
    public function post(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->canBePosted()) {
            return back()->with('error', 'This purchase return cannot be posted.');
        }

        try {
            DB::beginTransaction();

            $this->postPurchaseReturn($purchaseReturn);

            DB::commit();

            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('success', 'Purchase return posted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase return posting failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to post purchase return. Please try again.');
        }
    }

    /**
     * Cancel the purchase return.
     */
    public function cancel(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->canBeCancelled()) {
            return back()->with('error', 'This purchase return cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Reverse inventory impacts
            $purchaseReturn->reverseInventoryImpacts();

            // Reverse journal entries
            $purchaseReturn->reverseJournalEntries();

            // Update status
            $purchaseReturn->update([
                'status' => 'cancelled',
                'cancelled_by' => auth()->id()
            ]);

            // Create audit log
            PurchaseReturnAuditLog::create([
                'purchase_return_id' => $purchaseReturn->id,
                'action' => 'cancelled',
                'old_values' => ['status' => 'posted'],
                'new_values' => ['status' => 'cancelled'],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('success', 'Purchase return cancelled successfully and inventory has been restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase return cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel purchase return. Please try again.');
        }
    }

    /**
     * Show the audit log for the purchase return.
     */
    public function auditLog(PurchaseReturn $purchaseReturn)
    {
        $auditLogs = $purchaseReturn->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchase_returns.audit-log', compact('purchaseReturn', 'auditLogs'));
    }

    /**
     * Soft delete the specified purchase return.
     */
    public function destroy(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->canBeDeleted()) {
            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('error', 'This purchase return cannot be deleted.');
        }

        try {
            DB::beginTransaction();

            // Use enhanced delete method
            $purchaseReturn->performSoftDelete();

            DB::commit();

            return redirect()->route('purchase-returns.index')
                ->with('success', 'Purchase return deleted successfully. All inventory has been restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase return deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete purchase return. Please try again.']);
        }
    }

    /**
     * Restore a soft-deleted purchase return.
     */
    public function restore($id)
    {
        $purchaseReturn = PurchaseReturn::withTrashed()->findOrFail($id);
        
        if (!$purchaseReturn->trashed()) {
            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('error', 'This purchase return is not deleted.');
        }

        try {
            DB::beginTransaction();

            // Restore the return
            $purchaseReturn->restore();
            $purchaseReturn->update(['deleted_by' => null]);

            // Restore child records
            $purchaseReturn->generalLines()->withTrashed()->restore();
            $purchaseReturn->armLines()->withTrashed()->restore();
            $purchaseReturn->auditLogs()->withTrashed()->restore();

            // Update deleted_by to null for child records
            $purchaseReturn->generalLines()->update(['deleted_by' => null]);
            $purchaseReturn->armLines()->update(['deleted_by' => null]);
            $purchaseReturn->auditLogs()->update(['deleted_by' => null]);

            // Create audit log for restoration
            PurchaseReturnAuditLog::create([
                'purchase_return_id' => $purchaseReturn->id,
                'action' => 'restored',
                'old_values' => ['deleted_at' => $purchaseReturn->deleted_at],
                'new_values' => ['deleted_at' => null, 'deleted_by' => null],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('success', 'Purchase return restored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase return restoration failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to restore purchase return. Please try again.']);
        }
    }

    /**
     * Permanently delete a soft-deleted purchase return.
     */
    public function forceDelete($id)
    {
        $purchaseReturn = PurchaseReturn::withTrashed()->findOrFail($id);
        
        if (!$purchaseReturn->trashed()) {
            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('error', 'This purchase return is not deleted.');
        }

        try {
            DB::beginTransaction();

            // Permanently delete child records first
            $purchaseReturn->generalLines()->withTrashed()->forceDelete();
            $purchaseReturn->armLines()->withTrashed()->forceDelete();
            $purchaseReturn->auditLogs()->withTrashed()->forceDelete();

            // Permanently delete the main return
            $purchaseReturn->forceDelete();

            DB::commit();

            return redirect()->route('purchase-returns.index')
                ->with('success', 'Purchase return permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase return permanent deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to permanently delete purchase return. Please try again.']);
        }
    }

    /**
     * Post purchase return and create all related entries.
     */
    private function postPurchaseReturn(PurchaseReturn $purchaseReturn)
    {
        try {
            $businessId = $purchaseReturn->business_id;
            $userId = auth()->id();

            // Update status
            $purchaseReturn->update([
                'status' => 'posted',
                'posted_by' => $userId
            ]);

            // Load fresh relationships to ensure we have the current data
            $purchaseReturn->load(['generalLines.generalItem', 'generalLines.batch', 'armLines.arm']);

            // Create stock ledger entries for general items (reduce inventory)
            foreach ($purchaseReturn->generalLines as $line) {
                // Find the batch that was originally received (if available)
                $batch = GeneralBatch::where('item_id', $line->general_item_id)
                    ->where('qty_remaining', '>=', $line->quantity)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$batch) {
                    \Log::warning('No batch found for general item in purchase return', [
                        'purchase_return_id' => $purchaseReturn->id,
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
                    'transaction_date' => $purchaseReturn->return_date,
                    'quantity' => -$line->quantity, // Negative quantity to reduce stock
                    'quantity_out' => $line->quantity,
                    'balance_quantity' => $batch->qty_remaining - $line->quantity,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $line->quantity * $batch->unit_cost,
                    'reference_id' => $purchaseReturn->id,
                    'reference_no' => $purchaseReturn->return_number,
                    'remarks' => 'Return to ' . ($purchaseReturn->party->name ?? 'Vendor'),
                    'created_by' => $userId,
                ]);

                // Reduce batch remaining quantity
                $batch->decrement('qty_remaining', $line->quantity);

                // Update line with batch information
                $line->update(['batch_id' => $batch->id]);
            }

            // Create stock ledger entries for arms (change arm status)
            foreach ($purchaseReturn->armLines as $line) {
                // Store old values for history
                $oldValues = $line->arm->toArray();
                
                // Create arms stock ledger entry
                ArmsStockLedger::create([
                    'business_id' => $businessId,
                    'arm_id' => $line->arm_id,
                    'transaction_date' => $purchaseReturn->return_date,
                    'transaction_type' => 'return',
                    'quantity_out' => 1,
                    'balance' => 0, // Remove arm from available
                    'reference_id' => $purchaseReturn->return_number,
                    'remarks' => 'Return to ' . ($purchaseReturn->party->name ?? 'Vendor'),
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
                    'transaction_date' => $purchaseReturn->return_date,
                    'price' => $line->return_price,
                    'remarks' => 'Return to ' . ($purchaseReturn->party->name ?? 'Vendor'),
                    'user_id' => $userId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            // Create party ledger entry for credit returns
            if ($purchaseReturn->return_type === 'credit' && $purchaseReturn->party_id) {
                PartyLedger::create([
                    'business_id' => $purchaseReturn->business_id,
                    'party_id' => $purchaseReturn->party_id,
                    'voucher_id' => $purchaseReturn->id,
                    'voucher_type' => 'Purchase Return',
                    'date_added' => $purchaseReturn->return_date,
                    'user_id' => $userId,
                    'debit_amount' => $purchaseReturn->total_amount, // Increase what we owe vendor
                    'credit_amount' => 0,
                ]);
            }

            // Create journal entries
            $this->createJournalEntries($purchaseReturn);

            // Create audit log
            PurchaseReturnAuditLog::create([
                'purchase_return_id' => $purchaseReturn->id,
                'action' => 'posted',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'posted'],
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error posting purchase return: ' . $e->getMessage(), [
                'purchase_return_id' => $purchaseReturn->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }

    /**
     * Create journal entries for the purchase return.
     */
    private function createJournalEntries(PurchaseReturn $purchaseReturn)
    {
        $businessId = $purchaseReturn->business_id;
        $userId = auth()->id();

        // Get party's chart of account for credit returns (REQUIRED - NO FALLBACK)
        $partyAccountId = null;
        if ($purchaseReturn->return_type === 'credit' && $purchaseReturn->party_id) {
            $party = \App\Models\Party::find($purchaseReturn->party_id);
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
                'purchase_return_id' => $purchaseReturn->id,
                'accounts_payable_id' => $accountsPayableId,
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
                'purchase_return_id' => $purchaseReturn->id,
                'arms_inventory_id' => $armsInventoryId
            ]);
        }

        // Entry 1: Debit Party Account (reduce what you owe vendor) for credit returns
        if ($purchaseReturn->return_type === 'credit') {
            // Credit return - MUST use party's specific account
            if (!$partyAccountId) {
                throw new \Exception('Party chart of account is required for credit purchase returns.');
            }
            
            JournalEntry::create([
                'business_id' => $businessId,
                'account_head' => $partyAccountId,
                'debit_amount' => $purchaseReturn->total_amount,
                'credit_amount' => 0,
                'voucher_id' => $purchaseReturn->id,
                'voucher_type' => 'PurchaseReturn',
                'comments' => 'Purchase Return ' . $purchaseReturn->return_number,
                'user_id' => $userId,
                'date_added' => $purchaseReturn->return_date,
            ]);
        } else {
            // Cash return - debit bank account (you receive money back)
            if ($purchaseReturn->bank_id) {
                $bank = \App\Models\Bank::find($purchaseReturn->bank_id);
                if ($bank && $bank->chart_of_account_id) {
                    JournalEntry::create([
                        'business_id' => $businessId,
                        'account_head' => $bank->chart_of_account_id,
                        'debit_amount' => $purchaseReturn->total_amount,
                        'credit_amount' => 0,
                        'voucher_id' => $purchaseReturn->id,
                        'voucher_type' => 'PurchaseReturn',
                        'comments' => 'Purchase Return ' . $purchaseReturn->return_number,
                        'user_id' => $userId,
                        'date_added' => $purchaseReturn->return_date,
                    ]);

                    // Create bank ledger entry for cash return
                    \App\Models\BankLedger::create([
                        'business_id' => $businessId,
                        'bank_id' => $purchaseReturn->bank_id,
                        'voucher_id' => $purchaseReturn->id,
                        'voucher_type' => 'PurchaseReturn',
                        'date' => $purchaseReturn->return_date,
                        'user_id' => $userId,
                        'withdrawal_amount' => 0,
                        'deposit_amount' => $purchaseReturn->total_amount, // Money coming in (vendor refund)
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
            'credit_amount' => $purchaseReturn->total_amount,
            'voucher_id' => $purchaseReturn->id,
            'voucher_type' => 'PurchaseReturn',
            'comments' => 'Purchase Return ' . $purchaseReturn->return_number,
            'user_id' => $userId,
            'date_added' => $purchaseReturn->return_date,
        ]);

        // Note: Simple approach - just reverse the original purchase entry
        // Debit Accounts Payable, Credit Inventory Asset
    }

    /**
     * Validate stock availability for general items.
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
     * Validate arm availability.
     */
    private function validateArmAvailability(array $armLines, int $businessId): array
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

            if ($arm->status !== 'available') {
                $errors[] = "Arm '{$arm->arm_title}' (Serial: {$arm->serial_no}) is not available for return. Current status: " . ucfirst(str_replace('_', ' ', $arm->status));
            }
        }

        return $errors;
    }
}
