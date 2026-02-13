<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankTransfer;
use App\Models\BankLedger;
use App\Models\JournalEntry;
use App\Models\BankTransferAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BankTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = BankTransfer::with(['fromAccount', 'toAccount', 'user'])
            ->where('business_id', session('active_business'));

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('fromAccount', function ($q) use ($searchTerm) {
                    $q->where('account_name', 'like', "%{$searchTerm}%");
                })->orWhereHas('toAccount', function ($q) use ($searchTerm) {
                    $q->where('account_name', 'like', "%{$searchTerm}%");
                })->orWhere('details', 'like', "%{$searchTerm}%");
            });
        }

        $transfers = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('bank_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $banks = Bank::where('business_id', session('active_business'))
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();
            
        return view('bank_transfers.create', compact('banks'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'transfer_date' => 'required|date',
                'from_account_id' => 'required|exists:banks,id',
                'to_account_id' => 'required|exists:banks,id|different:from_account_id',
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

            // Check if From Account has sufficient balance
            $fromAccountBalance = $this->getBankBalance($validated['from_account_id'], $validated['transfer_date']);
            if ($fromAccountBalance < $validated['amount']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['from_account_id' => 'Insufficient balance in From Account.']);
            }

            DB::beginTransaction();
            try {
                // Create transfer record
                $transfer = BankTransfer::create($validated);

                // Handle file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('bank-transfers/' . $transfer->id, 'public');
                            
                            BankTransferAttachment::create([
                                'bank_transfer_id' => $transfer->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Create bank ledger entries
                // Withdrawal entry for source account
                BankLedger::create([
                    'business_id' => session('active_business'),
                    'bank_id' => $validated['from_account_id'],
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Bank Transfer',
                    'date' => $validated['transfer_date'],
                    'user_id' => auth()->id(),
                    'withdrawal_amount' => $validated['amount'],
                    'deposit_amount' => 0,
                ]);

                // Deposit entry for destination account
                BankLedger::create([
                    'business_id' => session('active_business'),
                    'bank_id' => $validated['to_account_id'],
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Bank Transfer',
                    'date' => $validated['transfer_date'],
                    'user_id' => auth()->id(),
                    'withdrawal_amount' => 0,
                    'deposit_amount' => $validated['amount'],
                ]);

                // Create journal entries
                $fromAccount = Bank::findOrFail($validated['from_account_id']);
                $toAccount = Bank::findOrFail($validated['to_account_id']);

                // Debit entry (To Account)
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $toAccount->chart_of_account_id,
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Bank Transfer',
                    'date_added' => $validated['transfer_date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => $validated['amount'],
                    'credit_amount' => 0,
                ]);

                // Credit entry (From Account)
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $fromAccount->chart_of_account_id,
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Bank Transfer',
                    'date_added' => $validated['transfer_date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => $validated['amount'],
                ]);

                DB::commit();

                return redirect()
                    ->route('bank-transfers.index')
                    ->with('success', 'Bank transfer created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in bank transfer transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Bank transfer creation failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating bank transfer: ' . $e->getMessage());
        }
    }

    public function show(BankTransfer $bankTransfer)
    {
        $bankTransfer->load(['fromAccount', 'toAccount', 'attachments', 'user']);
        return view('bank_transfers.show', compact('bankTransfer'));
    }

    public function edit(BankTransfer $bankTransfer)
    {
        $banks = Bank::where('business_id', session('active_business'))
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();
            
        return view('bank_transfers.edit', compact('bankTransfer', 'banks'));
    }

    public function update(Request $request, BankTransfer $bankTransfer)
    {
        try {
            $validated = $request->validate([
                'transfer_date' => 'required|date',
                'from_account_id' => 'required|exists:banks,id',
                'to_account_id' => 'required|exists:banks,id|different:from_account_id',
                'amount' => 'required|numeric|min:0.01',
                'details' => 'nullable|string',
                'attachment_titles.*' => 'nullable|string|max:255',
                'attachment_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);

            // Check if From Account has sufficient balance (excluding current transfer)
            $fromAccountBalance = $this->getBankBalanceExcludingTransfer($validated['from_account_id'], $validated['transfer_date'], $bankTransfer->id);
            if ($fromAccountBalance < $validated['amount']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['from_account_id' => 'Insufficient balance in From Account.']);
            }

            DB::beginTransaction();
            try {
                // Update transfer record
                $bankTransfer->update($validated);

                // Handle new file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('bank-transfers/' . $bankTransfer->id, 'public');
                            
                            BankTransferAttachment::create([
                                'bank_transfer_id' => $bankTransfer->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Update bank ledger entries
                $ledgerEntries = BankLedger::where('voucher_id', $bankTransfer->id)
                    ->where('voucher_type', 'Bank Transfer')
                    ->get();

                foreach ($ledgerEntries as $entry) {
                    if ($entry->withdrawal_amount > 0) {
                        // This is the source account's entry
                        $entry->update([
                            'business_id' => session('active_business'),
                            'bank_id' => $validated['from_account_id'],
                            'date' => $validated['transfer_date'],
                            'withdrawal_amount' => $validated['amount'],
                        ]);
                    } else {
                        // This is the destination account's entry
                        $entry->update([
                            'business_id' => session('active_business'),
                            'bank_id' => $validated['to_account_id'],
                            'date' => $validated['transfer_date'],
                            'deposit_amount' => $validated['amount'],
                        ]);
                    }
                }

                // Update journal entries
                $journalEntries = JournalEntry::where('voucher_id', $bankTransfer->id)
                    ->where('voucher_type', 'Bank Transfer')
                    ->get();

                $fromAccount = Bank::findOrFail($validated['from_account_id']);
                $toAccount = Bank::findOrFail($validated['to_account_id']);

                foreach ($journalEntries as $entry) {
                    if ($entry->debit_amount > 0) {
                        // This is the debit entry (To Account)
                        $entry->update([
                            'account_head' => $toAccount->chart_of_account_id,
                            'date_added' => $validated['transfer_date'],
                            'debit_amount' => $validated['amount'],
                            'credit_amount' => 0,
                        ]);
                    } else {
                        // This is the credit entry (From Account)
                        $entry->update([
                            'account_head' => $fromAccount->chart_of_account_id,
                            'date_added' => $validated['transfer_date'],
                            'debit_amount' => 0,
                            'credit_amount' => $validated['amount'],
                        ]);
                    }
                }

                DB::commit();

                return redirect()
                    ->route('bank-transfers.index')
                    ->with('success', 'Bank transfer updated successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in bank transfer update transaction: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Bank transfer update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating bank transfer: ' . $e->getMessage());
        }
    }

    public function destroy(BankTransfer $bankTransfer)
    {
        try {
            // Check if transfer belongs to current business
            if ($bankTransfer->business_id != session('active_business')) {
                return redirect()
                    ->route('bank-transfers.index')
                    ->with('error', 'You do not have permission to delete this transfer.');
            }

            DB::beginTransaction();
            try {
                // Delete all attachments and their files
                foreach ($bankTransfer->attachments as $attachment) {
                    try {
                        Storage::disk('public')->delete($attachment->file_path);
                    } catch (\Exception $fileError) {
                        Log::warning('Failed to delete attachment file: ' . $attachment->file_path . ' - ' . $fileError->getMessage());
                        // Continue even if file deletion fails
                    }
                    $attachment->delete();
                }

                // Delete bank ledger entries (this will automatically adjust balances)
                $deletedLedgerEntries = BankLedger::where('voucher_id', $bankTransfer->id)
                    ->where('voucher_type', 'Bank Transfer')
                    ->delete();

                if ($deletedLedgerEntries === 0) {
                    Log::warning('No ledger entries found for bank transfer ID: ' . $bankTransfer->id);
                }

                // Delete journal entries
                $deletedJournalEntries = JournalEntry::where('voucher_id', $bankTransfer->id)
                    ->where('voucher_type', 'Bank Transfer')
                    ->delete();

                if ($deletedJournalEntries === 0) {
                    Log::warning('No journal entries found for bank transfer ID: ' . $bankTransfer->id);
                }

                // Delete the transfer record
                $bankTransfer->delete();

                DB::commit();

                return redirect()
                    ->route('bank-transfers.index')
                    ->with('success', 'Bank transfer has been deleted successfully.');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error('Database error in bank transfer deletion: ' . $e->getMessage());
                
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return redirect()
                        ->route('bank-transfers.index')
                        ->with('error', 'Cannot delete this transfer because it is referenced by other records. Please contact support.');
                }
                
                return redirect()
                    ->route('bank-transfers.index')
                    ->with('error', 'A database error occurred while deleting the transfer. Please try again or contact support if the problem persists.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in bank transfer deletion transaction: ' . $e->getMessage());
                
                // Handle specific error cases
                if (str_contains($e->getMessage(), 'ledger') || str_contains($e->getMessage(), 'BankLedger')) {
                    return redirect()
                        ->route('bank-transfers.index')
                        ->with('error', 'Failed to delete bank ledger entries. The transfer may have already been deleted or modified. Please refresh the page.');
                }
                
                if (str_contains($e->getMessage(), 'journal') || str_contains($e->getMessage(), 'JournalEntry')) {
                    return redirect()
                        ->route('bank-transfers.index')
                        ->with('error', 'Failed to delete journal entries. The transfer may have already been deleted or modified. Please refresh the page.');
                }
                
                if (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'storage')) {
                    return redirect()
                        ->route('bank-transfers.index')
                        ->with('error', 'The transfer was deleted, but some attachment files could not be removed. This does not affect the ledger balances.');
                }
                
                return redirect()
                    ->route('bank-transfers.index')
                    ->with('error', 'Unable to delete the bank transfer. Please try again or contact support if the problem continues.');
            }

        } catch (\Exception $e) {
            Log::error('Bank transfer deletion failed: ' . $e->getMessage());
            
            $errorMessage = 'Unable to delete bank transfer. ';
            
            if (str_contains($e->getMessage(), 'permission') || str_contains($e->getMessage(), 'Unauthorized')) {
                $errorMessage = 'You do not have permission to delete this transfer.';
            } elseif (str_contains($e->getMessage(), 'constraint') || str_contains($e->getMessage(), 'foreign')) {
                $errorMessage = 'This transfer cannot be deleted because it is being used by other records in the system.';
            } else {
                $errorMessage .= 'Please try again or contact support if the problem persists.';
            }
            
            return redirect()
                ->route('bank-transfers.index')
                ->with('error', $errorMessage);
        }
    }

    public function deleteAttachment(BankTransferAttachment $attachment)
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

    /**
     * Get bank balance up to a specific date
     */
    private function getBankBalance($bankId, $date)
    {
        $businessId = session('active_business');
        
        $deposits = BankLedger::where('business_id', $businessId)
            ->where('bank_id', $bankId)
            ->where('date', '<=', $date)
            ->sum('deposit_amount');
            
        $withdrawals = BankLedger::where('business_id', $businessId)
            ->where('bank_id', $bankId)
            ->where('date', '<=', $date)
            ->sum('withdrawal_amount');
            
        return $deposits - $withdrawals;
    }

    /**
     * Get bank balance up to a specific date, excluding a specific transfer
     */
    private function getBankBalanceExcludingTransfer($bankId, $date, $excludeTransferId)
    {
        $businessId = session('active_business');
        
        $deposits = BankLedger::where('business_id', $businessId)
            ->where('bank_id', $bankId)
            ->where('date', '<=', $date)
            ->where(function($query) use ($excludeTransferId) {
                $query->where('voucher_id', '!=', $excludeTransferId)
                      ->orWhere('voucher_type', '!=', 'Bank Transfer');
            })
            ->sum('deposit_amount');
            
        $withdrawals = BankLedger::where('business_id', $businessId)
            ->where('bank_id', $bankId)
            ->where('date', '<=', $date)
            ->where(function($query) use ($excludeTransferId) {
                $query->where('voucher_id', '!=', $excludeTransferId)
                      ->orWhere('voucher_type', '!=', 'Bank Transfer');
            })
            ->sum('withdrawal_amount');
            
        return $deposits - $withdrawals;
    }

    /**
     * Get available balance for editing a transfer (current balance + transfer amount)
     */
    public function getAvailableBalanceForEdit(Bank $bank, BankTransfer $bankTransfer)
    {
        // Check if bank belongs to current business
        if ($bank->business_id != session('active_business')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if transfer belongs to current business
        if ($bankTransfer->business_id != session('active_business')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get current balance
        $currentBalance = $bank->getBalance();
        
        // If this bank is the source account of the transfer, add back the transfer amount
        if ($bank->id == $bankTransfer->from_account_id) {
            $availableBalance = $currentBalance + $bankTransfer->amount;
        } else {
            $availableBalance = $currentBalance;
        }

        return response()->json([
            'balance' => $availableBalance,
            'formatted_balance' => number_format($availableBalance, 2),
            'is_source_account' => $bank->id == $bankTransfer->from_account_id,
            'transfer_amount' => $bankTransfer->amount
        ]);
    }
}