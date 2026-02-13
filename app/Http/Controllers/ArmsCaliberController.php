<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArmsCaliber;
use Illuminate\Support\Facades\Validator;

class ArmsCaliberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = ArmsCaliber::where('business_id', $businessId);

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('arm_caliber', 'like', '%' . $request->search . '%');
        }

        $armsCalibers = $query->latest()->paginate(10)->withQueryString();
        return view('arms_calibers.index', compact('armsCalibers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        return view('arms_calibers.create', compact('businessId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $validator = Validator::make($request->all(), [
            'arm_caliber' => 'required|string|max:255|unique:arms_calibers,arm_caliber,NULL,id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-calibers.create')->withErrors($validator)->withInput();
        }
        ArmsCaliber::create([
            'arm_caliber' => $request->arm_caliber,
            'business_id' => $businessId,
            'status'=>$request->status,
        ]);
        return redirect()->route('arms-calibers.index')->with('success', 'Arms Caliber created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $armsCaliber = ArmsCaliber::where('business_id', $businessId)->findOrFail($id);
        return view('arms_calibers.show', compact('armsCaliber'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $businessId = session('active_business');
        $armsCaliber = ArmsCaliber::where('business_id', $businessId)->findOrFail($id);
        return view('arms_calibers.edit', compact('armsCaliber'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $armsCaliber = ArmsCaliber::where('business_id', $businessId)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'arm_caliber' => 'required|string|max:255|unique:arms_calibers,arm_caliber,'.$id.',id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-calibers.edit', $id)->withErrors($validator)->withInput();
        }
        $armsCaliber->update([
            'arm_caliber' => $request->arm_caliber,
            'status' => $request->status,
        ]);
        return redirect()->route('arms-calibers.index')->with('success', 'Arms Caliber updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
   
}
