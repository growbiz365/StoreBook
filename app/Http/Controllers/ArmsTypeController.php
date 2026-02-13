<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArmsType;
use Illuminate\Support\Facades\Validator;

class ArmsTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = ArmsType::where('business_id', $businessId);

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('arm_type', 'like', '%' . $request->search . '%');
        }

        $armsTypes = $query->latest()->paginate(10)->withQueryString();
        return view('arms_types.index', compact('armsTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        return view('arms_types.create', compact('businessId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $validator = Validator::make($request->all(), [
            'arm_type' => 'required|string|max:255|unique:arms_types,arm_type,NULL,id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-types.create')->withErrors($validator)->withInput();
        }
        ArmsType::create([
            'arm_type' => $request->arm_type,
            'business_id' => $businessId,
            'status' => $request->status,
        ]);
        return redirect()->route('arms-types.index')->with('success', 'Arms Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $armsType = ArmsType::where('business_id', $businessId)->findOrFail($id);
        return view('arms_types.show', compact('armsType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $businessId = session('active_business');
        $armsType = ArmsType::where('business_id', $businessId)->findOrFail($id);
        return view('arms_types.edit', compact('armsType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $armsType = ArmsType::where('business_id', $businessId)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'arm_type' => 'required|string|max:255|unique:arms_types,arm_type,'.$id.',id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-types.edit', $id)->withErrors($validator)->withInput();
        }
        $armsType->update([
            'arm_type' => $request->arm_type,
            'status' => $request->status,
        ]);
        return redirect()->route('arms-types.index')->with('success', 'Arms Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    
}
