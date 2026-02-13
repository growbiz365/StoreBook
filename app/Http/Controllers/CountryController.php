<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {

        $countries = Country::query();

        if ($request->has('search')) {
            $countries = $countries->where('country_name', 'like', '%' . $request->search . '%')
                ->orWhere('country_code', 'like', '%' . $request->search . '%');
        }

        $countries = $countries->paginate(10);

        return view('countries.index', compact('countries'));
    }

    public function create()
    {
        return view('countries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:2',
        ]);

        Country::create($request->all());

        return redirect()->route('countries.index')->with('success', 'Country created successfully');
    }

    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:2',
        ]);

        $country->update($request->all());

        return redirect()->route('countries.index')->with('success', 'Country updated successfully');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('countries.index')->with('success', 'Country deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $countryId = $request->input('id');

        if ($countryId) {
            // If a specific ID is passed, return that country
            $country = Country::find($countryId);
            return response()->json([
                'id' => $country->id,
                'name' => $country->country_name,
            ]);
        } elseif ($query) {
            // If query is provided, search countries based on name or code
            $countries = Country::where('country_name', 'like', '%' . $query . '%')
                ->orWhere('country_code', 'like', '%' . $query . '%')
                ->get(['id', 'country_name']);

            // Rename country_name to name in the response
            $countries = $countries->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->country_name,
                ];
            });


            return response()->json($countries);
        }

        return response()->json([]);
    }
}
