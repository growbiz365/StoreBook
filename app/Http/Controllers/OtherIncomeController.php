<?php

namespace App\Http\Controllers;

use App\Models\OtherIncome;
use App\Models\OtherIncomeAttachment;
use App\Models\Bank;
use App\Models\BankLedger;
use App\Models\ChartOfAccount;
use App\Models\IncomeHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OtherIncomeController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        $query = OtherIncome::with(['bank', 'chartOfAccount', 'user'])
            ->where('business_id', $businessId);

        // Apply search filter if provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhere('amount', 'like', "%{$searchTerm}%")
                  ->orWhereHas('bank', function ($bankQuery) use ($searchTerm) {
                      $bankQuery->where('account_name', 'like', "%{$searchTerm}%")
                               ->orWhereHas('chartOfAccount', function ($chartQuery) use ($searchTerm) {
                                   $chartQuery->where('name', 'like', "%{$searchTerm}%");
                               });
                  })
                  ->orWhereHas('chartOfAccount', function ($chartQuery) use ($searchTerm) {
                      $chartQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $otherIncomes = $query->orderBy('income_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('other_incomes.index', compact('otherIncomes'));
    }

    public function create()
    {
        $businessId = session('active_business');
        
        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();
        
        // Get income heads created through IncomeHead module (with chart_of_account_id)
        $incomeHeads = IncomeHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->with('chartOfAccount')
            ->get()
            ->map(function($head) {
                return [
                    'id' => $head->chart_of_account_id,
                    'name' => $head->name,
                    'code' => $head->chartOfAccount->code ?? '',
                    'is_income_head' => true,
                ];
            })
            ->sortBy('name')
            ->values();
        
        // Get all other active income accounts from Chart of Accounts
        $incomeHeadChartAccountIds = IncomeHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->pluck('chart_of_account_id')
            ->toArray();
        
        $otherIncomeAccounts = ChartOfAccount::where('business_id', $businessId)
            ->where('type', 'income')
            ->where('is_active', true)
            ->whereNotIn('id', $incomeHeadChartAccountIds)
            ->orderBy('name')
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code ?? '',
                    'is_income_head' => false,
                ];
            });
        
        // Combine: IncomeHeads first, then other accounts
        $incomeAccounts = $incomeHeads->concat($otherIncomeAccounts);

        return view('other_incomes.create', compact('banks', 'incomeAccounts'));
    }

    public function store(Request $request)
    {
        $businessId = session('active_business');
        
        $request->validate([
            'income_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'bank_id' => 'required|exists:banks,id',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'attachment_titles.*' => 'nullable|string|max:255',
            'attachment_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max
        ]);

        $otherIncome = OtherIncome::create([
            'business_id' => $businessId,
            'user_id' => Auth::id(),
            'income_date' => $request->income_date,
            'amount' => $request->amount,
            // If description is omitted, store empty string to satisfy NOT NULL column
            'description' => $request->description ?? '',
            'bank_id' => $request->bank_id,
            'chart_of_account_id' => $request->chart_of_account_id,
        ]);

        // Handle file uploads
        if ($request->hasFile('attachment_files')) {
            foreach ($request->file('attachment_files') as $index => $file) {
                if ($file) {
                    $filePath = $file->store('other_income_attachments', 'public');
                    
                    OtherIncomeAttachment::create([
                        'other_income_id' => $otherIncome->id,
                        'file_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }
        }

        return redirect()->route('other-incomes.index')
            ->with('success', 'Other income recorded successfully.');
    }

    public function show(OtherIncome $otherIncome)
    {
        $businessId = session('active_business');
        
        // Ensure the other income belongs to the active business
        if ($otherIncome->business_id !== $businessId) {
            abort(403, 'Unauthorized access to other income record.');
        }
        
        $otherIncome->load(['bank', 'chartOfAccount', 'user', 'attachments', 'journalEntries.account']);
        
        return view('other_incomes.show', compact('otherIncome'));
    }

    public function edit(OtherIncome $otherIncome)
    {
        $businessId = session('active_business');
        
        // Ensure the other income belongs to the active business
        if ($otherIncome->business_id !== $businessId) {
            abort(403, 'Unauthorized access to other income record.');
        }
        
        $banks = Bank::where('business_id', $businessId)
            ->where('status', 1) // Only active banks
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('account_name')
            ->get();
        
        // Get income heads created through IncomeHead module (with chart_of_account_id)
        $incomeHeads = IncomeHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->whereHas('chartOfAccount', function($query) {
                $query->where('is_active', true);
            })
            ->with('chartOfAccount')
            ->get()
            ->map(function($head) {
                return [
                    'id' => $head->chart_of_account_id,
                    'name' => $head->name,
                    'code' => $head->chartOfAccount->code ?? '',
                    'is_income_head' => true,
                ];
            })
            ->sortBy('name')
            ->values();
        
        // Get all other active income accounts from Chart of Accounts
        $incomeHeadChartAccountIds = IncomeHead::where('business_id', $businessId)
            ->whereNotNull('chart_of_account_id')
            ->pluck('chart_of_account_id')
            ->toArray();
        
        $otherIncomeAccounts = ChartOfAccount::where('business_id', $businessId)
            ->where('type', 'income')
            ->where('is_active', true)
            ->whereNotIn('id', $incomeHeadChartAccountIds)
            ->orderBy('name')
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code ?? '',
                    'is_income_head' => false,
                ];
            });
        
        // Combine: IncomeHeads first, then other accounts
        $incomeAccounts = $incomeHeads->concat($otherIncomeAccounts);

        $otherIncome->load('attachments');

        return view('other_incomes.edit', compact('otherIncome', 'banks', 'incomeAccounts'));
    }

    public function update(Request $request, OtherIncome $otherIncome)
    {
        $businessId = session('active_business');
        
        // Ensure the other income belongs to the active business
        if ($otherIncome->business_id !== $businessId) {
            abort(403, 'Unauthorized access to other income record.');
        }

        $request->validate([
            'income_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'bank_id' => 'required|exists:banks,id',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'attachment_titles.*' => 'nullable|string|max:255',
            'attachment_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $updateData = [
            'income_date' => $request->income_date,
            'amount' => $request->amount,
            'bank_id' => $request->bank_id,
            'chart_of_account_id' => $request->chart_of_account_id,
        ];

        // Only update description if it was present in the request; otherwise keep existing
        if ($request->has('description')) {
            $updateData['description'] = $request->description ?? $otherIncome->description;
        }

        $otherIncome->update($updateData);

        // Handle new file uploads
        if ($request->hasFile('attachment_files')) {
            foreach ($request->file('attachment_files') as $index => $file) {
                if ($file) {
                    $filePath = $file->store('other_income_attachments', 'public');
                    
                    OtherIncomeAttachment::create([
                        'other_income_id' => $otherIncome->id,
                        'file_name' => $request->attachment_titles[$index] ?? $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }
        }

        return redirect()->route('other-incomes.index')
            ->with('success', 'Other income updated successfully.');
    }

    public function destroy(OtherIncome $otherIncome)
    {
        $businessId = session('active_business');
        
        // Ensure the other income belongs to the active business
        if ($otherIncome->business_id !== $businessId) {
            abort(403, 'Unauthorized access to other income record.');
        }
        
        $otherIncome->delete();

        return redirect()->route('other-incomes.index')
            ->with('success', 'Other income deleted successfully.');
    }

    public function deleteAttachment(OtherIncomeAttachment $attachment)
    {
        $businessId = session('active_business');
        
        // Ensure the attachment belongs to an other income from the active business
        if ($attachment->otherIncome->business_id !== $businessId) {
            abort(403, 'Unauthorized access to attachment.');
        }
        
        $attachment->delete();

        return response()->json(['success' => true]);
    }

    public function downloadAttachment(OtherIncomeAttachment $attachment)
    {
        $businessId = session('active_business');
        
        // Ensure the attachment belongs to an other income from the active business
        if ($attachment->otherIncome->business_id !== $businessId) {
            abort(403, 'Unauthorized access to attachment.');
        }
        
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }
}