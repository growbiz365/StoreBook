<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Bank;
use App\Models\ExpenseAttachment;
use App\Models\BankLedger;
use App\Models\JournalEntry;
use App\Models\Expense;
use App\Models\ExpenseHead;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['expenseHead', 'bank', 'user'])
            ->where('business_id', session('active_business'));

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('expenseHead', function ($q) use ($searchTerm) {
                    $q->where('expense_head', 'like', "%{$searchTerm}%");
                })->orWhereHas('bank', function ($q) use ($searchTerm) {
                    $q->where('account_name', 'like', "%{$searchTerm}%");
                })->orWhere('details', 'like', "%{$searchTerm}%");
            });
        }

        $expenses = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $businessId = session('active_business');
        
        // Get expense heads created through ExpenseHead module (with chart_of_account_id)
        $expenseHeads = ExpenseHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->with('chartOfAccount')
            ->get()
            ->map(function($head) {
                return [
                    'id' => $head->chart_of_account_id,
                    'name' => $head->expense_head,
                    'is_expense_head' => true,
                ];
            })
            ->sortBy('name')
            ->values();
        
        // Get all other active expense accounts from Chart of Accounts
        $expenseHeadChartAccountIds = ExpenseHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->pluck('chart_of_account_id')
            ->toArray();
        
        $otherExpenseAccounts = ChartOfAccount::where('business_id', $businessId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->whereNotIn('id', $expenseHeadChartAccountIds)
            ->orderBy('name')
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'is_expense_head' => false,
                ];
            });
        
        // Combine: ExpenseHeads first, then other accounts
        $expenseAccounts = $expenseHeads->concat($otherExpenseAccounts);

        $banks = Bank::where('business_id', session('active_business'))
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();
            
        return view('expenses.create', compact('expenseAccounts', 'banks'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                'bank_id' => 'required|exists:banks,id',
                'amount' => 'required|numeric|min:0.01',
                'date_added' => 'required|date',
                'details' => 'nullable|string',
                'attachment_titles.*' => 'nullable|string|max:255',
                'attachment_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);

            $validated['business_id'] = session('active_business');
            $validated['user_id'] = auth()->id();
            
            if (!$validated['business_id']) {
                throw new \Exception('No active business selected.');
            }

            // Validate bank balance before proceeding
            $bank = Bank::where('business_id', $validated['business_id'])
                ->find($validated['bank_id']);

            if (!$bank) {
                throw new \Exception('Selected bank is invalid.');
            }

            $bankBalance = (float) $bank->getBalance();
            $requestedAmount = (float) $validated['amount'];

            if (round($bankBalance, 2) < round($requestedAmount, 2)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Insufficient bank balance. Available: PKR ' . number_format($bankBalance, 2) . ', Required: PKR ' . number_format($requestedAmount, 2));
            }

            DB::beginTransaction();
            try {
                // Get or create expense head based on chart of account
                $chartOfAccount = ChartOfAccount::findOrFail($validated['chart_of_account_id']);
                
                // Check if chart of account is an expense type
                if ($chartOfAccount->type !== 'expense') {
                    throw new \Exception('Selected account is not an expense account.');
                }
                
                // Find or create expense head for this chart of account
                $expenseHead = ExpenseHead::firstOrCreate(
                    [
                        'business_id' => session('active_business'),
                        'chart_of_account_id' => $chartOfAccount->id,
                    ],
                    [
                        'expense_head' => $chartOfAccount->name,
                    ]
                );
                
                // Add expense_head_id to validated data
                $validated['expense_head_id'] = $expenseHead->id;

                // Create expense record
                $expense = Expense::create($validated);

                // Handle file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('expenses/' . $expense->id, 'public');
                            
                            ExpenseAttachment::create([
                                'expense_id' => $expense->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Check if bank has chart of account
                if (!$bank->chart_of_account_id) {
                    throw new \Exception('Bank account does not have a chart of account assigned.');
                }

                // Create bank ledger entry (withdrawal)
                BankLedger::create([
                    'business_id' => session('active_business'),
                    'bank_id' => $validated['bank_id'],
                    'voucher_id' => $expense->id,
                    'voucher_type' => 'Expense',
                    'date' => $validated['date_added'],
                    'user_id' => auth()->id(),
                    'withdrawal_amount' => $validated['amount'],
                    'deposit_amount' => 0,
                ]);

                // Create journal entries
                // Debit: Expense head account
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $expenseHead->chart_of_account_id,
                    'voucher_id' => $expense->id,
                    'voucher_type' => 'Expense',
                    'date_added' => $validated['date_added'],
                    'user_id' => auth()->id(),
                    'debit_amount' => $validated['amount'],
                    'credit_amount' => 0,
                ]);

                // Credit: Bank account
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $bank->chart_of_account_id,
                    'voucher_id' => $expense->id,
                    'voucher_type' => 'Expense',
                    'date_added' => $validated['date_added'],
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => $validated['amount'],
                ]);

                DB::commit();

                return redirect()
                    ->route('expenses.index')
                    ->with('success', 'Expense created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in expense transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Expense creation failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating expense: ' . $e->getMessage());
        }
    }

    public function show(Expense $expense)
    {
        $expense->load(['expenseHead', 'bank', 'attachments', 'user']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $businessId = session('active_business');
        
        // Get expense heads created through ExpenseHead module (with chart_of_account_id)
        $expenseHeads = ExpenseHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->with('chartOfAccount')
            ->get()
            ->map(function($head) {
                return [
                    'id' => $head->chart_of_account_id,
                    'name' => $head->expense_head,
                    'is_expense_head' => true,
                ];
            })
            ->sortBy('name')
            ->values();
        
        // Get all other active expense accounts from Chart of Accounts
        $expenseHeadChartAccountIds = ExpenseHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->pluck('chart_of_account_id')
            ->toArray();
        
        $otherExpenseAccounts = ChartOfAccount::where('business_id', $businessId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->whereNotIn('id', $expenseHeadChartAccountIds)
            ->orderBy('name')
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'is_expense_head' => false,
                ];
            });
        
        // Combine: ExpenseHeads first, then other accounts
        $expenseAccounts = $expenseHeads->concat($otherExpenseAccounts);

        $banks = Bank::where('business_id', session('active_business'))
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();
            
        return view('expenses.edit', compact('expense', 'expenseAccounts', 'banks'));
    }

    public function update(Request $request, Expense $expense)
    {
        try {
            $validated = $request->validate([
                'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                'bank_id' => 'required|exists:banks,id',
                'amount' => 'required|numeric|min:0.01',
                'date_added' => 'required|date',
                'details' => 'nullable|string',
                'attachment_titles.*' => 'nullable|string|max:255',
                'attachment_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);

            $businessId = session('active_business');

            // Validate bank balance before proceeding
            $bank = Bank::where('business_id', $businessId)
                ->find($validated['bank_id']);

            if (!$bank) {
                throw new \Exception('Selected bank is invalid.');
            }

            $currentAmount = (float) $expense->amount;
            $requestedAmount = (float) $validated['amount'];
            $bankBalance = (float) $bank->getBalance();

            if ($expense->bank_id == $validated['bank_id']) {
                // Add back the current expense amount since it will be updated
                $bankBalance += $currentAmount;
            }

            if (round($bankBalance, 2) < round($requestedAmount, 2)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Insufficient bank balance. Available: PKR ' . number_format($bankBalance, 2) . ', Required: PKR ' . number_format($requestedAmount, 2));
            }

            DB::beginTransaction();
            try {
                // Get or create expense head based on chart of account
                $chartOfAccount = ChartOfAccount::findOrFail($validated['chart_of_account_id']);
                
                // Check if chart of account is an expense type
                if ($chartOfAccount->type !== 'expense') {
                    throw new \Exception('Selected account is not an expense account.');
                }
                
                // Find or create expense head for this chart of account
                $expenseHead = ExpenseHead::firstOrCreate(
                    [
                        'business_id' => session('active_business'),
                        'chart_of_account_id' => $chartOfAccount->id,
                    ],
                    [
                        'expense_head' => $chartOfAccount->name,
                    ]
                );
                
                // Add expense_head_id to validated data
                $validated['expense_head_id'] = $expenseHead->id;

                // Update expense record
                $expense->update($validated);

                // Handle new file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('expenses/' . $expense->id, 'public');
                            
                            ExpenseAttachment::create([
                                'expense_id' => $expense->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Check if bank has chart of account
                if (!$bank->chart_of_account_id) {
                    throw new \Exception('Bank account does not have a chart of account assigned.');
                }

                // Update bank ledger entry
                $bankLedger = BankLedger::where('voucher_id', $expense->id)
                    ->where('voucher_type', 'Expense')
                    ->first();

                if ($bankLedger) {
                    $bankLedger->update([
                        'bank_id' => $validated['bank_id'],
                        'date' => $validated['date_added'],
                        'withdrawal_amount' => $validated['amount'],
                    ]);
                }

                // Update journal entries
                $journalEntries = JournalEntry::where('voucher_id', $expense->id)
                    ->where('voucher_type', 'Expense')
                    ->get();

                foreach ($journalEntries as $entry) {
                    if ($entry->debit_amount > 0) {
                        // Update expense debit entry
                        $entry->update([
                            'account_head' => $expenseHead->chart_of_account_id,
                            'date_added' => $validated['date_added'],
                            'debit_amount' => $validated['amount'],
                            'credit_amount' => 0,
                        ]);
                    } else {
                        // Update bank credit entry
                        $entry->update([
                            'account_head' => $bank->chart_of_account_id,
                            'date_added' => $validated['date_added'],
                            'debit_amount' => 0,
                            'credit_amount' => $validated['amount'],
                        ]);
                    }
                }

                DB::commit();

                return redirect()
                    ->route('expenses.index')
                    ->with('success', 'Expense updated successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in expense update transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Expense update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating expense: ' . $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            // Check if expense belongs to current business
            if ($expense->business_id != session('active_business')) {
                return redirect()
                    ->route('expenses.index')
                    ->with('error', 'You do not have permission to delete this expense.');
            }

            DB::beginTransaction();
            try {
                // Delete all attachments and their files
                foreach ($expense->attachments as $attachment) {
                    try {
                        Storage::disk('public')->delete($attachment->file_path);
                    } catch (\Exception $fileError) {
                        Log::warning('Failed to delete attachment file: ' . $attachment->file_path . ' - ' . $fileError->getMessage());
                        // Continue even if file deletion fails
                    }
                    $attachment->delete();
                }

                // Delete bank ledger entry (this will automatically adjust balances)
                $deletedLedgerEntries = BankLedger::where('voucher_id', $expense->id)
                    ->where('voucher_type', 'Expense')
                    ->delete();

                if ($deletedLedgerEntries === 0) {
                    Log::warning('No ledger entries found for expense ID: ' . $expense->id);
                }

                // Delete journal entries
                $deletedJournalEntries = JournalEntry::where('voucher_id', $expense->id)
                    ->where('voucher_type', 'Expense')
                    ->delete();

                if ($deletedJournalEntries === 0) {
                    Log::warning('No journal entries found for expense ID: ' . $expense->id);
                }

                // Delete the expense record
                $expense->delete();

                DB::commit();

                return redirect()
                    ->route('expenses.index')
                    ->with('success', 'Expense has been deleted successfully.');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error('Database error in expense deletion: ' . $e->getMessage());
                
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return redirect()
                        ->route('expenses.index')
                        ->with('error', 'Cannot delete this expense because it is referenced by other records. Please contact support.');
                }
                
                return redirect()
                    ->route('expenses.index')
                    ->with('error', 'A database error occurred while deleting the expense. Please try again or contact support if the problem persists.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in expense deletion transaction: ' . $e->getMessage());
                
                // Handle specific error cases
                if (str_contains($e->getMessage(), 'ledger') || str_contains($e->getMessage(), 'BankLedger')) {
                    return redirect()
                        ->route('expenses.index')
                        ->with('error', 'Failed to delete bank ledger entries. The expense may have already been deleted or modified. Please refresh the page.');
                }
                
                if (str_contains($e->getMessage(), 'journal') || str_contains($e->getMessage(), 'JournalEntry')) {
                    return redirect()
                        ->route('expenses.index')
                        ->with('error', 'Failed to delete journal entries. The expense may have already been deleted or modified. Please refresh the page.');
                }
                
                if (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'storage')) {
                    return redirect()
                        ->route('expenses.index')
                        ->with('error', 'The expense was deleted, but some attachment files could not be removed. This does not affect the ledger balances.');
                }
                
                return redirect()
                    ->route('expenses.index')
                    ->with('error', 'Unable to delete the expense. Please try again or contact support if the problem continues.');
            }

        } catch (\Exception $e) {
            Log::error('Expense deletion failed: ' . $e->getMessage());
            
            $errorMessage = 'Unable to delete expense. ';
            
            if (str_contains($e->getMessage(), 'permission') || str_contains($e->getMessage(), 'Unauthorized')) {
                $errorMessage = 'You do not have permission to delete this expense.';
            } elseif (str_contains($e->getMessage(), 'constraint') || str_contains($e->getMessage(), 'foreign')) {
                $errorMessage = 'This expense cannot be deleted because it is being used by other records in the system.';
            } else {
                $errorMessage .= 'Please try again or contact support if the problem persists.';
            }
            
            return redirect()
                ->route('expenses.index')
                ->with('error', $errorMessage);
        }
    }

    public function deleteAttachment(ExpenseAttachment $attachment)
    {
        try {
            DB::beginTransaction();

            // Delete the file from storage
            Storage::disk('public')->delete($attachment->file_path);

            // Delete the record
            $attachment->delete();

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting attachment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function report(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->back()->with('error', 'No active business selected.');
        }
        
        $business = Business::find($businessId);
        
        // Get expense heads for filter
        $expenseHeads = ExpenseHead::where('business_id', $businessId)
            ->orderBy('expense_head')
            ->get();



        // Build query for journal entries (expense transactions)
        $query = JournalEntry::with(['account', 'user', 'voucher.bank', 'voucher'])
            ->where('business_id', $businessId)
            ->where('voucher_type', 'Expense')
            ->where('debit_amount', '>', 0); // Only debit entries (expense side)

        // Apply filters
        if ($request->filled('from_date')) {
            $query->where('date_added', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('date_added', '<=', $request->to_date);
        }

        if ($request->filled('expense_head_id')) {
            $query->whereHas('account', function($q) use ($request) {
                $q->whereHas('expenseHead', function($q) use ($request) {
                    $q->where('id', $request->expense_head_id);
                });
            });
        }



        // Get the expense entries
        $expenseEntries = $query->orderBy('date_added', 'desc')->get();

        // Calculate totals
        $totalExpense = $expenseEntries->sum('debit_amount');
        $totalEntries = $expenseEntries->count();

        // Group by expense head for summary
        $expenseByHead = $expenseEntries->groupBy(function($entry) {
            $expenseHead = $entry->account->expenseHead->first();
            return $expenseHead ? $expenseHead->expense_head : 'Unknown';
        })->map(function($entries) {
            return [
                'count' => $entries->count(),
                'total' => $entries->sum('debit_amount'),
                'entries' => $entries
            ];
        });

        return view('expenses.report', compact(
            'expenseEntries',
            'expenseByHead',
            'totalExpense',
            'totalEntries',
            'expenseHeads',
            'business'
        ));
    }

    public function dashboard(Request $request)
    {
        $businessId = session('active_business');
        
        // Debug information
        if (!$businessId) {
            return redirect()->back()->with('error', 'No active business selected.');
        }

        // Check if business exists
        $business = Business::find($businessId);
        if (!$business) {
            return redirect()->back()->with('error', 'Business not found.');
        }

        // Base query for all expense journal entries
        $baseQuery = JournalEntry::with(['account', 'user', 'voucher.bank', 'voucher'])
            ->where('business_id', $businessId)
            ->where('voucher_type', 'Expense')
            ->where('debit_amount', '>', 0);

        // Handle date filtering
        $filterType = $request->get('filter_type', 'current_month');
        $filteredQuery = clone $baseQuery;

        switch ($filterType) {
            case 'custom':
                if ($request->filled('from_date') && $request->filled('to_date')) {
                    $filteredQuery->whereBetween('date_added', [$request->from_date, $request->to_date]);
                } else {
                    // Default to current month if no dates provided
                    $filteredQuery->whereBetween('date_added', [now()->startOfMonth(), now()->endOfMonth()]);
                }
                break;
            case 'yearly':
                $filteredQuery->whereYear('date_added', now()->year);
                break;
            case 'current_month':
            default:
                $filteredQuery->whereBetween('date_added', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
        }

        // Get filtered expense data
        $expenseEntries = $filteredQuery->orderBy('date_added', 'desc')->get();

        // Summary Cards Data (based on filtered data)
        $totalExpenses = $expenseEntries->sum('debit_amount');
        $totalEntries = $expenseEntries->count();
        
        // Monthly average (last 12 months - always calculated from all data for comparison)
        $monthlyAverage = $baseQuery->where('date_added', '>=', now()->subMonths(12))
            ->sum('debit_amount') / 12;
        
        // Top expense head (from filtered data)
        $topExpenseHead = $expenseEntries->groupBy(function($entry) {
            $expenseHead = $entry->account->expenseHead->first();
            return $expenseHead ? $expenseHead->expense_head : 'Unknown';
        })->map(function($entries) {
            return $entries->sum('debit_amount');
        })->sortDesc()->keys()->first() ?? 'N/A';

        // Recent expenses (from filtered data, last 15)
        $recentExpenses = $expenseEntries->take(15);

        // Expenses by expense head for pie chart (from filtered data)
        $expensesByHead = $expenseEntries->groupBy(function($entry) {
            $expenseHead = $entry->account->expenseHead->first();
            return $expenseHead ? $expenseHead->expense_head : 'Unknown';
        })->map(function($entries) {
            return $entries->sum('debit_amount');
        });

        // Monthly trends for bar chart (last 6 months - always calculated from all data for context)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthExpenses = $baseQuery->where('date_added', '>=', $month->startOfMonth())
                ->where('date_added', '<=', $month->endOfMonth())
                ->sum('debit_amount');
            
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'amount' => $monthExpenses
            ];
        }

        // Prepare filter data for view with defaults
        $filterData = [
            'filter_type' => $filterType,
            'from_date' => $request->get('from_date', now()->startOfMonth()->format('Y-m-d')),
            'to_date' => $request->get('to_date', now()->endOfMonth()->format('Y-m-d')),
        ];

        return view('expenses.dashboard', compact(
            'totalExpenses',
            'totalEntries',
            'monthlyAverage',
            'topExpenseHead',
            'recentExpenses',
            'expensesByHead',
            'monthlyTrends',
            'filterData'
        ));
    }
}
