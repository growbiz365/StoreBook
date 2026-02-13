<?php

namespace App\Observers;

use App\Models\Business;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessObserver
{
    /**
     * Handle the Business "created" event.
     */
    public function created(Business $business): void
    {
        // Only create chart of accounts if they don't already exist
        if (ChartOfAccount::where('business_id', $business->id)->exists()) {
            Log::info("Chart of accounts already exists for business: {$business->business_name}");
            return;
        }

        try {
            DB::beginTransaction();

            Log::info("Creating chart of accounts and master data for business: {$business->business_name}");

            $this->createChartOfAccounts($business);
            

            DB::commit();

            Log::info("Chart of accounts and master data created successfully for business: {$business->business_name}");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to create chart of accounts for business: {$business->business_name}", [
                'business_id' => $business->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Don't throw the exception - business creation should succeed even if chart of accounts fails
            // The user will need to manually create accounts or retry later
        }
    }

    /**
     * Create chart of accounts for the business
     */
    private function createChartOfAccounts(Business $business): void
    {
        $businessId = $business->id;

        // Assets (1000-1999)
        $assets = $this->createAccount('1000', 'Assets', 'asset', null, $businessId);

        // Current Assets (1100-1199)
        $currentAssets = $this->createAccount('1100', 'Current Assets', 'asset', $assets->id, $businessId);

        // Cash in Hand (1110-1119)
        $cashInHand = $this->createAccount('1110', 'Cash in Hand', 'asset', $currentAssets->id, $businessId);

        // Bank Accounts (1120-1129)
        $bankAccounts = $this->createAccount('1120', 'Bank Accounts', 'asset', $currentAssets->id, $businessId);

        // Receivables (1200-1299)
        $receivables = $this->createAccount('1200', 'Receivables', 'asset', $currentAssets->id, $businessId);
        $this->createAccount('1210', 'Accounts Receivable', 'asset', $receivables->id, $businessId);
        $this->createAccount('1220', 'Employee Advances', 'asset', $receivables->id, $businessId);
        $this->createAccount('1230', 'Security Deposits', 'asset', $receivables->id, $businessId);

        // Inventory (1250-1299)
        $inventory = $this->createAccount('1250', 'Inventory', 'asset', $currentAssets->id, $businessId);

        // Fixed Assets (1300-1399)
        $fixedAssets = $this->createAccount('1300', 'Fixed Assets', 'asset', $assets->id, $businessId);
        $this->createAccount('1310', 'Land', 'asset', $fixedAssets->id, $businessId);
        $this->createAccount('1320', 'Buildings', 'asset', $fixedAssets->id, $businessId);
        $this->createAccount('1330', 'Furniture and Fixtures', 'asset', $fixedAssets->id, $businessId);
        $this->createAccount('1340', 'Computer Equipment', 'asset', $fixedAssets->id, $businessId);
        $this->createAccount('1350', 'Machinery and Equipment', 'asset', $fixedAssets->id, $businessId);
        $this->createAccount('1360', 'Office Equipment', 'asset', $fixedAssets->id, $businessId);
        $this->createAccount('1370', 'Tools and Equipment', 'asset', $fixedAssets->id, $businessId);
        $this->createAccount('1380', 'Vehicles', 'asset', $fixedAssets->id, $businessId);

        // Liabilities (2000-2999)
        $liabilities = $this->createAccount('2000', 'Liabilities', 'liability', null, $businessId);

        // Current Liabilities (2100-2199)
        $currentLiabilities = $this->createAccount('2100', 'Current Liabilities', 'liability', $liabilities->id, $businessId);
        $this->createAccount('2102', 'Accounts Payable', 'liability', $currentLiabilities->id, $businessId);
        $this->createAccount('2120', 'Salary Payable', 'liability', $currentLiabilities->id, $businessId);
        $this->createAccount('2130', 'Tax Payable', 'liability', $currentLiabilities->id, $businessId);
        $this->createAccount('2140', 'Security Deposits', 'liability', $currentLiabilities->id, $businessId);

        // Long Term Liabilities (2200-2299)
        $longTermLiabilities = $this->createAccount('2200', 'Long Term Liabilities', 'liability', $liabilities->id, $businessId);
        $this->createAccount('2210', 'Bank Loans', 'liability', $longTermLiabilities->id, $businessId);
        $this->createAccount('2220', 'Mortgages Payable', 'liability', $longTermLiabilities->id, $businessId);

        // Adjustments & Reconciliation (2300-2399)
        $adjustments = $this->createAccount('2300', 'Adjustments & Reconciliation', 'liability', $liabilities->id, $businessId);
        $this->createAccount('2303', 'Opening Balance Adjustment', 'liability', $adjustments->id, $businessId);

        // Income (3000-3999)
        $income = $this->createAccount('3000', 'Income', 'income', null, $businessId);

        // Sales Income (3100-3199)
        $salesIncome = $this->createAccount('3100', 'Sales Income', 'income', $income->id, $businessId);
        $this->createAccount('3110', 'Product Sales', 'income', $salesIncome->id, $businessId);
        $this->createAccount('3120', 'Service Revenue', 'income', $salesIncome->id, $businessId);
        $this->createAccount('3130', 'Commission Income', 'income', $salesIncome->id, $businessId);
        $this->createAccount('3140', 'Consulting Fees', 'income', $salesIncome->id, $businessId);
        $this->createAccount('3150', 'Rental Income', 'income', $salesIncome->id, $businessId);
        $this->createAccount('3160', 'Licensing Fees', 'income', $salesIncome->id, $businessId);
        $this->createAccount('3170', 'Subscription Revenue', 'income', $salesIncome->id, $businessId);
        $this->createAccount('3180', 'Maintenance Fees', 'income', $salesIncome->id, $businessId);

        // Other Income (3200-3299)
        $otherIncome = $this->createAccount('3200', 'Other Income', 'income', $income->id, $businessId);
        $this->createAccount('3210', 'Interest Income', 'income', $otherIncome->id, $businessId);
        $this->createAccount('3220', 'Dividend Income', 'income', $otherIncome->id, $businessId);
        $this->createAccount('3230', 'Foreign Exchange Gain', 'income', $otherIncome->id, $businessId);
        $this->createAccount('3240', 'Miscellaneous Income', 'income', $otherIncome->id, $businessId);

        // Expenses (4000-4999)
        $expenses = $this->createAccount('4000', 'Expenses', 'expense', null, $businessId);

        // Direct Expenses (4050-4099)
        $directExpenses = $this->createAccount('4050', 'Direct Expenses', 'expense', $expenses->id, $businessId);
        $this->createAccount('4051', 'Cost of Goods Sold', 'expense', $directExpenses->id, $businessId);
        $this->createAccount('4052', 'Direct Labor', 'expense', $directExpenses->id, $businessId);
        $this->createAccount('4053', 'Direct Materials', 'expense', $directExpenses->id, $businessId);
        $this->createAccount('4054', 'Manufacturing Overhead', 'expense', $directExpenses->id, $businessId);
        $this->createAccount('4055', 'Freight and Delivery', 'expense', $directExpenses->id, $businessId);

        // Employee Expenses (4100-4199)
        $employeeExpenses = $this->createAccount('4100', 'Employee Expenses', 'expense', $expenses->id, $businessId);
        $this->createAccount('4110', 'Employee Salaries', 'expense', $employeeExpenses->id, $businessId);
        $this->createAccount('4120', 'Employee Benefits', 'expense', $employeeExpenses->id, $businessId);
        $this->createAccount('4130', 'Employee Training', 'expense', $employeeExpenses->id, $businessId);
        $this->createAccount('4140', 'Employee Insurance', 'expense', $employeeExpenses->id, $businessId);

        // Administrative Expenses (4200-4299)
        $adminExpenses = $this->createAccount('4200', 'Administrative Expenses', 'expense', $expenses->id, $businessId);
        $this->createAccount('4210', 'Office Supplies', 'expense', $adminExpenses->id, $businessId);
        $this->createAccount('4220', 'Utilities', 'expense', $adminExpenses->id, $businessId);
        $this->createAccount('4230', 'Communication', 'expense', $adminExpenses->id, $businessId);
        $this->createAccount('4240', 'Insurance', 'expense', $adminExpenses->id, $businessId);
        $this->createAccount('4250', 'Repairs and Maintenance', 'expense', $adminExpenses->id, $businessId);

        // Operating Expenses (4300-4399)
        $operatingExpenses = $this->createAccount('4300', 'Operating Expenses', 'expense', $expenses->id, $businessId);
        $this->createAccount('4310', 'Rent Expense', 'expense', $operatingExpenses->id, $businessId);
        $this->createAccount('4320', 'Depreciation', 'expense', $operatingExpenses->id, $businessId);
        $this->createAccount('4330', 'Amortization', 'expense', $operatingExpenses->id, $businessId);
        $this->createAccount('4340', 'Professional Fees', 'expense', $operatingExpenses->id, $businessId);
        $this->createAccount('4350', 'Legal Fees', 'expense', $operatingExpenses->id, $businessId);

        // Transportation Expenses (4400-4499)
        $transportExpenses = $this->createAccount('4400', 'Transportation Expenses', 'expense', $expenses->id, $businessId);
        $this->createAccount('4410', 'Vehicle Fuel', 'expense', $transportExpenses->id, $businessId);
        $this->createAccount('4420', 'Vehicle Maintenance', 'expense', $transportExpenses->id, $businessId);
        $this->createAccount('4430', 'Driver Salary', 'expense', $transportExpenses->id, $businessId);

        // Marketing Expenses (4500-4599)
        $marketingExpenses = $this->createAccount('4500', 'Marketing Expenses', 'expense', $expenses->id, $businessId);
        $this->createAccount('4510', 'Advertising', 'expense', $marketingExpenses->id, $businessId);
        $this->createAccount('4520', 'Promotional Events', 'expense', $marketingExpenses->id, $businessId);

        // Equity (5000-5999)
        $equity = $this->createAccount('5000', 'Equity', 'equity', null, $businessId);
        $this->createAccount('5100', 'Capital', 'equity', $equity->id, $businessId);
        $this->createAccount('5200', 'Retained Earnings', 'equity', $equity->id, $businessId);
        $this->createAccount('5300', 'Current Year Earnings', 'equity', $equity->id, $businessId);
    }

    /**
     * Create a single chart of account entry
     */
    private function createAccount(string $code, string $name, string $type, ?int $parentId, int $businessId): ChartOfAccount
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
     * Create default master data for the business
     */
   
}

