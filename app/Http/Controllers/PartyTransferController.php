<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\PartyTransfer;
use App\Models\PartyLedger;
use App\Models\PartyTransferAttachment;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PartyTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = PartyTransfer::with(['user', 'debitParty', 'creditParty'])
            ->where('business_id', session('active_business'));

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('debitParty', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('creditParty', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })->orWhere('details', 'like', "%{$searchTerm}%");
            });
        }

        $transfers = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('party_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $parties = Party::where('business_id', session('active_business'))
            ->where('status', 1)
            ->orderBy('name')
            ->get();
            
        return view('party_transfers.create', compact('parties'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'debit_party_id' => 'required|exists:parties,id',
                'credit_party_id' => 'required|exists:parties,id|different:debit_party_id',
                'transfer_amount' => 'required|numeric|min:0.01',
                'details' => 'nullable|string',
                'attachment_titles.*' => 'nullable|string|max:255',
                'attachment_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ], [
                'date.required' => 'Please select a transfer date.',
                'date.date' => 'The transfer date must be a valid date.',
                'debit_party_id.required' => 'Please select a debit party (the party that will pay).',
                'debit_party_id.exists' => 'The selected debit party does not exist. Please select a valid party.',
                'credit_party_id.required' => 'Please select a credit party (the party that will receive).',
                'credit_party_id.exists' => 'The selected credit party does not exist. Please select a valid party.',
                'credit_party_id.different' => 'Debit party and credit party cannot be the same. Please select different parties.',
                'transfer_amount.required' => 'Please enter the transfer amount.',
                'transfer_amount.numeric' => 'Transfer amount must be a number.',
                'transfer_amount.min' => 'Transfer amount must be greater than 0.',
                'attachment_files.*.file' => 'One or more files failed to upload. Please try again.',
                'attachment_files.*.max' => 'File size cannot exceed 10MB. Please upload smaller files.',
                'attachment_files.*.mimes' => 'Only PDF, Word, and image files (PDF, DOC, DOCX, JPG, JPEG, PNG) are allowed.',
            ]);

            $validated['business_id'] = session('active_business');
            $validated['user_id'] = auth()->id();
            
            if (!$validated['business_id']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'No active business selected. Please select a business from the menu.');
            }

            DB::beginTransaction();
            try {
                // Create transfer record
                $transfer = PartyTransfer::create($validated);

                // Handle file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('party-transfers/' . $transfer->id, 'public');
                            
                            PartyTransferAttachment::create([
                                'party_transfer_id' => $transfer->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Create party ledger entries
                // Debit entry for payer
                PartyLedger::create([
                    'business_id' => session('active_business'),
                    'party_id' => $validated['debit_party_id'],
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Party Transfer',
                    'date_added' => $validated['date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => $validated['transfer_amount'],
                    'credit_amount' => 0,
                ]);

                // Credit entry for receiver
                PartyLedger::create([
                    'business_id' => session('active_business'),
                    'party_id' => $validated['credit_party_id'],
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Party Transfer',
                    'date_added' => $validated['date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => $validated['transfer_amount'],
                ]);

                // Get parties for journal entries
                $debitParty = Party::findOrFail($validated['debit_party_id']);
                $creditParty = Party::findOrFail($validated['credit_party_id']);

                // Ensure debit party has chart of account
                if (!$debitParty->chart_of_account_id) {
                    $debitPartyAccount = ChartOfAccount::createPartyAccount($debitParty->name, session('active_business'));
                    $debitParty->update(['chart_of_account_id' => $debitPartyAccount->id]);
                    $debitParty->refresh();
                }

                // Ensure credit party has chart of account
                if (!$creditParty->chart_of_account_id) {
                    $creditPartyAccount = ChartOfAccount::createPartyAccount($creditParty->name, session('active_business'));
                    $creditParty->update(['chart_of_account_id' => $creditPartyAccount->id]);
                    $creditParty->refresh();
                }

                if (!$debitParty->chart_of_account_id || !$creditParty->chart_of_account_id) {
                    throw new \Exception('Party chart of account not found. Please ensure both parties have chart of accounts assigned.');
                }

                // Create journal entries
                // Debit entry: Debit party's account (they're paying)
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $debitParty->chart_of_account_id,
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Party Transfer',
                    'date_added' => $validated['date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => $validated['transfer_amount'],
                    'credit_amount' => 0,
                    'comments' => 'Party Transfer: From ' . $debitParty->name . ' to ' . $creditParty->name,
                ]);

                // Credit entry: Credit party's account (they're receiving)
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $creditParty->chart_of_account_id,
                    'voucher_id' => $transfer->id,
                    'voucher_type' => 'Party Transfer',
                    'date_added' => $validated['date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => $validated['transfer_amount'],
                    'comments' => 'Party Transfer: From ' . $debitParty->name . ' to ' . $creditParty->name,
                ]);

                DB::commit();

                return redirect()
                    ->route('party-transfers.index')
                    ->with('success', 'Party transfer created successfully');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error('Database error in party transfer creation: ' . $e->getMessage());
                
                // Check for specific database errors
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Unable to create transfer. The selected party may have been deleted or is invalid. Please refresh the page and try again.');
                }
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'A database error occurred while creating the transfer. Please try again or contact support if the problem persists.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in party transfer transaction: ' . $e->getMessage());
                
                // Handle file storage errors
                if (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'storage') || str_contains($e->getMessage(), 'attachment')) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Failed to save attachment files. Please check file permissions or try uploading the files again.');
                }
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Unable to create the party transfer. Please check all fields and try again. If the problem continues, contact support.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Party transfer creation failed: ' . $e->getMessage());
            
            // Provide user-friendly error message
            $errorMessage = 'Unable to create party transfer. ';
            
            if (str_contains($e->getMessage(), 'business')) {
                $errorMessage = 'Please select a business before creating a transfer.';
            } elseif (str_contains($e->getMessage(), 'party')) {
                $errorMessage = 'Please select valid parties for the transfer.';
            } elseif (str_contains($e->getMessage(), 'amount')) {
                $errorMessage = 'Please enter a valid transfer amount greater than zero.';
            } else {
                $errorMessage .= 'Please check all fields and try again.';
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    public function show(PartyTransfer $partyTransfer)
    {
        return view('party_transfers.show', compact('partyTransfer'));
    }

    public function edit(PartyTransfer $partyTransfer)
    {
        $parties = Party::where('business_id', session('active_business'))
            ->where('status', 1)
            ->orderBy('name')
            ->get();
            
        return view('party_transfers.edit', compact('partyTransfer', 'parties'));
    }

    public function update(Request $request, PartyTransfer $partyTransfer)
    {
        try {
            // Check if transfer belongs to current business
            if ($partyTransfer->business_id != session('active_business')) {
                return redirect()
                    ->route('party-transfers.index')
                    ->with('error', 'You do not have permission to edit this transfer.');
            }
            
            $validated = $request->validate([
                'date' => 'required|date',
                'debit_party_id' => 'required|exists:parties,id',
                'credit_party_id' => 'required|exists:parties,id|different:debit_party_id',
                'transfer_amount' => 'required|numeric|min:0.01',
                'details' => 'nullable|string',
                'attachment_titles.*' => 'nullable|string|max:255',
                'attachment_files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ], [
                'date.required' => 'Please select a transfer date.',
                'date.date' => 'The transfer date must be a valid date.',
                'debit_party_id.required' => 'Please select a debit party (the party that will pay).',
                'debit_party_id.exists' => 'The selected debit party does not exist. Please select a valid party.',
                'credit_party_id.required' => 'Please select a credit party (the party that will receive).',
                'credit_party_id.exists' => 'The selected credit party does not exist. Please select a valid party.',
                'credit_party_id.different' => 'Debit party and credit party cannot be the same. Please select different parties.',
                'transfer_amount.required' => 'Please enter the transfer amount.',
                'transfer_amount.numeric' => 'Transfer amount must be a number.',
                'transfer_amount.min' => 'Transfer amount must be greater than 0.',
                'attachment_files.*.file' => 'One or more files failed to upload. Please try again.',
                'attachment_files.*.max' => 'File size cannot exceed 10MB. Please upload smaller files.',
                'attachment_files.*.mimes' => 'Only PDF, Word, and image files (PDF, DOC, DOCX, JPG, JPEG, PNG) are allowed.',
            ]);

            DB::beginTransaction();
            try {
                // Update transfer record
                $partyTransfer->update($validated);

                // Handle new file attachments
                if ($request->hasFile('attachment_files')) {
                    foreach ($request->file('attachment_files') as $index => $file) {
                        if ($file) {
                            $path = $file->store('party-transfers/' . $partyTransfer->id, 'public');
                            
                            PartyTransferAttachment::create([
                                'party_transfer_id' => $partyTransfer->id,
                                'original_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                                
                            ]);
                        }
                    }
                }

                // Update party ledger entries
                $ledgerEntries = PartyLedger::where('voucher_id', $partyTransfer->id)
                    ->where('voucher_type', 'Party Transfer')
                    ->get();

                foreach ($ledgerEntries as $entry) {
                    if ($entry->debit_amount > 0) {
                        // This is the payer's entry
                        $entry->update([
                            'party_id' => $validated['debit_party_id'],
                            'date_added' => $validated['date'],
                            'debit_amount' => $validated['transfer_amount'],
                        ]);
                    } else {
                        // This is the receiver's entry
                        $entry->update([
                            'party_id' => $validated['credit_party_id'],
                            'date_added' => $validated['date'],
                            'credit_amount' => $validated['transfer_amount'],
                        ]);
                    }
                }

                // Delete existing journal entries
                $deletedJournalEntries = JournalEntry::where('voucher_id', $partyTransfer->id)
                    ->where('voucher_type', 'Party Transfer')
                    ->delete();

                // Get parties for journal entries
                $debitParty = Party::findOrFail($validated['debit_party_id']);
                $creditParty = Party::findOrFail($validated['credit_party_id']);

                // Ensure debit party has chart of account
                if (!$debitParty->chart_of_account_id) {
                    $debitPartyAccount = ChartOfAccount::createPartyAccount($debitParty->name, session('active_business'));
                    $debitParty->update(['chart_of_account_id' => $debitPartyAccount->id]);
                    $debitParty->refresh();
                }

                // Ensure credit party has chart of account
                if (!$creditParty->chart_of_account_id) {
                    $creditPartyAccount = ChartOfAccount::createPartyAccount($creditParty->name, session('active_business'));
                    $creditParty->update(['chart_of_account_id' => $creditPartyAccount->id]);
                    $creditParty->refresh();
                }

                if (!$debitParty->chart_of_account_id || !$creditParty->chart_of_account_id) {
                    throw new \Exception('Party chart of account not found. Please ensure both parties have chart of accounts assigned.');
                }

                // Create new journal entries
                // Debit entry: Debit party's account (they're paying)
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $debitParty->chart_of_account_id,
                    'voucher_id' => $partyTransfer->id,
                    'voucher_type' => 'Party Transfer',
                    'date_added' => $validated['date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => $validated['transfer_amount'],
                    'credit_amount' => 0,
                    'comments' => 'Party Transfer: From ' . $debitParty->name . ' to ' . $creditParty->name,
                ]);

                // Credit entry: Credit party's account (they're receiving)
                JournalEntry::create([
                    'business_id' => session('active_business'),
                    'account_head' => $creditParty->chart_of_account_id,
                    'voucher_id' => $partyTransfer->id,
                    'voucher_type' => 'Party Transfer',
                    'date_added' => $validated['date'],
                    'user_id' => auth()->id(),
                    'debit_amount' => 0,
                    'credit_amount' => $validated['transfer_amount'],
                    'comments' => 'Party Transfer: From ' . $debitParty->name . ' to ' . $creditParty->name,
                ]);

                DB::commit();

                return redirect()
                    ->route('party-transfers.index')
                    ->with('success', 'Party transfer updated successfully');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error('Database error in party transfer update: ' . $e->getMessage());
                
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Unable to update transfer. The selected party may have been deleted. Please refresh and try again.');
                }
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'A database error occurred while updating the transfer. Please try again.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in party transfer update transaction: ' . $e->getMessage());
                
                // Handle file storage errors
                if (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'storage')) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Failed to save attachment files. Please check file permissions or try again.');
                }
                
                // Handle ledger update errors
                if (str_contains($e->getMessage(), 'ledger') || str_contains($e->getMessage(), 'PartyLedger')) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Failed to update party ledger entries. The transfer may have been modified. Please refresh and try again.');
                }
                
                // Handle journal entry errors
                if (str_contains($e->getMessage(), 'journal') || str_contains($e->getMessage(), 'JournalEntry')) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Failed to update journal entries. The transfer may have been modified. Please refresh and try again.');
                }
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Unable to update the party transfer. Please check all fields and try again.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Party transfer update failed: ' . $e->getMessage());
            
            $errorMessage = 'Unable to update party transfer. ';
            
            if (str_contains($e->getMessage(), 'permission') || str_contains($e->getMessage(), 'Unauthorized')) {
                $errorMessage = 'You do not have permission to edit this transfer.';
            } elseif (str_contains($e->getMessage(), 'party')) {
                $errorMessage = 'Please select valid parties for the transfer.';
            } elseif (str_contains($e->getMessage(), 'amount')) {
                $errorMessage = 'Please enter a valid transfer amount greater than zero.';
            } else {
                $errorMessage .= 'Please check all fields and try again.';
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    public function destroy(PartyTransfer $partyTransfer)
    {
        try {
            // Check if transfer belongs to current business
            if ($partyTransfer->business_id != session('active_business')) {
                return redirect()
                    ->route('party-transfers.index')
                    ->with('error', 'You do not have permission to delete this transfer.');
            }

            DB::beginTransaction();
            try {
                // Delete all attachments and their files
                foreach ($partyTransfer->attachments as $attachment) {
                    try {
                        Storage::disk('public')->delete($attachment->file_path);
                    } catch (\Exception $fileError) {
                        Log::warning('Failed to delete attachment file: ' . $attachment->file_path . ' - ' . $fileError->getMessage());
                        // Continue even if file deletion fails
                    }
                    $attachment->delete();
                }

                // Delete party ledger entries (this will automatically adjust balances)
                $deletedLedgerEntries = PartyLedger::where('voucher_id', $partyTransfer->id)
                    ->where('voucher_type', 'Party Transfer')
                    ->delete();

                if ($deletedLedgerEntries === 0) {
                    Log::warning('No ledger entries found for party transfer ID: ' . $partyTransfer->id);
                }

                // Delete journal entries
                $deletedJournalEntries = JournalEntry::where('voucher_id', $partyTransfer->id)
                    ->where('voucher_type', 'Party Transfer')
                    ->delete();

                if ($deletedJournalEntries === 0) {
                    Log::warning('No journal entries found for party transfer ID: ' . $partyTransfer->id);
                }

                // Delete the transfer record
                $partyTransfer->delete();

                DB::commit();

                return redirect()
                    ->route('party-transfers.index')
                    ->with('success', 'Party transfer has been deleted successfully.');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error('Database error in party transfer deletion: ' . $e->getMessage());
                
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return redirect()
                        ->route('party-transfers.index')
                        ->with('error', 'Cannot delete this transfer because it is referenced by other records. Please contact support.');
                }
                
                return redirect()
                    ->route('party-transfers.index')
                    ->with('error', 'A database error occurred while deleting the transfer. Please try again or contact support if the problem persists.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in party transfer deletion transaction: ' . $e->getMessage());
                
                // Handle specific error cases
                if (str_contains($e->getMessage(), 'ledger') || str_contains($e->getMessage(), 'PartyLedger')) {
                    return redirect()
                        ->route('party-transfers.index')
                        ->with('error', 'Failed to delete party ledger entries. The transfer may have already been deleted or modified. Please refresh the page.');
                }
                
                if (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'storage')) {
                    return redirect()
                        ->route('party-transfers.index')
                        ->with('error', 'The transfer was deleted, but some attachment files could not be removed. This does not affect the ledger balances.');
                }
                
                return redirect()
                    ->route('party-transfers.index')
                    ->with('error', 'Unable to delete the party transfer. Please try again or contact support if the problem continues.');
            }

        } catch (\Exception $e) {
            Log::error('Party transfer deletion failed: ' . $e->getMessage());
            
            $errorMessage = 'Unable to delete party transfer. ';
            
            if (str_contains($e->getMessage(), 'permission') || str_contains($e->getMessage(), 'Unauthorized')) {
                $errorMessage = 'You do not have permission to delete this transfer.';
            } elseif (str_contains($e->getMessage(), 'constraint') || str_contains($e->getMessage(), 'foreign')) {
                $errorMessage = 'This transfer cannot be deleted because it is being used by other records in the system.';
            } else {
                $errorMessage .= 'Please try again or contact support if the problem persists.';
            }
            
            return redirect()
                ->route('party-transfers.index')
                ->with('error', $errorMessage);
        }
    }

    public function deleteAttachment(PartyTransferAttachment $attachment)
    {
        try {
            // Check if attachment belongs to current business's transfer
            $partyTransfer = $attachment->partyTransfer;
            if (!$partyTransfer || $partyTransfer->business_id != session('active_business')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'You do not have permission to delete this attachment.'
                ], 403);
            }

            DB::beginTransaction();
            try {
                // Delete the file from storage
                try {
                    Storage::disk('public')->delete($attachment->file_path);
                } catch (\Exception $fileError) {
                    Log::warning('Failed to delete attachment file: ' . $attachment->file_path . ' - ' . $fileError->getMessage());
                    // Continue even if file deletion fails
                }

                // Delete the record
                $attachment->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Attachment deleted successfully.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error deleting attachment in transaction: ' . $e->getMessage());
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error deleting attachment: ' . $e->getMessage());
            
            $errorMessage = 'Unable to delete the attachment. ';
            
            if (str_contains($e->getMessage(), 'permission') || str_contains($e->getMessage(), 'Unauthorized')) {
                $errorMessage = 'You do not have permission to delete this attachment.';
            } elseif (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'storage')) {
                $errorMessage = 'The attachment record was deleted, but the file could not be removed from storage.';
            } else {
                $errorMessage .= 'Please try again.';
            }
            
            return response()->json([
                'success' => false, 
                'message' => $errorMessage
            ], 500);
        }
    }
} 