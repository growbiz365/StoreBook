<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\Country;
use App\Models\Timezone;
use App\Models\City;
use App\Models\Currency;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    /**
     * Display a listing of the businesses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $businesses = Business::with(['country', 'timezone', 'currency', 'package']);

        // Only super-admin sees all businesses
        if (!$user->hasRole('Super Admin')) {
            $businesses = $businesses->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $businesses = $businesses->where(function ($query) use ($search) {
                $query->where('business_name', 'like', "%$search%")
                    ->orWhere('owner_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('country', function ($q) use ($search) {
                        $q->where('country_name', 'like', "%$search%");
                    })
                    ->orWhereHas('timezone', function ($q) use ($search) {
                        $q->where('timezone_name', 'like', "%$search%");
                    });
            });
        }

        $businesses = $businesses->paginate(10);
        return view('businesses.index', compact('businesses'));
    }

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        $countries = Country::orderBy('country_name')->get();
        $timezones = Timezone::orderBy('timezone_name')->get();
        $currencies = Currency::orderBy('currency_name')->get();
        $packages = Package::orderBy('package_name')->get();
        $cities = City::orderBy('name')->get();
        $isAdmin = auth()->user()->hasRole('admin');
        return view('businesses.create', compact('countries', 'timezones', 'currencies', 'packages', 'cities', 'isAdmin'));
    }

    /**
     * Store a newly created business in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Only business_name and owner_name are required
            $validated = $request->validate([
                // Business info
                'business_name' => 'required|string|max:255',
                'owner_name' => 'required|string|max:255',
                'cnic' => 'nullable|string|max:15|unique:businesses,cnic',
                'contact_no' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:businesses,email',
                'address' => 'nullable|string',
                'country_id' => 'nullable|exists:countries,id',
                'timezone_id' => 'nullable|exists:timezones,id',
                'currency_id' => 'nullable|exists:currencies,id',
                'date_format' => 'nullable|in:Y-m-d,d/m/Y,m/d/Y,d-m-Y',
                'package_id' => 'required|exists:packages,id',
                // Store info
                'store_name' => 'nullable|string|max:255',
                'store_license_number' => 'nullable|string|max:100',
                'license_expiry_date' => 'nullable|date',
                'issuing_authority' => 'nullable|string|max:255',
                'store_type' => 'nullable|string|max:100',
                'ntn' => 'nullable|string|max:50',
                'strn' => 'nullable|string|max:50',
                'store_phone' => 'nullable|string|max:20',
                'store_email' => 'nullable|email',
                'store_address' => 'nullable|string',
                'store_city_id' => 'nullable|exists:cities,id',
                'store_country_id' => 'nullable|exists:countries,id',
                'store_postal_code' => 'nullable|string|max:20',
            ]);
        } else {
            // Non-admin: validate only store info, but still require business_name, owner_name, and package_id
            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'owner_name' => 'required|string|max:255',
                'package_id' => 'required|exists:packages,id',
                'store_name' => 'nullable|string|max:255',
                'store_license_number' => 'nullable|string|max:100',
                'license_expiry_date' => 'nullable|date',
                'issuing_authority' => 'nullable|string|max:255',
                'store_type' => 'nullable|string|max:100',
                'ntn' => 'nullable|string|max:50',
                'strn' => 'nullable|string|max:50',
                'store_phone' => 'nullable|string|max:20',
                'store_email' => 'nullable|email',
                'store_address' => 'nullable|string',
                'store_city_id' => 'nullable|exists:cities,id',
                'store_country_id' => 'nullable|exists:countries,id',
                'store_postal_code' => 'nullable|string|max:20',
            ]);
        }

        $business = Business::create($validated);
        // Optionally assign to current user
        $business->users()->attach(Auth::id());

        // Automatically create chart of accounts for the new business
        $this->createChartOfAccountsForBusiness($business);

        // Create default master data for the new business
        $this->createDefaultMasterDataForBusiness($business);

        return redirect()->route('businesses.index')->with('success', 'Business created successfully.');
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business)
    {
        $business->load(['country', 'timezone', 'currency', 'package']);
        $isAdmin = auth()->user()->hasRole('Super Admin');
        return view('businesses.show', compact('business', 'isAdmin'));
    }

    /**
     * Show the form for editing store info (store details, not business profile).
     */
    public function editStoreInfo(Business $business)
    {
        $countries = Country::orderBy('country_name')->get();
        $cities = City::orderBy('name')->get();
        return view('businesses.edit', compact('business', 'countries', 'cities'));
    }

    /**
     * Update the store info (store details, not business profile).
     */
    public function updateStoreInfo(Request $request, Business $business)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'license_no' => 'nullable|string|max:100',
            'expiry' => 'nullable|date',
            'issuing_authority' => 'nullable|string|max:255',
            'store_type' => 'nullable|string|max:100',
            'ntn' => 'nullable|string|max:50',
            'strn' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);
        $business->update($validated);
        return redirect()->route('businesses.show', $business)->with('success', 'Store information updated successfully.');
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(Business $business)
    {
        $countries = Country::orderBy('country_name')->get();
        $timezones = Timezone::orderBy('timezone_name')->get();
        $currencies = Currency::orderBy('currency_name')->get();
        $packages = Package::orderBy('package_name')->get();
        $cities = City::orderBy('name')->get();
        $isAdmin = auth()->user()->hasRole('Super Admin');
        return view('businesses.edit', compact('business', 'countries', 'timezones', 'currencies', 'packages', 'cities', 'isAdmin'));
    }

    /**
     * Update the specified business in storage.
     */
    public function update(Request $request, Business $business)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Only business_name and owner_name are required
            $validated = $request->validate([
                // Business info
                'business_name' => 'required|string|max:255',
                'owner_name' => 'required|string|max:255',
                'cnic' => 'nullable|string|max:15|unique:businesses,cnic,' . $business->id,
                'contact_no' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:businesses,email,' . $business->id,
                'address' => 'nullable|string',
                'country_id' => 'nullable|exists:countries,id',
                'timezone_id' => 'nullable|exists:timezones,id',
                'currency_id' => 'nullable|exists:currencies,id',
                'date_format' => 'nullable|in:Y-m-d,d/m/Y,m/d/Y,d-m-Y',
                'package_id' => 'required|exists:packages,id',
                // Store info
                'store_name' => 'nullable|string|max:255',
                'store_license_number' => 'nullable|string|max:100',
                'license_expiry_date' => 'nullable|date',
                'issuing_authority' => 'nullable|string|max:255',
                'store_type' => 'nullable|string|max:100',
                'ntn' => 'nullable|string|max:50',
                'strn' => 'nullable|string|max:50',
                'store_phone' => 'nullable|string|max:20',
                'store_email' => 'nullable|email',
                'store_address' => 'nullable|string',
                'store_city_id' => 'nullable|exists:cities,id',
                'store_country_id' => 'nullable|exists:countries,id',
                'store_postal_code' => 'nullable|string|max:20',
            ]);
        } else {
            // Non-admin: validate only store info, but still require business_name, owner_name, and package_id
            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'owner_name' => 'required|string|max:255',
                'package_id' => 'required|exists:packages,id',
                'store_name' => 'nullable|string|max:255',
                'store_license_number' => 'nullable|string|max:100',
                'license_expiry_date' => 'nullable|date',
                'issuing_authority' => 'nullable|string|max:255',
                'store_type' => 'nullable|string|max:100',
                'ntn' => 'nullable|string|max:50',
                'strn' => 'nullable|string|max:50',
                'store_phone' => 'nullable|string|max:20',
                'store_email' => 'nullable|email',
                'store_address' => 'nullable|string',
                'store_city_id' => 'nullable|exists:cities,id',
                'store_country_id' => 'nullable|exists:countries,id',
                'store_postal_code' => 'nullable|string|max:20',
            ]);
        }
        $business->update($validated);
        return redirect()->route('businesses.index')->with('success', 'Business updated successfully.');
    }

    /**
     * Remove the specified business from storage.
     * This will delete ALL data associated with the business including transactions,
     * inventory, master data, and the business record itself.
     */
    public function destroy(Request $request, Business $business)
    {
        // Only Super Admin can delete businesses
        if (!Auth::user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'You do not have permission to delete businesses.');
        }

        // Validate confirmation
        $request->validate([
            'confirmation_text' => 'required|string',
        ]);

        // Check if confirmation matches business name
        if ($request->confirmation_text !== $business->business_name) {
            return redirect()->back()->with('error', 'Business name confirmation does not match. Deletion cancelled.');
        }

        try {
            DB::beginTransaction();

            $businessId = $business->id;
            $businessName = $business->business_name;

            // Phase 1: Delete Audit & Activity Logs
            \DB::table('activity_logs')->where('business_id', $businessId)->delete();
            
            // Get all purchases for this business to delete their audit logs
            $purchaseIds = \DB::table('purchases')->where('business_id', $businessId)->pluck('id');
            if ($purchaseIds->isNotEmpty()) {
                \DB::table('purchase_audit_logs')->whereIn('purchase_id', $purchaseIds)->delete();
            }

            // Get all sale invoices for this business to delete their audit logs
            $saleInvoiceIds = \DB::table('sale_invoices')->where('business_id', $businessId)->pluck('id');
            if ($saleInvoiceIds->isNotEmpty()) {
                \DB::table('sale_invoice_audit_logs')->whereIn('sale_invoice_id', $saleInvoiceIds)->delete();
            }

            // Get all sale returns for this business to delete their audit logs
            $saleReturnIds = \DB::table('sale_returns')->where('business_id', $businessId)->pluck('id');
            if ($saleReturnIds->isNotEmpty()) {
                \DB::table('sale_return_audit_logs')->whereIn('sale_return_id', $saleReturnIds)->delete();
            }

            // Get all purchase returns for this business to delete their audit logs
            $purchaseReturnIds = \DB::table('purchase_returns')->where('business_id', $businessId)->pluck('id');
            if ($purchaseReturnIds->isNotEmpty()) {
                \DB::table('purchase_return_audit_logs')->whereIn('purchase_return_id', $purchaseReturnIds)->delete();
            }

            // Phase 2: Delete Transaction Detail Lines (Child Records)
            
            // Sale invoice lines
            if ($saleInvoiceIds->isNotEmpty()) {
                \DB::table('sale_invoice_arms')->whereIn('sale_invoice_id', $saleInvoiceIds)->delete();
                \DB::table('sale_invoice_general_items')->whereIn('sale_invoice_id', $saleInvoiceIds)->delete();
            }

            // Sale return lines
            if ($saleReturnIds->isNotEmpty()) {
                \DB::table('sale_return_arms')->whereIn('sale_return_id', $saleReturnIds)->delete();
                \DB::table('sale_return_general_items')->whereIn('sale_return_id', $saleReturnIds)->delete();
            }

            // Purchase lines (handle nested relationship for arm serials)
            if ($purchaseIds->isNotEmpty()) {
                // First, get all purchase_arm_line_ids for these purchases
                $purchaseArmLineIds = \DB::table('purchase_arm_lines')
                    ->whereIn('purchase_id', $purchaseIds)
                    ->pluck('id');
                
                // Delete purchase_arm_serials using purchase_arm_line_id
                if ($purchaseArmLineIds->isNotEmpty()) {
                    \DB::table('purchase_arm_serials')->whereIn('purchase_arm_line_id', $purchaseArmLineIds)->delete();
                }
                
                // Now delete purchase lines
                \DB::table('purchase_arm_lines')->whereIn('purchase_id', $purchaseIds)->delete();
                \DB::table('purchase_general_lines')->whereIn('purchase_id', $purchaseIds)->delete();
            }

            // Purchase return lines
            if ($purchaseReturnIds->isNotEmpty()) {
                \DB::table('purchase_return_arms')->whereIn('purchase_return_id', $purchaseReturnIds)->delete();
                \DB::table('purchase_return_general_items')->whereIn('purchase_return_id', $purchaseReturnIds)->delete();
            }

            // Quotation lines
            $quotationIds = \DB::table('quotations')->where('business_id', $businessId)->pluck('id');
            if ($quotationIds->isNotEmpty()) {
                \DB::table('quotation_arms')->whereIn('quotation_id', $quotationIds)->delete();
                \DB::table('quotation_general_items')->whereIn('quotation_id', $quotationIds)->delete();
            }

            // Approval lines
            $approvalIds = \DB::table('approvals')->where('business_id', $businessId)->pluck('id');
            if ($approvalIds->isNotEmpty()) {
                \DB::table('approval_arms')->whereIn('approval_id', $approvalIds)->delete();
                \DB::table('approval_general_items')->whereIn('approval_id', $approvalIds)->delete();
            }

            // Stock adjustment lines
            $stockAdjustmentIds = \DB::table('stock_adjustments')->where('business_id', $businessId)->pluck('id');
            if ($stockAdjustmentIds->isNotEmpty()) {
                \DB::table('stock_adjustment_arms')->whereIn('stock_adjustment_id', $stockAdjustmentIds)->delete();
                \DB::table('stock_adjustment_items')->whereIn('stock_adjustment_id', $stockAdjustmentIds)->delete();
            }

            // Phase 3: Delete Main Transactions
            \DB::table('sale_invoices')->where('business_id', $businessId)->delete();
            \DB::table('sale_returns')->where('business_id', $businessId)->delete();
            \DB::table('purchases')->where('business_id', $businessId)->delete();
            \DB::table('purchase_returns')->where('business_id', $businessId)->delete();
            \DB::table('quotations')->where('business_id', $businessId)->delete();
            \DB::table('approvals')->where('business_id', $businessId)->delete();
            \DB::table('stock_adjustments')->where('business_id', $businessId)->delete();

            // Phase 4: Delete Financial Transactions & Attachments
            
            // Expenses
            $expenseIds = \DB::table('expenses')->where('business_id', $businessId)->pluck('id');
            if ($expenseIds->isNotEmpty()) {
                \DB::table('expense_attachments')->whereIn('expense_id', $expenseIds)->delete();
            }
            \DB::table('expenses')->where('business_id', $businessId)->delete();

            // Other incomes
            $otherIncomeIds = \DB::table('other_incomes')->where('business_id', $businessId)->pluck('id');
            if ($otherIncomeIds->isNotEmpty()) {
                \DB::table('other_income_attachments')->whereIn('other_income_id', $otherIncomeIds)->delete();
            }
            \DB::table('other_incomes')->where('business_id', $businessId)->delete();

            // General vouchers
            $generalVoucherIds = \DB::table('general_vouchers')->where('business_id', $businessId)->pluck('id');
            if ($generalVoucherIds->isNotEmpty()) {
                \DB::table('general_voucher_attachments')->whereIn('general_voucher_id', $generalVoucherIds)->delete();
            }
            \DB::table('general_vouchers')->where('business_id', $businessId)->delete();

            // Bank transfers
            $bankTransferIds = \DB::table('bank_transfers')->where('business_id', $businessId)->pluck('id');
            if ($bankTransferIds->isNotEmpty()) {
                \DB::table('bank_transfer_attachments')->whereIn('bank_transfer_id', $bankTransferIds)->delete();
            }
            \DB::table('bank_transfers')->where('business_id', $businessId)->delete();

            // Party transfers
            $partyTransferIds = \DB::table('party_transfers')->where('business_id', $businessId)->pluck('id');
            if ($partyTransferIds->isNotEmpty()) {
                \DB::table('party_transfer_attachments')->whereIn('party_transfer_id', $partyTransferIds)->delete();
            }
            \DB::table('party_transfers')->where('business_id', $businessId)->delete();

            // Journal entries
            \DB::table('journal_entries')->where('business_id', $businessId)->delete();

            // Phase 5: Delete Ledger Entries
            \DB::table('bank_ledger')->where('business_id', $businessId)->delete();
            \DB::table('party_ledgers')->where('business_id', $businessId)->delete();
            \DB::table('arms_stock_ledger')->where('business_id', $businessId)->delete();
            \DB::table('general_items_stock_ledger')->where('business_id', $businessId)->delete();
            \DB::table('arms_history')->where('business_id', $businessId)->delete();
            \DB::table('inventory_transactions')->where('business_id', $businessId)->delete();

            // Phase 6: Delete Inventory Items
            \DB::table('arms')->where('business_id', $businessId)->delete();
            \DB::table('general_batches')->where('business_id', $businessId)->delete();
            \DB::table('general_items')->where('business_id', $businessId)->delete();

            // Phase 7: Delete Master Data
            \DB::table('banks')->where('business_id', $businessId)->delete();
            \DB::table('parties')->where('business_id', $businessId)->delete();
            \DB::table('expense_heads')->where('business_id', $businessId)->delete();
            \DB::table('income_heads')->where('business_id', $businessId)->delete();
            \DB::table('item_types')->where('business_id', $businessId)->delete();
            \DB::table('arms_categories')->where('business_id', $businessId)->delete();
            \DB::table('arms_makes')->where('business_id', $businessId)->delete();
            \DB::table('arms_types')->where('business_id', $businessId)->delete();
            \DB::table('arms_calibers')->where('business_id', $businessId)->delete();
            \DB::table('arms_conditions')->where('business_id', $businessId)->delete();
            \DB::table('chart_of_accounts')->where('business_id', $businessId)->delete();

            // Phase 8: Delete Business-User Relationships
            // The business_user pivot table has cascade delete, but we'll explicitly delete it
            \DB::table('business_user')->where('business_id', $businessId)->delete();

            // Phase 9: Delete the Business Record Itself
        $business->delete();

            // Log the action
            Log::info('Business completely deleted', [
                'business_id' => $businessId,
                'business_name' => $businessName,
                'deleted_by' => Auth::user()->id,
                'deleted_by_name' => Auth::user()->name,
                'timestamp' => now()
            ]);

            DB::commit();

            return redirect()->route('businesses.index')
                ->with('success', 'Business and all associated data have been permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete business', [
                'business_id' => $business->id,
                'business_name' => $business->business_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to delete business: ' . $e->getMessage());
        }
    }

    /**
     * Search for businesses (AJAX, e.g. for comboboxes).
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $user = Auth::user();
        $businesses = Business::query();
        if (!$user->hasRole('Super Admin')) {
            $businesses->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        if ($query) {
            $businesses->where('business_name', 'like', "%$query%")
                ->orWhere('owner_name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%");
        }
        $results = $businesses->limit(10)->get(['id', 'business_name', 'owner_name']);
        return response()->json($results);
    }

    public function setActiveBusiness($businessId)
    {
        $business = Business::findOrFail($businessId);

        if ($business->users->contains(auth()->user())) {
            session(['active_business' => $businessId]);
            session()->save();
            \Log::info('Active business set in session', [
                'user_id' => auth()->user()->id,
                'business_id' => $businessId,
                'session' => session('active_business')
            ]);
        } else {
            \Log::warning('User does not belong to business', [
                'user_id' => auth()->user()->id,
                'business_id' => $businessId
            ]);
        }

        return redirect()->back();
    }

    /**
     * Create chart of accounts for a new business
     */
    private function createChartOfAccountsForBusiness(Business $business)
    {
        // Check if business already has chart of accounts
        if (ChartOfAccount::where('business_id', $business->id)->exists()) {
            return;
        }

        // Assets (1000-1999)
        $assets = $this->createAccount('1000', 'Assets', 'asset', null, $business->id);

        // Current Assets (1100-1199)
        $currentAssets = $this->createAccount('1100', 'Current Assets', 'asset', $assets->id, $business->id);

        // Cash in Hand (1110-1119)
        $cashInHand = $this->createAccount('1110', 'Cash in Hand', 'asset', $currentAssets->id, $business->id);

        // Bank Accounts (1120-1129)
        $bankAccounts = $this->createAccount('1120', 'Bank Accounts', 'asset', $currentAssets->id, $business->id);

        // Receivables (1200-1299)
        $receivables = $this->createAccount('1200', 'Receivables', 'asset', $assets->id, $business->id);
        $this->createAccount('1210', 'Accounts Receivable', 'asset', $receivables->id, $business->id);
        $this->createAccount('1220', 'Employee Advances', 'asset', $receivables->id, $business->id);
        $this->createAccount('1230', 'Security Deposits', 'asset', $receivables->id, $business->id);

        // Inventory (1250-1299)
        $inventory = $this->createAccount('1250', 'Inventory', 'asset', $currentAssets->id, $business->id);

        // Fixed Assets (1300-1399)
        $fixedAssets = $this->createAccount('1300', 'Fixed Assets', 'asset', $assets->id, $business->id);
        $this->createAccount('1310', 'Land', 'asset', $fixedAssets->id, $business->id);
        $this->createAccount('1320', 'Buildings', 'asset', $fixedAssets->id, $business->id);
        $this->createAccount('1330', 'Furniture and Fixtures', 'asset', $fixedAssets->id, $business->id);
        $this->createAccount('1340', 'Computer Equipment', 'asset', $fixedAssets->id, $business->id);
        $this->createAccount('1350', 'Machinery and Equipment', 'asset', $fixedAssets->id, $business->id);
        $this->createAccount('1360', 'Office Equipment', 'asset', $fixedAssets->id, $business->id);
        $this->createAccount('1370', 'Tools and Equipment', 'asset', $fixedAssets->id, $business->id);
        $this->createAccount('1380', 'Vehicles', 'asset', $fixedAssets->id, $business->id);

        // Liabilities (2000-2999)
        $liabilities = $this->createAccount('2000', 'Liabilities', 'liability', null, $business->id);

        // Current Liabilities (2100-2199)
        $currentLiabilities = $this->createAccount('2100', 'Current Liabilities', 'liability', $liabilities->id, $business->id);
        $this->createAccount('2102', 'Accounts Payable', 'liability', $currentLiabilities->id, $business->id);
        $this->createAccount('2120', 'Salary Payable', 'liability', $currentLiabilities->id, $business->id);
        $this->createAccount('2130', 'Tax Payable', 'liability', $currentLiabilities->id, $business->id);
        $this->createAccount('2140', 'Security Deposits', 'liability', $currentLiabilities->id, $business->id);

        // Long Term Liabilities (2200-2299)
        $longTermLiabilities = $this->createAccount('2200', 'Long Term Liabilities', 'liability', $liabilities->id, $business->id);
        $this->createAccount('2210', 'Bank Loans', 'liability', $longTermLiabilities->id, $business->id);
        $this->createAccount('2220', 'Mortgages Payable', 'liability', $longTermLiabilities->id, $business->id);

        // Adjustments & Reconciliation (2300-2399)
        $adjustments = $this->createAccount('2300', 'Adjustments & Reconciliation', 'liability', $liabilities->id, $business->id);
        $this->createAccount('2303', 'Opening Balance Adjustment', 'liability', $adjustments->id, $business->id);

        // Income (3000-3999)
        $income = $this->createAccount('3000', 'Income', 'income', null, $business->id);

        // Sales Income (3100-3199)
        $salesIncome = $this->createAccount('3100', 'Sales Income', 'income', $income->id, $business->id);
        $this->createAccount('3110', 'Product Sales', 'income', $salesIncome->id, $business->id);
        $this->createAccount('3120', 'Service Revenue', 'income', $salesIncome->id, $business->id);
        $this->createAccount('3130', 'Commission Income', 'income', $salesIncome->id, $business->id);
        $this->createAccount('3140', 'Consulting Fees', 'income', $salesIncome->id, $business->id);
        $this->createAccount('3150', 'Rental Income', 'income', $salesIncome->id, $business->id);
        $this->createAccount('3160', 'Licensing Fees', 'income', $salesIncome->id, $business->id);
        $this->createAccount('3170', 'Subscription Revenue', 'income', $salesIncome->id, $business->id);
        $this->createAccount('3180', 'Maintenance Fees', 'income', $salesIncome->id, $business->id);

        // Other Income (3200-3299)
        $otherIncome = $this->createAccount('3200', 'Other Income', 'income', $income->id, $business->id);
        $this->createAccount('3210', 'Interest Income', 'income', $otherIncome->id, $business->id);
        $this->createAccount('3220', 'Dividend Income', 'income', $otherIncome->id, $business->id);
        $this->createAccount('3230', 'Foreign Exchange Gain', 'income', $otherIncome->id, $business->id);
        $this->createAccount('3240', 'Miscellaneous Income', 'income', $otherIncome->id, $business->id);

        // Expenses (4000-4999)
        $expenses = $this->createAccount('4000', 'Expenses', 'expense', null, $business->id);

        // Direct Expenses (4050-4099)
        $directExpenses = $this->createAccount('4050', 'Direct Expenses', 'expense', $expenses->id, $business->id);
        $this->createAccount('4051', 'Cost of Goods Sold', 'expense', $directExpenses->id, $business->id);
        $this->createAccount('4052', 'Direct Labor', 'expense', $directExpenses->id, $business->id);
        $this->createAccount('4053', 'Direct Materials', 'expense', $directExpenses->id, $business->id);
        $this->createAccount('4054', 'Manufacturing Overhead', 'expense', $directExpenses->id, $business->id);
        $this->createAccount('4055', 'Freight and Delivery', 'expense', $directExpenses->id, $business->id);

        // Employee Expenses (4100-4199)
        $employeeExpenses = $this->createAccount('4100', 'Employee Expenses', 'expense', $expenses->id, $business->id);
        $this->createAccount('4110', 'Employee Salaries', 'expense', $employeeExpenses->id, $business->id);
        $this->createAccount('4120', 'Employee Benefits', 'expense', $employeeExpenses->id, $business->id);
        $this->createAccount('4130', 'Employee Training', 'expense', $employeeExpenses->id, $business->id);
        $this->createAccount('4140', 'Employee Insurance', 'expense', $employeeExpenses->id, $business->id);

        // Administrative Expenses (4200-4299)
        $adminExpenses = $this->createAccount('4200', 'Administrative Expenses', 'expense', $expenses->id, $business->id);
        $this->createAccount('4210', 'Office Supplies', 'expense', $adminExpenses->id, $business->id);
        $this->createAccount('4220', 'Utilities', 'expense', $adminExpenses->id, $business->id);
        $this->createAccount('4230', 'Communication', 'expense', $adminExpenses->id, $business->id);
        $this->createAccount('4240', 'Insurance', 'expense', $adminExpenses->id, $business->id);
        $this->createAccount('4250', 'Repairs and Maintenance', 'expense', $adminExpenses->id, $business->id);

        // Operating Expenses (4300-4399)
        $operatingExpenses = $this->createAccount('4300', 'Operating Expenses', 'expense', $expenses->id, $business->id);
        $this->createAccount('4310', 'Rent Expense', 'expense', $operatingExpenses->id, $business->id);
        $this->createAccount('4320', 'Depreciation', 'expense', $operatingExpenses->id, $business->id);
        $this->createAccount('4330', 'Amortization', 'expense', $operatingExpenses->id, $business->id);
        $this->createAccount('4340', 'Professional Fees', 'expense', $operatingExpenses->id, $business->id);
        $this->createAccount('4350', 'Legal Fees', 'expense', $operatingExpenses->id, $business->id);

        // Transportation Expenses (4400-4499)
        $transportExpenses = $this->createAccount('4400', 'Transportation Expenses', 'expense', $expenses->id, $business->id);
        $this->createAccount('4410', 'Vehicle Fuel', 'expense', $transportExpenses->id, $business->id);
        $this->createAccount('4420', 'Vehicle Maintenance', 'expense', $transportExpenses->id, $business->id);
        $this->createAccount('4430', 'Driver Salary', 'expense', $transportExpenses->id, $business->id);

        // Marketing Expenses (4500-4599)
        $marketingExpenses = $this->createAccount('4500', 'Marketing Expenses', 'expense', $expenses->id, $business->id);
        $this->createAccount('4510', 'Advertising', 'expense', $marketingExpenses->id, $business->id);
        $this->createAccount('4520', 'Promotional Events', 'expense', $marketingExpenses->id, $business->id);

        // Equity (5000-5999)
        $equity = $this->createAccount('5000', 'Equity', 'equity', null, $business->id);
        $this->createAccount('5100', 'Capital', 'equity', $equity->id, $business->id);
        $this->createAccount('5200', 'Retained Earnings', 'equity', $equity->id, $business->id);
        $this->createAccount('5300', 'Current Year Earnings', 'equity', $equity->id, $business->id);
    }

    /**
     * Helper method to create chart of account entries
     */
    private function createAccount($code, $name, $type, $parentId, $businessId)
    {
        // Check if account already exists
        $existingAccount = ChartOfAccount::where([
            'code' => $code,
            'business_id' => $businessId
        ])->first();

        if ($existingAccount) {
            return $existingAccount;
        }

        return ChartOfAccount::create([
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'parent_id' => $parentId,
            'business_id' => $businessId,
            'is_default' => true,
        ]);
    }

    /**
     * Create default master data for a new business
     */
    private function createDefaultMasterDataForBusiness(Business $business)
    {
        $businessId = $business->id;

        // Check if master data already exists to prevent duplicates
        if (\App\Models\ArmsCategory::where('business_id', $businessId)->exists()) {
            return;
        }

        // Create default Arms Categories
        \App\Models\ArmsCategory::create([
            'arm_category' => 'PISTOL',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsCategory::create([
            'arm_category' => 'RIFLE',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsCategory::create([
            'arm_category' => 'SHOTGUN',
            'business_id' => $businessId,
            'status' => true,
        ]);

        // Create default Arms Makes
        \App\Models\ArmsMake::create([
            'arm_make' => 'BERETTA',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsMake::create([
            'arm_make' => 'GLOCK',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsMake::create([
            'arm_make' => 'SMITH & WESSON',
            'business_id' => $businessId,
            'status' => true,
        ]);

        // Create default Arms Types
        \App\Models\ArmsType::create([
            'arm_type' => 'SEMI-AUTOMATIC',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsType::create([
            'arm_type' => 'REVOLVER',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsType::create([
            'arm_type' => 'BOLT ACTION',
            'business_id' => $businessId,
            'status' => true,
        ]);

        // Create default Arms Calibers
        \App\Models\ArmsCaliber::create([
            'arm_caliber' => '9MM',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsCaliber::create([
            'arm_caliber' => '.45 ACP',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsCaliber::create([
            'arm_caliber' => '7.62MM',
            'business_id' => $businessId,
            'status' => true,
        ]);

        // Create default Arms Conditions
        \App\Models\ArmsCondition::create([
            'arm_condition' => 'NEW',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsCondition::create([
            'arm_condition' => 'USED',
            'business_id' => $businessId,
            'status' => true,
        ]);
        \App\Models\ArmsCondition::create([
            'arm_condition' => 'REFURBISHED',
            'business_id' => $businessId,
            'status' => true,
        ]);

        // Item Types will be added by users from the UI
    }

    /**
     * Suspend a business
     */
    public function suspend(Request $request, Business $business)
    {
        // Only Super Admin can suspend businesses
        if (!Auth::user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'You do not have permission to suspend businesses.');
        }

        $request->validate([
            'suspension_reason' => 'nullable|string|max:1000'
        ]);

        $business->suspend($request->suspension_reason);

        Log::info('Business suspended', [
            'business_id' => $business->id,
            'business_name' => $business->business_name,
            'suspended_by' => Auth::user()->id,
            'reason' => $request->suspension_reason
        ]);

        return redirect()->back()->with('success', 'Business has been suspended successfully.');
    }

    /**
     * Unsuspend a business
     */
    public function unsuspend(Business $business)
    {
        // Only Super Admin can unsuspend businesses
        if (!Auth::user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'You do not have permission to unsuspend businesses.');
        }

        $business->unsuspend();

        Log::info('Business unsuspended', [
            'business_id' => $business->id,
            'business_name' => $business->business_name,
            'unsuspended_by' => Auth::user()->id
        ]);

        return redirect()->back()->with('success', 'Business has been unsuspended successfully.');
    }

    /**
     * Clear all data for a specific business (Complete Clear)
     * This will delete all transactions, inventory, master data but keep the business record
     */
    public function clearAllData(Request $request, Business $business)
    {
        // Only Super Admin can clear business data
        if (!Auth::user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'You do not have permission to clear business data.');
        }

        // Validate confirmation
        $request->validate([
            'confirmation_text' => 'required|string',
        ]);

        // Check if confirmation matches business name
        if ($request->confirmation_text !== $business->business_name) {
            return redirect()->back()->with('error', 'Business name confirmation does not match. Data clear cancelled.');
        }

        try {
            DB::beginTransaction();

            $businessId = $business->id;
            $businessName = $business->business_name;

            // Phase 1: Delete Audit & Activity Logs
            \DB::table('activity_logs')->where('business_id', $businessId)->delete();
            
            // Get all purchases for this business to delete their audit logs
            $purchaseIds = \DB::table('purchases')->where('business_id', $businessId)->pluck('id');
            if ($purchaseIds->isNotEmpty()) {
                \DB::table('purchase_audit_logs')->whereIn('purchase_id', $purchaseIds)->delete();
            }

            // Get all sale invoices for this business to delete their audit logs
            $saleInvoiceIds = \DB::table('sale_invoices')->where('business_id', $businessId)->pluck('id');
            if ($saleInvoiceIds->isNotEmpty()) {
                \DB::table('sale_invoice_audit_logs')->whereIn('sale_invoice_id', $saleInvoiceIds)->delete();
            }

            // Get all sale returns for this business to delete their audit logs
            $saleReturnIds = \DB::table('sale_returns')->where('business_id', $businessId)->pluck('id');
            if ($saleReturnIds->isNotEmpty()) {
                \DB::table('sale_return_audit_logs')->whereIn('sale_return_id', $saleReturnIds)->delete();
            }

            // Get all purchase returns for this business to delete their audit logs
            $purchaseReturnIds = \DB::table('purchase_returns')->where('business_id', $businessId)->pluck('id');
            if ($purchaseReturnIds->isNotEmpty()) {
                \DB::table('purchase_return_audit_logs')->whereIn('purchase_return_id', $purchaseReturnIds)->delete();
            }

            // Phase 2: Delete Transaction Detail Lines (Child Records)
            
            // Sale invoice lines
            if ($saleInvoiceIds->isNotEmpty()) {
                \DB::table('sale_invoice_arms')->whereIn('sale_invoice_id', $saleInvoiceIds)->delete();
                \DB::table('sale_invoice_general_items')->whereIn('sale_invoice_id', $saleInvoiceIds)->delete();
            }

            // Sale return lines
            if ($saleReturnIds->isNotEmpty()) {
                \DB::table('sale_return_arms')->whereIn('sale_return_id', $saleReturnIds)->delete();
                \DB::table('sale_return_general_items')->whereIn('sale_return_id', $saleReturnIds)->delete();
            }

            // Purchase lines (handle nested relationship for arm serials)
            if ($purchaseIds->isNotEmpty()) {
                // First, get all purchase_arm_line_ids for these purchases
                $purchaseArmLineIds = \DB::table('purchase_arm_lines')
                    ->whereIn('purchase_id', $purchaseIds)
                    ->pluck('id');
                
                // Delete purchase_arm_serials using purchase_arm_line_id
                if ($purchaseArmLineIds->isNotEmpty()) {
                    \DB::table('purchase_arm_serials')->whereIn('purchase_arm_line_id', $purchaseArmLineIds)->delete();
                }
                
                // Now delete purchase lines
                \DB::table('purchase_arm_lines')->whereIn('purchase_id', $purchaseIds)->delete();
                \DB::table('purchase_general_lines')->whereIn('purchase_id', $purchaseIds)->delete();
            }

            // Purchase return lines
            if ($purchaseReturnIds->isNotEmpty()) {
                \DB::table('purchase_return_arms')->whereIn('purchase_return_id', $purchaseReturnIds)->delete();
                \DB::table('purchase_return_general_items')->whereIn('purchase_return_id', $purchaseReturnIds)->delete();
            }

            // Quotation lines
            $quotationIds = \DB::table('quotations')->where('business_id', $businessId)->pluck('id');
            if ($quotationIds->isNotEmpty()) {
                \DB::table('quotation_arms')->whereIn('quotation_id', $quotationIds)->delete();
                \DB::table('quotation_general_items')->whereIn('quotation_id', $quotationIds)->delete();
            }

            // Approval lines
            $approvalIds = \DB::table('approvals')->where('business_id', $businessId)->pluck('id');
            if ($approvalIds->isNotEmpty()) {
                \DB::table('approval_arms')->whereIn('approval_id', $approvalIds)->delete();
                \DB::table('approval_general_items')->whereIn('approval_id', $approvalIds)->delete();
            }

            // Stock adjustment lines
            $stockAdjustmentIds = \DB::table('stock_adjustments')->where('business_id', $businessId)->pluck('id');
            if ($stockAdjustmentIds->isNotEmpty()) {
                \DB::table('stock_adjustment_arms')->whereIn('stock_adjustment_id', $stockAdjustmentIds)->delete();
                \DB::table('stock_adjustment_items')->whereIn('stock_adjustment_id', $stockAdjustmentIds)->delete();
            }

            // Phase 3: Delete Main Transactions
            \DB::table('sale_invoices')->where('business_id', $businessId)->delete();
            \DB::table('sale_returns')->where('business_id', $businessId)->delete();
            \DB::table('purchases')->where('business_id', $businessId)->delete();
            \DB::table('purchase_returns')->where('business_id', $businessId)->delete();
            \DB::table('quotations')->where('business_id', $businessId)->delete();
            \DB::table('approvals')->where('business_id', $businessId)->delete();
            \DB::table('stock_adjustments')->where('business_id', $businessId)->delete();

            // Phase 4: Delete Financial Transactions & Attachments
            
            // Expenses
            $expenseIds = \DB::table('expenses')->where('business_id', $businessId)->pluck('id');
            if ($expenseIds->isNotEmpty()) {
                \DB::table('expense_attachments')->whereIn('expense_id', $expenseIds)->delete();
            }
            \DB::table('expenses')->where('business_id', $businessId)->delete();

            // Other incomes
            $otherIncomeIds = \DB::table('other_incomes')->where('business_id', $businessId)->pluck('id');
            if ($otherIncomeIds->isNotEmpty()) {
                \DB::table('other_income_attachments')->whereIn('other_income_id', $otherIncomeIds)->delete();
            }
            \DB::table('other_incomes')->where('business_id', $businessId)->delete();

            // General vouchers
            $generalVoucherIds = \DB::table('general_vouchers')->where('business_id', $businessId)->pluck('id');
            if ($generalVoucherIds->isNotEmpty()) {
                \DB::table('general_voucher_attachments')->whereIn('general_voucher_id', $generalVoucherIds)->delete();
            }
            \DB::table('general_vouchers')->where('business_id', $businessId)->delete();

            // Bank transfers
            $bankTransferIds = \DB::table('bank_transfers')->where('business_id', $businessId)->pluck('id');
            if ($bankTransferIds->isNotEmpty()) {
                \DB::table('bank_transfer_attachments')->whereIn('bank_transfer_id', $bankTransferIds)->delete();
            }
            \DB::table('bank_transfers')->where('business_id', $businessId)->delete();

            // Party transfers
            $partyTransferIds = \DB::table('party_transfers')->where('business_id', $businessId)->pluck('id');
            if ($partyTransferIds->isNotEmpty()) {
                \DB::table('party_transfer_attachments')->whereIn('party_transfer_id', $partyTransferIds)->delete();
            }
            \DB::table('party_transfers')->where('business_id', $businessId)->delete();

            // Journal entries
            \DB::table('journal_entries')->where('business_id', $businessId)->delete();

            // Phase 5: Delete Ledger Entries
            \DB::table('bank_ledger')->where('business_id', $businessId)->delete();
            \DB::table('party_ledgers')->where('business_id', $businessId)->delete();
            \DB::table('arms_stock_ledger')->where('business_id', $businessId)->delete();
            \DB::table('general_items_stock_ledger')->where('business_id', $businessId)->delete();
            \DB::table('arms_history')->where('business_id', $businessId)->delete();
            \DB::table('inventory_transactions')->where('business_id', $businessId)->delete();

            // Phase 6: Delete Inventory Items
            \DB::table('arms')->where('business_id', $businessId)->delete();
            \DB::table('general_batches')->where('business_id', $businessId)->delete();
            \DB::table('general_items')->where('business_id', $businessId)->delete();

            // Phase 7: Delete Master Data
            \DB::table('banks')->where('business_id', $businessId)->delete();
            \DB::table('parties')->where('business_id', $businessId)->delete();
            \DB::table('expense_heads')->where('business_id', $businessId)->delete();
            \DB::table('income_heads')->where('business_id', $businessId)->delete();
            \DB::table('item_types')->where('business_id', $businessId)->delete();
            \DB::table('arms_categories')->where('business_id', $businessId)->delete();
            \DB::table('arms_makes')->where('business_id', $businessId)->delete();
            \DB::table('arms_types')->where('business_id', $businessId)->delete();
            \DB::table('arms_calibers')->where('business_id', $businessId)->delete();
            \DB::table('arms_conditions')->where('business_id', $businessId)->delete();
            \DB::table('chart_of_accounts')->where('business_id', $businessId)->delete();

            // Phase 8: Recreate Chart of Accounts
            $this->createChartOfAccountsForBusiness($business);
            
            // Recreate default master data
            $this->createDefaultMasterDataForBusiness($business);

            // Log the action
            Log::info('Business data completely cleared', [
                'business_id' => $businessId,
                'business_name' => $businessName,
                'cleared_by' => Auth::user()->id,
                'cleared_by_name' => Auth::user()->name,
                'timestamp' => now()
            ]);

            DB::commit();

            return redirect()->route('businesses.show', $business)
                ->with('success', 'All business data has been cleared successfully. The business now has a fresh chart of accounts.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to clear business data', [
                'business_id' => $business->id,
                'business_name' => $business->business_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to clear business data: ' . $e->getMessage());
        }
    }
}
