<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $currencies = Currency::query();

        // Search functionality
        if ($request->has('search')) {
            $currencies = $currencies->where('currency_name', 'like', '%' . $request->search . '%')
                ->orWhere('currency_code', 'like', '%' . $request->search . '%');
        }

        $currencies = $currencies->paginate(10);

        return view('currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|max:3|unique:currencies',
            'currency_name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
        ]);

        Currency::create($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency created successfully');
    }

    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'currency_code' => 'required|string|max:3|unique:currencies,currency_code,' . $currency->id,
            'currency_name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
        ]);

        $currency->update($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('currencies.index')->with('success', 'Currency deleted successfully');
    }

    // Search for currencies using AJAX
    public function search(Request $request)
    {
        $query = $request->input('query');
        $currencyId = $request->input('id');

        if ($currencyId) {
            // If there's an ID, return the specific currency
            $currency = Currency::find($currencyId);
            return response()->json([
                'id' => $currency->id,
                'name' => $currency->currency_name,  // Rename currency_name to name
                'currency_code' => $currency->currency_code
            ]);
        } elseif ($query) {
            // Search currencies based on the query
            $currencies = Currency::where('currency_name', 'like', '%' . $query . '%')
                ->orWhere('currency_code', 'like', '%' . $query . '%')
                ->get(['id', 'currency_name', 'currency_code']);

            // Rename currency_name to name in the response
            $currencies = $currencies->map(function ($currency) {
                return [
                    'id' => $currency->id,
                    'name' => $currency->currency_name, // Rename here
                    'currency_code' => $currency->currency_code
                ];
            });

            return response()->json($currencies);
        }

        return response()->json([]);
    }
}
