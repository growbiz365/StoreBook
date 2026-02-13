<?php

namespace App\Http\Controllers;

use App\Models\IncomeHead;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeHeadController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomeHead::where('business_id', session('active_business'));

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $incomeHeads = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('income_heads.index', compact('incomeHeads'));
    }

    public function create()
    {
        return view('income_heads.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('income_heads')->where(function ($query) {
                        return $query->where('business_id', session('active_business'));
                    }),
                ],
            ]);

            $validated['name'] = Str::upper($validated['name']);
            $validated['business_id'] = session('active_business');
            
            if (!$validated['business_id']) {
                throw new \Exception('No active business selected.');
            }

            // Prevent duplicate chart of account names
            $existingAccountByName = ChartOfAccount::where('business_id', session('active_business'))
                ->where('name', $validated['name'])
                ->where('type', 'income')
                ->first();

            if ($existingAccountByName) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'An income account with this name already exists in Chart of Accounts.');
            }

            DB::beginTransaction();
            try {
                // Find the Other Income parent account (code: 3200)
                $parentAccount = ChartOfAccount::where('code', '3200')
                    ->where('business_id', session('active_business'))
                    ->first();

                if (!$parentAccount) {
                    throw new \Exception('Other Income account (3200) not found.');
                }

                // Generate a unique code for the income head
                // Use a more robust approach to get the next code
                $lastAccount = ChartOfAccount::where('business_id', session('active_business'))
                    ->where('code', 'like', '3200%')
                    ->where('code', '!=', '3200') // Exclude the parent account itself
                    ->orderBy('code', 'desc')
                    ->lockForUpdate() // Lock the rows to prevent race conditions
                    ->first();

                if ($lastAccount) {
                    $nextCode = intval($lastAccount->code) + 1;
                } else {
                    $nextCode = 3201;
                }

                // Double-check that the code doesn't already exist
                while (ChartOfAccount::where('business_id', session('active_business'))
                    ->where('code', (string)$nextCode)
                    ->exists()) {
                    $nextCode++;
                }

                // Additional safeguard for duplicate codes
                if (ChartOfAccount::where('business_id', session('active_business'))
                    ->where('code', (string)$nextCode)
                    ->exists()) {
                    throw new \Exception('An account with code ' . $nextCode . ' already exists in Chart of Accounts.');
                }

                // Create income head
                $incomeHead = IncomeHead::create($validated);

                // Create chart of account entry
                $chartOfAccount = ChartOfAccount::create([
                    'business_id' => session('active_business'),
                    'code' => (string)$nextCode,
                    'name' => $validated['name'],
                    'type' => 'income',
                    'parent_id' => $parentAccount->id,
                ]);

                // Update income head with chart of account ID
                $incomeHead->update(['chart_of_account_id' => $chartOfAccount->id]);

                DB::commit();

                return redirect()
                    ->route('income-heads.index')
                    ->with('success', 'Income head created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in income head creation: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Income head creation failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating income head: ' . $e->getMessage());
        }
    }

    public function edit(IncomeHead $incomeHead)
    {
        return view('income_heads.edit', compact('incomeHead'));
    }

    public function update(Request $request, IncomeHead $incomeHead)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('income_heads')->where(function ($query) {
                        return $query->where('business_id', session('active_business'));
                    })->ignore($incomeHead->id),
                ],
            ]);

            $validated['name'] = Str::upper($validated['name']);

            // Prevent duplicate chart of account names (excluding current)
            $existingAccountByName = ChartOfAccount::where('business_id', session('active_business'))
                ->where('name', $validated['name'])
                ->where('type', 'income')
                ->where('id', '!=', $incomeHead->chart_of_account_id)
                ->first();

            if ($existingAccountByName) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'An income account with this name already exists in Chart of Accounts.');
            }

            DB::beginTransaction();
            try {
                // Update income head
                $incomeHead->update($validated);

                // Update the linked chart of account
                if ($incomeHead->chartOfAccount) {
                    $incomeHead->chartOfAccount->update([
                        'name' => $validated['name'],
                    ]);
                }

                DB::commit();

                return redirect()
                    ->route('income-heads.index')
                    ->with('success', 'Income head updated successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in income head update: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Income head update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating income head: ' . $e->getMessage());
        }
    }

    public function destroy(IncomeHead $incomeHead)
    {
        try {
            DB::beginTransaction();

            // Check if income head has any other incomes
            if ($incomeHead->otherIncomes()->count() > 0) {
                throw new \Exception('Cannot delete income head that has associated other incomes.');
            }

            // Delete the linked chart of account
            if ($incomeHead->chartOfAccount) {
                $incomeHead->chartOfAccount->delete();
            }

            // Delete the income head
            $incomeHead->delete();

            DB::commit();

            return redirect()
                ->route('income-heads.index')
                ->with('success', 'Income head deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting income head: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error deleting income head: ' . $e->getMessage());
        }
    }
}
