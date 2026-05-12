<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankLedger;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\OwnerDrawing;
use App\Models\OwnerDrawingAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OwnerDrawingController extends Controller
{
    private function businessId(): ?int
    {
        return session('active_business');
    }

    private function authorizeBusiness(OwnerDrawing $ownerDrawing): void
    {
        if ($ownerDrawing->business_id !== $this->businessId()) {
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

    private function validateDepositAccountBelongsToCashTree(int $businessId, int $coaId): bool
    {
        $cashParentIds = $this->getCashInHandParentIds($businessId);
        if ($cashParentIds->isEmpty()) {
            return false;
        }

        return ChartOfAccount::where('business_id', $businessId)
            ->where('id', $coaId)
            ->whereIn('parent_id', $cashParentIds)
            ->where('is_active', true)
            ->exists();
    }

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

    private function findBankWalletByChartAccount(int $businessId, int $chartOfAccountId): ?Bank
    {
        return Bank::where('business_id', $businessId)
            ->where('chart_of_account_id', $chartOfAccountId)
            ->where('status', 1)
            ->whereIn('account_type', ['bank', 'cash'])
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

        $query = OwnerDrawing::with(['fromAccount', 'toAccount', 'createdBy', 'attachments', 'bank'])
            ->forBusiness($businessId);

        if ($request->filled('date_from')) {
            $query->where('drawing_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('drawing_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('fromAccount', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('toAccount', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('bank', fn ($q2) => $q2->where('account_name', 'like', "%{$search}%"));
            });
        }

        $drawings = $query->orderBy('drawing_date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $statistics = [
            'total_amount' => OwnerDrawing::forBusiness($businessId)->sum('amount'),
            'total_count' => OwnerDrawing::forBusiness($businessId)->count(),
            'this_month' => OwnerDrawing::forBusiness($businessId)
                ->whereYear('drawing_date', now()->year)
                ->whereMonth('drawing_date', now()->month)
                ->sum('amount'),
        ];

        return view('owner-drawings.index', compact('drawings', 'statistics'));
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

        return view('owner-drawings.create', compact('cashBanks', 'banks', 'equityAccounts'));
    }

    public function store(Request $request)
    {
        $businessId = $this->businessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'No active business selected.');
        }

        $request->request->remove('from_account_id');

        $validated = $request->validate([
            'drawing_via' => 'required|in:cash,bank',
            'bank_id' => 'required|exists:banks,id',
            'to_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'drawing_date' => 'required|date',
            'paid_via' => 'required|in:cash,bank_transfer,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
        ]);

        $bank = Bank::where('business_id', $businessId)->where('id', $validated['bank_id'])->first();
        if (! $bank || ! $bank->chart_of_account_id) {
            return back()->withInput()->with('error', 'Selected account is invalid or has no chart of account.');
        }

        if ($validated['drawing_via'] === 'bank') {
            if ($bank->account_type !== 'bank') {
                return back()->withInput()->with('error', 'For Via = Bank, choose a bank account (not a cash wallet).');
            }
            if (! $this->chartCoaIsUnderBankAccounts($businessId, (int) $bank->chart_of_account_id)) {
                return back()->withInput()->with(
                    'error',
                    'This bank must be linked to a chart account under "Bank Accounts" (not cash).'
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

        $fromAccountId = (int) $bank->chart_of_account_id;

        if (! $this->validateEquityAccount($businessId, (int) $validated['to_account_id'])) {
            return back()->withInput()->with('error', 'To account must be an active equity account.');
        }

        DB::beginTransaction();
        try {
            $drawing = OwnerDrawing::create([
                'business_id' => $businessId,
                'drawing_via' => $validated['drawing_via'],
                'bank_id' => $validated['bank_id'],
                'from_account_id' => $fromAccountId,
                'to_account_id' => $validated['to_account_id'],
                'amount' => $validated['amount'],
                'drawing_date' => $validated['drawing_date'],
                'paid_via' => $validated['paid_via'],
                'reference_number' => $validated['reference_number'] ?? null,
                'description' => $validated['description'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file) {
                        $this->uploadAttachment($drawing, $file);
                    }
                }
            }

            $this->createJournalEntries($drawing);
            if ($drawing->bank_id) {
                $this->createBankLedger($drawing);
            }

            DB::commit();

            return redirect()->route('owner-drawings.index')
                ->with('success', 'Owner drawing recorded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error recording drawing: '.$e->getMessage());
        }
    }

    public function show(OwnerDrawing $ownerDrawing)
    {
        $this->authorizeBusiness($ownerDrawing);
        $ownerDrawing->load(['fromAccount', 'toAccount', 'createdBy', 'attachments.uploadedBy', 'bank']);

        return view('owner-drawings.show', compact('ownerDrawing'));
    }

    public function edit(OwnerDrawing $ownerDrawing)
    {
        $this->authorizeBusiness($ownerDrawing);

        $businessId = $this->businessId();
        $cashBanks = $this->getCashBanksForBusiness($businessId);
        $banks = $this->getBanksForBusiness($businessId);
        $equityAccounts = $this->getEquityAccounts($businessId);
        $ownerDrawing->load(['attachments']);

        $defaultBankId = $ownerDrawing->bank_id
            ?? $this->findBankWalletByChartAccount($businessId, (int) $ownerDrawing->from_account_id)?->id;

        return view('owner-drawings.edit', compact('ownerDrawing', 'cashBanks', 'banks', 'equityAccounts', 'defaultBankId'));
    }

    public function update(Request $request, OwnerDrawing $ownerDrawing)
    {
        $this->authorizeBusiness($ownerDrawing);

        $businessId = $this->businessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'No active business selected.');
        }

        $request->request->remove('from_account_id');

        $validated = $request->validate([
            'drawing_via' => 'required|in:cash,bank',
            'bank_id' => 'required|exists:banks,id',
            'to_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'drawing_date' => 'required|date',
            'paid_via' => 'required|in:cash,bank_transfer,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
        ]);

        $bank = Bank::where('business_id', $businessId)->where('id', $validated['bank_id'])->first();
        if (! $bank || ! $bank->chart_of_account_id) {
            return back()->withInput()->with('error', 'Selected account is invalid or has no chart of account.');
        }

        if ($validated['drawing_via'] === 'bank') {
            if ($bank->account_type !== 'bank') {
                return back()->withInput()->with('error', 'For Via = Bank, choose a bank account (not a cash wallet).');
            }
            if (! $this->chartCoaIsUnderBankAccounts($businessId, (int) $bank->chart_of_account_id)) {
                return back()->withInput()->with(
                    'error',
                    'This bank must be linked to a chart account under "Bank Accounts" (not cash).'
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

        $fromAccountId = (int) $bank->chart_of_account_id;

        if (! $this->validateEquityAccount($businessId, (int) $validated['to_account_id'])) {
            return back()->withInput()->with('error', 'To account must be an active equity account.');
        }

        DB::beginTransaction();
        try {
            $this->removeAccountingForDrawing($ownerDrawing);

            $ownerDrawing->update([
                'drawing_via' => $validated['drawing_via'],
                'bank_id' => $validated['bank_id'],
                'from_account_id' => $fromAccountId,
                'to_account_id' => $validated['to_account_id'],
                'amount' => $validated['amount'],
                'drawing_date' => $validated['drawing_date'],
                'paid_via' => $validated['paid_via'],
                'reference_number' => $validated['reference_number'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file) {
                        $this->uploadAttachment($ownerDrawing, $file);
                    }
                }
            }

            $ownerDrawing->refresh();
            $this->createJournalEntries($ownerDrawing);
            if ($ownerDrawing->bank_id) {
                $this->createBankLedger($ownerDrawing);
            }

            DB::commit();

            return redirect()->route('owner-drawings.show', $ownerDrawing)
                ->with('success', 'Owner drawing updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error updating drawing: '.$e->getMessage());
        }
    }

    public function destroy(OwnerDrawing $ownerDrawing)
    {
        $this->authorizeBusiness($ownerDrawing);

        DB::beginTransaction();
        try {
            $this->removeAccountingForDrawing($ownerDrawing);

            foreach ($ownerDrawing->attachments as $attachment) {
                $attachment->delete();
            }

            $ownerDrawing->delete();
            DB::commit();

            return redirect()->route('owner-drawings.index')
                ->with('success', 'Owner drawing deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('owner-drawings.index')
                ->with('error', 'Error deleting drawing: '.$e->getMessage());
        }
    }

    public function deleteAttachment(OwnerDrawingAttachment $attachment)
    {
        $drawing = $attachment->ownerDrawing;
        if (! $drawing || $drawing->business_id !== $this->businessId()) {
            abort(403);
        }

        try {
            $attachment->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function uploadAttachment(OwnerDrawing $drawing, $file): void
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid().'.'.$extension;
        $path = $file->storeAs('owner-drawings/'.$drawing->id, $fileName, 'public');

        OwnerDrawingAttachment::create([
            'owner_drawing_id' => $drawing->id,
            'original_name' => $originalName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_extension' => $extension,
            'uploaded_by' => Auth::id(),
        ]);
    }

    private function createJournalEntries(OwnerDrawing $drawing): void
    {
        $desc = $drawing->description ?? 'Owner Drawing';
        $comments = OwnerDrawing::VOUCHER_TYPE.': '.$desc;

        JournalEntry::create([
            'business_id' => $drawing->business_id,
            'account_head' => $drawing->to_account_id,
            'debit_amount' => $drawing->amount,
            'credit_amount' => 0,
            'voucher_id' => $drawing->id,
            'voucher_type' => OwnerDrawing::VOUCHER_TYPE,
            'comments' => $comments,
            'user_id' => $drawing->created_by,
            'date_added' => $drawing->drawing_date,
        ]);

        JournalEntry::create([
            'business_id' => $drawing->business_id,
            'account_head' => $drawing->from_account_id,
            'debit_amount' => 0,
            'credit_amount' => $drawing->amount,
            'voucher_id' => $drawing->id,
            'voucher_type' => OwnerDrawing::VOUCHER_TYPE,
            'comments' => $comments,
            'user_id' => $drawing->created_by,
            'date_added' => $drawing->drawing_date,
        ]);
    }

    private function createBankLedger(OwnerDrawing $drawing): void
    {
        BankLedger::create([
            'business_id' => $drawing->business_id,
            'bank_id' => $drawing->bank_id,
            'voucher_id' => $drawing->id,
            'voucher_type' => OwnerDrawing::VOUCHER_TYPE,
            'date' => $drawing->drawing_date,
            'user_id' => $drawing->created_by,
            'withdrawal_amount' => $drawing->amount,
            'deposit_amount' => 0,
            'details' => $drawing->reference_number,
        ]);
    }

    private function removeAccountingForDrawing(OwnerDrawing $drawing): void
    {
        JournalEntry::where('voucher_type', OwnerDrawing::VOUCHER_TYPE)
            ->where('voucher_id', $drawing->id)
            ->where('business_id', $drawing->business_id)
            ->delete();

        BankLedger::where('voucher_type', OwnerDrawing::VOUCHER_TYPE)
            ->where('voucher_id', $drawing->id)
            ->where('business_id', $drawing->business_id)
            ->delete();
    }
}
