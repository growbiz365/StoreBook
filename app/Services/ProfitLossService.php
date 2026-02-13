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

        // Skip zero or negative balance accounts
        // For P&L reports, we only show positive balances
        // Negative balances can occur with reversals, but should net to zero
        if ($balance <= 0) {
            return;
        }

        $accountData = [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'balance' => $balance
        ];

        switch ($accountType) {
            case 'income':
                $profitLossData['revenue'][] = $accountData;
                break;

            case 'expense':
                // Check if it's a COGS account by name or code
                if (str_contains(strtolower($account->name), 'cost of goods sold') || 
                    str_contains(strtolower($account->name), 'cogs') ||
                    str_contains($account->code, '4051')) {
                    $profitLossData['cost_of_goods_sold'][] = $accountData;
                } else {
                    $profitLossData['operating_expenses'][] = $accountData;
                }
                break;
        }
    }

    /**
     * Get account balance for the specified period
     */
    private function getAccountBalance($account, $fromDate, $toDate, $basis)
    {
        $query = JournalEntry::where('business_id', $account->business_id)
            ->where('account_head', $account->id)
            ->whereBetween('date_added', [$fromDate, $toDate]);

        // Handle cash basis accounting
        if ($basis === 'cash') {
            // For cash basis, we need to check if the account represents cash transactions
            if ($this->isNonCashAccount($account)) {
                $this->excludedAccounts[] = $account->name . ' (' . $account->code . ')';
                return 0;
            }
        }

        // Get all journal entries for calculation
        $journalEntries = $query->get();
        
        $totalDebits = $journalEntries->sum('debit_amount');
        $totalCredits = $journalEntries->sum('credit_amount');

        // Calculate balance based on account type
        $balance = 0;
        
        if ($account->type === 'income') {
            // For income accounts: Credit - Debit (net credit)
            // Reversals will have debits that offset original credits
            // Income should be positive (credits increase income)
            $balance = $totalCredits - $totalDebits;
            // Ensure income is never negative in P&L (if negative due to reversals, show 0)
            if ($balance < 0) {
                $balance = 0;
            }
        } elseif ($account->type === 'expense') {
            // For expense accounts: Debit - Credit (net debit)
            // Reversals will have credits that offset original debits
            // Expenses should be positive (debits increase expenses)
            $balance = $totalDebits - $totalCredits;
            // Ensure expenses are never negative in P&L (if negative due to reversals, show 0)
            if ($balance < 0) {
                $balance = 0;
            }
        }

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
}
