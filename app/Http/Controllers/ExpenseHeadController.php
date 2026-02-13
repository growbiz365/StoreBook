<?php

namespace App\Http\Controllers;

use App\Models\ExpenseHead;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpenseHeadController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseHead::where('business_id', session('active_business'))
            ->where(function ($subQuery) {
                $subQuery->whereHas('chartOfAccount', function ($accountQuery) {
                    $accountQuery->where('is_default', false);
                })->orWhereNull('chart_of_account_id');
            });

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('expense_head', 'like', "%{$searchTerm}%");
        }

        $expenseHeads = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('expense_heads.index', compact('expenseHeads'));
    }

    public function create()
    {
        return view('expense_heads.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'expense_head' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('expense_heads')->where(function ($query) {
                        return $query->where('business_id', session('active_business'));
                    }),
                ],
            ]);

            $validated['expense_head'] = Str::upper($validated['expense_head']);
            $validated['business_id'] = session('active_business');
            
            if (!$validated['business_id']) {
                throw new \Exception('No active business selected.');
            }

            // Check if expense head name already exists in ChartOfAccount
            $existingAccountByName = ChartOfAccount::where('business_id', session('active_business'))
                ->where('name', $validated['expense_head'])
                ->where('type', 'expense')
                ->first();

            if ($existingAccountByName) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'An expense account with this name already exists in Chart of Accounts.');
            }

            DB::beginTransaction();
            try {
                // Find the Operating Expenses parent account (code: 4300)
                $parentAccount = ChartOfAccount::where('code', '4300')
                    ->where('business_id', session('active_business'))
                    ->first();

                if (!$parentAccount) {
                    throw new \Exception('Operating Expenses account (4300) not found.');
                }

                // Generate a unique code for the expense head
                // Use a more robust approach to get the next code
                $lastAccount = ChartOfAccount::where('business_id', session('active_business'))
                    ->where('code', 'like', '4300%')
                    ->where('code', '!=', '4300') // Exclude the parent account itself
                    ->orderBy('code', 'desc')
                    ->lockForUpdate() // Lock the rows to prevent race conditions
                    ->first();

                if ($lastAccount) {
                    $nextCode = intval($lastAccount->code) + 1;
                } else {
                    $nextCode = 4301;
                }

                // Double-check that the code doesn't already exist
                while (ChartOfAccount::where('business_id', session('active_business'))
                    ->where('code', (string)$nextCode)
                    ->exists()) {
                    $nextCode++;
                }

                // Check if the generated code already exists (additional safety check)
                $existingAccountByCode = ChartOfAccount::where('business_id', session('active_business'))
                    ->where('code', (string)$nextCode)
                    ->first();

                if ($existingAccountByCode) {
                    throw new \Exception('An account with code ' . $nextCode . ' already exists in Chart of Accounts.');
                }

                // Create expense head
                $expenseHead = ExpenseHead::create($validated);

                // Create chart of account entry
                $chartOfAccount = ChartOfAccount::create([
                    'business_id' => session('active_business'),
                    'code' => (string)$nextCode,
                    'name' => $validated['expense_head'],
                    'type' => 'expense',
                    'parent_id' => $parentAccount->id,
                ]);

                // Update expense head with chart of account ID
                $expenseHead->update(['chart_of_account_id' => $chartOfAccount->id]);

                DB::commit();

                return redirect()
                    ->route('expense-heads.index')
                    ->with('success', 'Expense head created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in expense head creation: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Expense head creation failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating expense head: ' . $e->getMessage());
        }
    }

    public function show(ExpenseHead $expenseHead)
    {
        return view('expense_heads.show', compact('expenseHead'));
    }

    public function edit(ExpenseHead $expenseHead)
    {
        return view('expense_heads.edit', compact('expenseHead'));
    }

    public function update(Request $request, ExpenseHead $expenseHead)
    {
        try {
            $validated = $request->validate([
                'expense_head' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('expense_heads')->where(function ($query) {
                        return $query->where('business_id', session('active_business'));
                    })->ignore($expenseHead->id),
                ],
            ]);

            $validated['expense_head'] = Str::upper($validated['expense_head']);

            // Check if expense head name already exists in ChartOfAccount (excluding the current one)
            $existingAccountByName = ChartOfAccount::where('business_id', session('active_business'))
                ->where('name', $validated['expense_head'])
                ->where('type', 'expense')
                ->where('id', '!=', $expenseHead->chart_of_account_id)
                ->first();

            if ($existingAccountByName) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'An expense account with this name already exists in Chart of Accounts.');
            }

            DB::beginTransaction();
            try {
                // Update expense head
                $expenseHead->update($validated);

                // Update the linked chart of account
                if ($expenseHead->chartOfAccount) {
                    $expenseHead->chartOfAccount->update([
                        'name' => $validated['expense_head'],
                    ]);
                }

                DB::commit();

                return redirect()
                    ->route('expense-heads.index')
                    ->with('success', 'Expense head updated successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in expense head update: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Expense head update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating expense head: ' . $e->getMessage());
        }
    }

    public function destroy(ExpenseHead $expenseHead)
    {
        try {
            DB::beginTransaction();

            // Check if expense head has any expenses
            if ($expenseHead->expenses()->count() > 0) {
                throw new \Exception('Cannot delete expense head that has associated expenses.');
            }

            // Delete the linked chart of account
            if ($expenseHead->chartOfAccount) {
                $expenseHead->chartOfAccount->delete();
            }

            // Delete the expense head
            $expenseHead->delete();

            DB::commit();

            return redirect()
                ->route('expense-heads.index')
                ->with('success', 'Expense head deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting expense head: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error deleting expense head: ' . $e->getMessage());
        }
    }
}
