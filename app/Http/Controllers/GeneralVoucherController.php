<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Party;
use App\Models\GeneralVoucher;
use App\Models\BankLedger;
use App\Models\PartyLedger;
use App\Models\JournalEntry;
use App\Models\GeneralVoucherAttachment;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneralVoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = GeneralVoucher::with(['bank', 'party', 'user'])
            ->where('business_id', session('active_business'));

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('bank', function ($q) use ($searchTerm) {
                    $q->where('account_name', 'like', "%{$searchTerm}%");
                })->orWhereHas('party', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })->orWhere('details', 'like', "%{$searchTerm}%");
            });
        }

        $vouchers = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('general_vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $banks = Bank::where('business_id', session('active_business'))
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->whereNotNull('chart_of_account_id')
            ->orderBy('account_name')
            ->get();

        $parties = Party::where('business_id', session('active_business'))
            ->where('status', 1) // Only active parties
            ->orderBy('name')
            ->get();
            
        return view('general_vouchers.create', compact('banks', 'parties'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'entry_date' => 'required|date',
                'bank_id' => 'required|exists:banks,id',
                'party_id' => 'required|exists:parties,id',
                'entry_type' => 'required|in:debit,credit',
                'amount' => 'required|numeric|min:0.01',
                
                'details' => 'nullable|string',
                'attachment_titles.*' => 'nullable|string|max:255',
                'attachment_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);

            $validated['business_id'] = session('active_business');
            
            if (!$validated['business_id']) {
                throw new \Exception('No active business selected.');
            }

            $validated['user_id'] = auth()->id();

            // Server-side balance validation
            $bank = Bank::findOrFail($validated['bank_id']);
            $party = Party::findOrFail($validated['party_id']);
            
            // Get current balances
            $bankBalance = BankLedger::where('bank_id', $validated['bank_id'])
                ->where('business_id', session('active_business'))
                ->sum(DB::raw('deposit_amount - withdrawal_amount'));
                
            // Calculate party balance using the same formula as Party model: credit - debit
            // Positive balance = we owe them, Negative balance = they owe us
            $partyBalance = PartyLedger::where('party_id', $validated['party_id'])
                ->where('business_id', session('active_business'))
                ->sum(DB::raw('credit_amount - debit_amount'));
            
            // Validate sufficient balance
            if ($validated['entry_type'] === 'debit' && $validated['amount'] > $bankBalance) {
                throw new \Exception("Insufficient bank balance. Available: " . number_format($bankBalance, 2));
            }
            
            // For Credit (from party to bank): Party is paying us
            // This is always valid - we can receive payment regardless of balance
            // No validation needed for credit entries
            
            // Note: Credit entry reduces what we owe them (if balance is positive)
            // or increases what they owe us (if balance is negative)
            // Both scenarios are valid accounting transactions

            DB::beginTransaction();
            try {
                // Create voucher record
                $voucher = GeneralVoucher::create($validated);

                // Handle file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('general-vouchers/' . $voucher->id, 'public');
                            
                            GeneralVoucherAttachment::create([
                                'general_voucher_id' => $voucher->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Create party ledger entry
                PartyLedger::create([
                    'business_id' => session('active_business'),
                    'party_id' => $validated['party_id'],
                    'voucher_id' => $voucher->id,
                    'voucher_type' => 'General Voucher',
                    'date_added' => $validated['entry_date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => $validated['entry_type'] === 'debit' ? $validated['amount'] : 0,
                    'credit_amount' => $validated['entry_type'] === 'credit' ? $validated['amount'] : 0,
                ]);

                // Create bank ledger entry
                BankLedger::create([
                    'business_id' => session('active_business'),
                    'bank_id' => $validated['bank_id'],
                    'voucher_id' => $voucher->id,
                    'voucher_type' => 'General Voucher',
                    'date' => $validated['entry_date'],
                    'user_id' => auth()->id(),
                    'withdrawal_amount' => $validated['entry_type'] === 'debit' ? $validated['amount'] : 0,
                    'deposit_amount' => $validated['entry_type'] === 'credit' ? $validated['amount'] : 0,
                ]);

                // Bank and party accounts already fetched above for validation
                // Check if bank has chart of account ID
                if (!$bank->chart_of_account_id) {
                    throw new \Exception('Bank account does not have a chart of account assigned.');
                }

                // Get party's chart of account
                // If party doesn't have a chart of account, create one
                if (!$party->chart_of_account_id) {
                    $partyAccount = ChartOfAccount::createPartyAccount($party->name, session('active_business'));
                    $party->update(['chart_of_account_id' => $partyAccount->id]);
                    $party->refresh();
                }

                $partyAccountId = $party->chart_of_account_id;

                if (!$partyAccountId) {
                    throw new \Exception('Party chart of account not found. Please ensure the party has a chart of account assigned.');
                }

                if ($validated['entry_type'] === 'debit') {
                    // Debit: Transfer from Bank → Party (Bank decreases, Party increases)
                    // Debit party account (Party receives money)
                    JournalEntry::create([
                        'business_id' => session('active_business'),
                        'account_head' => $partyAccountId,
                        'voucher_id' => $voucher->id,
                        'voucher_type' => 'General Voucher',
                        'date_added' => $validated['entry_date'],
                        'user_id' => auth()->id(),
                        'debit_amount' => $validated['amount'],
                        'credit_amount' => 0,
                    ]);

                    // Credit bank account (Bank gives money)
                    JournalEntry::create([
                        'business_id' => session('active_business'),
                        'account_head' => $bank->chart_of_account_id,
                        'voucher_id' => $voucher->id,
                        'voucher_type' => 'General Voucher',
                        'date_added' => $validated['entry_date'],
                        'user_id' => auth()->id(),
                        'debit_amount' => 0,
                        'credit_amount' => $validated['amount'],
                    ]);
                } else {
                    // Credit: Transfer from Party → Bank (Party decreases, Bank increases)
                    // Credit party account (Party gives money)
                    JournalEntry::create([
                        'business_id' => session('active_business'),
                        'account_head' => $partyAccountId,
                        'voucher_id' => $voucher->id,
                        'voucher_type' => 'General Voucher',
                        'date_added' => $validated['entry_date'],
                        'user_id' => auth()->id(),
                        'debit_amount' => 0,
                        'credit_amount' => $validated['amount'],
                    ]);

                    // Debit bank account (Bank receives money)
                    JournalEntry::create([
                        'business_id' => session('active_business'),
                        'account_head' => $bank->chart_of_account_id,
                        'voucher_id' => $voucher->id,
                        'voucher_type' => 'General Voucher',
                        'date_added' => $validated['entry_date'],
                        'user_id' => auth()->id(),
                        'debit_amount' => $validated['amount'],
                        'credit_amount' => 0,
                    ]);
                }

                DB::commit();

                return redirect()
                    ->route('general-vouchers.index')
                    ->with('success', 'General voucher created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in general voucher transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('General voucher creation failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating general voucher: ' . $e->getMessage());
        }
    }

    public function show(GeneralVoucher $generalVoucher)
    {
        $generalVoucher->load(['bank', 'party', 'attachments', 'user']);
        return view('general_vouchers.show', compact('generalVoucher'));
    }

    public function edit(GeneralVoucher $generalVoucher)
    {
        $banks = Bank::where('business_id', session('active_business'))
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->whereNotNull('chart_of_account_id')
            ->orderBy('account_name')
            ->get();

        $parties = Party::where('business_id', session('active_business'))
            ->where('status', 1) // Only active parties
            ->orderBy('name')
            ->get();
            
        return view('general_vouchers.edit', compact('generalVoucher', 'banks', 'parties'));
    }

    public function update(Request $request, GeneralVoucher $generalVoucher)
    {
        try {
            $validated = $request->validate([
                'entry_date' => 'required|date',
                'bank_id' => 'required|exists:banks,id',
                'party_id' => 'required|exists:parties,id',
                'entry_type' => 'required|in:debit,credit',
                'amount' => 'required|numeric|min:0.01',
                
                'details' => 'nullable|string',
                'attachment_titles.*' => 'nullable|string|max:255',
                'attachment_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);

            // Server-side balance validation for update
            $bank = Bank::findOrFail($validated['bank_id']);
            $party = Party::findOrFail($validated['party_id']);
            
            // Get current balances (excluding this transaction)
            $bankBalance = BankLedger::where('bank_id', $validated['bank_id'])
                ->where('business_id', session('active_business'))
                ->where(function($query) use ($generalVoucher) {
                    $query->where('voucher_id', '!=', $generalVoucher->id)
                          ->orWhere('voucher_type', '!=', 'General Voucher');
                })
                ->sum(DB::raw('deposit_amount - withdrawal_amount'));
                
            // Calculate party balance using the same formula as Party model: credit - debit
            // Positive balance = we owe them, Negative balance = they owe us
            $partyBalance = PartyLedger::where('party_id', $validated['party_id'])
                ->where('business_id', session('active_business'))
                ->where(function($query) use ($generalVoucher) {
                    $query->where('voucher_id', '!=', $generalVoucher->id)
                          ->orWhere('voucher_type', '!=', 'General Voucher');
                })
                ->sum(DB::raw('credit_amount - debit_amount'));
            
            // Validate sufficient balance
            if ($validated['entry_type'] === 'debit' && $validated['amount'] > $bankBalance) {
                throw new \Exception("Insufficient bank balance. Available: " . number_format($bankBalance, 2));
            }
            
            // For Credit (from party to bank): Party is paying us
            // This is always valid - we can receive payment regardless of balance
            // No validation needed for credit entries

            DB::beginTransaction();
            try {
                // Update voucher record
                $generalVoucher->update($validated);

                // Handle new file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('general-vouchers/' . $generalVoucher->id, 'public');
                            
                            GeneralVoucherAttachment::create([
                                'general_voucher_id' => $generalVoucher->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Update party ledger entry
                $partyLedger = PartyLedger::where('voucher_id', $generalVoucher->id)
                    ->where('voucher_type', 'General Voucher')
                    ->first();

                if ($partyLedger) {
                    $partyLedger->update([
                        'party_id' => $validated['party_id'],
                        'date_added' => $validated['entry_date'],
                        'debit_amount' => $validated['entry_type'] === 'debit' ? $validated['amount'] : 0,
                        'credit_amount' => $validated['entry_type'] === 'credit' ? $validated['amount'] : 0,
                    ]);
                }

                // Update bank ledger entry
                $bankLedger = BankLedger::where('voucher_id', $generalVoucher->id)
                    ->where('voucher_type', 'General Voucher')
                    ->first();

                if ($bankLedger) {
                    $bankLedger->update([
                        'bank_id' => $validated['bank_id'],
                        'date' => $validated['entry_date'],
                        'withdrawal_amount' => $validated['entry_type'] === 'debit' ? $validated['amount'] : 0,
                        'deposit_amount' => $validated['entry_type'] === 'credit' ? $validated['amount'] : 0,
                    ]);
                }

                // Bank and party accounts already fetched above for validation
                // Check if bank has chart of account ID
                if (!$bank->chart_of_account_id) {
                    throw new \Exception('Bank account does not have a chart of account assigned.');
                }

                // Get party's chart of account
                // If party doesn't have a chart of account, create one
                if (!$party->chart_of_account_id) {
                    $partyAccount = ChartOfAccount::createPartyAccount($party->name, session('active_business'));
                    $party->update(['chart_of_account_id' => $partyAccount->id]);
                    $party->refresh();
                }

                $partyAccountId = $party->chart_of_account_id;

                if (!$partyAccountId) {
                    throw new \Exception('Party chart of account not found. Please ensure the party has a chart of account assigned.');
                }

                // Update journal entries
                $journalEntries = JournalEntry::where('voucher_id', $generalVoucher->id)
                    ->where('voucher_type', 'General Voucher')
                    ->get();

                // Find party and bank entries by account head
                $partyEntry = $journalEntries->where('account_head', $partyAccountId)->first();
                $bankEntry = $journalEntries->where('account_head', $bank->chart_of_account_id)->first();

                if ($validated['entry_type'] === 'debit') {
                    // Debit: Transfer from Bank → Party (Bank decreases, Party increases)
                    // Party receives money (debit party)
                    if ($partyEntry) {
                        $partyEntry->update([
                            'date_added' => $validated['entry_date'],
                            'debit_amount' => $validated['amount'],
                            'credit_amount' => 0,
                        ]);
                    }
                    
                    // Bank gives money (credit bank)
                    if ($bankEntry) {
                        $bankEntry->update([
                            'date_added' => $validated['entry_date'],
                            'debit_amount' => 0,
                            'credit_amount' => $validated['amount'],
                        ]);
                    }
                } else {
                    // Credit: Transfer from Party → Bank (Party decreases, Bank increases)
                    // Party gives money (credit party)
                    if ($partyEntry) {
                        $partyEntry->update([
                            'date_added' => $validated['entry_date'],
                            'debit_amount' => 0,
                            'credit_amount' => $validated['amount'],
                        ]);
                    }
                    
                    // Bank receives money (debit bank)
                    if ($bankEntry) {
                        $bankEntry->update([
                            'date_added' => $validated['entry_date'],
                            'debit_amount' => $validated['amount'],
                            'credit_amount' => 0,
                        ]);
                    }
                }

                DB::commit();

                return redirect()
                    ->route('general-vouchers.index')
                    ->with('success', 'General voucher updated successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in general voucher update transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('General voucher update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating general voucher: ' . $e->getMessage());
        }
    }

    public function destroy(GeneralVoucher $generalVoucher)
    {
        try {
            // Check if voucher belongs to current business
            if ($generalVoucher->business_id != session('active_business')) {
                return redirect()
                    ->route('general-vouchers.index')
                    ->with('error', 'You do not have permission to delete this voucher.');
            }

            DB::beginTransaction();
            try {
                // Delete all attachments and their files
                foreach ($generalVoucher->attachments as $attachment) {
                    try {
                        Storage::disk('public')->delete($attachment->file_path);
                    } catch (\Exception $fileError) {
                        Log::warning('Failed to delete attachment file: ' . $attachment->file_path . ' - ' . $fileError->getMessage());
                        // Continue even if file deletion fails
                    }
                    $attachment->delete();
                }

                // Delete party ledger entry (this will automatically adjust balances)
                $deletedPartyLedgerEntries = PartyLedger::where('voucher_id', $generalVoucher->id)
                    ->where('voucher_type', 'General Voucher')
                    ->delete();

                if ($deletedPartyLedgerEntries === 0) {
                    Log::warning('No party ledger entries found for general voucher ID: ' . $generalVoucher->id);
                }

                // Delete bank ledger entry (this will automatically adjust balances)
                $deletedBankLedgerEntries = BankLedger::where('voucher_id', $generalVoucher->id)
                    ->where('voucher_type', 'General Voucher')
                    ->delete();

                if ($deletedBankLedgerEntries === 0) {
                    Log::warning('No bank ledger entries found for general voucher ID: ' . $generalVoucher->id);
                }

                // Delete journal entries
                $deletedJournalEntries = JournalEntry::where('voucher_id', $generalVoucher->id)
                    ->where('voucher_type', 'General Voucher')
                    ->delete();

                if ($deletedJournalEntries === 0) {
                    Log::warning('No journal entries found for general voucher ID: ' . $generalVoucher->id);
                }

                // Delete the voucher record
                $generalVoucher->delete();

                DB::commit();

                return redirect()
                    ->route('general-vouchers.index')
                    ->with('success', 'General voucher has been deleted successfully.');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error('Database error in general voucher deletion: ' . $e->getMessage());
                
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return redirect()
                        ->route('general-vouchers.index')
                        ->with('error', 'Cannot delete this voucher because it is referenced by other records. Please contact support.');
                }
                
                return redirect()
                    ->route('general-vouchers.index')
                    ->with('error', 'A database error occurred while deleting the voucher. Please try again or contact support if the problem persists.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in general voucher deletion transaction: ' . $e->getMessage());
                
                // Handle specific error cases
                if (str_contains($e->getMessage(), 'ledger') || str_contains($e->getMessage(), 'PartyLedger') || str_contains($e->getMessage(), 'BankLedger')) {
                    return redirect()
                        ->route('general-vouchers.index')
                        ->with('error', 'Failed to delete ledger entries. The voucher may have already been deleted or modified. Please refresh the page.');
                }
                
                if (str_contains($e->getMessage(), 'journal') || str_contains($e->getMessage(), 'JournalEntry')) {
                    return redirect()
                        ->route('general-vouchers.index')
                        ->with('error', 'Failed to delete journal entries. The voucher may have already been deleted or modified. Please refresh the page.');
                }
                
                if (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'storage')) {
                    return redirect()
                        ->route('general-vouchers.index')
                        ->with('error', 'The voucher was deleted, but some attachment files could not be removed. This does not affect the ledger balances.');
                }
                
                return redirect()
                    ->route('general-vouchers.index')
                    ->with('error', 'Unable to delete the general voucher. Please try again or contact support if the problem continues.');
            }

        } catch (\Exception $e) {
            Log::error('General voucher deletion failed: ' . $e->getMessage());
            
            $errorMessage = 'Unable to delete general voucher. ';
            
            if (str_contains($e->getMessage(), 'permission') || str_contains($e->getMessage(), 'Unauthorized')) {
                $errorMessage = 'You do not have permission to delete this voucher.';
            } elseif (str_contains($e->getMessage(), 'constraint') || str_contains($e->getMessage(), 'foreign')) {
                $errorMessage = 'This voucher cannot be deleted because it is being used by other records in the system.';
            } else {
                $errorMessage .= 'Please try again or contact support if the problem persists.';
            }
            
            return redirect()
                ->route('general-vouchers.index')
                ->with('error', $errorMessage);
        }
    }

    public function deleteAttachment(GeneralVoucherAttachment $attachment)
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
}