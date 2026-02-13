<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrialBalanceService
{
    private $excludedAccounts = [];

    /**
     * Generate trial balance data for a business
     */
    public function generateTrialBalance($businessId, $asOfDate, $basis = 'accrual')
    {
        $asOfDate = Carbon::parse($asOfDate)->endOfDay();
        
        // Reset excluded accounts for this report
        $this->excludedAccounts = [];
        
        // Get all chart of accounts for the business
        $accounts = ChartOfAccount::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $trialBalanceData = [
            'accounts' => [],
            'totals' => [
                'debit' => 0,
                'credit' => 0
            ],
            'basis' => $basis,
            'excluded_accounts' => $this->excludedAccounts
        ];

        // Process each account
        foreach ($accounts as $account) {
            $balances = $this->calculateAccountBalances($account, $asOfDate, $basis);
            
            // Include accounts with non-zero debit or credit
            if ($balances['debit'] != 0 || $balances['credit'] != 0) {
                $accountData = [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $balances['debit'],
                    'credit' => $balances['credit']
                ];

                $trialBalanceData['accounts'][] = $accountData;
                $trialBalanceData['totals']['debit'] += $balances['debit'];
                $trialBalanceData['totals']['credit'] += $balances['credit'];
            }
        }

        // Sort accounts by type and then by code
        usort($trialBalanceData['accounts'], function($a, $b) {
            $typeOrder = ['asset' => 1, 'liability' => 2, 'equity' => 3, 'income' => 4, 'expense' => 5];
            $aOrder = $typeOrder[$a['type']] ?? 99;
            $bOrder = $typeOrder[$b['type']] ?? 99;
            
            if ($aOrder !== $bOrder) {
                return $aOrder <=> $bOrder;
            }
            
            return strcmp($a['code'], $b['code']);
        });

        return $trialBalanceData;
    }

    /**
     * Calculate debit and credit balances for a specific account
     */
    private function calculateAccountBalances(ChartOfAccount $account, $asOfDate, $basis = 'accrual')
    {
        // Get journal entries for this account up to the as of date
        $query = JournalEntry::where('account_head', $account->id)
            ->where('date_added', '<=', $asOfDate);

        // For cash basis, only include entries where cash was actually received/paid
        if ($basis === 'cash') {
            $accountName = strtolower($account->name);
            $shouldExclude = false;
            
            // Exclude accounts with specific keywords
            $excludeKeywords = ['receivable', 'payable', 'prepaid', 'accrued', 'deferred'];
            foreach ($excludeKeywords as $keyword) {
                if (strpos($accountName, $keyword) !== false) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) {
                $this->excludedAccounts[] = $account->name;
                return ['debit' => 0, 'credit' => 0];
            }
        }

        $journalEntries = $query->get();

        $totalDebits = $journalEntries->sum('debit_amount');
        $totalCredits = $journalEntries->sum('credit_amount');

        // For trial balance, we show net debit and net credit
        // Standard trial balance logic:
        // - Assets and Expenses: ending balance = debit - credit (if positive, show in debit; if negative, show in credit)
        // - Liabilities, Equity, and Income: ending balance = credit - debit (if positive, show in credit; if negative, show in debit)
        
        $debitMinusCredit = $totalDebits - $totalCredits;
        $creditMinusDebit = $totalCredits - $totalDebits;
        
        switch ($account->type) {
            case 'asset':
            case 'expense':
                // Assets/Expenses: ending balance = debit - credit
                if ($debitMinusCredit > 0) {
                    return ['debit' => $debitMinusCredit, 'credit' => 0];
                } elseif ($debitMinusCredit < 0) {
                    return ['debit' => 0, 'credit' => abs($debitMinusCredit)];
                } else {
                    return ['debit' => 0, 'credit' => 0];
                }
                
            case 'liability':
            case 'equity':
            case 'income':
                // Liabilities/Equity/Income: ending balance = credit - debit
                if ($creditMinusDebit > 0) {
                    return ['debit' => 0, 'credit' => $creditMinusDebit];
                } elseif ($creditMinusDebit < 0) {
                    return ['debit' => abs($creditMinusDebit), 'credit' => 0];
                } else {
                    return ['debit' => 0, 'credit' => 0];
                }
                
            default:
                return ['debit' => 0, 'credit' => 0];
        }
    }
}

