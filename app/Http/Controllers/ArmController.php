<?php

namespace App\Http\Controllers;

use App\Models\Arm;
use App\Models\ArmsType;
use App\Models\ArmsCategory;
use App\Models\ArmsMake;
use App\Models\ArmsCaliber;
use App\Models\ArmsCondition;
use App\Models\ArmsStockLedger;
use App\Models\ArmHistory;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Business; // Added this import for the new methods

class ArmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = Arm::with(['armType', 'armCategory', 'armCaliber', 'armCondition', 'armMake'])
            ->forBusiness($businessId)
            ->whereNotNull('purchase_id'); // Only show arms from purchases, exclude opening stock

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->byStatus($request->status);
        }

        // Apply type filter
        if ($request->filled('arm_type_id')) {
            $query->where('arm_type_id', $request->arm_type_id);
        }

        // Apply category filter
        if ($request->filled('arm_category_id')) {
            $query->where('arm_category_id', $request->arm_category_id);
        }

        // Apply make filter
        if ($request->filled('arm_make_id')) {
            $query->where('make', $request->arm_make_id);
        }

        // Apply caliber filter
        if ($request->filled('arm_caliber_id')) {
            $query->where('arm_caliber_id', $request->arm_caliber_id);
        }

        // Apply condition filter
        if ($request->filled('arm_condition_id')) {
            $query->where('arm_condition_id', $request->arm_condition_id);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['serial_no', 'make', 'purchase_price', 'purchase_date', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $arms = $query->paginate(15)->withQueryString();

        // Get filter options
        $armTypes = ArmsType::where('business_id', $businessId)->where('status', true)->get();
        $armCategories = ArmsCategory::where('business_id', $businessId)->where('status', true)->get();
        $armMakes = ArmsMake::where('business_id', $businessId)->where('status', true)->get();
        $armCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', true)->get();
        $armConditions = ArmsCondition::where('business_id', $businessId)->where('status', true)->get();

        return view('arms.index', compact('arms', 'armTypes', 'armCategories', 'armMakes', 'armCalibers', 'armConditions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        
        $armTypes = ArmsType::where('business_id', $businessId)->where('status', true)->get();
        $armCategories = ArmsCategory::where('business_id', $businessId)->where('status', true)->get();
        $armMakes = ArmsMake::where('business_id', $businessId)->where('status', true)->get();
        $armCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', true)->get();
        $armConditions = ArmsCondition::where('business_id', $businessId)->where('status', true)->get();

        return view('arms.create', compact('armTypes', 'armCategories', 'armMakes', 'armCalibers', 'armConditions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        
        // Get arm data from hidden field
        $armDataJson = $request->input('arm_data');
        $armData = json_decode($armDataJson, true);
        
        if (!$armData || !is_array($armData) || empty($armData)) {
            return redirect()->route('arms.create')->with('error', 'No arm data provided.');
        }
        
        // Validate each arm
        $validator = Validator::make(['arms' => $armData], [
            'arms' => 'required|array|min:1',
            'arms.*.arm_type_id' => 'required|exists:arms_types,id',
            'arms.*.arm_category_id' => 'required|exists:arms_categories,id',
            'arms.*.make' => 'required|string|max:255',
            'arms.*.arm_caliber_id' => 'required|exists:arms_calibers,id',
            'arms.*.arm_condition_id' => 'required|exists:arms_conditions,id',
            'arms.*.serial_no' => 'required|string|max:255|unique:arms,serial_no,NULL,id,business_id,' . $businessId,
            'arms.*.purchase_price' => 'required|numeric|min:0',
            'arms.*.sale_price' => 'required|numeric|min:0',
            'arms.*.purchase_date' => 'required|date',
            'arms.*.status' => 'required|in:available,sold,under_repair,decommissioned',
            'arms.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('arms.create')->withErrors($validator)->withInput();
        }

        $createdArms = [];
        
        DB::transaction(function () use ($armData, $businessId, $request, &$createdArms) {
            foreach ($armData as $arm) {
                // Create the arm record
                $armRecord = Arm::create([
                    'business_id' => $businessId,
                    'arm_type_id' => $arm['arm_type_id'],
                    'arm_category_id' => $arm['arm_category_id'],
                    'make' => $arm['make'],
                    'arm_caliber_id' => $arm['arm_caliber_id'],
                    'arm_condition_id' => $arm['arm_condition_id'],
                    'serial_no' => $arm['serial_no'],
                    'purchase_price' => $arm['purchase_price'],
                    'sale_price' => $arm['sale_price'],
                    'purchase_date' => $arm['purchase_date'],
                    'status' => $arm['status'],
                    'notes' => $arm['notes'],
                    'arm_title' => '', // Will be set after creation
                ]);

                // Generate and update arm title
                $armRecord->update(['arm_title' => $armRecord->generateArmTitle()]);

                // Create stock ledger entry for opening stock
                ArmsStockLedger::createEntry([
                    'business_id' => $businessId,
                    'arm_id' => $armRecord->id,
                    'transaction_date' => $arm['purchase_date'],
                    'transaction_type' => 'opening_stock',
                    'quantity_in' => 1, // Each arm is unique, so quantity is 1
                    'quantity_out' => 0,
                    'reference_id' => $armRecord->id,
                    'remarks' => 'Opening stock arm record',
                ]);

                // Create history record with audit trail
                $armRecord->history()->create([
                    'business_id' => $businessId,
                    'arm_id' => $armRecord->id,
                    'action' => 'opening',
                    'old_values' => null,
                    'new_values' => $armRecord->toArray(),
                    'transaction_date' => $arm['purchase_date'],
                    'price' => $arm['purchase_price'],
                    'remarks' => 'Opening stock arm record',
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Create journal entries for opening stock
                $this->createJournalEntriesForOpeningStock($armRecord, $arm['purchase_price'], $arm['purchase_date']);
                
                $createdArms[] = $armRecord;
            }
        });

        $count = count($createdArms);
        return redirect()->route('arms.create')->with('success', "{$count} arm(s) created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $arm = Arm::with(['armType', 'armCategory', 'armCaliber', 'armCondition', 'armMake', 'history.user', 'stockLedger'])
            ->forBusiness($businessId)
            ->findOrFail($id);

        return view('arms.show', compact('arm'));
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit($id)
    {
        $businessId = session('active_business');
        $arm = Arm::forBusiness($businessId)->findOrFail($id);
        
        // Prevent editing sold arms
        if ($arm->status === 'sold') {
            return redirect()->route('arms.show', $arm->id)
                ->with('error', 'Cannot edit sold arms. This arm has been sold and is no longer editable.');
        }
        
        $armTypes = ArmsType::where('business_id', $businessId)->where('status', true)->get();
        $armCategories = ArmsCategory::where('business_id', $businessId)->where('status', true)->get();
        $armMakes = ArmsMake::where('business_id', $businessId)->where('status', true)->get();
        $armCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', true)->get();
        $armConditions = ArmsCondition::where('business_id', $businessId)->where('status', true)->get();

        return view('arms.edit', compact('arm', 'armTypes', 'armCategories', 'armMakes', 'armCalibers', 'armConditions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $arm = Arm::forBusiness($businessId)->findOrFail($id);
        
        // Prevent updating sold arms
        if ($arm->status === 'sold') {
            return redirect()->route('arms.show', $arm->id)
                ->with('error', 'Cannot update sold arms. This arm has been sold and is no longer editable.');
        }
        
        $validator = Validator::make($request->all(), [
            'arm_type_id' => 'required|exists:arms_types,id',
            'arm_category_id' => 'required|exists:arms_categories,id',
            'make' => 'required|string|max:255',
            'arm_caliber_id' => 'required|exists:arms_calibers,id',
            'arm_condition_id' => 'required|exists:arms_conditions,id',
            'serial_no' => 'required|string|max:255|unique:arms,serial_no,'.$id.',id,business_id,' . $businessId,
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:available,sold,under_repair,decommissioned',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('arms.edit', $id)->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $arm) {
            // Store old values for audit trail
            $oldValues = $arm->toArray();
            
            $arm->update([
                'arm_type_id' => $request->arm_type_id,
                'arm_category_id' => $request->arm_category_id,
                'make' => $request->make,
                'arm_caliber_id' => $request->arm_caliber_id,
                'arm_condition_id' => $request->arm_condition_id,
                'serial_no' => $request->serial_no,
                'purchase_price' => $request->purchase_price,
                'sale_price' => $request->sale_price,
                'purchase_date' => $request->purchase_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Regenerate arm title
            $arm->update(['arm_title' => $arm->generateArmTitle()]);
            
            // Refresh to get new values
            $arm->refresh();
            $newValues = $arm->toArray();

            // Create stock ledger entry for adjustment if any changes occurred
            $this->createStockLedgerAdjustmentEntry($arm, $oldValues, $newValues, $request->purchase_date);

            // Create comprehensive audit trail
            $arm->history()->create([
                'business_id' => session('active_business'),
                'arm_id' => $arm->id,
                'action' => 'edit',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'transaction_date' => $request->purchase_date,
                'price' => $request->purchase_price,
                'remarks' => 'Arm details updated',
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // If price changed, create adjustment journal entries
            if ($oldValues['purchase_price'] != $request->purchase_price) {
                $this->createJournalEntriesForPriceAdjustment($arm, $oldValues['purchase_price'], $request->purchase_price, $request->purchase_date);
            }
        });

        return redirect()->route('arms.opening-stock')->with('success', 'Arm updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $businessId = session('active_business');
        $arm = Arm::forBusiness($businessId)->findOrFail($id);
        
        // Prevent deleting arms that were created via purchase
        if (!is_null($arm->purchase_id)) {
            return redirect()->route('arms.opening-stock')
                ->withErrors(['delete_error' => 'This arm was received through a purchase and cannot be deleted. Mark it inactive instead.']);
        }

        // Allow deletion if history only contains opening or edit entries
        $hasTransactions = $arm->history()
            ->whereNotIn('action', ['opening', 'edit'])
            ->exists();
        
        if ($hasTransactions) {
            return redirect()->route('arms.opening-stock')
                ->withErrors(['delete_error' => 'Cannot delete arm with transaction history.']);
        }

        if (in_array($arm->status, ['sold', 'pending_approval'])) {
            return redirect()->route('arms.opening-stock')
                ->withErrors(['delete_error' => 'This arm cannot be deleted while its status is ' . str_replace('_', ' ', $arm->status) . '. Please resolve the pending status first.']);
        }

        DB::transaction(function () use ($arm, $businessId) {
            // Delete stock ledger entries
            ArmsStockLedger::where('business_id', $businessId)
                ->where('arm_id', $arm->id)
                ->delete();

            // Delete journal entries related to this arm (opening and adjustments)
            JournalEntry::where('business_id', $businessId)
                ->where('voucher_id', $arm->id)
                ->whereIn('voucher_type', ['Arm Opening Stock', 'Arm Price Adjustment'])
                ->delete();

            // Delete arm history records
            $arm->history()->delete();

            // Finally delete the arm
            $arm->delete();
        });

        return redirect()->route('arms.opening-stock')->with('success', 'Arm deleted successfully.');
    }

    





    /**
     * Create journal entries for opening stock.
     */
    private function createJournalEntriesForOpeningStock($arm, $price, $date)
    {
        $businessId = session('active_business');
        
        // Get required accounts
        $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
        $openingStockAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

        // Create journal entries
        JournalEntry::create([
            'business_id' => $businessId,
            'date_added' => $date,
            'chart_of_account_id' => $inventoryAccountId,
            'account_head' => $inventoryAccountId,
            'debit_amount' => $price,
            'credit_amount' => 0,
            'voucher_type' => 'Arm Opening Stock',
            'voucher_id' => $arm->id,
            'comments' => "Opening stock for arm: {$arm->arm_title}",
            'user_id' => auth()->id(),
        ]);

        JournalEntry::create([
            'business_id' => $businessId,
            'date_added' => $date,
            'chart_of_account_id' => $openingStockAccountId,
            'account_head' => $openingStockAccountId,
            'debit_amount' => 0,
            'credit_amount' => $price,
            'voucher_type' => 'Arm Opening Stock',
            'voucher_id' => $arm->id,
            'comments' => "Opening stock for arm: {$arm->arm_title}",
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Create journal entries for price adjustment.
     */
    private function createJournalEntriesForPriceAdjustment($arm, $oldPrice, $newPrice, $date)
    {
        $businessId = session('active_business');
        $difference = $newPrice - $oldPrice;
        
        if ($difference == 0) return;

        // If this is an opening stock arm, update the original opening entries instead of creating new adjustments
        if (is_null($arm->purchase_id)) {
            $openingEntries = JournalEntry::where('business_id', $businessId)
                ->where('voucher_type', 'Arm Opening Stock')
                ->where('voucher_id', $arm->id)
                ->get();

            if ($openingEntries->count() >= 2) {
                foreach ($openingEntries as $entry) {
                    $entry->date_added = $date;
                    $entry->comments = "Opening stock for arm: {$arm->arm_title} (updated)";

                    if ($entry->debit_amount > 0) {
                        $entry->debit_amount = $newPrice;
                        $entry->credit_amount = 0;
                    } else {
                        $entry->credit_amount = $newPrice;
                        $entry->debit_amount = 0;
                    }

                    $entry->save();
                }

                // Remove any previous price adjustment entries created for this arm to prevent duplicate balances
                JournalEntry::where('business_id', $businessId)
                    ->where('voucher_type', 'Arm Price Adjustment')
                    ->where('voucher_id', $arm->id)
                    ->delete();

                return;
            }
        }

        // Get required accounts
        $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
        $openingStockAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

        if ($difference > 0) {
            // Increase in value
            JournalEntry::create([
                'business_id' => $businessId,
                'date_added' => $date,
                'chart_of_account_id' => $inventoryAccountId,
                'account_head' => $inventoryAccountId,
                'debit_amount' => $difference,
                'credit_amount' => 0,
                'voucher_type' => 'Arm Price Adjustment',
                'voucher_id' => $arm->id,
                'comments' => "Price adjustment for arm: {$arm->arm_title}",
                'user_id' => auth()->id(),
            ]);

            JournalEntry::create([
                'business_id' => $businessId,
                'date_added' => $date,
                'chart_of_account_id' => $openingStockAccountId,
                'account_head' => $openingStockAccountId,
                'debit_amount' => 0,
                'credit_amount' => $difference,
                'voucher_type' => 'Arm Price Adjustment',
                'voucher_id' => $arm->id,
                'comments' => "Price adjustment for arm: {$arm->arm_title}",
                'user_id' => auth()->id(),
            ]);
        } else {
            // Decrease in value
            JournalEntry::create([
                'business_id' => $businessId,
                'date_added' => $date,
                'chart_of_account_id' => $openingStockAccountId,
                'account_head' => $openingStockAccountId,
                'debit_amount' => abs($difference),
                'credit_amount' => 0,
                'voucher_type' => 'Arm Price Adjustment',
                'voucher_id' => $arm->id,
                'comments' => "Price adjustment for arm: {$arm->arm_title}",
                'user_id' => auth()->id(),
            ]);

            JournalEntry::create([
                'business_id' => $businessId,
                'date_added' => $date,
                'chart_of_account_id' => $inventoryAccountId,
                'account_head' => $inventoryAccountId,
                'debit_amount' => 0,
                'credit_amount' => abs($difference),
                'voucher_type' => 'Arm Price Adjustment',
                'voucher_id' => $arm->id,
                'comments' => "Price adjustment for arm: {$arm->arm_title}",
                'user_id' => auth()->id(),
            ]);
        }
    }





    /**
     * Create stock ledger adjustment entry for arm updates.
     */
    private function createStockLedgerAdjustmentEntry($arm, $oldValues, $newValues, $transactionDate)
    {
        $businessId = session('active_business');
        $hasChanges = false;
        $remarks = [];

        // Check for significant changes that affect stock ledger
        if ($oldValues['status'] !== $newValues['status']) {
            $hasChanges = true;
            $remarks[] = "Status changed from {$oldValues['status']} to {$newValues['status']}";
        }

        if ($oldValues['purchase_price'] !== $newValues['purchase_price']) {
            $hasChanges = true;
            $remarks[] = "Price adjusted from PKR " . number_format($oldValues['purchase_price'], 2) . " to PKR " . number_format($newValues['purchase_price'], 2);
        }

        if ($oldValues['serial_no'] !== $newValues['serial_no']) {
            $hasChanges = true;
            $remarks[] = "Serial number changed from {$oldValues['serial_no']} to {$newValues['serial_no']}";
        }

        // Only create ledger entry if there are significant changes
        if ($hasChanges) {
            ArmsStockLedger::createEntry([
                'business_id' => $businessId,
                'arm_id' => $arm->id,
                'transaction_date' => $transactionDate,
                'transaction_type' => 'adjustment',
                'quantity_in' => 0,
                'quantity_out' => 0, // No quantity change, just adjustment
                'reference_id' => $arm->id,
                'remarks' => implode(', ', $remarks),
            ]);
        }
    }

    /**
     * Reverse journal entries for opening stock.
     */
    private function reverseJournalEntriesForOpeningStock($arm)
    {
        $businessId = session('active_business');
        
        // Get required accounts
        $inventoryAccountId = ChartOfAccount::getInventoryAssetAccountId();
        $openingStockAccountId = ChartOfAccount::getOpeningStockEquityAccountId();

        // Create reversal entries
        JournalEntry::create([
            'business_id' => $businessId,
            'date' => now(),
            'date_added' => now(),
            'chart_of_account_id' => $openingStockAccountId,
            'account_head' => $openingStockAccountId,
            'debit_amount' => $arm->purchase_price,
            'credit_amount' => 0,
            'voucher_type' => 'Arm Opening Stock Reversal',
            'voucher_id' => $arm->id,
            'comments' => "Reversal of opening stock for arm: {$arm->arm_title}",
            'user_id' => auth()->id(),
        ]);

        JournalEntry::create([
            'business_id' => $businessId,
            'date' => now(),
            'date_added' => now(),
            'chart_of_account_id' => $inventoryAccountId,
            'account_head' => $inventoryAccountId,
            'debit_amount' => 0,
            'credit_amount' => $arm->purchase_price,
            'voucher_type' => 'Arm Opening Stock Reversal',
            'voucher_id' => $arm->id,
            'comments' => "Reversal of opening stock for arm: {$arm->arm_title}",
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Display arms report.
     */
    public function report(Request $request)
    {
        $businessId = session('active_business');
        $business = Business::find($businessId);

        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));
        $perPageParam = $request->get('per_page', 100);

        // Build query
        $query = Arm::with(['armType', 'armCategory', 'armMake', 'armCaliber', 'armCondition', 'business'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('serial_no', 'like', "%{$search}%")
                  ->orWhere('arm_title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Apply status filter (including acquisition type filters)
        if ($status !== 'all') {
            if ($status === 'purchase') {
                // Filter arms acquired through purchase
                $query->whereHas('history', function($q) {
                    $q->where('action', 'purchase');
                });
            } elseif ($status === 'opening') {
                // Filter arms from opening stock
                $query->whereHas('history', function($q) {
                    $q->where('action', 'opening');
                });
            } else {
                // Filter by regular status (available, sold, etc.)
                $query->where('status', $status);
            }
        }

        // Date range filters (always applied with defaults)
        $query->where('purchase_date', '>=', $fromDate);
        $query->where('purchase_date', '<=', $toDate);

        // Determine pagination size
        if ($perPageParam === 'all') {
            $totalRecords = (clone $query)->count();
            $perPage = max($totalRecords, 1);
        } else {
            $perPage = (int) $perPageParam;
            if ($perPage <= 0) {
                $perPage = 100;
                $perPageParam = 100;
            }
        }

        // Get arms with pagination
        $arms = $query->orderBy('purchase_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Get summary statistics (apply same filters to summary)
        $summaryQuery = Arm::where('business_id', $businessId);
        
        // Apply status filter to summary (including acquisition type filters)
        if ($status !== 'all') {
            if ($status === 'purchase') {
                // Filter arms acquired through purchase
                $summaryQuery->whereHas('history', function($q) {
                    $q->where('action', 'purchase');
                });
            } elseif ($status === 'opening') {
                // Filter arms from opening stock
                $summaryQuery->whereHas('history', function($q) {
                    $q->where('action', 'opening');
                });
            } else {
                // Filter by regular status (available, sold, etc.)
                $summaryQuery->where('status', $status);
            }
        }
        
        // Apply date range filters (always applied with defaults)
        $summaryQuery->where('purchase_date', '>=', $fromDate);
        $summaryQuery->where('purchase_date', '<=', $toDate);

        $summary = [
            'total_arms' => $summaryQuery->count(),
            'available' => (clone $summaryQuery)->where('status', 'available')->count(),
            'sold' => (clone $summaryQuery)->where('status', 'sold')->count(),
            'under_repair' => (clone $summaryQuery)->where('status', 'under_repair')->count(),
            'decommissioned' => (clone $summaryQuery)->where('status', 'decommissioned')->count(),
            'total_value' => (clone $summaryQuery)->where('status', 'available')->sum('purchase_price'),
            'average_price' => (clone $summaryQuery)->where('status', 'available')->avg('purchase_price'),
        ];

        return view('arms.report', compact(
            'arms', 
            'business', 
            'summary', 
            'search',
            'status',
            'fromDate',
            'toDate',
            'perPage',
            'perPageParam'
        ));
    }

    /**
     * Export arms report to CSV.
     */
    public function exportReport(Request $request)
    {
        $businessId = session('active_business');
        $business = Business::find($businessId);

        // Get filter parameters (same as report method)
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        // Build query (same as report method)
        $query = Arm::with(['armType', 'armCategory', 'armMake', 'armCaliber', 'armCondition'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('serial_no', 'like', "%{$search}%")
                  ->orWhere('arm_title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Apply status filter (including acquisition type filters)
        if ($status !== 'all') {
            if ($status === 'purchase') {
                // Filter arms acquired through purchase
                $query->whereHas('history', function($q) {
                    $q->where('action', 'purchase');
                });
            } elseif ($status === 'opening') {
                // Filter arms from opening stock
                $query->whereHas('history', function($q) {
                    $q->where('action', 'opening');
                });
            } else {
                // Filter by regular status (available, sold, etc.)
                $query->where('status', $status);
            }
        }

        // Date range filters (always applied with defaults)
        $query->where('purchase_date', '>=', $fromDate);
        $query->where('purchase_date', '<=', $toDate);

        $arms = $query->orderBy('purchase_date', 'desc')->get();

        $filename = 'arms_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($arms, $business) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'Arms Report - ' . ($business->business_name ?? 'Business'),
                'Generated on: ' . now()->format('d M Y H:i A'),
                '',
                ''
            ]);

            // Add column headers
            fputcsv($file, [
                'Serial No',
                'Arm Title',
                'Type',
                'Category',
                'Make',
                'Caliber',
                'Condition',
                'Purchase Price (PKR)',
                'Sale Price (PKR)',
                'Purchase Date',
                'Status',
                'Notes'
            ]);

            // Add data rows
            foreach ($arms as $arm) {
                fputcsv($file, [
                    $arm->serial_no,
                    $arm->arm_title,
                    $arm->armType->arm_type ?? 'N/A',
                    $arm->armCategory->arm_category ?? 'N/A',
                    $arm->armMake->arm_make ?? 'N/A',
                    $arm->armCaliber->arm_caliber ?? 'N/A',
                    $arm->armCondition->arm_condition ?? 'N/A',
                    number_format($arm->purchase_price, 2),
                    number_format($arm->sale_price, 2),
                    $arm->purchase_date->format('d M Y'),
                    ucfirst(str_replace('_', ' ', $arm->status)),
                    $arm->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display arms dashboard.
     */
    public function dashboard()
    {
        $businessId = session('active_business');
        
        // Fallback: if no active business, try to set the user's first business
        if (!$businessId) {
            $user = Auth::user();
            if ($user) {
                $firstBusiness = $user->businesses()->first();
                if ($firstBusiness) {
                    $businessId = $firstBusiness->id;
                    session(['active_business' => $businessId]);
                    \Log::info('Fallback: Set active business in arms dashboard', [
                        'user_id' => $user->id,
                        'business_id' => $businessId
                    ]);
                }
            }
        }
        
        // If still no business ID, return empty dashboard
        if (!$businessId) {
            \Log::warning('No active business found for arms dashboard', [
                'user_id' => Auth::id()
            ]);
            return view('arms.dashboard', [
                'business' => null,
                'armsStats' => [
                    'total' => 0,
                    'available' => 0,
                    'sold' => 0,
                    'under_repair' => 0,
                    'decommissioned' => 0,
                    'total_value' => 0,
                    'available_value' => 0,
                    'sold_value' => 0,
                    'this_month' => 0,
                    'purchased' => 0,
                    'purchased_value' => 0,
                    'opening_stock' => 0,
                    'opening_stock_value' => 0,
                ],
                'armsTypesStats' => ['top_types' => collect()],
                'armsCategoriesStats' => [],
                'armsMakesStats' => [],
                'armsCalibersStats' => [],
                'armsConditionsStats' => [],
                'armsStockLedgerStats' => [],
                'armsHistoryStats' => [],
                'recentActivities' => [],
                'lowStockAlerts' => []
            ]);
        }
        
        $business = Business::find($businessId);

        // Arms Statistics
        $armsStats = $this->getArmsStats($businessId);
        
        // Arms Types Statistics
        $armsTypesStats = $this->getArmsTypesStats($businessId);
        
        // Arms Categories Statistics
        $armsCategoriesStats = $this->getArmsCategoriesStats($businessId);
        
        // Arms Makes Statistics
        $armsMakesStats = $this->getArmsMakesStats($businessId);
        
        // Arms Calibers Statistics
        $armsCalibersStats = $this->getArmsCalibersStats($businessId);
        
        // Arms Conditions Statistics
        $armsConditionsStats = $this->getArmsConditionsStats($businessId);
        
        // Arms Stock Ledger Statistics
        $armsStockLedgerStats = $this->getArmsStockLedgerStats($businessId);
        
        // Arms History Statistics
        $armsHistoryStats = $this->getArmsHistoryStats($businessId);
        
        // Recent Activities
        $recentActivities = $this->getRecentActivities($businessId);
        
        // Low Stock Alerts
        $lowStockAlerts = $this->getLowStockAlerts($businessId);

        return view('arms.dashboard', compact(
            'business',
            'armsStats',
            'armsTypesStats',
            'armsCategoriesStats',
            'armsMakesStats',
            'armsCalibersStats',
            'armsConditionsStats',
            'armsStockLedgerStats',
            'armsHistoryStats',
            'recentActivities',
            'lowStockAlerts'
        ));
    }

    /**
     * Display arms added through opening stock only.
     */
    public function openingStock(Request $request)
    {
        $businessId = session('active_business');
        $query = Arm::with(['armType', 'armCategory', 'armCaliber', 'armCondition', 'armMake'])
            ->forBusiness($businessId)
            ->whereNull('purchase_id'); // Opening stock arms have null purchase_id

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->byStatus($request->status);
        }

        // Apply type filter
        if ($request->filled('arm_type_id')) {
            $query->where('arm_type_id', $request->arm_type_id);
        }

        // Apply category filter
        if ($request->filled('arm_category_id')) {
            $query->where('arm_category_id', $request->arm_category_id);
        }

        // Apply make filter
        if ($request->filled('arm_make_id')) {
            $query->where('make', $request->arm_make_id);
        }

        // Apply caliber filter
        if ($request->filled('arm_caliber_id')) {
            $query->where('arm_caliber_id', $request->arm_caliber_id);
        }

        // Apply condition filter
        if ($request->filled('arm_condition_id')) {
            $query->where('arm_condition_id', $request->arm_condition_id);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['serial_no', 'make', 'purchase_price', 'purchase_date', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $arms = $query->paginate(15)->withQueryString();

        // Get filter options
        $armTypes = ArmsType::where('business_id', $businessId)->where('status', true)->get();
        $armCategories = ArmsCategory::where('business_id', $businessId)->where('status', true)->get();
        $armMakes = ArmsMake::where('business_id', $businessId)->where('status', true)->get();
        $armCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', true)->get();
        $armConditions = ArmsCondition::where('business_id', $businessId)->where('status', true)->get();

        return view('arms.opening-stock', compact('arms', 'armTypes', 'armCategories', 'armMakes', 'armCalibers', 'armConditions'));
    }

    /**
     * Get comprehensive arms statistics.
     */
    private function getArmsStats($businessId)
    {
        $totalArms = Arm::where('business_id', $businessId)->count();
        $availableArms = Arm::where('business_id', $businessId)->where('status', 'available')->count();
        $soldArms = Arm::where('business_id', $businessId)->where('status', 'sold')->count();
        $underRepairArms = Arm::where('business_id', $businessId)->where('status', 'under_repair')->count();
        $decommissionedArms = Arm::where('business_id', $businessId)->where('status', 'decommissioned')->count();
        
        $totalArmsValue = Arm::where('business_id', $businessId)->where('status', 'available')->sum('purchase_price');
        $availableArmsValue = Arm::where('business_id', $businessId)->where('status', 'available')->sum('purchase_price');
        $soldArmsValue = Arm::where('business_id', $businessId)->where('status', 'sold')->sum('sale_price');
        
        $thisMonthArms = Arm::where('business_id', $businessId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Get purchased arms (excluding opening stock)
        $purchasedArms = Arm::where('business_id', $businessId)
            ->whereHas('history', function($query) {
                $query->where('action', 'purchase');
            })
            ->count();
        
        $purchasedArmsValue = Arm::where('business_id', $businessId)
            ->whereHas('history', function($query) {
                $query->where('action', 'purchase');
            })
            ->sum('purchase_price');
        
        // Get opening stock arms
        $openingStockArms = Arm::where('business_id', $businessId)
            ->whereHas('history', function($query) {
                $query->where('action', 'opening');
            })
            ->count();
        
        $openingStockValue = Arm::where('business_id', $businessId)
            ->whereHas('history', function($query) {
                $query->where('action', 'opening');
            })
            ->sum('purchase_price');
        
        return [
            'total' => $totalArms,
            'available' => $availableArms,
            'sold' => $soldArms,
            'under_repair' => $underRepairArms,
            'decommissioned' => $decommissionedArms,
            'total_value' => $totalArmsValue,
            'available_value' => $availableArmsValue,
            'sold_value' => $soldArmsValue,
            'this_month' => $thisMonthArms,
            'purchased' => $purchasedArms,
            'purchased_value' => $purchasedArmsValue,
            'opening_stock' => $openingStockArms,
            'opening_stock_value' => $openingStockValue,
        ];
    }

    /**
     * Get arms types statistics.
     */
    private function getArmsTypesStats($businessId)
    {
        $totalTypes = ArmsType::where('business_id', $businessId)->count();
        $activeTypes = ArmsType::where('business_id', $businessId)->where('status', true)->count();
        
        $armsByType = Arm::where('arms.business_id', $businessId)
            ->join('arms_types', 'arms.arm_type_id', '=', 'arms_types.id')
            ->select('arms_types.arm_type', DB::raw('count(*) as count'))
            ->groupBy('arms_types.id', 'arms_types.arm_type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'total' => $totalTypes,
            'active' => $activeTypes,
            'top_types' => $armsByType,
        ];
    }

    /**
     * Get arms categories statistics.
     */
    private function getArmsCategoriesStats($businessId)
    {
        $totalCategories = ArmsCategory::where('business_id', $businessId)->count();
        $activeCategories = ArmsCategory::where('business_id', $businessId)->where('status', true)->count();
        
        $armsByCategory = Arm::where('arms.business_id', $businessId)
            ->join('arms_categories', 'arms.arm_category_id', '=', 'arms_categories.id')
            ->select('arms_categories.arm_category', DB::raw('count(*) as count'))
            ->groupBy('arms_categories.id', 'arms_categories.arm_category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'total' => $totalCategories,
            'active' => $activeCategories,
            'top_categories' => $armsByCategory,
        ];
    }

    /**
     * Get arms makes statistics.
     */
    private function getArmsMakesStats($businessId)
    {
        $totalMakes = ArmsMake::where('business_id', $businessId)->count();
        $activeMakes = ArmsMake::where('business_id', $businessId)->where('status', true)->count();
        
        $armsByMake = Arm::where('arms.business_id', $businessId)
            ->join('arms_makes', 'arms.make', '=', 'arms_makes.id')
            ->select('arms_makes.arm_make', DB::raw('count(*) as count'))
            ->groupBy('arms_makes.id', 'arms_makes.arm_make')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'total' => $totalMakes,
            'active' => $activeMakes,
            'top_makes' => $armsByMake,
        ];
    }

    /**
     * Get arms calibers statistics.
     */
    private function getArmsCalibersStats($businessId)
    {
        $totalCalibers = ArmsCaliber::where('business_id', $businessId)->count();
        $activeCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', true)->count();
        
        $armsByCaliber = Arm::where('arms.business_id', $businessId)
            ->join('arms_calibers', 'arms.arm_caliber_id', '=', 'arms_calibers.id')
            ->select('arms_calibers.arm_caliber', DB::raw('count(*) as count'))
            ->groupBy('arms_calibers.id', 'arms_calibers.arm_caliber')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'total' => $totalCalibers,
            'active' => $activeCalibers,
            'top_calibers' => $armsByCaliber,
        ];
    }

    /**
     * Get arms conditions statistics.
     */
    private function getArmsConditionsStats($businessId)
    {
        $totalConditions = ArmsCondition::where('business_id', $businessId)->count();
        $activeConditions = ArmsCondition::where('business_id', $businessId)->where('status', true)->count();
        
        $armsByCondition = Arm::where('arms.business_id', $businessId)
            ->join('arms_conditions', 'arms.arm_condition_id', '=', 'arms_conditions.id')
            ->select('arms_conditions.arm_condition', DB::raw('count(*) as count'))
            ->groupBy('arms_conditions.id', 'arms_conditions.arm_condition')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'total' => $totalConditions,
            'active' => $activeConditions,
            'top_conditions' => $armsByCondition,
        ];
    }

    /**
     * Get arms stock ledger statistics.
     */
    private function getArmsStockLedgerStats($businessId)
    {
        $totalEntries = ArmsStockLedger::where('business_id', $businessId)->count();
        $thisMonthEntries = ArmsStockLedger::where('business_id', $businessId)
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->count();
        
        $stockInEntries = ArmsStockLedger::where('business_id', $businessId)
            ->where('quantity_in', '>', 0)
            ->count();
        $stockOutEntries = ArmsStockLedger::where('business_id', $businessId)
            ->where('quantity_out', '>', 0)
            ->count();
        
        return [
            'total_entries' => $totalEntries,
            'this_month' => $thisMonthEntries,
            'stock_in' => $stockInEntries,
            'stock_out' => $stockOutEntries,
        ];
    }

    /**
     * Get arms history statistics.
     */
    private function getArmsHistoryStats($businessId)
    {
        $totalEntries = ArmHistory::where('business_id', $businessId)->count();
        $uniqueArms = ArmHistory::where('business_id', $businessId)->distinct()->count('arm_id');
        $uniqueUsers = ArmHistory::where('business_id', $businessId)->distinct()->count('user_id');
        
        $openingActions = ArmHistory::where('business_id', $businessId)->where('action', 'opening')->count();
        $editActions = ArmHistory::where('business_id', $businessId)->where('action', 'edit')->count();
        $deleteActions = ArmHistory::where('business_id', $businessId)->where('action', 'delete')->count();
        
        $thisMonthEntries = ArmHistory::where('business_id', $businessId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return [
            'total_entries' => $totalEntries,
            'unique_arms' => $uniqueArms,
            'unique_users' => $uniqueUsers,
            'opening_actions' => $openingActions,
            'edit_actions' => $editActions,
            'delete_actions' => $deleteActions,
            'this_month' => $thisMonthEntries,
        ];
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities($businessId)
    {
        $recentArms = Arm::where('business_id', $businessId)
            ->with(['armType', 'armCategory', 'armMake'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentArmHistory = ArmHistory::where('business_id', $businessId)
            ->with(['arm', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'recent_arms' => $recentArms,
            'recent_arm_history' => $recentArmHistory,
        ];
    }

    /**
     * Get low stock alerts.
     */
    private function getLowStockAlerts($businessId)
    {
        $availableArms = Arm::where('business_id', $businessId)
            ->where('status', 'available')
            ->orderBy('purchase_price', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'available_arms' => $availableArms,
        ];
    }
}
