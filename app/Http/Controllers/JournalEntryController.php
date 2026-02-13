<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalEntry::with(['account', 'user'])
            ->where('business_id', session('active_business'));

        // Apply date filters - default to current month
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();

        $query->whereBetween('date_added', [$fromDate->startOfDay(), $toDate->endOfDay()]);

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('account', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })
                    ->orWhere('voucher_type', 'like', "%{$searchTerm}%")
                    ->orWhere('debit_amount', 'like', "%{$searchTerm}%")
                    ->orWhere('credit_amount', 'like', "%{$searchTerm}%")
                    ->orWhere('comments', 'like', "%{$searchTerm}%");
            });
        }

        // Get all entries without pagination - order by newest first
        $journalEntries = $query->orderBy('date_added', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('voucher_type', 'asc')
            ->orderBy('voucher_id', 'asc')
            ->get();

        // Group entries by voucher and maintain order
        $groupedEntries = $journalEntries->groupBy(function ($entry) {
            return $entry->date_added->format('Y-m-d') . '|' . $entry->voucher_type . '|' . $entry->voucher_id;
        })->sortKeysDesc();

        // Calculate totals
        $totalDebit = $journalEntries->sum('debit_amount');
        $totalCredit = $journalEntries->sum('credit_amount');

        return view('finance.journal-entries.index', compact(
            'groupedEntries', 
            'totalDebit', 
            'totalCredit', 
            'fromDate', 
            'toDate'
        ));
    }
}
