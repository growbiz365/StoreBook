<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\FeePayment;
use App\Models\FeePosting;
use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        $businessId = session('active_business');

        if (!$businessId) {
            abort(403, 'No active business selected.');
        }

        $business = Business::with('currency')->find($businessId);

        if (!$business) {
            abort(404, 'Business not found.');
        }

        // Get currency object, or default to PKR if not set
        $currency = $business->currency;
        if (!$currency) {
            // Get default PKR currency or create a fallback object
            $currency = \App\Models\Currency::where('currency_code', 'PKR')->first();
            if (!$currency) {
                // Create a fallback currency object with default values
                $currency = (object) [
                    'symbol' => 'PKR',
                    'currency_code' => 'PKR',
                    'currency_name' => 'Pakistani Rupee'
                ];
            }
        }
        $currentMonth = now();
        $lastMonth = now()->subMonth();
        $startOfYear = now()->startOfYear();

        // Get financial statistics
        $statistics = [
            'total_income' => $this->getTotalIncome($businessId, $currentMonth),
            'total_expenses' => $this->getTotalExpenses($businessId, $currentMonth),
            'net_balance' => $this->getNetBalance($businessId),
            // 'pending_payments' => $this->getPendingPayments($businessId),
            'income_change' => $this->getIncomeChange($businessId, $currentMonth, $lastMonth),
            'expenses_change' => $this->getExpensesChange($businessId, $currentMonth, $lastMonth),
            'average_monthly_income' => $this->getAverageMonthlyIncome($businessId, $startOfYear),
            'average_monthly_expenses' => $this->getAverageMonthlyExpenses($businessId, $startOfYear),
            'average_transaction_amount' => $this->getAverageTransactionAmount($businessId),
            // 'pending_transactions_count' => $this->getPendingTransactionsCount($businessId)
        ];

        return view('finance.index', compact('statistics', 'currency'));
    }

    private function getTotalIncome($businessId, $month)
    {
        return JournalEntry::where('business_id', $businessId)
            ->whereHas('account', function ($query) {
                $query->where('type', 'income');
            })
            ->whereMonth('date_added', $month->month)
            ->whereYear('date_added', $month->year)
            ->sum('credit_amount');
    }

    private function getTotalExpenses($businessId, $month)
    {
        return JournalEntry::where('business_id', $businessId)
            ->whereHas('account', function ($query) {
                $query->where('type', 'expense');
            })
            ->whereMonth('date_added', $month->month)
            ->whereYear('date_added', $month->year)
            ->sum('debit_amount');
    }

    private function getNetBalance($businessId)
    {
        $totalIncome = JournalEntry::where('business_id', $businessId)
            ->whereHas('account', function ($query) {
                $query->where('type', 'income');
            })
            ->sum('credit_amount');

        $totalExpenses = JournalEntry::where('business_id', $businessId)
            ->whereHas('account', function ($query) {
                $query->where('type', 'expense');
            })
            ->sum('debit_amount');

        return $totalIncome - $totalExpenses;
    }

    // private function getPendingPayments($businessId)
    // {
    //     return FeePayment::where('business_id', $businessId)
    //         ->sum('total_amount');
    // }

    private function getIncomeChange($businessId, $currentMonth, $lastMonth)
    {
        $currentIncome = $this->getTotalIncome($businessId, $currentMonth);
        $lastIncome = $this->getTotalIncome($businessId, $lastMonth);

        if ($lastIncome == 0)
            return 0;
        return (($currentIncome - $lastIncome) / $lastIncome) * 100;
    }

    private function getExpensesChange($businessId, $currentMonth, $lastMonth)
    {
        $currentExpenses = $this->getTotalExpenses($businessId, $currentMonth);
        $lastExpenses = $this->getTotalExpenses($businessId, $lastMonth);

        if ($lastExpenses == 0)
            return 0;
        return (($currentExpenses - $lastExpenses) / $lastExpenses) * 100;
    }

    private function getAverageMonthlyIncome($businessId, $startOfYear)
    {
        $totalIncome = JournalEntry::where('business_id', $businessId)
            ->whereHas('account', function ($query) {
                $query->where('type', 'income');
            })
            ->where('date_added', '>=', $startOfYear)
            ->sum('credit_amount');

        $monthsCount = now()->diffInMonths($startOfYear) + 1;
        return $monthsCount > 0 ? $totalIncome / $monthsCount : 0;
    }

    private function getAverageMonthlyExpenses($businessId, $startOfYear)
    {
        $totalExpenses = JournalEntry::where('business_id', $businessId)
            ->whereHas('account', function ($query) {
                $query->where('type', 'expense');
            })
            ->where('date_added', '>=', $startOfYear)
            ->sum('debit_amount');

        $monthsCount = now()->diffInMonths($startOfYear) + 1;
        return $monthsCount > 0 ? $totalExpenses / $monthsCount : 0;
    }

    private function getAverageTransactionAmount($businessId)
    {
        $totalAmount = JournalEntry::where('business_id', $businessId)
            ->select(DB::raw('SUM(debit_amount + credit_amount) as total'))
            ->first()
            ->total;

        $transactionCount = JournalEntry::where('business_id', $businessId)->count();

        return $transactionCount > 0 ? $totalAmount / $transactionCount : 0;
    }

    // private function getPendingTransactionsCount($businessId)
    // {
    //     return FeePayment::where('business_id', $businessId)
    //         ->count();
    // }
}
