<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArmsCondition;
use Illuminate\Support\Facades\Validator;

class ArmsConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = ArmsCondition::where('business_id', $businessId);

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('arm_condition', 'like', '%' . $request->search . '%');
        }

        $armsConditions = $query->latest()->paginate(10)->withQueryString();
        return view('arms_conditions.index', compact('armsConditions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        return view('arms_conditions.create', compact('businessId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $validator = Validator::make($request->all(), [
            'arm_condition' => 'required|string|max:255|unique:arms_conditions,arm_condition,NULL,id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-conditions.create')->withErrors($validator)->withInput();
        }
        ArmsCondition::create([
            'arm_condition' => $request->arm_condition,
            'business_id' => $businessId,
            'status'=>$request->status,
        ]);
        return redirect()->route('arms-conditions.index')->with('success', 'Arms Condition created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $armsCondition = ArmsCondition::where('business_id', $businessId)->findOrFail($id);
        return view('arms_conditions.show', compact('armsCondition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $businessId = session('active_business');
        $armsCondition = ArmsCondition::where('business_id', $businessId)->findOrFail($id);
        return view('arms_conditions.edit', compact('armsCondition'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $armsCondition = ArmsCondition::where('business_id', $businessId)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'arm_condition' => 'required|string|max:255|unique:arms_conditions,arm_condition,'.$id.',id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-conditions.edit', $id)->withErrors($validator)->withInput();
        }
        $armsCondition->update([
            'arm_condition' => $request->arm_condition,
            'status' => $request->status,
        ]);
        return redirect()->route('arms-conditions.index')->with('success', 'Arms Condition updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
   
}
