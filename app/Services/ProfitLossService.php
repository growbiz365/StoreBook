<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfitLossService
{
    private $excludedAccounts = [];

    /**
     * Generate profit and loss data for a business
     */
    public function generateProfitLoss($businessId, $fromDate, $toDate, $basis = 'accrual')
    {
        $fromDate = Carbon::parse($fromDate)->startOfDay();
        $toDate = Carbon::parse($toDate)->endOfDay();
        
        // Reset excluded accounts for this report
        $this->excludedAccounts = [];
        
        // Get all chart of accounts for the business
        $accounts = ChartOfAccount::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $profitLossData = [
            'revenue' => [],
            'cost_of_goods_sold' => [],
            'gross_profit' => 0,
            'operating_expenses' => [],
            'operating_profit' => 0,
            'other_income' => [],
            'other_expenses' => [],
            'net_profit' => 0,
            'totals' => [
                'revenue' => 0,
                'cost_of_goods_sold' => 0,
                'gross_profit' => 0,
                'operating_expenses' => 0,
                'operating_profit' => 0,
                'other_income' => 0,
                'other_expenses' => 0,
                'net_profit' => 0
            ],
            'basis' => $basis,
            'excluded_accounts' => $this->excludedAccounts,
            'period' => [
                'from' => $fromDate,
                'to' => $toDate
            ]
        ];

        // Process each account type
        foreach ($accounts as $account) {
            $this->processAccountForProfitLoss($account, $profitLossData, $fromDate, $toDate, $basis);
        }

        // Calculate totals
        $this->calculateProfitLossTotals($profitLossData);

        return $profitLossData;
    }

    /**
     * Process individual account for profit and loss
     */
    private function processAccountForProfitLoss($account, &$profitLossData, $fromDate, $toDate, $basis)
    {
        $accountType = $account->type;
        $balance = $this->getAccountBalance($account, $fromDate, $toDate, $basis);

        // Skip zero balance accounts
        // Note: We now show accounts with balance, even if negative (which shouldn't normally happen in P&L)
        if ($balance == 0) {
            return;
        }

        $accountData = [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'balance' => abs($balance) // Use absolute value for display
        ];

        switch ($accountType) {
            case 'income':
                // Check if it's non-operating income
                if ($this->isNonOperatingIncome($account)) {
                    $profitLossData['other_income'][] = $accountData;
                } else {
                    // Operating income (revenue from sales, services, etc.)
                    $profitLossData['revenue'][] = $accountData;
                }
                break;

            case 'expense':
                // Check if it's a COGS account by name or code
                if ($this->isCostOfGoodsSold($account)) {
                    $profitLossData['cost_of_goods_sold'][] = $accountData;
                } elseif ($this->isNonOperatingExpense($account)) {
                    // Non-operating expenses (interest, losses, etc.)
                    $profitLossData['other_expenses'][] = $accountData;
                } else {
                    // Operating expenses (salaries, rent, utilities, etc.)
                    $profitLossData['operating_expenses'][] = $accountData;
                }
                break;
        }
    }
    
    /**
     * Check if account is Cost of Goods Sold
     */
    private function isCostOfGoodsSold($account)
    {
        $accountNameLower = strtolower($account->name);
        $accountCodeLower = strtolower($account->code);
        
        return str_contains($accountNameLower, 'cost of goods sold') || 
               str_contains($accountNameLower, 'cogs') ||
               str_contains($accountNameLower, 'cost of sales') ||
               str_contains($accountCodeLower, '4051') ||
               str_contains($accountCodeLower, '5000'); // Common COGS codes
    }
    
    /**
     * Check if income account is non-operating
     */
    private function isNonOperatingIncome($account)
    {
        $accountNameLower = strtolower($account->name);
        
        $nonOperatingKeywords = [
            'interest income',
            'interest received',
            'dividend income',
            'investment income',
            'gain on sale',
            'other income',
            'miscellaneous income',
            'rental income' // If not primary business
        ];
        
        foreach ($nonOperatingKeywords as $keyword) {
            if (str_contains($accountNameLower, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if expense account is non-operating
     */
    private function isNonOperatingExpense($account)
    {
        $accountNameLower = strtolower($account->name);
        
        // Note: "OTHER EXPENSE" is treated as operating expense (general business expenses)
        // Only specific non-operating expenses are classified as such
        $nonOperatingKeywords = [
            'interest expense',
            'interest paid',
            'bank charges',
            'bank fees',
            'loss on sale',
            'loss on disposal',
            'finance charges',
            'finance costs',
            'penalties',
            'fines',
            'legal fees',
            'tax penalties'
        ];
        
        foreach ($nonOperatingKeywords as $keyword) {
            if (str_contains($accountNameLower, $keyword)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get account balance for the specified period
     * IMPORTANT: This method calculates the NET balance after considering all journal entries
     * including original postings and any reversals from cancellations/edits
     */
    private function getAccountBalance($account, $fromDate, $toDate, $basis)
    {
        // Handle cash basis accounting
        if ($basis === 'cash') {
            // For cash basis, we need to check if the account represents cash transactions
            if ($this->isNonCashAccount($account)) {
                $this->excludedAccounts[] = $account->name . ' (' . $account->code . ')';
                return 0;
            }
        }

        // Get all journal entries for this account in the date range
        // Include ALL entries (both original and reversals) - they will net out correctly
        $journalEntries = JournalEntry::where('business_id', $account->business_id)
            ->where('account_head', $account->id)
            ->whereBetween('date_added', [$fromDate, $toDate])
            ->get();
        
        // Calculate NET balances by summing ALL debits and credits
        // Original entries: Dr COGS 3000, Cr Revenue 3000
        // Reversal entries: Cr COGS 3000, Dr Revenue 3000
        // Net result: COGS = 0, Revenue = 0 (correctly cancelled)
        $totalDebits = 0;
        $totalCredits = 0;
        
        // Sum all debits and credits (including reversals)
        foreach ($journalEntries as $entry) {
            $totalDebits += $entry->debit_amount;
            $totalCredits += $entry->credit_amount;
        }

        // Calculate balance based on account type
        $balance = 0;
        
        if ($account->type === 'income') {
            // For income accounts: Credits - Debits = Net Income
            // Normal sale: Cr Revenue 1000 → Balance = +1000
            // Reversal: Dr Revenue 1000 → Balance = 0 (cancelled out)
            $balance = $totalCredits - $totalDebits;
        } elseif ($account->type === 'expense') {
            // For expense accounts: Debits - Credits = Net Expense
            // Normal COGS: Dr COGS 600 → Balance = +600
            // Reversal: Cr COGS 600 → Balance = 0 (cancelled out)
            $balance = $totalDebits - $totalCredits;
        }

        // Return the net balance
        // This represents the actual income/expense after all reversals
        return $balance;
    }

    /**
     * Check if account represents non-cash transactions
     */
    private function isNonCashAccount($account)
    {
        $nonCashAccountTypes = [
            'asset', // Assets are generally non-cash for P&L
            'liability', // Liabilities are generally non-cash for P&L
            'equity' // Equity is generally non-cash for P&L
        ];

        return in_array($account->type, $nonCashAccountTypes);
    }

    /**
     * Calculate profit and loss totals
     */
    private function calculateProfitLossTotals(&$profitLossData)
    {
        // Calculate revenue total
        $profitLossData['totals']['revenue'] = collect($profitLossData['revenue'])->sum('balance');
        
        // Calculate cost of goods sold total
        $profitLossData['totals']['cost_of_goods_sold'] = collect($profitLossData['cost_of_goods_sold'])->sum('balance');
        
        // Calculate gross profit
        $profitLossData['gross_profit'] = $profitLossData['totals']['revenue'] - $profitLossData['totals']['cost_of_goods_sold'];
        $profitLossData['totals']['gross_profit'] = $profitLossData['gross_profit'];
        
        // Calculate operating expenses total
        $profitLossData['totals']['operating_expenses'] = collect($profitLossData['operating_expenses'])->sum('balance');
        
        // Calculate operating profit
        $profitLossData['operating_profit'] = $profitLossData['gross_profit'] - $profitLossData['totals']['operating_expenses'];
        $profitLossData['totals']['operating_profit'] = $profitLossData['operating_profit'];
        
        // Calculate other income total
        $profitLossData['totals']['other_income'] = collect($profitLossData['other_income'])->sum('balance');
        
        // Calculate other expenses total
        $profitLossData['totals']['other_expenses'] = collect($profitLossData['other_expenses'])->sum('balance');
        
        // Calculate net profit
        $profitLossData['net_profit'] = $profitLossData['operating_profit'] + $profitLossData['totals']['other_income'] - $profitLossData['totals']['other_expenses'];
        $profitLossData['totals']['net_profit'] = $profitLossData['net_profit'];
    }
    
    /**
     * Diagnostic method to check journal entry integrity
     * This helps identify issues with COGS and Revenue calculations
     */
    public function diagnoseProfitLossIssues($businessId, $fromDate, $toDate)
    {
        $fromDate = Carbon::parse($fromDate)->startOfDay();
        $toDate = Carbon::parse($toDate)->endOfDay();
        
        $diagnostics = [];
        
        // Get Revenue account
        $revenueAccount = ChartOfAccount::where('business_id', $businessId)
            ->where(function($q) {
                $q->where('name', 'like', '%Sales%')
                  ->orWhere('name', 'like', '%Revenue%')
                  ->orWhere('name', 'like', '%Income%');
            })
            ->where('type', 'income')
            ->first();
            
        if ($revenueAccount) {
            $revenueEntries = JournalEntry::where('business_id', $businessId)
                ->where('account_head', $revenueAccount->id)
                ->whereBetween('date_added', [$fromDate, $toDate])
                ->orderBy('date_added', 'desc')
                ->get();
                
            $diagnostics['revenue'] = [
                'account_name' => $revenueAccount->name,
                'total_entries' => $revenueEntries->count(),
                'total_credits' => $revenueEntries->sum('credit_amount'),
                'total_debits' => $revenueEntries->sum('debit_amount'),
                'net_balance' => $revenueEntries->sum('credit_amount') - $revenueEntries->sum('debit_amount'),
                'entries_by_type' => [
                    'normal' => $revenueEntries->filter(function($e) {
                        return !str_contains(strtolower($e->comments ?? ''), 'reversal');
                    })->count(),
                    'reversals' => $revenueEntries->filter(function($e) {
                        return str_contains(strtolower($e->comments ?? ''), 'reversal');
                    })->count(),
                ]
            ];
        }
        
        // Get COGS account
        $cogsAccount = ChartOfAccount::where('business_id', $businessId)
            ->where(function($q) {
                $q->where('name', 'like', '%Cost of Goods%')
                  ->orWhere('name', 'like', '%COGS%');
            })
            ->where('type', 'expense')
            ->first();
            
        if ($cogsAccount) {
            $cogsEntries = JournalEntry::where('business_id', $businessId)
                ->where('account_head', $cogsAccount->id)
                ->whereBetween('date_added', [$fromDate, $toDate])
                ->orderBy('date_added', 'desc')
                ->get();
                
            $diagnostics['cogs'] = [
                'account_name' => $cogsAccount->name,
                'total_entries' => $cogsEntries->count(),
                'total_debits' => $cogsEntries->sum('debit_amount'),
                'total_credits' => $cogsEntries->sum('credit_amount'),
                'net_balance' => $cogsEntries->sum('debit_amount') - $cogsEntries->sum('credit_amount'),
                'entries_by_type' => [
                    'normal' => $cogsEntries->filter(function($e) {
                        return !str_contains(strtolower($e->comments ?? ''), 'reversal');
                    })->count(),
                    'reversals' => $cogsEntries->filter(function($e) {
                        return str_contains(strtolower($e->comments ?? ''), 'reversal');
                    })->count(),
                ]
            ];
        }
        
        return $diagnostics;
    }
}
