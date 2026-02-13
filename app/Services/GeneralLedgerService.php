<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneralLedgerService
{
    private $excludedAccounts = [];

    /**
     * Generate general ledger data for a business
     */
    public function generateGeneralLedger($businessId, $fromDate, $toDate, $basis = 'accrual')
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

        $generalLedgerData = [
            'accounts' => [],
            'totals' => [
                'total_debit' => 0,
                'total_credit' => 0,
                'total_balance' => 0
            ],
            'basis' => $basis,
            'excluded_accounts' => $this->excludedAccounts
        ];

        // Process each account
        foreach ($accounts as $account) {
            $accountData = $this->processAccount($account, $businessId, $fromDate, $toDate, $basis);
            
            if ($accountData) {
                $generalLedgerData['accounts'][] = $accountData;
                
                // Add to totals
                $generalLedgerData['totals']['total_debit'] += $accountData['debit'];
                $generalLedgerData['totals']['total_credit'] += $accountData['credit'];
                // Note: Total Balance is not calculated by summing individual balances
                // because different account types have different normal balances.
                // Instead, it represents the net difference (should be 0 if books are balanced)
                // We'll calculate it as the difference between total debits and credits
            }
        }
        
        // Calculate total balance as net difference (should be 0 if books are balanced)
        // This represents the net change in the period, not the sum of all account balances
        $generalLedgerData['totals']['total_balance'] = 
            $generalLedgerData['totals']['total_debit'] - $generalLedgerData['totals']['total_credit'];

        return $generalLedgerData;
    }

    /**
     * Process individual account data
     */
    private function processAccount($account, $businessId, $fromDate, $toDate, $basis)
    {
        // Get journal entries for this account within the date range
        $query = JournalEntry::where('business_id', $businessId)
            ->where('account_head', $account->id)
            ->whereBetween('date_added', [$fromDate, $toDate]);

        // Apply cash basis filtering if needed
        if ($basis === 'cash') {
            $query = $this->applyCashBasisFilter($query, $account);
        }

        $entries = $query->get();

        // Calculate totals
        $debit = $entries->sum('debit_amount');
        $credit = $entries->sum('credit_amount');
        
        // Determine account type for balance calculation
        $accountType = strtolower($account->type ?? '');
        
        // Calculate balance based on account type
        // For assets and expenses: Debit - Credit (normal debit balance)
        // For liabilities, equity, and income/revenue: Credit - Debit (normal credit balance)
        if (in_array($accountType, ['liability', 'equity', 'income', 'revenue'])) {
            $balance = $credit - $debit;
        } else {
            // For assets and expenses
            $balance = $debit - $credit;
        }

        return [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'type' => $account->type,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'entries_count' => $entries->count()
        ];
    }

    /**
     * Apply cash basis filtering to exclude non-cash transactions
     */
    private function applyCashBasisFilter($query, $account)
    {
        $accountName = strtolower($account->name);
        $accountType = strtolower($account->type ?? '');

        // Exclude certain accounts in cash basis
        $excludePatterns = [
            'depreciation',
            'prepaid',
            'accrued',
            'accounts receivable',
            'accounts payable',
            'party:', // Exclude party-specific accounts (they are accrual-based)
            'unearned revenue',
            'retained earnings'
        ];

        foreach ($excludePatterns as $pattern) {
            if (strpos($accountName, $pattern) !== false) {
                $this->excludedAccounts[] = $account->name;
                return $query->whereRaw('1 = 0'); // Return empty result
            }
        }

        // Exclude certain account types in cash basis
        if (in_array($accountType, ['asset', 'liability']) && 
            (strpos($accountName, 'cash') === false && 
             strpos($accountName, 'bank') === false)) {
            $this->excludedAccounts[] = $account->name;
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        return $query;
    }

    /**
     * Get account balance as of a specific date
     */
    public function getAccountBalanceAsOf($businessId, $accountId, $asOfDate)
    {
        $asOfDate = Carbon::parse($asOfDate)->endOfDay();
        
        $entries = JournalEntry::where('business_id', $businessId)
            ->where('account_head', $accountId)
            ->where('date_added', '<=', $asOfDate)
            ->get();

        $debit = $entries->sum('debit_amount');
        $credit = $entries->sum('credit_amount');
        
        $account = ChartOfAccount::find($accountId);
        $accountType = strtolower($account->type ?? '');
        
        // For liability, equity, and income/revenue accounts, credit balance is positive
        if (in_array($accountType, ['liability', 'equity', 'income', 'revenue'])) {
            return $credit - $debit;
        }
        
        // For assets and expenses, debit balance is positive
        return $debit - $credit;
    }
}
