<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArmsCategory;
use Illuminate\Support\Facades\Validator;

class ArmsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = ArmsCategory::where('business_id', $businessId);

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('arm_category', 'like', '%' . $request->search . '%');
        }

        $armsCategories = $query->latest()->paginate(10)->withQueryString();
        return view('arms_categories.index', compact('armsCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        return view('arms_categories.create', compact('businessId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $validator = Validator::make($request->all(), [
            'arm_category' => 'required|string|max:255|unique:arms_categories,arm_category,NULL,id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-categories.create')->withErrors($validator)->withInput();
        }
        ArmsCategory::create([
            'arm_category' => $request->arm_category,
            'business_id' => $businessId,
            'status' => $request->status,
        ]);
        return redirect()->route('arms-categories.index')->with('success', 'Arms Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $armsCategory = ArmsCategory::where('business_id', $businessId)->findOrFail($id);
        return view('arms_categories.show', compact('armsCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $businessId = session('active_business');
        $armsCategory = ArmsCategory::where('business_id', $businessId)->findOrFail($id);
        return view('arms_categories.edit', compact('armsCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $armsCategory = ArmsCategory::where('business_id', $businessId)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'arm_category' => 'required|string|max:255|unique:arms_categories,arm_category,'.$id.',id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-categories.edit', $id)->withErrors($validator)->withInput();
        }
        $armsCategory->update([
            'arm_category' => $request->arm_category,
            'status' => $request->status,
        ]);
        return redirect()->route('arms-categories.index')->with('success', 'Arms Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    
}
