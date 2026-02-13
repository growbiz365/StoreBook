<?php

namespace App\Http\Controllers;

use App\Models\Timezone;
use Illuminate\Http\Request;

class TimezoneController extends Controller
{
    public function index(Request $request)
    {
        $timezones = Timezone::query();

        // Search functionality
        if ($request->has('search')) {
            $timezones = $timezones->where('timezone_name', 'like', '%' . $request->search . '%')
                ->orWhere('utc_offset', 'like', '%' . $request->search . '%');
        }

        $timezones = $timezones->paginate(10);

        return view('timezones.index', compact('timezones'));
    }

    public function create()
    {
        return view('timezones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'timezone_name' => 'required|string|max:255',
            'utc_offset' => 'required|numeric|between:-12.00,14.00', // Adjust the range as needed
        ]);

        Timezone::create($request->all());

        return redirect()->route('timezones.index')->with('success', 'Timezone created successfully');
    }

    public function edit(Timezone $timezone)
    {
        return view('timezones.edit', compact('timezone'));
    }

    public function update(Request $request, Timezone $timezone)
    {
        $request->validate([
            'timezone_name' => 'required|string|max:255',
            'utc_offset' => 'required|numeric|between:-12.00,14.00', // Adjust the range as needed
        ]);

        $timezone->update($request->all());

        return redirect()->route('timezones.index')->with('success', 'Timezone updated successfully');
    }

    public function destroy(Timezone $timezone)
    {
        $timezone->delete();

        return redirect()->route('timezones.index')->with('success', 'Timezone deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $timezoneId = $request->input('id');

        if ($timezoneId) {
            // If a specific ID is passed, return that timezone
            $timezone = Timezone::find($timezoneId);
            return response()->json([
                'id' => $timezone->id,
                'name' => $timezone->timezone_name,
            ]);
        } elseif ($query) {
            // If query is provided, search timezones based on name
            $timezones = Timezone::where('timezone_name', 'like', '%' . $query . '%')
                ->get(['id', 'timezone_name']);


            // Rename timezone_name to name in the response
            $timezones = $timezones->map(function ($timezone) {
                return [
                    'id' => $timezone->id,
                    'name' => $timezone->timezone_name, // Rename here
                ];
            });

            return response()->json($timezones);
        }

        return response()->json([]);
    }
}
