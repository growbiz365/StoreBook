<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DetailedGeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');

        if (!$businessId) {
            return redirect()
                ->route('businesses.index')
                ->with('error', 'Please select an active business to view the general ledger.');
        }

        $business = Business::findOrFail($businessId);

        // Filters
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $accountId = $request->get('account_id');
        $search = $request->get('search');

        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        // Accounts dropdown (only active)
        $accounts = ChartOfAccount::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        // Base query
        $entriesQuery = JournalEntry::with('account')
            ->where('business_id', $businessId)
            ->whereBetween('date_added', [$from, $to]);

        if ($accountId) {
            $entriesQuery->where('account_head', $accountId);
        }

        if ($search) {
            $entriesQuery->where(function ($q) use ($search) {
                $q->whereHas('account', function ($qa) use ($search) {
                    $qa->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhere('voucher_type', 'like', "%{$search}%")
                ->orWhere('voucher_id', 'like', "%{$search}%")
                ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        $entries = $entriesQuery
            ->orderByRaw('account_head asc, date_added asc, voucher_type asc, voucher_id asc, id asc')
            ->get();

        // Group by account
        $groupedByAccount = $entries->groupBy('account_head');

        $ledgerData = [
            'accounts' => [],
            'totals' => [
                'debit' => 0,
                'credit' => 0,
            ],
        ];

        foreach ($groupedByAccount as $accId => $accountEntries) {
            /** @var ChartOfAccount $account */
            $account = $accountEntries->first()->account;
            if (!$account) {
                continue;
            }

            $accountType = strtolower($account->type ?? '');
            $runningBalance = 0;

            $rows = [];
            foreach ($accountEntries as $entry) {
                // Normal balance logic: assets & expenses debit-positive; others credit-positive
                $debit = (float)$entry->debit_amount;
                $credit = (float)$entry->credit_amount;

                if (in_array($accountType, ['liability', 'equity', 'income', 'revenue'])) {
                    $runningBalance += ($credit - $debit);
                } else {
                    $runningBalance += ($debit - $credit);
                }

                $rows[] = [
                    'date' => $entry->date_added,
                    'voucher_type' => $entry->voucher_type,
                    'voucher_id' => $entry->voucher_id,
                    'comments' => $entry->comments,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $runningBalance,
                ];
            }

            $totalDebit = $accountEntries->sum('debit_amount');
            $totalCredit = $accountEntries->sum('credit_amount');

            $ledgerData['accounts'][] = [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'rows' => $rows,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'ending_balance' => $runningBalance,
            ];

            $ledgerData['totals']['debit'] += $totalDebit;
            $ledgerData['totals']['credit'] += $totalCredit;
        }

        // Sort accounts by code
        usort($ledgerData['accounts'], function ($a, $b) {
            return strcmp($a['code'], $b['code']);
        });

        return view('finance.detailed-general-ledger.index', [
            'business' => $business,
            'ledgerData' => $ledgerData,
            'accounts' => $accounts,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'accountId' => $accountId,
            'search' => $search,
        ]);
    }
}


