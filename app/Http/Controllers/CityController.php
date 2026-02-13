<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::with('country');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('country', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $cities = $query->paginate(10);
        return view('cities.index', compact('cities'));
    }

    public function create()
    {
        return view('cities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        City::create($request->all());

        return redirect()->route('cities.index')
            ->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        return view('cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $city->update($request->all());

        return redirect()->route('cities.index')
            ->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('cities.index')
            ->with('success', 'City deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $cityId = $request->input('id'); // Changed from city_id to id to match combobox

        // Return single city for edit form
        if ($cityId) {
            $city = City::with('country')->find($cityId);
            if (!$city) {
                return response()->json([]);
            }
            return response()->json([
                'id' => $city->id,
                'name' => $city->name . ' (' . $city->country->country_name . ')'
            ]);
        }

        // Search cities for dropdown
        if ($query) {
            $cities = City::where('name', 'like', '%' . $query . '%')
                ->with('country')
                ->take(10)
                ->get()
                ->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name . ' (' . $city->country->country_name . ')'
                    ];
                });
            return response()->json($cities);
        }

        return response()->json([]);
    }
}
