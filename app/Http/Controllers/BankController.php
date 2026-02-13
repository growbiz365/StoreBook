<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankLedger;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\BankTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    public function ledgerReport(Request $request)
    {
        $businessId = session('active_business');
        $business = \App\Models\Business::find($businessId);
        
        // Always fetch all active banks for the dropdown
        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();
        
        $selectedBank = null;
        $ledgerEntries = collect();
        $openingBalance = 0;
        $totals = [
            'deposits' => 0,
            'withdrawals' => 0,
            'balance' => 0
        ];
    
        if ($request->filled('bank_id')) {
            $selectedBank = Bank::find($request->bank_id);
            
            $query = BankLedger::where('bank_id', $request->bank_id)
                ->where('business_id', $businessId);
    
            // Apply date filters
            if ($request->filled('from_date')) {
                $query->where('date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->where('date', '<=', $request->to_date);
            }
    
            // Get opening balance before from_date
            $openingQuery = BankLedger::where('bank_id', $request->bank_id)
                ->where('business_id', $businessId);
    
            if ($request->filled('from_date')) {
                $openingQuery->where('date', '<', $request->from_date);
            }
    
            $openingBalance = $openingQuery->get()
                ->reduce(function ($carry, $item) {
                    return $carry + $item->deposit_amount - $item->withdrawal_amount;
                }, 0);
    
            // Get filtered ledger entries
            $ledgerEntries = $query->orderBy('date')->orderBy('id')->get();
    
            // Calculate running balance
            $runningBalance = $openingBalance;
            $ledgerEntries->transform(function ($item) use (&$runningBalance) {
                $runningBalance += $item->deposit_amount - $item->withdrawal_amount;
                $item->running_balance = $runningBalance;
                return $item;
            });
    
            // Calculate totals
            $totals = [
                'deposits' => $ledgerEntries->sum('deposit_amount'),
                'withdrawals' => $ledgerEntries->sum('withdrawal_amount'),
                'balance' => $runningBalance
            ];
        }
    
        // Pass date filters to view for display
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate = $request->filled('to_date') ? $request->to_date : null;
        
        return view('banks.ledger-report', compact(
            'business',
            'banks',
            'selectedBank',
            'ledgerEntries',
            'openingBalance',
            'totals',
            'fromDate',
            'toDate'
        ));
    }
    
    public function balancesReport(Request $request)
    {
        $businessId = session('active_business');
        $business = \App\Models\Business::find($businessId);
        
        // Get the date filter (default to today if not provided)
        $asOfDate = $request->filled('date') ? $request->date : now()->format('Y-m-d');
        
        // Get all bank accounts with their ledger entries (including inactive)
        $query = Bank::where('business_id', $businessId)
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->withSum(['ledgerEntries as balance' => function($query) use ($asOfDate) {
                $query->where('date', '<=', $asOfDate);
            }], DB::raw('deposit_amount - withdrawal_amount'));

        // Separate accounts into bank and cash
        $bankAccounts = clone $query;
        $bankAccounts = $bankAccounts->where('account_type', 'bank')
            ->orderBy('account_name')
            ->get();

        $cashAccounts = clone $query;
        $cashAccounts = $cashAccounts->where('account_type', 'cash')
            ->orderBy('account_name')
            ->get();

        return view('banks.balances-report', compact(
            'business',
            'bankAccounts',
            'cashAccounts',
            'asOfDate'
        ));
    }
    public function dashboard()
    {
        $businessId = session('active_business');

        // Get all bank accounts with their balances (including inactive)
        $accounts = Bank::where('business_id', $businessId)
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->withSum(['ledgerEntries as balance' => function($query) {
                $query->where('business_id', session('active_business'));
            }], DB::raw('deposit_amount - withdrawal_amount'))
            ->get();

        // Calculate totals
        $totalAccounts = $accounts->count();
        $totalBalance = $accounts->sum('balance');
        $cashBalance = $accounts->where('account_type', 'cash')->sum('balance');
        $bankBalance = $accounts->where('account_type', 'bank')->sum('balance');

        // Get recent transfers
        $recentTransfers = BankTransfer::with(['fromAccount', 'toAccount'])
            ->where('business_id', $businessId)
            ->latest()
            ->take(5)
            ->get();

        // Get top accounts by transaction volume
        $topAccounts = BankLedger::with('bank')
            ->where('business_id', $businessId)
            ->selectRaw('bank_id, SUM(deposit_amount + withdrawal_amount) as total_volume')
            ->groupBy('bank_id')
            ->orderByDesc('total_volume')
            ->take(5)
            ->get();

        return view('banks.dashboard', compact(
            'totalAccounts',
            'totalBalance',
            'cashBalance',
            'bankBalance',
            'recentTransfers',
            'topAccounts'
        ));
    }
    public function index(Request $request)
    {
        $query = Bank::with(['chartOfAccount'])
            ->where('business_id', session('active_business'));

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('account_name', 'like', "%{$searchTerm}%")
                    ->orWhere('bank_name', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('account_type', $request->type);
        }

        $banks = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        try {
            $businessId = session('active_business');
            
            // Check for duplicate account name (case-insensitive) before validation
            $accountNameUpper = strtoupper($request->account_name);
            $existingBank = Bank::where('business_id', $businessId)
                ->whereRaw('UPPER(account_name) = ?', [$accountNameUpper])
                ->first();
            
            if ($existingBank) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['account_name' => 'An account with this name already exists for this business. Please use a different name.']);
            }
            
            $validated = $request->validate([
                'account_type' => 'required|in:bank,cash',
                'account_name' => 'required|string|max:255',
                'bank_name' => 'required_if:account_type,bank|nullable|string|max:255',
                'description' => 'nullable|string',
                'opening_balance' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:1,0',
            ]);

            $validated['business_id'] = $businessId;
            $validated['user_id'] = auth()->id();
            // Set default status to 1 (active) if not provided
            if (!isset($validated['status'])) {
                $validated['status'] = 1;
            }

            DB::beginTransaction();
            try {
                // Get parent account code based on account type
                $parentCode = $validated['account_type'] === 'bank' ? '1120' : '1110';
                
                // Get parent account
                $parentAccount = ChartOfAccount::where('code', $parentCode)
                    ->where('business_id', session('active_business'))
                    ->first();

                if (!$parentAccount) {
                    throw new \Exception("Parent account with code {$parentCode} not found.");
                }

                // Get last sibling code to generate new code
                $lastSibling = ChartOfAccount::where('parent_id', $parentAccount->id)
                    ->where('business_id', session('active_business'))
                    ->orderBy('code', 'desc')
                    ->first();

                if ($lastSibling) {
                    // Extract the numeric part after the parent code prefix
                    // For parent code 1120, children should be 1121, 1122, etc.
                    $lastCode = $lastSibling->code;
                    // Get the last digit(s) - for 1120 parent, we want to increment the last digit
                    // So 1121 -> 1122, 1122 -> 1123, etc.
                    $lastCodeInt = (int) $lastCode;
                    $newCode = str_pad($lastCodeInt + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    // First child: parent code 1120 -> child code 1121
                    // Replace last digit with 1
                    $newCode = substr($parentCode, 0, -1) . '1';
                }

                // Create combined account name: Account Name + Bank Name
                $combinedAccountName = $validated['account_name'];
                if ($validated['account_type'] === 'bank' && !empty($validated['bank_name'])) {
                    $combinedAccountName = $validated['account_name'] . ' ' . $validated['bank_name'];
                }

                // Create chart of account entry
                $chartOfAccount = ChartOfAccount::create([
                    'business_id' => session('active_business'),
                    'parent_id' => $parentAccount->id,
                    'name' => $combinedAccountName,
                    'code' => $newCode,
                    'type' => 'asset',
                    'is_active' => true,
                    'user_id' => auth()->id(),
                ]);

                // Create bank record
                $validated['chart_of_account_id'] = $chartOfAccount->id;
                $bank = Bank::create($validated);

                // Handle opening balance if provided and > 0
                if (!empty($validated['opening_balance']) && $validated['opening_balance'] > 0) {
                    // Create bank ledger entry
                    BankLedger::create([
                        'business_id' => session('active_business'),
                        'bank_id' => $bank->id,
                        'date' => now(),
                        'deposit_amount' => $validated['opening_balance'],
                        'withdrawal_amount' => 0,
                        'voucher_type' => 'Bank OB',
                        'voucher_id' => $bank->id,
                        'details' => 'Opening Balance',
                        'user_id' => auth()->id(),
                    ]);

                    // Get opening balance adjustment account
                    $openingBalanceAccount = ChartOfAccount::where('code', '2303')
                        ->where('business_id', session('active_business'))
                        ->first();

                    if (!$openingBalanceAccount) {
                        throw new \Exception('Opening Balance Adjustment account (2303) not found.');
                    }

                    // Create journal entries
                    JournalEntry::create([
                        'business_id' => session('active_business'),
                        'date' => now(),
                        'date_added' => now(),
                        'chart_of_account_id' => $chartOfAccount->id,
                        'account_head' => $chartOfAccount->id,
                        'debit_amount' => $validated['opening_balance'],
                        'credit_amount' => 0,
                        'voucher_type' => 'Bank OB',
                        'voucher_id' => $bank->id,
                        'details' => 'Opening Balance',
                        'user_id' => auth()->id(),
                    ]);

                    JournalEntry::create([
                        'business_id' => session('active_business'),
                        'date' => now(),
                        'date_added' => now(),
                        'chart_of_account_id' => $openingBalanceAccount->id,
                        'account_head' => $openingBalanceAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => $validated['opening_balance'],
                        'voucher_type' => 'Bank OB',
                        'voucher_id' => $bank->id,
                        'details' => 'Opening Balance',
                        'user_id' => auth()->id(),
                    ]);
                }

                DB::commit();

                return redirect()
                    ->route('banks.index')
                    ->with('success', 'Bank account created successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in bank creation transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Bank creation failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating bank account: ' . $e->getMessage());
        }
    }

    public function edit(Bank $bank)
    {
        return view('banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        try {
            $businessId = session('active_business');
            
            // Check for duplicate account name (case-insensitive) before validation, excluding current bank
            $accountNameUpper = strtoupper($request->account_name);
            $existingBank = Bank::where('business_id', $businessId)
                ->where('id', '!=', $bank->id)
                ->whereRaw('UPPER(account_name) = ?', [$accountNameUpper])
                ->first();
            
            if ($existingBank) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['account_name' => 'An account with this name already exists for this business. Please use a different name.']);
            }
            
            $validated = $request->validate([
                'account_name' => 'required|string|max:255',
                'bank_name' => 'required_if:account_type,bank|nullable|string|max:255',
                'description' => 'nullable|string',
                'opening_balance' => 'nullable|numeric|min:0',
                'account_type' => 'required|in:bank,cash',
                'status' => 'nullable|in:1,0',
            ]);

            DB::beginTransaction();
            try {
                // Update bank record
                $bank->update($validated);

                // Create combined account name: Account Name + Bank Name
                $combinedAccountName = $validated['account_name'];
                if ($validated['account_type'] === 'bank' && !empty($validated['bank_name'])) {
                    $combinedAccountName = $validated['account_name'] . ' ' . $validated['bank_name'];
                }

                // Update chart of account
                $bank->chartOfAccount->update([
                    'name' => $combinedAccountName,
                    'is_active' => true
                ]);

                // Handle opening balance change if different
                if ($bank->wasChanged('opening_balance')) {
                    // Delete old opening balance entries
                    BankLedger::where('bank_id', $bank->id)
                        ->where('voucher_type', 'Bank OB')
                        ->delete();

                    JournalEntry::where('voucher_id', $bank->id)
                        ->where('voucher_type', 'Bank OB')
                        ->delete();

                    // Create new opening balance entries if provided and > 0
                    if (!empty($validated['opening_balance']) && $validated['opening_balance'] > 0) {
                        // Create bank ledger entry
                        BankLedger::create([
                            'business_id' => session('active_business'),
                            'bank_id' => $bank->id,
                            'date' => now(),
                            'deposit_amount' => $validated['opening_balance'],
                            'withdrawal_amount' => 0,
                            'voucher_type' => 'Bank OB',
                            'voucher_id' => $bank->id,
                            'details' => 'Opening Balance',
                            'user_id' => auth()->id(),
                        ]);

                        // Get opening balance adjustment account
                        $openingBalanceAccount = ChartOfAccount::where('code', '2303')
                            ->where('business_id', session('active_business'))
                            ->first();

                        if (!$openingBalanceAccount) {
                            throw new \Exception('Opening Balance Adjustment account (2303) not found.');
                        }

                        // Create journal entries
                        JournalEntry::create([
                            'business_id' => session('active_business'),
                            'date' => now(),
                            'date_added' => now(),
                            'chart_of_account_id' => $bank->chart_of_account_id,
                            'account_head' => $bank->chart_of_account_id,
                            'debit_amount' => $validated['opening_balance'],
                            'credit_amount' => 0,
                            'voucher_type' => 'Bank OB',
                            'voucher_id' => $bank->id,
                            'details' => 'Opening Balance',
                            'user_id' => auth()->id(),
                        ]);

                        JournalEntry::create([
                            'business_id' => session('active_business'),
                            'date' => now(),
                            'date_added' => now(),
                            'chart_of_account_id' => $openingBalanceAccount->id,
                            'account_head' => $openingBalanceAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $validated['opening_balance'],
                            'voucher_type' => 'Bank OB',
                            'voucher_id' => $bank->id,
                            'details' => 'Opening Balance',
                            'user_id' => auth()->id(),
                        ]);
                    }
                } else {
                    // Handle case where bank previously had no opening balance but now gets one
                    $oldOpeningBalance = $bank->getOriginal('opening_balance');
                    $newOpeningBalance = $validated['opening_balance'];
                    
                    // If old was null/empty and new has value, create entries
                    if ((empty($oldOpeningBalance) || $oldOpeningBalance == 0) && !empty($newOpeningBalance) && $newOpeningBalance > 0) {
                        // Create bank ledger entry
                        BankLedger::create([
                            'business_id' => session('active_business'),
                            'bank_id' => $bank->id,
                            'date' => now(),
                            'deposit_amount' => $newOpeningBalance,
                            'withdrawal_amount' => 0,
                            'voucher_type' => 'Bank OB',
                            'voucher_id' => $bank->id,
                            'details' => 'Opening Balance',
                            'user_id' => auth()->id(),
                        ]);

                        // Get opening balance adjustment account
                        $openingBalanceAccount = ChartOfAccount::where('code', '2303')
                            ->where('business_id', session('active_business'))
                            ->first();

                        if (!$openingBalanceAccount) {
                            throw new \Exception('Opening Balance Adjustment account (2303) not found.');
                        }

                        // Create journal entries
                        JournalEntry::create([
                            'business_id' => session('active_business'),
                            'date' => now(),
                            'date_added' => now(),
                            'chart_of_account_id' => $bank->chart_of_account_id,
                            'account_head' => $bank->chart_of_account_id,
                            'debit_amount' => $newOpeningBalance,
                            'credit_amount' => 0,
                            'voucher_type' => 'Bank OB',
                            'voucher_id' => $bank->id,
                            'details' => 'Opening Balance',
                            'user_id' => auth()->id(),
                        ]);

                        JournalEntry::create([
                            'business_id' => session('active_business'),
                            'date' => now(),
                            'date_added' => now(),
                            'chart_of_account_id' => $openingBalanceAccount->id,
                            'account_head' => $openingBalanceAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $newOpeningBalance,
                            'voucher_type' => 'Bank OB',
                            'voucher_id' => $bank->id,
                            'details' => 'Opening Balance',
                            'user_id' => auth()->id(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()
                    ->route('banks.index')
                    ->with('success', 'Bank account updated successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in bank update transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Bank update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating bank account: ' . $e->getMessage());
        }
    }

    /**
     * Get bank balance via AJAX
     */
    public function getBalance(Bank $bank, Request $request)
    {
        // Check if bank belongs to current business
        if ($bank->business_id != session('active_business')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the date parameter if provided
        $asOfDate = $request->get('date');

        $balance = $bank->getBalance($asOfDate);
        $formattedBalance = $bank->getFormattedBalance($asOfDate);
        $status = $bank->getBalanceStatus($asOfDate);

        \Log::info('Bank balance request', [
            'bank_id' => $bank->id,
            'bank_name' => $bank->account_name,
            'as_of_date' => $asOfDate,
            'balance' => $balance,
            'formatted_balance' => $formattedBalance,
            'status' => $status
        ]);

        return response()->json([
            'balance' => $balance,
            'formatted_balance' => $formattedBalance,
            'status' => $status
        ]);
    }
} 