<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Party;
use App\Models\Bank;
use App\Models\GeneralItem;
use App\Models\ArmsMake;
use App\Models\ArmsCaliber;
use App\Models\ArmsCategory;
use App\Models\ArmsCondition;
use App\Models\ArmsType;
use App\Models\GeneralBatch;
use App\Models\Arm;
use App\Models\GeneralItemStockLedger;
use App\Models\ArmsStockLedger;
use App\Models\ArmHistory;
use App\Models\BankLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    /**
     * Render the create form with inline errors instead of redirecting,
     * ensuring error bag is present on initial paint.
     */
    private function renderCreateWithErrors(Request $request, $businessId, array $errors, string $flashMessage = null)
    {
        if ($flashMessage) {
            session()->flash('form_error', $flashMessage);
        }

        // Rebuild the same data as in create()
        $vendors = Party::where('business_id', $businessId)->orderBy('name')->get();
        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) { $query->where('is_active', true); })
            ->orderBy('account_name')
            ->get();
        $generalItems = GeneralItem::where('business_id', $businessId)->orderBy('item_name')->get();
        
        // Arms data loading disabled - StoreBook is items-only
        // $armsMakes = ArmsMake::where('business_id', $businessId)->where('status', 1)->orderBy('arm_make')->get();
        // $armsCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', 1)->orderBy('arm_caliber')->get();
        // $armsCategories = ArmsCategory::where('business_id', $businessId)->where('status', 1)->orderBy('arm_category')->get();
        // $armsTypes = ArmsType::where('business_id', $businessId)->where('status', 1)->orderBy('arm_type')->get();
        // $armsConditions = ArmsCondition::where('business_id', $businessId)->where('status', 1)->orderBy('arm_condition')->get();

        // Empty collections for arms data to prevent errors in views
        $armsMakes = collect();
        $armsCalibers = collect();
        $armsCategories = collect();
        $armsTypes = collect();
        $armsConditions = collect();

        // Set old input so the form re-populates
        $request->flash();

        // Return view with a validator-like error bag
        $messageBag = new \Illuminate\Support\MessageBag($errors);
        return view('purchases.create', compact(
            'vendors','banks','generalItems','armsMakes','armsCalibers','armsCategories','armsTypes','armsConditions'
        ))->withErrors($messageBag);
    }

    /**
     * Render the edit form with inline errors instead of redirecting.
     */
    private function renderEditWithErrors(Request $request, Purchase $purchase, array $errors, string $flashMessage = null)
    {
        if ($flashMessage) {
            session()->flash('form_error', $flashMessage);
        }

        $businessId = $purchase->business_id;

        $vendors = Party::where('business_id', $businessId)->orderBy('name')->get();
        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) { $query->where('is_active', true); })
            ->orderBy('account_name')
            ->get();
        $generalItems = GeneralItem::where('business_id', $businessId)->orderBy('item_name')->get();
        $armsMakes = ArmsMake::where('business_id', $businessId)->where('status', 1)->orderBy('arm_make')->get();
        $armsCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', 1)->orderBy('arm_caliber')->get();
        $armsCategories = ArmsCategory::where('business_id', $businessId)->where('status', 1)->orderBy('arm_category')->get();
        $armsTypes = ArmsType::where('business_id', $businessId)->where('status', 1)->orderBy('arm_type')->get();
        $armsConditions = ArmsCondition::where('business_id', $businessId)->where('status', 1)->orderBy('arm_condition')->get();

        $purchase->load([
            'generalLines.generalItem',
            'armLines.armSerials.make',
            'armLines.armSerials.caliber',
            'armLines.armSerials.category'
        ]);

        $request->flash();

        $messageBag = new \Illuminate\Support\MessageBag($errors);
        return view('purchases.edit', compact(
            'purchase','vendors','banks','generalItems','armsMakes','armsCalibers','armsCategories','armsTypes','armsConditions'
        ))->withErrors($messageBag);
    }
    /**
     * Display a listing of purchases.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = Purchase::with(['party', 'bank', 'createdBy'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('vendor')) {
            $query->byVendor($request->vendor);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
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
        
        if (in_array($sortBy, ['id', 'invoice_date', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $purchases = $query->paginate(15)->withQueryString();
        
        // Get vendors for filter dropdown
        $vendors = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        return view('purchases.index', compact('purchases', 'vendors'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $businessId = session('active_business');
        
        $vendors = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();

        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Arms data loading disabled - StoreBook is items-only
        // $armsMakes = ArmsMake::where('business_id', $businessId)->where('status', 1)->orderBy('arm_make')->get();
        // $armsCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', 1)->orderBy('arm_caliber')->get();
        // $armsCategories = ArmsCategory::where('business_id', $businessId)->where('status', 1)->orderBy('arm_category')->get();
        // $armsTypes = ArmsType::where('business_id', $businessId)->where('status', 1)->orderBy('arm_type')->get();
        // $armsConditions = ArmsCondition::where('business_id', $businessId)->where('status', 1)->orderBy('arm_condition')->get();

        // Empty arrays for arms data to prevent errors in views
        $armsMakes = collect();
        $armsCalibers = collect();
        $armsCategories = collect();
        $armsTypes = collect();
        $armsConditions = collect();

        return view('purchases.create', compact(
            'vendors', 
            'banks', 
            'generalItems', 
            'armsMakes', 
            'armsCalibers', 
            'armsCategories', 
            'armsTypes',
            'armsConditions'
        ));
    }

    /**
     * Store a newly created purchase in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $userId = auth()->id();

        try {
            DB::beginTransaction();

            // TEMP DEBUG: mark that store was hit
            session()->flash('store_debug', 'hit');
            \Log::info('Purchase store hit', [
                'user_id' => $userId,
                'business_id' => $businessId,
            ]);

            // Validate main purchase data
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:payment_type,credit|exists:parties,id',
                'payment_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:payment_type,cash|exists:banks,id',
                'invoice_date' => 'required|date',
                'shipping_charges' => 'nullable|numeric|min:0',
                'action' => 'required|in:save,post',
                
                // Customer details validation (only for cash payments)
                'name_of_customer' => 'nullable|string|max:255',
                'father_name' => 'nullable|string|max:255',
                'contact' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'cnic' => 'nullable|string|max:20',
                'licence_no' => 'nullable|string|max:255',
                'licence_issue_date' => 'nullable|date',
                'licence_valid_upto' => 'nullable|date',
                'licence_issued_by' => 'nullable|string|max:255',
                're_reg_no' => 'nullable|string|max:255',
                'dc' => 'nullable|string|max:255',
                'Date' => 'nullable|date',
                
                // Party license details validation (only for credit payments)
                'party_license_no' => 'nullable|string|max:255',
                'party_license_issue_date' => 'nullable|date',
                'party_license_valid_upto' => 'nullable|date',
                'party_license_issued_by' => 'nullable|string|max:255',
                'party_re_reg_no' => 'nullable|string|max:255',
                'party_dc' => 'nullable|string|max:255',
                'party_dc_date' => 'nullable|date',
                
                // General lines validation
                'general_lines' => 'nullable|array',
                'general_lines.*.general_item_id' => 'required_with:general_lines|exists:general_items,id',
                'general_lines.*.qty' => 'required_with:general_lines|numeric|min:0.01',
                'general_lines.*.unit_price' => 'required_with:general_lines|numeric|min:0',
                'general_lines.*.sale_price' => 'required_with:general_lines|numeric|min:0',
                'general_lines.*.description' => 'nullable|string',
                
                // Arm lines validation
                'arm_lines' => 'nullable|array',
                'arm_lines.*.unit_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.sale_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.serials' => 'required_with:arm_lines|string',
                'arm_lines.*.arm_type_id' => 'nullable|exists:arms_types,id,business_id,' . $businessId,
                'arm_lines.*.arm_make_id' => 'nullable|exists:arms_makes,id,business_id,' . $businessId,
                'arm_lines.*.arm_caliber_id' => 'nullable|exists:arms_calibers,id,business_id,' . $businessId,
                'arm_lines.*.arm_category_id' => 'nullable|exists:arms_categories,id,business_id,' . $businessId,
                'arm_lines.*.arm_condition_id' => 'nullable|exists:arms_conditions,id,business_id,' . $businessId,
            ], [
                'general_lines.*.general_item_id.required_with' => 'Please select a general item for line :position.',
                'general_lines.*.general_item_id.exists' => 'The selected general item for line :position is invalid.',
                'general_lines.*.qty.required_with' => 'Quantity is required for general item line :position.',
                'general_lines.*.qty.numeric' => 'Quantity must be a number for general item line :position.',
                'general_lines.*.qty.min' => 'Quantity must be at least 0.01 for general item line :position.',
                'general_lines.*.unit_price.required_with' => 'Unit price is required for general item line :position.',
                'general_lines.*.unit_price.numeric' => 'Unit price must be a number for general item line :position.',
                'general_lines.*.unit_price.min' => 'Unit price must be at least 0 for general item line :position.',
                'general_lines.*.sale_price.required_with' => 'Sale price is required for general item line :position.',
                'general_lines.*.sale_price.numeric' => 'Sale price must be a number for general item line :position.',
                'general_lines.*.sale_price.min' => 'Sale price must be at least 0 for general item line :position.',
                
                'arm_lines.*.serials.required_with' => 'Serial numbers are required for arm line :position.',
                'arm_lines.*.serials.string' => 'Serial numbers must be text for arm line :position.',
                'arm_lines.*.unit_price.required_with' => 'Unit price is required for arm line :position.',
                'arm_lines.*.unit_price.numeric' => 'Unit price must be a number for arm line :position.',
                'arm_lines.*.unit_price.min' => 'Unit price must be at least 0 for arm line :position.',
                'arm_lines.*.sale_price.required_with' => 'Sale price is required for arm line :position.',
                'arm_lines.*.sale_price.numeric' => 'Sale price must be a number for arm line :position.',
                'arm_lines.*.sale_price.min' => 'Sale price must be at least 0 for arm line :position.',
            ]);

            if ($validator->fails()) {
                // Customize error messages to include line numbers
                $errors = $validator->errors();
                $customErrors = [];
                
                foreach ($errors->all() as $key => $message) {
                    $field = $errors->keys()[$key];
                    
                    // Extract line number from field name
                    if (preg_match('/general_lines\.(\d+)\./', $field, $matches)) {
                        $lineNumber = intval($matches[1]) + 1;
                        $customErrors[$field] = str_replace(':position', $lineNumber, $message);
                    } elseif (preg_match('/arm_lines\.(\d+)\./', $field, $matches)) {
                        $lineNumber = intval($matches[1]) + 1;
                        $customErrors[$field] = str_replace(':position', $lineNumber, $message);
                    } else {
                        $customErrors[$field] = $message;
                    }
                }
                
                return redirect()->back()->withErrors($customErrors)->withInput();
            }

            // Check if at least one line exists
            if (empty($request->general_lines) && empty($request->arm_lines)) {
                return redirect()->back()->withErrors(['error' => 'At least one line item is required'])->withInput();
            }

            // Calculate purchase total amount for bank balance validation
            $calculatedSubtotal = 0;
            
            // Sum general lines
            if ($request->general_lines) {
                foreach ($request->general_lines as $lineData) {
                    $calculatedSubtotal += ($lineData['qty'] ?? 0) * ($lineData['unit_price'] ?? 0);
                }
            }
            
            // Sum arm lines
            if ($request->arm_lines) {
                foreach ($request->arm_lines as $lineData) {
                    // Calculate qty from serials
                    $serials = isset($lineData['serials']) ? array_map('trim', explode(',', $lineData['serials'])) : [];
                    $qty = count(array_filter($serials, function($serial) { return !empty($serial); }));
                    $calculatedSubtotal += $qty * ($lineData['unit_price'] ?? 0);
                }
            }
            
            $calculatedTotal = $calculatedSubtotal + ($request->shipping_charges ?? 0);

            // Validate bank balance for cash payments
            if ($request->payment_type === 'cash') {
                if (empty($request->bank_id)) {
                    \Log::warning('Purchase store validation: missing bank for cash', [
                        'user_id' => $userId,
                        'business_id' => $businessId,
                        'calculated_total' => $calculatedTotal,
                    ]);
                    session()->flash('store_debug', 'missing_bank_for_cash');
                    return $this->renderCreateWithErrors($request, $businessId, [
                        'bank_id' => 'Bank selection is required for cash payments.'
                    ], 'Bank selection is required for cash payments.');
                }
                
                $bank = Bank::find($request->bank_id);
                if (!$bank) {
                    \Log::warning('Purchase store validation: invalid bank id', [
                        'user_id' => $userId,
                        'business_id' => $businessId,
                        'bank_id' => $request->bank_id,
                    ]);
                    session()->flash('store_debug', 'invalid_bank');
                    return $this->renderCreateWithErrors($request, $businessId, [
                        'bank_id' => 'Selected bank is invalid.'
                    ], 'Selected bank is invalid.');
                }
                
                // Get current bank balance (includes opening balance via ledger entries)
                $bankBalance = (float) $bank->getBalance();
                $calculatedTotal = (float) $calculatedTotal;
                
                // Check if balance is insufficient (with small tolerance for floating point)
                if (round($bankBalance, 2) < round($calculatedTotal, 2)) {
                    \Log::warning('Purchase store validation: insufficient bank balance', [
                        'user_id' => $userId,
                        'business_id' => $businessId,
                        'bank_id' => $request->bank_id,
                        'bank_balance' => $bankBalance,
                        'required_total' => $calculatedTotal,
                    ]);
                    session()->flash('store_debug', 'insufficient_balance');
                    return $this->renderCreateWithErrors($request, $businessId, [
                        'bank_id' => 'Insufficient bank balance. Available: PKR ' . number_format($bankBalance, 2) . ', Required: PKR ' . number_format($calculatedTotal, 2)
                    ], 'Insufficient bank balance. Available: PKR ' . number_format($bankBalance, 2) . ', Required: PKR ' . number_format($calculatedTotal, 2));
                }
            }

            // Check for duplicate serial numbers
            $allSerials = [];
            if ($request->arm_lines) {
                foreach ($request->arm_lines as $armLine) {
                    if (isset($armLine['serials']) && !empty($armLine['serials'])) {
                        $serials = array_map('trim', explode(',', $armLine['serials']));
                        foreach ($serials as $serial) {
                            if (!empty($serial)) {
                                $allSerials[] = $serial;
                            }
                        }
                    }
                }
            }
            
            $duplicateSerials = array_diff_assoc($allSerials, array_unique($allSerials));
            if (!empty($duplicateSerials)) {
                $message = 'Duplicate serial numbers found: ' . implode(', ', array_unique($duplicateSerials));
                session()->flash('store_debug', 'duplicate_serials_in_request');
                return $this->renderCreateWithErrors($request, $businessId, [
                    'arm_lines.*.serials' => $message,
                ], $message);
            }

            // Check for existing serial numbers in the same business
            if (!empty($allSerials)) {
                $existingSerials = Arm::where('business_id', $businessId)
                    ->whereIn('serial_no', $allSerials)
                    ->pluck('serial_no')
                    ->toArray();
                
                if (!empty($existingSerials)) {
                    // Create line-specific errors for existing serial numbers and a top banner
                    $errors = [];
                    foreach ($request->arm_lines as $index => $armLine) {
                        if (isset($armLine['serials']) && !empty($armLine['serials'])) {
                            $serials = array_map('trim', explode(',', $armLine['serials']));
                            $lineExistingSerials = array_intersect($serials, $existingSerials);
                            if (!empty($lineExistingSerials)) {
                                $errors["arm_lines.{$index}.serials"] = 'Serial numbers already exist in database: ' . implode(', ', $lineExistingSerials);
                            }
                        }
                    }
                    
                    if (!empty($errors)) {
                        $message = 'Serial numbers already exist: ' . implode(', ', array_unique($existingSerials));
                        session()->flash('store_debug', 'duplicate_serials_in_db');
                        return $this->renderCreateWithErrors($request, $businessId, $errors, $message);
                    }
                }
            }

            // Create purchase
            $purchase = Purchase::create([
                'business_id' => $businessId,
                'party_id' => $request->party_id, // Can be null for cash payments
                'payment_type' => $request->payment_type,
                'bank_id' => $request->bank_id,
                'invoice_date' => $request->invoice_date,
                'shipping_charges' => $request->shipping_charges ?? 0,
                'status' => $request->action === 'post' ? 'posted' : 'draft',
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

            // Create general lines and update item prices if changed
            if ($request->general_lines) {
                foreach ($request->general_lines as $index => $lineData) {
                    $purchase->generalLines()->create([
                        'line_no' => $index + 1,
                        'general_item_id' => $lineData['general_item_id'],
                        'description' => $lineData['description'] ?? null,
                        'qty' => $lineData['qty'],
                        'unit_price' => $lineData['unit_price'],
                        'sale_price' => $lineData['sale_price'],
                    ]);

                    // Update general item prices if they have changed
                    $this->updateGeneralItemPrices($lineData);
                }
            }

            // Create arm lines and serials
            if ($request->arm_lines) {
                foreach ($request->arm_lines as $index => $lineData) {
                    // Calculate qty from serials
                    $serials = isset($lineData['serials']) ? array_map('trim', explode(',', $lineData['serials'])) : [];
                    $qty = count(array_filter($serials, function($serial) { return !empty($serial); }));
                    
                    $armLine = $purchase->armLines()->create([
                        'line_no' => $index + 1,
                        'qty' => $qty,
                        'unit_price' => $lineData['unit_price'],
                        'sale_price' => $lineData['sale_price'],
                        'arm_type_id' => $lineData['arm_type_id'] ?? null,
                        'arm_make_id' => $lineData['arm_make_id'] ?? null,
                        'arm_caliber_id' => $lineData['arm_caliber_id'] ?? null,
                        'arm_category_id' => $lineData['arm_category_id'] ?? null,
                        'arm_condition_id' => $lineData['arm_condition_id'] ?? null,
                    ]);

                    // Create arm serials
                    if (isset($lineData['serials']) && !empty($lineData['serials'])) {
                        foreach ($serials as $serialNo) {
                            if (!empty($serialNo)) {
                                $armLine->armSerials()->create([
                                    'serial_no' => $serialNo,
                                    'arm_title' => $lineData['arm_title'] ?? 'Arm from Purchase',
                                    'make_id' => $lineData['arm_make_id'] ?? null,
                                    'caliber_id' => $lineData['arm_caliber_id'] ?? null,
                                    'category_id' => $lineData['arm_category_id'] ?? null,
                                    'purchase_price' => $lineData['unit_price'] ?? null,
                                    'purchase_date' => $request->invoice_date,
                                    'sale_price' => $lineData['sale_price'] ?? null,
                                    'condition_id' => $lineData['arm_condition_id'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }

            // Calculate and update totals
            $purchase->subtotal = $purchase->calculateSubtotal();
            $purchase->total_amount = $purchase->calculateTotalAmount();
            $purchase->save();

            // If posting immediately, create inventory entries
            if ($request->action === 'post') {
                $this->postPurchase($purchase);
            }

            DB::commit();

            // Clear any old input data from session
            $request->session()->forget('_old_input');

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Show the actual error message
            $errorMessage = 'Failed to create purchase. Error: ' . $e->getMessage();
            
            return redirect()->back()
                ->withErrors(['error' => $errorMessage])
                ->withInput();
        }
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        $user = auth()->user();
        $businessId = session('active_business');
        
        \Log::info('Purchase show method called', [
            'purchase_id' => $purchase->id,
            'purchase_business_id' => $purchase->business_id,
            'user_id' => $user ? $user->id : 'not authenticated',
            'session_business_id' => $businessId,
            'user_businesses' => $user ? $user->businesses->pluck('id')->toArray() : []
        ]);
        
        $this->authorizePurchase($purchase);

        $purchase->load([
            'party', 
            'bank', 
            'createdBy', 
            'generalLines.generalItem', 
            'armLines.armSerials.make',
            'armLines.armSerials.caliber',
            'armLines.armSerials.category',
            'generalBatches',
            'arms'
        ]);

        return view('purchases.show', compact('purchase', 'businessId'));
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit(Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        if (!$purchase->canBeEdited()) {
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'This purchase cannot be edited.');
        }

        $businessId = session('active_business');
        
        $vendors = Party::where('business_id', $businessId)
            ->orderBy('name')
            ->get();

        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();

        $generalItems = GeneralItem::where('business_id', $businessId)
            ->orderBy('item_name')
            ->get();

        // Arms data loading disabled - StoreBook is items-only
        // $armsMakes = ArmsMake::where('business_id', $businessId)->where('status', 1)->orderBy('arm_make')->get();
        // $armsCalibers = ArmsCaliber::where('business_id', $businessId)->where('status', 1)->orderBy('arm_caliber')->get();
        // $armsCategories = ArmsCategory::where('business_id', $businessId)->where('status', 1)->orderBy('arm_category')->get();
        // $armsTypes = ArmsType::where('business_id', $businessId)->where('status', 1)->orderBy('arm_type')->get();
        // $armsConditions = ArmsCondition::where('business_id', $businessId)->where('status', 1)->orderBy('arm_condition')->get();

        // Empty collections for arms data to prevent errors in views
        $armsMakes = collect();
        $armsCalibers = collect();
        $armsCategories = collect();
        $armsTypes = collect();
        $armsConditions = collect();

        $purchase->load([
            'generalLines.generalItem',
            'armLines.armSerials.make',
            'armLines.armSerials.caliber',
            'armLines.armSerials.category'
        ]);

        return view('purchases.edit', compact(
            'purchase',
            'vendors', 
            'banks', 
            'generalItems', 
            'armsMakes', 
            'armsCalibers', 
            'armsCategories', 
            'armsTypes',
            'armsConditions'
        ));
    }

    /**
     * Update the specified purchase in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        if (!$purchase->canBeEdited()) {
            return redirect()->route('purchases.show', $purchase)
                ->withErrors(['error' => 'This purchase cannot be edited.']);
        }

        $businessId = session('active_business');

        try {
            DB::beginTransaction();

            // Validate request (same as store)
            $validator = Validator::make($request->all(), [
                'party_id' => 'nullable|required_if:payment_type,credit|exists:parties,id',
                'payment_type' => 'required|in:cash,credit',
                'bank_id' => 'nullable|required_if:payment_type,cash|exists:banks,id',
                'invoice_date' => 'required|date',
                'shipping_charges' => 'nullable|numeric|min:0',
                
                // Customer details validation (only for cash payments)
                'name_of_customer' => 'nullable|string|max:255',
                'father_name' => 'nullable|string|max:255',
                'contact' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'cnic' => 'nullable|string|max:20',
                'licence_no' => 'nullable|string|max:255',
                'licence_issue_date' => 'nullable|date',
                'licence_valid_upto' => 'nullable|date',
                'licence_issued_by' => 'nullable|string|max:255',
                're_reg_no' => 'nullable|string|max:255',
                'dc' => 'nullable|string|max:255',
                'Date' => 'nullable|date',
                
                'general_lines' => 'nullable|array',
                'general_lines.*.general_item_id' => 'required_with:general_lines|exists:general_items,id',
                'general_lines.*.qty' => 'required_with:general_lines|numeric|min:0.01',
                'general_lines.*.unit_price' => 'required_with:general_lines|numeric|min:0',
                'general_lines.*.sale_price' => 'required_with:general_lines|numeric|min:0',
                'general_lines.*.description' => 'nullable|string',
                
                'arm_lines' => 'nullable|array',
                'arm_lines.*.unit_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.sale_price' => 'required_with:arm_lines|numeric|min:0',
                'arm_lines.*.serials' => 'required_with:arm_lines|string',
                'arm_lines.*.arm_type_id' => 'nullable|exists:arms_types,id,business_id,' . $businessId,
                'arm_lines.*.arm_make_id' => 'nullable|exists:arms_makes,id,business_id,' . $businessId,
                'arm_lines.*.arm_caliber_id' => 'nullable|exists:arms_calibers,id,business_id,' . $businessId,
                'arm_lines.*.arm_category_id' => 'nullable|exists:arms_categories,id,business_id,' . $businessId,
                'arm_lines.*.arm_condition_id' => 'nullable|exists:arms_conditions,id,business_id,' . $businessId,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Check if at least one line exists
            if (empty($request->general_lines) && empty($request->arm_lines)) {
                return redirect()->back()->withErrors(['error' => 'At least one line item is required'])->withInput();
            }

            // Calculate purchase total amount for bank balance validation
            $calculatedSubtotal = 0;
            
            // Sum general lines
            if ($request->general_lines) {
                foreach ($request->general_lines as $lineData) {
                    $calculatedSubtotal += ($lineData['qty'] ?? 0) * ($lineData['unit_price'] ?? 0);
                }
            }
            
            // Sum arm lines
            if ($request->arm_lines) {
                foreach ($request->arm_lines as $lineData) {
                    // Calculate qty from serials
                    $serials = isset($lineData['serials']) ? array_map('trim', explode(',', $lineData['serials'])) : [];
                    $qty = count(array_filter($serials, function($serial) { return !empty($serial); }));
                    $calculatedSubtotal += $qty * ($lineData['unit_price'] ?? 0);
                }
            }
            
            $calculatedTotal = $calculatedSubtotal + ($request->shipping_charges ?? 0);

            // Validate bank balance for cash payments
            if ($request->payment_type === 'cash') {
                if (empty($request->bank_id)) {
                    return $this->renderEditWithErrors($request, $purchase, [
                        'bank_id' => 'Bank selection is required for cash payments.'
                    ], 'Bank selection is required for cash payments.');
                }
                
                $bank = Bank::find($request->bank_id);
                if (!$bank) {
                    return $this->renderEditWithErrors($request, $purchase, [
                        'bank_id' => 'Selected bank is invalid.'
                    ], 'Selected bank is invalid.');
                }
                
                // Get current bank balance (includes opening balance via ledger entries)
                $bankBalance = (float) $bank->getBalance();
                $calculatedTotal = (float) $calculatedTotal;
                
                // If editing a posted purchase that was originally cash with the same bank, add back the old purchase amount
                // because it will be reversed before the new amount is deducted
                if ($purchase->isPosted() && $purchase->payment_type === 'cash' && $purchase->bank_id == $request->bank_id) {
                    // Add back the old purchase total to available balance
                    $oldPurchaseTotal = (float) $purchase->total_amount;
                    $bankBalance += $oldPurchaseTotal;
                }
                
                // Check if balance is insufficient (with small tolerance for floating point)
                if (round($bankBalance, 2) < round($calculatedTotal, 2)) {
                    return $this->renderEditWithErrors($request, $purchase, [
                        'bank_id' => 'Insufficient bank balance. Available: PKR ' . number_format($bankBalance, 2) . ', Required: PKR ' . number_format($calculatedTotal, 2)
                    ], 'Insufficient bank balance. Available: PKR ' . number_format($bankBalance, 2) . ', Required: PKR ' . number_format($calculatedTotal, 2));
                }
            }

            // Duplicate serials within request (arm lines)
            $allSerials = [];
            if ($request->arm_lines) {
                foreach ($request->arm_lines as $armLine) {
                    if (isset($armLine['serials']) && !empty($armLine['serials'])) {
                        $serials = array_map('trim', explode(',', $armLine['serials']));
                        foreach ($serials as $serial) {
                            if (!empty($serial)) { $allSerials[] = $serial; }
                        }
                    }
                }
            }
            $duplicateSerials = array_diff_assoc($allSerials, array_unique($allSerials));
            if (!empty($duplicateSerials)) {
                $message = 'Duplicate serial numbers found: ' . implode(', ', array_unique($duplicateSerials));
                return $this->renderEditWithErrors($request, $purchase, [
                    'arm_lines.*.serials' => $message,
                ], $message);
            }

            // Existing serials in DB (exclude current purchase arms)
            if (!empty($allSerials)) {
                $existingSerials = Arm::where('business_id', $businessId)
                    ->whereIn('serial_no', $allSerials)
                    ->where(function($q) use ($purchase) {
                        $q->whereNull('purchase_id')->orWhere('purchase_id', '!=', $purchase->id);
                    })
                    ->pluck('serial_no')
                    ->toArray();
                if (!empty($existingSerials)) {
                    $errors = [];
                    foreach ($request->arm_lines as $index => $armLine) {
                        if (isset($armLine['serials']) && !empty($armLine['serials'])) {
                            $serials = array_map('trim', explode(',', $armLine['serials']));
                            $lineExistingSerials = array_intersect($serials, $existingSerials);
                            if (!empty($lineExistingSerials)) {
                                $errors["arm_lines.{$index}.serials"] = 'Serial numbers already exist in database: ' . implode(', ', $lineExistingSerials);
                            }
                        }
                    }
                    if (!empty($errors)) {
                        $message = 'Serial numbers already exist: ' . implode(', ', array_unique($existingSerials));
                        return $this->renderEditWithErrors($request, $purchase, $errors, $message);
                    }
                }
            }

            // If this is a posted purchase, adjust inventory and financial entries before making changes
            if ($purchase->isPosted()) {
                $purchase->adjustInventoryForEdit(
                    $request->general_lines ?? [],
                    $request->arm_lines ?? []
                );
                
                // Reverse old financial entries before updating
                $this->reverseFinancialEntries($purchase, 'Purchase update - reversing old entries');
            }

            // Update purchase header
            $purchase->update([
                'party_id' => $request->party_id, // Can be null for cash payments
                'payment_type' => $request->payment_type,
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
            ]);

            // Delete existing lines
            $purchase->generalLines()->delete();
            $purchase->armLines()->delete();

            // Recreate general lines and update item prices if changed
            if ($request->general_lines) {
                foreach ($request->general_lines as $index => $lineData) {
                    $purchase->generalLines()->create([
                        'line_no' => $index + 1,
                        'general_item_id' => $lineData['general_item_id'],
                        'description' => $lineData['description'] ?? null,
                        'qty' => $lineData['qty'],
                        'unit_price' => $lineData['unit_price'],
                        'sale_price' => $lineData['sale_price'],
                    ]);

                    // Update general item prices if they have changed
                    $this->updateGeneralItemPrices($lineData);
                }
            }

            // Recreate arm lines and serials
            if ($request->arm_lines) {
                foreach ($request->arm_lines as $index => $lineData) {
                    // Calculate qty from serials
                    $serials = isset($lineData['serials']) ? array_map('trim', explode(',', $lineData['serials'])) : [];
                    $qty = count(array_filter($serials, function($serial) { return !empty($serial); }));
                    
                    $armLine = $purchase->armLines()->create([
                        'line_no' => $index + 1,
                        'qty' => $qty,
                        'unit_price' => $lineData['unit_price'],
                        'sale_price' => $lineData['sale_price'],
                        'arm_type_id' => $lineData['arm_type_id'] ?? null,
                        'arm_make_id' => $lineData['arm_make_id'] ?? null,
                        'arm_caliber_id' => $lineData['arm_caliber_id'] ?? null,
                        'arm_category_id' => $lineData['arm_category_id'] ?? null,
                        'arm_condition_id' => $lineData['arm_condition_id'] ?? null,
                    ]);

                    // Create arm serials
                    if (isset($lineData['serials']) && !empty($lineData['serials'])) {
                        foreach ($serials as $serialNo) {
                            if (!empty($serialNo)) {
                                $armLine->armSerials()->create([
                                    'serial_no' => $serialNo,
                                    'arm_title' => $lineData['arm_title'] ?? 'Arm from Purchase',
                                    'make_id' => $lineData['arm_make_id'] ?? null,
                                    'caliber_id' => $lineData['arm_caliber_id'] ?? null,
                                    'category_id' => $lineData['arm_category_id'] ?? null,
                                    'purchase_price' => $lineData['unit_price'] ?? null,
                                    'purchase_date' => $request->invoice_date,
                                    'sale_price' => $lineData['sale_price'] ?? null,
                                    'condition_id' => $lineData['arm_condition_id'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }

            // Calculate and update totals
            $purchase->subtotal = $purchase->calculateSubtotal();
            $purchase->total_amount = $purchase->calculateTotalAmount();
            $purchase->save();

            // If this was a posted purchase, create new financial entries
            if ($purchase->isPosted()) {
                $this->createFinancialEntries($purchase);
            }

            DB::commit();

            $message = $purchase->isPosted() 
                ? 'Posted purchase updated successfully. Inventory has been adjusted accordingly.'
                : 'Purchase updated successfully';

            return redirect()->route('purchases.show', $purchase)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase update failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Show the actual error message
            $errorMessage = 'Failed to update purchase. Error: ' . $e->getMessage();
            
            return redirect()->back()
                ->withErrors(['error' => $errorMessage])
                ->withInput();
        }
    }

    /**
     * Post a draft purchase.
     */
    public function post(Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        if (!$purchase->canBePosted()) {
            return redirect()->back()->withErrors(['error' => 'This purchase cannot be posted']);
        }

        try {
            DB::beginTransaction();

            $this->postPurchase($purchase);

            DB::commit();

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase posted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase posting failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to post purchase: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel a posted purchase.
     */
    public function cancel(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        if (!$purchase->canBeCancelled()) {
            return redirect()->back()->withErrors(['error' => 'This purchase cannot be cancelled']);
        }

        try {
            DB::beginTransaction();

            $this->cancelPurchase($purchase, $request->reason);

            DB::commit();

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase cancellation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to cancel purchase: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified purchase from storage.
     */
    public function destroy(Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        if (!$purchase->isDraft()) {
            return redirect()->back()->withErrors(['error' => 'Only draft purchases can be deleted']);
        }

        try {
            DB::beginTransaction();

            // Delete related records
            $purchase->generalLines()->delete();
            $purchase->armLines()->delete();
            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase deletion failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete purchase']);
        }
    }

    /**
     * Show stock impacts preview for a purchase.
     */
    public function stockImpacts(Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        $purchase->load([
            'generalLines.generalItem',
            'armLines.armSerials'
        ]);

        $allocations = $purchase->allocateCharges();

        return view('purchases.stock-impacts', compact('purchase', 'allocations'));
    }

    /**
     * Post a purchase (create inventory entries).
     */
    private function postPurchase(Purchase $purchase)
    {
        // Validate for posting
        $errors = $purchase->validateForPosting();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        // Check for existing serial numbers in arms_inventory (business-specific)
        $serialNumbers = $purchase->armSerials()->pluck('serial_no')->toArray();
        $existingSerials = Arm::where('business_id', $purchase->business_id)
            ->whereIn('serial_no', $serialNumbers)
            ->pluck('serial_no')
            ->toArray();
        
        if (!empty($existingSerials)) {
            throw new \Exception('Serial numbers already exist: ' . implode(', ', $existingSerials));
        }

        // Get allocations
        $allocations = $purchase->allocateCharges();

        // Create general item batches and stock ledger entries
        foreach ($purchase->generalLines as $line) {
            $allocation = $allocations[$line->id] ?? null;
            
            // For now, just use the line's unit_price to avoid allocation issues
            $effectiveUnitCost = $line->unit_price;
            
            // $effectiveUnitCost = $allocation ? $allocation['effective_unit_cost'] : $line->getEffectiveUnitCost();
            


            // Create batch
            $batch = GeneralBatch::create([
                'business_id' => $purchase->business_id,
                'item_id' => $line->general_item_id,
                'qty_received' => $line->qty,
                'qty_remaining' => $line->qty,
                'unit_cost' => $effectiveUnitCost,
                'total_cost' => $line->qty * $effectiveUnitCost,
                'received_date' => $purchase->invoice_date,
                'user_id' => $purchase->created_by,
                'purchase_id' => $purchase->id,
                'purchase_line_id' => $line->id,
                'batch_code' => 'PUR-' . $purchase->id . '-' . $line->line_no,
                'status' => 'active',
            ]);

            // Create stock ledger entry
            GeneralItemStockLedger::create([
                'business_id' => $purchase->business_id,
                'general_item_id' => $line->general_item_id,
                'batch_id' => $batch->id,
                'transaction_type' => 'purchase',
                'transaction_date' => $purchase->invoice_date,
                'quantity' => $line->qty,
                'quantity_in' => $line->qty,
                'quantity_out' => 0,
                'balance_quantity' => $line->qty, // This will be calculated by the system
                'unit_cost' => $effectiveUnitCost,
                'total_cost' => $line->qty * $effectiveUnitCost,
                'reference_id' => $purchase->id,
                'purchase_id' => $purchase->id,
                'purchase_line_id' => $line->id,
                'remarks' => 'Purchase from ' . ($purchase->party ? $purchase->party->name : 'Cash Purchase'),
                'created_by' => $purchase->created_by,
            ]);
        }

        // Create arm inventory and stock ledger entries
        foreach ($purchase->armLines as $line) {
            foreach ($line->armSerials as $serial) {
                $effectivePrice = $serial->getEffectivePurchasePrice();
                $effectiveSalePrice = $serial->sale_price ?? $line->sale_price;

                // Create arm inventory
                $arm = Arm::create([
                    'business_id' => $purchase->business_id,
                    'arm_type_id' => $line->arm_type_id ?? $serial->category?->arm_type_id ?? 1, // Default type
                    'arm_category_id' => $line->arm_category_id ?? $serial->category_id ?? 1, // Default category
                    'make' => $line->arm_make_id ? ArmsMake::where('business_id', $purchase->business_id)->find($line->arm_make_id)->arm_make : ($serial->make?->make ?? 'Unknown'),
                    'arm_caliber_id' => $line->arm_caliber_id ?? $serial->caliber_id ?? 1, // Default caliber
                    'arm_condition_id' => $line->arm_condition_id ?? 1, // Default condition
                    'serial_no' => $serial->serial_no,
                    'purchase_price' => $effectivePrice,
                    'sale_price' => $effectiveSalePrice,
                    'purchase_date' => $purchase->invoice_date,
                    'status' => 'available',
                    'arm_title' => $serial->arm_title ?? 'Unknown Arm',
                    'purchase_id' => $purchase->id,
                    'purchase_arm_serial_id' => $serial->id,
                ]);
                
                // Generate and set arm title based on arm attributes
                $arm->update(['arm_title' => $arm->generateArmTitle()]);

                // Create stock ledger entry
                ArmsStockLedger::create([
                    'business_id' => $purchase->business_id,
                    'arm_id' => $arm->id,
                    'transaction_date' => $purchase->invoice_date,
                    'transaction_type' => 'purchase',
                    'quantity_in' => 1,
                    'quantity_out' => 0,
                    'balance' => 1,
                    'reference_id' => $purchase->id,
                    'purchase_id' => $purchase->id,
                    'purchase_arm_serial_id' => $serial->id,
                    'remarks' => 'Purchase from ' . ($purchase->party ? $purchase->party->name : 'Cash Purchase'),
                ]);

                // Create arm history
                ArmHistory::create([
                    'business_id' => $purchase->business_id,
                    'arm_id' => $arm->id,
                    'action' => 'purchase',
                    'old_values' => null,
                    'new_values' => json_encode($arm->toArray()),
                    'transaction_date' => $purchase->invoice_date,
                    'price' => $arm->purchase_price,
                    'remarks' => 'Purchase from ' . ($purchase->party ? $purchase->party->name : 'Cash Purchase'),
                    'user_id' => $purchase->created_by,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }

        // Create financial accounting entries
        $this->createFinancialEntries($purchase);

        // Mark purchase as posted
        $purchase->markAsPosted();
    }

    /**
     * Create financial accounting entries for purchase.
     */
    private function createFinancialEntries(Purchase $purchase)
    {
        // Get required account IDs
        $inventoryAccountId = \App\Models\ChartOfAccount::where('code', '1250') // Inventory account
            ->where('business_id', $purchase->business_id)
            ->value('id');

        if (!$inventoryAccountId) {
            throw new \Exception('Inventory account (1250) not found. Please ensure the chart of accounts is properly set up.');
        }

        // Get party's chart of account for credit purchases
        $partyAccountId = null;
        if ($purchase->payment_type === 'credit' && $purchase->party_id) {
            $party = \App\Models\Party::find($purchase->party_id);
            if ($party) {
                // If party doesn't have a chart of account, create one
                if (!$party->chart_of_account_id) {
                    $partyAccount = \App\Models\ChartOfAccount::createPartyAccount($party->name, $purchase->business_id);
                    $party->update(['chart_of_account_id' => $partyAccount->id]);
                    $party->refresh();
                }
                $partyAccountId = $party->chart_of_account_id;

                if (!$partyAccountId) {
                    throw new \Exception('Party chart of account not found. Please ensure the party has a chart of account assigned.');
                }

        // Create party ledger entry for credit purchases
            \App\Models\PartyLedger::create([
                'business_id' => $purchase->business_id,
                'party_id' => $purchase->party_id,
                'voucher_id' => $purchase->id,
                'voucher_type' => 'Purchase',
                'date_added' => $purchase->invoice_date,
                'user_id' => $purchase->created_by,
                'debit_amount' => 0,
                'credit_amount' => $purchase->total_amount, // Credit party - you owe them this amount
            ]);
            }
        }

        // Create bank ledger entry for cash purchases
        if ($purchase->payment_type === 'cash' && $purchase->bank_id) {
            $bank = \App\Models\Bank::find($purchase->bank_id);
            if (!$bank->chart_of_account_id) {
                throw new \Exception('Bank account does not have a chart of account assigned.');
            }

            \App\Models\BankLedger::create([
                'business_id' => $purchase->business_id,
                'bank_id' => $purchase->bank_id,
                'voucher_id' => $purchase->id,
                'voucher_type' => 'Purchase',
                'date' => $purchase->invoice_date,
                'user_id' => $purchase->created_by,
                'withdrawal_amount' => $purchase->total_amount,
                'deposit_amount' => 0,
            ]);
        }

        // Create journal entries
        if ($purchase->payment_type === 'credit' && $purchase->party_id && $partyAccountId) {
            // Credit Purchase: Debit Inventory, Credit Party Account
            \App\Models\JournalEntry::create([
                'business_id' => $purchase->business_id,
                'account_head' => $inventoryAccountId,
                'voucher_id' => $purchase->id,
                'voucher_type' => 'Purchase',
                'date_added' => $purchase->invoice_date,
                'user_id' => $purchase->created_by,
                'debit_amount' => $purchase->total_amount,
                'credit_amount' => 0,
            ]);
            
            \App\Models\JournalEntry::create([
                'business_id' => $purchase->business_id,
                'account_head' => $partyAccountId,
                'voucher_id' => $purchase->id,
                'voucher_type' => 'Purchase',
                'date_added' => $purchase->invoice_date,
                'user_id' => $purchase->created_by,
                'debit_amount' => 0,
                'credit_amount' => $purchase->total_amount,
            ]);
        } else if ($purchase->payment_type === 'cash' && $purchase->bank_id) {
            // Cash Purchase: Debit Inventory, Credit Bank
                \App\Models\JournalEntry::create([
                    'business_id' => $purchase->business_id,
                    'account_head' => $inventoryAccountId,
                    'voucher_id' => $purchase->id,
                    'voucher_type' => 'Purchase',
                    'date_added' => $purchase->invoice_date,
                    'user_id' => $purchase->created_by,
                    'debit_amount' => $purchase->total_amount,
                    'credit_amount' => 0,
                ]);
                
                \App\Models\JournalEntry::create([
                    'business_id' => $purchase->business_id,
                    'account_head' => $bank->chart_of_account_id,
                    'voucher_id' => $purchase->id,
                    'voucher_type' => 'Purchase',
                    'date_added' => $purchase->invoice_date,
                    'user_id' => $purchase->created_by,
                    'debit_amount' => 0,
                    'credit_amount' => $purchase->total_amount,
                ]);
        }
    }

    /**
     * Cancel a posted purchase (reverse inventory entries).
     */
    private function cancelPurchase(Purchase $purchase, string $reason = null)
    {
        // Check if purchase is already cancelled to prevent duplicates
        if ($purchase->status === 'cancelled') {
            \Log::info('Purchase already cancelled, skipping cancelPurchase', [
                'purchase_id' => $purchase->id,
                'status' => $purchase->status
            ]);
            return;
        }

        // Reverse general item batches
        foreach ($purchase->generalBatches as $batch) {
            // Get all purchase entries for this purchase and item
            $purchaseEntries = GeneralItemStockLedger::where('business_id', $purchase->business_id)
                ->where('general_item_id', $batch->item_id)
                ->where('purchase_id', $purchase->id)
                ->where('transaction_type', 'purchase')
                ->get();

            // Get all reversal entries for this purchase and item
            $reversalEntries = GeneralItemStockLedger::where('business_id', $purchase->business_id)
                ->where('general_item_id', $batch->item_id)
                ->where('purchase_id', $purchase->id)
                ->where('transaction_type', 'reversal')
                ->get();

            // Check if all purchase entries have been reversed
            $totalPurchaseQty = $purchaseEntries->sum('quantity');
            $totalReversalQty = $reversalEntries->sum('quantity');

            if (abs($totalPurchaseQty) == abs($totalReversalQty)) {
                \Log::info('All purchase entries for this item have already been reversed, skipping', [
                    'purchase_id' => $purchase->id,
                    'batch_id' => $batch->id,
                    'item_id' => $batch->item_id,
                    'total_purchase_qty' => $totalPurchaseQty,
                    'total_reversal_qty' => $totalReversalQty
                ]);
                continue;
            }

            // Create reversals for each unreversed purchase entry
            foreach ($purchaseEntries as $purchaseEntry) {
                // Check if this specific purchase entry has been reversed
                $entryReversed = $reversalEntries->where('quantity', -$purchaseEntry->quantity)
                    ->where('unit_cost', $purchaseEntry->unit_cost)
                    ->isNotEmpty();

                if ($entryReversed) {
                    \Log::info('Purchase entry already reversed, skipping', [
                        'purchase_entry_id' => $purchaseEntry->id,
                        'quantity' => $purchaseEntry->quantity,
                        'unit_cost' => $purchaseEntry->unit_cost
                    ]);
                    continue;
                }

                // Create reversal for this specific purchase entry
                GeneralItemStockLedger::create([
                    'business_id' => $purchase->business_id,
                    'general_item_id' => $batch->item_id,
                    'batch_id' => $batch->id,
                    'transaction_type' => 'reversal',
                    'transaction_date' => $purchaseEntry->transaction_date, // Use original purchase date
                    'quantity' => -$purchaseEntry->quantity,
                    'quantity_in' => 0,
                    'quantity_out' => $purchaseEntry->quantity,
                    'balance_quantity' => 0,
                    'unit_cost' => $purchaseEntry->unit_cost,
                    'total_cost' => -$purchaseEntry->total_cost,
                    'reference_id' => $purchase->id,
                    'purchase_id' => $purchase->id,
                    'purchase_line_id' => $batch->purchase_line_id,
                    'remarks' => 'Purchase cancellation: ' . ($reason ?? 'No reason provided'),
                    'created_by' => auth()->id() ?? $purchase->created_by,
                ]);

                \Log::info('Created reversal for purchase entry', [
                    'purchase_entry_id' => $purchaseEntry->id,
                    'quantity' => $purchaseEntry->quantity,
                    'unit_cost' => $purchaseEntry->unit_cost
                ]);
            }

            // Mark batch as reversed
            $batch->update([
                'status' => 'reversed',
                'qty_remaining' => 0,
            ]);
        }

        // Reverse arm inventory
        foreach ($purchase->arms as $arm) {
            // Create reversal stock ledger entry
            ArmsStockLedger::create([
                'business_id' => $purchase->business_id,
                'arm_id' => $arm->id,
                'transaction_date' => now(),
                'transaction_type' => 'reversal',
                'quantity_in' => 0,
                'quantity_out' => 1,
                'balance' => 0,
                'reference_id' => $purchase->id,
                'purchase_id' => $purchase->id,
                'purchase_arm_serial_id' => $arm->purchase_arm_serial_id,
                'remarks' => 'Purchase cancellation: ' . ($reason ?? 'No reason provided'),
            ]);

            // Store old values before update
            $oldValues = $arm->toArray();
            
            // Mark arm as decommissioned
            $arm->update(['status' => 'decommissioned']);
            
            // Create arm history
            ArmHistory::create([
                'business_id' => $purchase->business_id,
                'arm_id' => $arm->id,
                'action' => 'decommission',
                'old_values' => $oldValues, // Store as array, not JSON string
                'new_values' => $arm->fresh()->toArray(), // Store as array, not JSON string
                'transaction_date' => now(),
                'price' => $arm->purchase_price ?? 0,
                'remarks' => 'Purchase cancellation: ' . ($reason ?? 'No reason provided'),
                'user_id' => auth()->id() ?? $purchase->created_by,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Reverse financial accounting entries
        $this->reverseFinancialEntries($purchase, $reason);

        // Mark purchase as cancelled (force update even if items are already reversed)
        $purchase->status = 'cancelled';
        $purchase->save();
    }

    /**
     * Reverse financial accounting entries for cancelled purchase.
     */
    private function reverseFinancialEntries(Purchase $purchase, string $reason = null)
    {
        $remarks = 'Purchase cancellation: ' . ($reason ?? 'No reason provided');

        // Check if this is a cancellation or an edit
        $isEdit = str_contains($reason ?? '', 'update');

        if ($isEdit) {
            // For edits, delete existing journal entries instead of creating reversal entries
            \App\Models\JournalEntry::where('business_id', $purchase->business_id)
                ->where('voucher_id', $purchase->id)
                ->where('voucher_type', 'Purchase')
                ->delete();

            // Delete existing party ledger entries
            \App\Models\PartyLedger::where('business_id', $purchase->business_id)
                ->where('voucher_id', $purchase->id)
                ->where('voucher_type', 'Purchase')
                ->delete();

            // Delete existing bank ledger entries
            \App\Models\BankLedger::where('business_id', $purchase->business_id)
                ->where('voucher_id', $purchase->id)
                ->where('voucher_type', 'Purchase')
                ->delete();
        } else {
            // For cancellations, create reversal entries
            // Reverse party ledger entry for credit purchases
            if ($purchase->payment_type === 'credit' && $purchase->party_id) {
                \App\Models\PartyLedger::create([
                    'business_id' => $purchase->business_id,
                    'party_id' => $purchase->party_id,
                    'voucher_id' => $purchase->id,
                    'voucher_type' => 'Purchase Cancellation',
                    'date_added' => now(),
                    'user_id' => auth()->id() ?? $purchase->created_by,
                    'debit_amount' => $purchase->total_amount, // Reverse the credit (debit to cancel)
                    'credit_amount' => 0,
                ]);
            }

            // Reverse bank ledger entry for cash purchases
            if ($purchase->payment_type === 'cash' && $purchase->bank_id) {
                \App\Models\BankLedger::create([
                        'business_id' => $purchase->business_id,
                        'bank_id' => $purchase->bank_id,
                        'voucher_id' => $purchase->id,
                        'voucher_type' => 'Purchase Cancellation',
                        'date' => now(),
                        'user_id' => auth()->id() ?? $purchase->created_by,
                        'withdrawal_amount' => 0,
                        'deposit_amount' => $purchase->total_amount, // Reverse the withdrawal
                    ]);
            }

            // Get required account IDs for reversal
            $inventoryAccountId = \App\Models\ChartOfAccount::where('code', '1250') // Inventory account
                ->where('business_id', $purchase->business_id)
                ->value('id');

            if (!$inventoryAccountId) {
                throw new \Exception('Inventory account (1250) not found for reversal.');
            }

            // Get party's chart of account for reversal
            $partyAccountId = null;
            if ($purchase->payment_type === 'credit' && $purchase->party_id) {
                $party = \App\Models\Party::find($purchase->party_id);
                if ($party && $party->chart_of_account_id) {
                    $partyAccountId = $party->chart_of_account_id;
                } else {
                        throw new \Exception('Party chart of account not found for reversal.');
                }
            }

            // Reverse journal entries
            if ($purchase->payment_type === 'credit' && $purchase->party_id && $partyAccountId) {
                // Credit Purchase Reversal: Credit Inventory, Debit Party Account
            \App\Models\JournalEntry::create([
                'business_id' => $purchase->business_id,
                'account_head' => $inventoryAccountId,
                'voucher_id' => $purchase->id,
                'voucher_type' => 'Purchase Cancellation',
                'date_added' => now(),
                'user_id' => auth()->id() ?? $purchase->created_by,
                'debit_amount' => 0,
                'credit_amount' => $purchase->total_amount,
            ]);

                \App\Models\JournalEntry::create([
                    'business_id' => $purchase->business_id,
                    'account_head' => $partyAccountId,
                    'voucher_id' => $purchase->id,
                    'voucher_type' => 'Purchase Cancellation',
                    'date_added' => now(),
                    'user_id' => auth()->id() ?? $purchase->created_by,
                    'debit_amount' => $purchase->total_amount,
                    'credit_amount' => 0,
                ]);
            } else if ($purchase->payment_type === 'cash' && $purchase->bank_id) {
                $bank = \App\Models\Bank::find($purchase->bank_id);
                if (!$bank->chart_of_account_id) {
                    throw new \Exception('Bank account does not have a chart of account assigned for reversal.');
                }

                // Cash Purchase Reversal: Credit Inventory, Debit Bank
                    \App\Models\JournalEntry::create([
                        'business_id' => $purchase->business_id,
                    'account_head' => $inventoryAccountId,
                        'voucher_id' => $purchase->id,
                        'voucher_type' => 'Purchase Cancellation',
                        'date_added' => now(),
                        'user_id' => auth()->id() ?? $purchase->created_by,
                        'debit_amount' => 0,
                        'credit_amount' => $purchase->total_amount,
                ]);

                    \App\Models\JournalEntry::create([
                        'business_id' => $purchase->business_id,
                        'account_head' => $bank->chart_of_account_id,
                        'voucher_id' => $purchase->id,
                        'voucher_type' => 'Purchase Cancellation',
                        'date_added' => now(),
                        'user_id' => auth()->id() ?? $purchase->created_by,
                        'debit_amount' => $purchase->total_amount,
                        'credit_amount' => 0,
                    ]);
            }
        }
    }

    /**
     * Show audit log for a purchase.
     */
    public function auditLog(Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        $auditLogs = $purchase->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchases.audit-log', compact('purchase', 'auditLogs'));
    }

    /**
     * Check for existing arm serial numbers via API.
     */
    public function checkArmSerials(Request $request)
    {
        $businessId = session('active_business');
        
        $request->validate([
            'serials' => 'required|array',
            'serials.*' => 'string'
        ]);
        
        $serials = $request->serials;
        $existingSerials = Arm::where('business_id', $businessId)
            ->whereIn('serial_no', $serials)
            ->pluck('serial_no')
            ->toArray();
        
        return response()->json([
            'existing_serials' => $existingSerials,
            'all_serials' => $serials
        ]);
    }

    /**
     * Authorize purchase access.
     */
    private function authorizePurchase(Purchase $purchase)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Authentication required');
        }

        // Check if user has access to the purchase's business
        $userHasAccessToBusiness = $user->businesses()->where('business_id', $purchase->business_id)->exists();
        
        if (!$userHasAccessToBusiness) {
            \Log::warning('User does not have access to purchase business', [
                'user_id' => $user->id,
                'purchase_business_id' => $purchase->business_id,
                'user_businesses' => $user->businesses->pluck('id')->toArray()
            ]);
            abort(403, 'Unauthorized access to purchase - you do not have access to this business');
        }
        
        // Set the purchase's business as the active business in session
        session(['active_business' => $purchase->business_id]);
        
        \Log::info('Purchase authorization successful', [
            'user_id' => $user->id,
            'purchase_id' => $purchase->id,
            'business_id' => $purchase->business_id
        ]);
    }

    /**
     * Update general item prices if they have changed during purchase.
     * This updates the item's cost_price and sale_price for future purchases.
     * Also updates existing inventory batches (except opening stock) with new prices.
     */
    private function updateGeneralItemPrices($lineData)
    {
        try {
            $generalItem = GeneralItem::find($lineData['general_item_id']);
            
            if (!$generalItem) {
                return;
            }

            $newCostPrice = floatval($lineData['unit_price']);
            $newSalePrice = floatval($lineData['sale_price'] ?? 0);
            
            $priceChanged = false;
            $changes = [];

            // Check if cost price has changed
            if (abs($generalItem->cost_price - $newCostPrice) > 0.01) {
                $changes['cost_price'] = [
                    'old' => $generalItem->cost_price,
                    'new' => $newCostPrice
                ];
                $priceChanged = true;
            }

            // Check if sale price has changed
            if (abs($generalItem->sale_price - $newSalePrice) > 0.01) {
                $changes['sale_price'] = [
                    'old' => $generalItem->sale_price,
                    'new' => $newSalePrice
                ];
                $priceChanged = true;
            }

            // Update the item prices if they have changed
            if ($priceChanged) {
                $generalItem->update([
                    'cost_price' => $newCostPrice,
                    'sale_price' => $newSalePrice
                ]);

                // Update existing inventory batches (except opening stock)
                $this->updateExistingInventoryPrices($generalItem, $newCostPrice, $newSalePrice);

                // Log the price change for audit purposes
                \Log::info('General item prices updated during purchase', [
                    'item_id' => $generalItem->id,
                    'item_name' => $generalItem->item_name,
                    'business_id' => $generalItem->business_id,
                    'changes' => $changes,
                    'purchase_line_data' => $lineData
                ]);

                // You could also create an audit log entry here if you have an audit system
                // $this->createPriceChangeAuditLog($generalItem, $changes);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update general item prices', [
                'line_data' => $lineData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw the exception to avoid breaking the purchase process
            // The price update is not critical for the purchase to succeed
        }
    }

    /**
     * Update existing inventory batches with new prices (except opening stock).
     * This ensures that existing inventory reflects the updated prices.
     */
    private function updateExistingInventoryPrices($generalItem, $newCostPrice, $newSalePrice)
    {
        try {
            // Get all active batches for this item (excluding opening stock)
            $batches = GeneralBatch::where('item_id', $generalItem->id)
                ->where('status', 'active')
                ->where('qty_remaining', '>', 0)
                ->whereNotNull('purchase_id') // Exclude opening stock (which has no purchase_id)
                ->get();

            $updatedBatches = 0;
            $totalQuantity = 0;

            foreach ($batches as $batch) {
                // Update the batch with new unit cost
                $oldUnitCost = $batch->unit_cost;
                $newTotalCost = $batch->qty_remaining * $newCostPrice;
                
                $batch->update([
                    'unit_cost' => $newCostPrice,
                    'total_cost' => $newTotalCost
                ]);

                $updatedBatches++;
                $totalQuantity += $batch->qty_remaining;

                // Log the batch update
                \Log::info('Updated batch prices', [
                    'batch_id' => $batch->id,
                    'batch_code' => $batch->batch_code,
                    'item_id' => $generalItem->id,
                    'item_name' => $generalItem->item_name,
                    'old_unit_cost' => $oldUnitCost,
                    'new_unit_cost' => $newCostPrice,
                    'quantity_remaining' => $batch->qty_remaining,
                    'old_total_cost' => $batch->getOriginal('total_cost'),
                    'new_total_cost' => $newTotalCost
                ]);
            }

            // Log summary of inventory updates
            if ($updatedBatches > 0) {
                \Log::info('Inventory prices updated successfully', [
                    'item_id' => $generalItem->id,
                    'item_name' => $generalItem->item_name,
                    'batches_updated' => $updatedBatches,
                    'total_quantity_updated' => $totalQuantity,
                    'new_cost_price' => $newCostPrice,
                    'new_sale_price' => $newSalePrice
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to update existing inventory prices', [
                'item_id' => $generalItem->id,
                'item_name' => $generalItem->item_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw the exception to avoid breaking the purchase process
        }
    }
}
