<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankLedger;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\OwnerContribution;
use App\Models\OwnerContributionAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OwnerContributionController extends Controller
{
    private function businessId(): ?int
    {
        return session('active_business');
    }

    private function authorizeBusiness(OwnerContribution $ownerContribution): void
    {
        if ($ownerContribution->business_id !== $this->businessId()) {
            abort(403);
        }
    }

    private function getCashInHandParentIds(int $businessId)
    {
        return ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'Cash in Hand')
            ->where('type', 'asset')
            ->pluck('id');
    }

    private function validateDepositAccountBelongsToCashTree(int $businessId, int $depositAccountId): bool
    {
        $cashParentIds = $this->getCashInHandParentIds($businessId);

        if ($cashParentIds->isEmpty()) {
            return false;
        }

        return ChartOfAccount::where('business_id', $businessId)
            ->where('id', $depositAccountId)
            ->whereIn('parent_id', $cashParentIds)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Cash wallets in Banks module: account_type cash + COA under Cash in Hand.
     */
    private function getCashBanksForBusiness(int $businessId)
    {
        $cashParentIds = $this->getCashInHandParentIds($businessId);
        if ($cashParentIds->isEmpty()) {
            return Bank::whereRaw('1 = 0')->orderBy('account_name')->get();
        }

        return Bank::where('business_id', $businessId)
            ->where('status', 1)
            ->where('account_type', 'cash')
            ->whereNotNull('chart_of_account_id')
            ->whereHas('chartOfAccount', function ($q) use ($cashParentIds) {
                $q->where('is_active', true)
                    ->whereIn('parent_id', $cashParentIds);
            })
            ->orderBy('account_name')
            ->get();
    }

    private function findCashBankByChartAccount(int $businessId, int $chartOfAccountId): ?Bank
    {
        return Bank::where('business_id', $businessId)
            ->where('account_type', 'cash')
            ->where('chart_of_account_id', $chartOfAccountId)
            ->where('status', 1)
            ->orderBy('id')
            ->first();
    }

    private function validateEquityAccount(int $businessId, int $accountId): bool
    {
        return ChartOfAccount::where('business_id', $businessId)
            ->where('id', $accountId)
            ->where('type', 'equity')
            ->where('is_active', true)
            ->exists();
    }

    private function getEquityAccounts(int $businessId)
    {
        return ChartOfAccount::where('business_id', $businessId)
            ->where('type', 'equity')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Only banks whose ledger COA is a child of "Bank Accounts" (not "Cash in Hand").
     */
    private function getBankAccountsParentIds(int $businessId)
    {
        return ChartOfAccount::where('business_id', $businessId)
            ->where('name', 'Bank Accounts')
            ->where('type', 'asset')
            ->pluck('id');
    }

    private function chartCoaIsUnderBankAccounts(int $businessId, int $chartOfAccountId): bool
    {
        $parentIds = $this->getBankAccountsParentIds($businessId);

        if ($parentIds->isEmpty()) {
            return false;
        }

        return ChartOfAccount::where('business_id', $businessId)
            ->where('id', $chartOfAccountId)
            ->where('is_active', true)
            ->whereIn('parent_id', $parentIds)
            ->exists();
    }

    private function getBanksForBusiness(int $businessId)
    {
        $bankParentIds = $this->getBankAccountsParentIds($businessId);
        if ($bankParentIds->isEmpty()) {
            return Bank::whereRaw('1 = 0')->orderBy('account_name')->get();
        }

        return Bank::where('business_id', $businessId)
            ->where('status', 1)
            ->where('account_type', 'bank')
            ->whereNotNull('chart_of_account_id')
            ->whereHas('chartOfAccount', function ($q) use ($bankParentIds) {
                $q->where('is_active', true)
                    ->whereIn('parent_id', $bankParentIds);
            })
            ->orderBy('account_name')
            ->get();
    }

    public function index(Request $request)
    {
        $businessId = $this->businessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'No active business selected.');
        }

        $query = OwnerContribution::with(['depositAccount', 'fromAccount', 'createdBy', 'attachments', 'bank'])
            ->forBusiness($businessId);

        if ($request->filled('date_from')) {
            $query->where('contribution_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('contribution_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('depositAccount', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('fromAccount', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('bank', fn ($q2) => $q2->where('account_name', 'like', "%{$search}%"));
            });
        }

        $contributions = $query->orderBy('contribution_date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $statistics = [
            'total_amount' => OwnerContribution::forBusiness($businessId)->sum('amount'),
            'total_count' => OwnerContribution::forBusiness($businessId)->count(),
            'this_month' => OwnerContribution::forBusiness($businessId)
                ->whereYear('contribution_date', now()->year)
                ->whereMonth('contribution_date', now()->month)
                ->sum('amount'),
        ];

        return view('owner-contributions.index', compact('contributions', 'statistics'));
    }

    public function create()
    {
        $businessId = $this->businessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'No active business selected.');
        }

        $cashBanks = $this->getCashBanksForBusiness($businessId);
        $banks = $this->getBanksForBusiness($businessId);
        $equityAccounts = $this->getEquityAccounts($businessId);

        return view('owner-contributions.create', compact('cashBanks', 'banks', 'equityAccounts'));
    }

    public function store(Request $request)
    {
        $businessId = $this->businessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'No active business selected.');
        }

        // Chart deposit line always follows the Banks module wallet (bank or cash type).
        $request->request->remove('deposit_account_id');

        $validated = $request->validate([
            'contribution_via' => 'required|in:cash,bank',
            'bank_id' => 'required|exists:banks,id',
            'from_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date',
            'received_via' => 'required|in:cash,bank_transfer,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
        ]);

        $bank = Bank::where('business_id', $businessId)->where('id', $validated['bank_id'])->first();
        if (! $bank || ! $bank->chart_of_account_id) {
            return back()->withInput()->with('error', 'Selected account is invalid or has no chart of account.');
        }

        if ($validated['contribution_via'] === 'bank') {
            if ($bank->account_type !== 'bank') {
                return back()->withInput()->with('error', 'For Via = Bank, choose a bank account (not a cash wallet).');
            }
            if (! $this->chartCoaIsUnderBankAccounts($businessId, (int) $bank->chart_of_account_id)) {
                return back()->withInput()->with(
                    'error',
                    'This bank must be linked to a chart account under "Bank Accounts" (not cash). Edit the bank record or chart of accounts.'
                );
            }
        } else {
            if ($bank->account_type !== 'cash') {
                return back()->withInput()->with('error', 'For Via = Cash, choose a cash wallet from Banks (account type Cash).');
            }
            if (! $this->validateDepositAccountBelongsToCashTree($businessId, (int) $bank->chart_of_account_id)) {
                return back()->withInput()->with('error', 'This cash wallet must be linked to a chart account under Cash in Hand.');
            }
        }

        $depositAccountId = (int) $bank->chart_of_account_id;

        if (! $this->validateEquityAccount($businessId, (int) $validated['from_account_id'])) {
            return back()->withInput()->with('error', 'From account must be an active equity account.');
        }

        DB::beginTransaction();
        try {
            $contribution = OwnerContribution::create([
                'business_id' => $businessId,
                'contribution_via' => $validated['contribution_via'],
                'bank_id' => $validated['bank_id'],
                'deposit_account_id' => $depositAccountId,
                'from_account_id' => $validated['from_account_id'],
                'amount' => $validated['amount'],
                'contribution_date' => $validated['contribution_date'],
                'received_via' => $validated['received_via'],
                'reference_number' => $validated['reference_number'] ?? null,
                'description' => $validated['description'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file) {
                        $this->uploadAttachment($contribution, $file);
                    }
                }
            }

            $this->createJournalEntries($contribution);
            if ($contribution->bank_id) {
                $this->createBankLedger($contribution);
            }

            DB::commit();

            return redirect()->route('owner-contributions.index')
                ->with('success', 'Owner contribution recorded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error recording contribution: '.$e->getMessage());
        }
    }

    public function show(OwnerContribution $ownerContribution)
    {
        $this->authorizeBusiness($ownerContribution);
        $ownerContribution->load(['depositAccount', 'fromAccount', 'createdBy', 'attachments.uploadedBy', 'bank']);

        return view('owner-contributions.show', compact('ownerContribution'));
    }

    public function edit(OwnerContribution $ownerContribution)
    {
        $this->authorizeBusiness($ownerContribution);

        $businessId = $this->businessId();
        $cashBanks = $this->getCashBanksForBusiness($businessId);
        $banks = $this->getBanksForBusiness($businessId);
        $equityAccounts = $this->getEquityAccounts($businessId);
        $ownerContribution->load(['attachments']);

        $defaultBankId = null;
        if ($ownerContribution->contribution_via === 'bank') {
            $defaultBankId = $ownerContribution->bank_id;
        } elseif ($ownerContribution->contribution_via === 'cash') {
            $defaultBankId = $ownerContribution->bank_id
                ?? $this->findCashBankByChartAccount($businessId, (int) $ownerContribution->deposit_account_id)?->id;
        }

        return view('owner-contributions.edit', compact('ownerContribution', 'cashBanks', 'banks', 'equityAccounts', 'defaultBankId'));
    }

    public function update(Request $request, OwnerContribution $ownerContribution)
    {
        $this->authorizeBusiness($ownerContribution);

        $businessId = $this->businessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'No active business selected.');
        }

        $request->request->remove('deposit_account_id');

        $validated = $request->validate([
            'contribution_via' => 'required|in:cash,bank',
            'bank_id' => 'required|exists:banks,id',
            'from_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date',
            'received_via' => 'required|in:cash,bank_transfer,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
        ]);

        $bank = Bank::where('business_id', $businessId)->where('id', $validated['bank_id'])->first();
        if (! $bank || ! $bank->chart_of_account_id) {
            return back()->withInput()->with('error', 'Selected account is invalid or has no chart of account.');
        }

        if ($validated['contribution_via'] === 'bank') {
            if ($bank->account_type !== 'bank') {
                return back()->withInput()->with('error', 'For Via = Bank, choose a bank account (not a cash wallet).');
            }
            if (! $this->chartCoaIsUnderBankAccounts($businessId, (int) $bank->chart_of_account_id)) {
                return back()->withInput()->with(
                    'error',
                    'This bank must be linked to a chart account under "Bank Accounts" (not cash). Edit the bank record or chart of accounts.'
                );
            }
        } else {
            if ($bank->account_type !== 'cash') {
                return back()->withInput()->with('error', 'For Via = Cash, choose a cash wallet from Banks (account type Cash).');
            }
            if (! $this->validateDepositAccountBelongsToCashTree($businessId, (int) $bank->chart_of_account_id)) {
                return back()->withInput()->with('error', 'This cash wallet must be linked to a chart account under Cash in Hand.');
            }
        }

        $depositAccountId = (int) $bank->chart_of_account_id;

        if (! $this->validateEquityAccount($businessId, (int) $validated['from_account_id'])) {
            return back()->withInput()->with('error', 'From account must be an active equity account.');
        }

        DB::beginTransaction();
        try {
            $this->removeAccountingForContribution($ownerContribution);

            $ownerContribution->update([
                'contribution_via' => $validated['contribution_via'],
                'bank_id' => $validated['bank_id'],
                'deposit_account_id' => $depositAccountId,
                'from_account_id' => $validated['from_account_id'],
                'amount' => $validated['amount'],
                'contribution_date' => $validated['contribution_date'],
                'received_via' => $validated['received_via'],
                'reference_number' => $validated['reference_number'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file) {
                        $this->uploadAttachment($ownerContribution, $file);
                    }
                }
            }

            $ownerContribution->refresh();
            $this->createJournalEntries($ownerContribution);
            if ($ownerContribution->bank_id) {
                $this->createBankLedger($ownerContribution);
            }

            DB::commit();

            return redirect()->route('owner-contributions.show', $ownerContribution)
                ->with('success', 'Owner contribution updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error updating contribution: '.$e->getMessage());
        }
    }

    public function destroy(OwnerContribution $ownerContribution)
    {
        $this->authorizeBusiness($ownerContribution);

        DB::beginTransaction();
        try {
            $this->removeAccountingForContribution($ownerContribution);

            foreach ($ownerContribution->attachments as $attachment) {
                $attachment->delete();
            }

            $ownerContribution->delete();
            DB::commit();

            return redirect()->route('owner-contributions.index')
                ->with('success', 'Owner contribution deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('owner-contributions.index')
                ->with('error', 'Error deleting contribution: '.$e->getMessage());
        }
    }

    public function deleteAttachment(OwnerContributionAttachment $attachment)
    {
        $contribution = $attachment->ownerContribution;
        if (! $contribution || $contribution->business_id !== $this->businessId()) {
            abort(403);
        }

        try {
            $attachment->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function uploadAttachment(OwnerContribution $contribution, $file): void
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid().'.'.$extension;
        $path = $file->storeAs('owner-contributions/'.$contribution->id, $fileName, 'public');

        OwnerContributionAttachment::create([
            'owner_contribution_id' => $contribution->id,
            'original_name' => $originalName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_extension' => $extension,
            'uploaded_by' => Auth::id(),
        ]);
    }

    private function createJournalEntries(OwnerContribution $contribution): void
    {
        $desc = $contribution->description ?? 'Owner Contribution';
        $comments = OwnerContribution::VOUCHER_TYPE.': '.$desc;

        JournalEntry::create([
            'business_id' => $contribution->business_id,
            'account_head' => $contribution->deposit_account_id,
            'debit_amount' => $contribution->amount,
            'credit_amount' => 0,
            'voucher_id' => $contribution->id,
            'voucher_type' => OwnerContribution::VOUCHER_TYPE,
            'comments' => $comments,
            'user_id' => $contribution->created_by,
            'date_added' => $contribution->contribution_date,
        ]);

        JournalEntry::create([
            'business_id' => $contribution->business_id,
            'account_head' => $contribution->from_account_id,
            'debit_amount' => 0,
            'credit_amount' => $contribution->amount,
            'voucher_id' => $contribution->id,
            'voucher_type' => OwnerContribution::VOUCHER_TYPE,
            'comments' => $comments,
            'user_id' => $contribution->created_by,
            'date_added' => $contribution->contribution_date,
        ]);
    }

    private function createBankLedger(OwnerContribution $contribution): void
    {
        BankLedger::create([
            'business_id' => $contribution->business_id,
            'bank_id' => $contribution->bank_id,
            'voucher_id' => $contribution->id,
            'voucher_type' => OwnerContribution::VOUCHER_TYPE,
            'date' => $contribution->contribution_date,
            'user_id' => $contribution->created_by,
            'withdrawal_amount' => 0,
            'deposit_amount' => $contribution->amount,
            'details' => $contribution->reference_number,
        ]);
    }

    private function removeAccountingForContribution(OwnerContribution $contribution): void
    {
        JournalEntry::where('voucher_type', OwnerContribution::VOUCHER_TYPE)
            ->where('voucher_id', $contribution->id)
            ->where('business_id', $contribution->business_id)
            ->delete();

        BankLedger::where('voucher_type', OwnerContribution::VOUCHER_TYPE)
            ->where('voucher_id', $contribution->id)
            ->where('business_id', $contribution->business_id)
            ->delete();
    }
}
