<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = ChartOfAccount::with('allChildren')
            ->where('business_id', $businessId)
            ->parentAccounts();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('code')->get();

        return view('finance.chart-of-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $businessId = session('active_business');
        $parentAccounts = ChartOfAccount::where('business_id', $businessId)
            ->orderBy('code')
            ->get();

        return view('finance.chart-of-accounts.create', compact('parentAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('chart_of_accounts')->where(function ($query) {
                    return $query->where('business_id', session('active_business'));
                }),
            ],
            'type' => 'required|in:asset,liability,income,expense,equity',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            // Bank account validations
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'branch_code' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50',
            'bank_address' => 'nullable|string',
        ]);

        $validated['business_id'] = session('active_business');
        $validated['is_active'] = $request->has('is_active');

        // Generate account code based on parent
        if ($request->parent_id) {
            $parent = ChartOfAccount::where('business_id', session('active_business'))
                ->find($request->parent_id);
            
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Selected parent account not found.'])->withInput();
            }
            
            // Validate parent type matches child type
            if ($parent->type !== $request->type) {
                return back()->withErrors(['parent_id' => 'Parent account type must match the selected account type.'])->withInput();
            }
            $parentCode = $parent->code;

            // Find siblings with same parent
            $siblings = ChartOfAccount::where('parent_id', $parent->id)
                ->orderBy('code', 'desc')
                ->first();

            // Determine the level and code generation strategy
            if (
                $parentCode === '1000' || $parentCode === '2000' || $parentCode === '3000' ||
                $parentCode === '4000' || $parentCode === '5000'
            ) {
                // Level 1 (Main categories) - Generate codes like 1100, 1200, etc.
                if ($siblings) {
                    $lastCode = intval($siblings->code);
                    $validated['code'] = str_pad($lastCode + 100, 4, '0', STR_PAD_LEFT);
                } else {
                    $validated['code'] = substr($parentCode, 0, 1) . '100';
                }
            } else if (substr($parentCode, -2) === '00') {
                // Level 2 (Like 1100, 1200) - Generate codes like 1110, 1120, etc.
                if ($siblings) {
                    $lastCode = intval($siblings->code);
                    $validated['code'] = str_pad($lastCode + 10, 4, '0', STR_PAD_LEFT);
                } else {
                    $validated['code'] = substr($parentCode, 0, 2) . '10';
                }
            } else if (substr($parentCode, -1) === '0') {
                // Level 3 (Like 1110, 1120) - Generate codes like 1111, 1112, etc.
                if ($siblings) {
                    $lastCode = intval($siblings->code);
                    $validated['code'] = str_pad($lastCode + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $validated['code'] = substr($parentCode, 0, 3) . '1';
                }
            } else {
                // Level 4 (Like 1111, 1121) - Generate next sequential number
                if ($siblings) {
                    $lastCode = intval($siblings->code);
                    $validated['code'] = str_pad($lastCode + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $validated['code'] = $parentCode . '1';
                }
            }
        } else {
            // Top level account - use type-based numbering
            $typePrefix = match ($request->type) {
                'asset' => '1',
                'liability' => '2',
                'income' => '3',
                'expense' => '4',
                'equity' => '5',
            };

            // Get all existing codes for this business to find next available
            $existingCodes = ChartOfAccount::where('business_id', session('active_business'))
                ->pluck('code')
                ->map(fn($code) => intval($code))
                ->toArray();

            // Find the next available code starting from the base code for this type
            $baseCode = intval($typePrefix . '000');
            $nextCode = $baseCode;
            
            // Check codes in increments of 100 (for top-level accounts: 4000, 4100, 4200, etc.)
            while (in_array($nextCode, $existingCodes) && $nextCode < (intval($typePrefix) + 1) * 1000) {
                $nextCode += 100;
            }
            
            // If we've exhausted all codes in this type range, use the base code
            // (This shouldn't happen in practice, but handle it gracefully)
            if ($nextCode >= (intval($typePrefix) + 1) * 1000) {
                $nextCode = $baseCode;
            }
            
            $validated['code'] = str_pad($nextCode, 4, '0', STR_PAD_LEFT);
        }

        ChartOfAccount::create($validated);

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account created successfully');
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        // Prevent editing default accounts (system-created accounts)
        if ($chartOfAccount->is_default) {
            return redirect()
                ->route('chart-of-accounts.index')
                ->with('error', 'Cannot edit a default account. Default accounts are system-created and locked.');
        }

        $businessId = session('active_business');
        $parentAccounts = ChartOfAccount::where('business_id', $businessId)
            ->where('id', '!=', $chartOfAccount->id)
            ->orderBy('code')
            ->get();

        return view('finance.chart-of-accounts.edit', compact('chartOfAccount', 'parentAccounts'));
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        // Prevent updating default accounts (system-created accounts)
        if ($chartOfAccount->is_default) {
            return redirect()
                ->route('chart-of-accounts.index')
                ->with('error', 'Cannot update a default account. Default accounts are system-created and locked.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('chart_of_accounts')
                    ->ignore($chartOfAccount->id)
                    ->where(function ($query) {
                        return $query->where('business_id', session('active_business'));
                    }),
            ],
            'type' => 'required|in:asset,liability,income,expense,equity',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            // Bank account validations
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'branch_code' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50',
            'bank_address' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $chartOfAccount->update($validated);

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account updated successfully');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->is_default) {
            return back()->with('error', 'Cannot delete a default account');
        }

        if ($chartOfAccount->children()->exists()) {
            return back()->with('error', 'Cannot delete an account with sub-accounts');
        }

        // Check if account has journal entries
        $journalEntryCount = $chartOfAccount->journalEntries()->count();
        if ($journalEntryCount > 0) {
            return back()->with('error', "Cannot delete account '{$chartOfAccount->name}' because it has {$journalEntryCount} journal entry/entries. Please delete all journal entries associated with this account first.");
        }

        $chartOfAccount->delete();

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account deleted successfully');
    }

    public function markActive(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->is_default) {
            return back()->with('error', 'Cannot modify a default account');
        }

        $chartOfAccount->update(['is_active' => true]);

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account marked as active successfully');
    }

    public function markInactive(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->is_default) {
            return back()->with('error', 'Cannot modify a default account');
        }

        $chartOfAccount->update(['is_active' => false]);

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account marked as inactive successfully');
    }

    public function getAccountsByType(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cash,bank'
        ]);

        $accounts = ChartOfAccount::query()
            ->where('business_id', session('active_business'))
            ->where('is_active', true)
            ->when($request->type === 'cash', function ($query) {
                return $query->whereIn('parent_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('chart_of_accounts')
                        ->where('code', '1110'); // Cash in Hand code
                });
            })
            ->when($request->type === 'bank', function ($query) {
                return $query->whereIn('parent_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('chart_of_accounts')
                        ->where('code', '1120'); // Bank Accounts code
                });
            })
            ->get(['id', 'name', 'code']);

        return response()->json($accounts);
    }
}
