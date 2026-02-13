<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\PartyLedger;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Models\PartyTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $query = Party::with(['user'])
            ->where('business_id', session('active_business'));

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('phone_no', 'like', "%{$searchTerm}%")
                    ->orWhere('ntn', 'like', "%{$searchTerm}%");
            });
        }

        $parties = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('parties.index', compact('parties'));
    }

    public function create()
    {
        return view('parties.create');
    }

    public function store(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Party creation request data:', $request->all());

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'phone_no' => 'nullable|string|max:20',
                'whatsapp_no' => 'nullable|string|max:20',
                'cnic' => 'nullable|string|max:15',
                'ntn' => 'nullable|string|max:20',
                'opening_balance' => 'nullable|numeric|min:0',
                'opening_date' => 'nullable|date',
                'opening_type' => 'nullable|in:credit,debit',
                'status' => 'nullable|in:1,0',
            ]);

            $validated['business_id'] = session('active_business');
            
            if (!$validated['business_id']) {
                throw new \Exception('No active business selected.');
            }

            $validated['user_id'] = auth()->id();

            DB::beginTransaction();
            try {
                // Create chart of account for the party
                $partyAccount = ChartOfAccount::createPartyAccount($validated['name'], session('active_business'));
                
                // Add chart_of_account_id to validated data
                $validated['chart_of_account_id'] = $partyAccount->id;

                // Create party record
                $party = Party::create($validated);

                if (!empty($validated['opening_balance']) && $validated['opening_balance'] > 0) {
                    // Create party ledger entry
                    PartyLedger::create([
                        'business_id' => session('active_business'),
                        'party_id' => $party->id,
                        'voucher_id' => $party->id,
                        'voucher_type' => 'Party OB',
                        'date_added' => $validated['opening_date'],
                        'user_id' => auth()->id(),
                        'debit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                        'credit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                    ]);

                    // Get required account IDs
                    $openingBalanceId = ChartOfAccount::where('code', '2303')
                        ->where('business_id', session('active_business'))
                        ->value('id');

                    if (!$openingBalanceId) {
                        throw new \Exception('Required account not found. Please make sure Opening Balance Adjustment (2303) account exists.');
                    }

                    // Use the party's specific chart of account
                    $partyAccountId = $partyAccount->id;

                    // Create journal entries
                    $now = now();
                    $journalEntries = [
                        [
                            'business_id' => session('active_business'),
                            'account_head' => $partyAccountId,
                            'date_added' => $validated['opening_date'],
                            'voucher_id' => $party->id,
                            'voucher_type' => 'Party OB',
                            'user_id' => auth()->id(),
                            'debit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                            'credit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'business_id' => session('active_business'),
                            'account_head' => $openingBalanceId,
                            'date_added' => $validated['opening_date'],
                            'voucher_id' => $party->id,
                            'voucher_type' => 'Party OB',
                            'user_id' => auth()->id(),
                            'debit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                            'credit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    ];

                    JournalEntry::insert($journalEntries);
                }

                DB::commit();

                return redirect()
                    ->route('parties.index')
                    ->with('success', 'Party created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in party creation transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Party creation failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating party: ' . $e->getMessage());
        }
    }

    public function edit(Party $party)
    {
        return view('parties.edit', compact('party'));
    }

    public function update(Request $request, Party $party)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'phone_no' => 'nullable|string|max:20',
                'whatsapp_no' => 'nullable|string|max:20',
                'cnic' => 'nullable|string|max:15',
                'ntn' => 'nullable|string|max:20',
                'opening_balance' => 'nullable|numeric|min:0',
                'opening_date' => 'nullable|date',
                'opening_type' => 'nullable|in:credit,debit',
                'status' => 'nullable|in:1,0',
            ]);

            DB::beginTransaction();
            try {
                // Update party's chart of account name if party name changed
                if ($party->name !== $validated['name'] && $party->chartOfAccount) {
                    $party->chartOfAccount->update([
                        'name' => $validated['name'] // Just the party name, no prefix
                    ]);
                }

                // If party doesn't have a chart of account, create one
                if (!$party->chart_of_account_id) {
                    $partyAccount = ChartOfAccount::createPartyAccount($validated['name'], session('active_business'));
                    $validated['chart_of_account_id'] = $partyAccount->id;
                }

                // Update party record
                $party->update($validated);

                // Get party's chart of account ID
                $partyAccountId = $party->chart_of_account_id ?? $party->chartOfAccount->id;

                // Update party ledger entry
                $partyLedger = PartyLedger::where('party_id', $party->id)
                    ->where('voucher_type', 'Party OB')
                    ->first();

                if (!empty($validated['opening_balance']) && $validated['opening_balance'] > 0) {
                    if ($partyLedger) {
                        // Update existing party ledger entry
                        $partyLedger->update([
                            'date_added' => $validated['opening_date'],
                            'debit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                            'credit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                        ]);

                        // Get opening balance account ID
                        $openingBalanceId = ChartOfAccount::where('code', '2303')
                            ->where('business_id', session('active_business'))
                            ->value('id');
                        
                        if (!$openingBalanceId) {
                            throw new \Exception('Required account not found. Please make sure Opening Balance Adjustment (2303) account exists.');
                        }
                        
                        // Delete existing journal entries to avoid duplicates or incorrect balances
                        JournalEntry::where('voucher_id', $party->id)
                            ->where('voucher_type', 'Party OB')
                            ->delete();
                        
                        // Create new journal entries with correct amounts
                        $now = now();
                        $journalEntries = [
                            [
                                'business_id' => session('active_business'),
                                'account_head' => $partyAccountId,
                                'date_added' => $validated['opening_date'],
                                'voucher_id' => $party->id,
                                'voucher_type' => 'Party OB',
                                'user_id' => auth()->id(),
                                'debit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                                'credit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'business_id' => session('active_business'),
                                'account_head' => $openingBalanceId,
                                'date_added' => $validated['opening_date'],
                                'voucher_id' => $party->id,
                                'voucher_type' => 'Party OB',
                                'user_id' => auth()->id(),
                                'debit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                                'credit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]
                        ];
                        
                        JournalEntry::insert($journalEntries);
                    } else {
                        // Create new party ledger entry
                        PartyLedger::create([
                            'business_id' => session('active_business'),
                            'party_id' => $party->id,
                            'voucher_id' => $party->id,
                            'voucher_type' => 'Party OB',
                            'date_added' => $validated['opening_date'],
                            'user_id' => auth()->id(),
                            'debit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                            'credit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                        ]);

                        // Get opening balance account ID
                        $openingBalanceId = ChartOfAccount::where('code', '2303')
                            ->where('business_id', session('active_business'))
                            ->value('id');

                        if (!$openingBalanceId) {
                            throw new \Exception('Required account not found. Please make sure Opening Balance Adjustment (2303) account exists.');
                        }

                        // Create new journal entries
                        $now = now();
                        $journalEntries = [
                            [
                                'business_id' => session('active_business'),
                                'account_head' => $partyAccountId,
                                'date_added' => $validated['opening_date'],
                                'voucher_id' => $party->id,
                                'voucher_type' => 'Party OB',
                                'user_id' => auth()->id(),
                                'debit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                                'credit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'business_id' => session('active_business'),
                                'account_head' => $openingBalanceId,
                                'date_added' => $validated['opening_date'],
                                'voucher_id' => $party->id,
                                'voucher_type' => 'Party OB',
                                'user_id' => auth()->id(),
                                'debit_amount' => $validated['opening_type'] === 'credit' ? $validated['opening_balance'] : 0,
                                'credit_amount' => $validated['opening_type'] === 'debit' ? $validated['opening_balance'] : 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]
                        ];

                        JournalEntry::insert($journalEntries);
                    }
                } else {
                    // If opening balance is not provided or is 0, delete existing opening balance entries
                    if ($partyLedger) {
                        $partyLedger->delete();
                    }
                    
                    // Also delete journal entries for opening balance
                    JournalEntry::where('voucher_id', $party->id)
                        ->where('voucher_type', 'Party OB')
                        ->delete();
                }

                DB::commit();

                return redirect()
                    ->route('parties.index')
                    ->with('success', 'Party updated successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in party update transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Party update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating party: ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        $businessId = session('active_business');

        // Get all parties with their balances
        $parties = Party::where('business_id', $businessId)
            ->withSum(['ledgerEntries as balance' => function($query) {
                $query->where('business_id', session('active_business'));
            }], DB::raw('credit_amount - debit_amount'))
            ->get();

        // Calculate totals
        $totalParties = Party::where('business_id', $businessId)->count();
        $accountPayable = $parties->where('balance', '>', 0)->sum('balance');
        $accountReceivable = abs($parties->where('balance', '<', 0)->sum('balance'));
        $totalBalance = $accountPayable - $accountReceivable;

        // Get recent transfers
        $recentTransfers = PartyTransfer::with(['debitParty', 'creditParty'])
            ->where('business_id', $businessId)
            ->latest()
            ->take(5)
            ->get();

        // Get top parties by transaction volume
        $topParties = PartyLedger::with('party')
            ->where('business_id', $businessId)
            ->selectRaw('party_id, SUM(debit_amount + credit_amount) as total_volume')
            ->groupBy('party_id')
            ->orderByDesc('total_volume')
            ->take(5)
            ->get();

        return view('parties.dashboard', compact(
            'accountPayable',
            'accountReceivable',
            'totalBalance',
            'recentTransfers',
            'topParties',
            'totalParties'
        ));
    }

    public function ledgerReport(Request $request)
    {
        $businessId = session('active_business');
        $business = \App\Models\Business::find($businessId);
        
        // Always fetch all parties for the dropdown
        $parties = Party::where('business_id', $businessId)
            ->where('status', 1) // Only active parties
            ->orderBy('name')
            ->get();
        
        $selectedParty = null;
        $ledgerEntries = collect();
        $openingBalance = 0;
        $totals = [
            'debit' => 0,
            'credit' => 0,
            'balance' => 0
        ];
    
        if ($request->filled('party_id')) {
            $selectedParty = Party::find($request->party_id);
            
            $query = PartyLedger::with(['party', 'user', 'partyTransfer.debitParty', 'partyTransfer.creditParty'])
                ->where('party_id', $request->party_id)
                ->where('business_id', $businessId);
    
            // Apply date filters
            if ($request->filled('from_date')) {
                $query->where('date_added', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->where('date_added', '<=', $request->to_date);
            }
    
            // Get opening balance before from_date
            $openingQuery = PartyLedger::where('party_id', $request->party_id)
                ->where('business_id', $businessId);
    
            if ($request->filled('from_date')) {
                $openingQuery->where('date_added', '<', $request->from_date);
            }
    
            $openingBalance = $openingQuery->get()
                ->reduce(function ($carry, $item) {
                    return $carry + $item->credit_amount - $item->debit_amount;
                }, 0);
    
            // Get filtered ledger entries
            $ledgerEntries = $query->orderBy('date_added')->orderBy('id')->get();
    
            // Calculate running balance
            $runningBalance = $openingBalance;
            $ledgerEntries->transform(function ($item) use (&$runningBalance) {
                $runningBalance += $item->credit_amount - $item->debit_amount;
                $item->running_balance = $runningBalance;
                return $item;
            });
    
            // Totals
            $totals = [
                'debit' => $ledgerEntries->sum('debit_amount'),
                'credit' => $ledgerEntries->sum('credit_amount'),
                'balance' => $runningBalance
            ];
        }
    
        return view('parties.ledger-report', compact(
            'business',
            'parties',
            'selectedParty',
            'ledgerEntries',
            'openingBalance',
            'totals'
        ));
    }
    
    public function balancesReport(Request $request)
    {
        $businessId = session('active_business');
        $business = \App\Models\Business::find($businessId);
        
        // Get all parties with their ledger entries
        $parties = Party::where('business_id', $businessId)
            ->withSum(['ledgerEntries as balance' => function($query) use ($request) {
                if ($request->filled('date')) {
                    $query->where('date_added', '<=', $request->date);
                }
            }], DB::raw('credit_amount - debit_amount'))
            ->having('balance', '!=', 0) // Only show parties with non-zero balance
            ->get();

        // Separate parties into debit and credit
        $debitParties = $parties->filter(function($party) {
            return $party->balance < 0;
        })->sortByDesc(function($party) {
            return abs($party->balance);
        })->values();

        $creditParties = $parties->filter(function($party) {
            return $party->balance > 0;
        })->sortByDesc('balance')
        ->values();

        return view('parties.balances-report', compact(
            'business',
            'debitParties',
            'creditParties'
        ));
    }

    /**
     * Get party balance via AJAX
     */
    public function getBalance(Party $party, Request $request)
    {
        // Check if party belongs to current business
        if ($party->business_id != session('active_business')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the date parameter if provided
        $asOfDate = $request->get('date');

        $balance = $party->getBalance($asOfDate);
        $formattedBalance = $party->getFormattedBalance($asOfDate);
        $status = $party->getBalanceStatus($asOfDate);

        \Log::info('Party balance request', [
            'party_id' => $party->id,
            'party_name' => $party->name,
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