<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArmsMake;
use Illuminate\Support\Facades\Validator;

class ArmsMakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = ArmsMake::where('business_id', $businessId);

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('arm_make', 'like', '%' . $request->search . '%');
        }

        $armsMakes = $query->latest()->paginate(10)->withQueryString();
        return view('arms_makes.index', compact('armsMakes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        return view('arms_makes.create', compact('businessId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $validator = Validator::make($request->all(), [
            'arm_make' => 'required|string|max:255|unique:arms_makes,arm_make,NULL,id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-makes.create')->withErrors($validator)->withInput();
        }
        ArmsMake::create([
            'arm_make' => $request->arm_make,
            'business_id' => $businessId,
            'status'=>$request->status,
        ]);
        return redirect()->route('arms-makes.index')->with('success', 'Arms Make created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $armsMake = ArmsMake::where('business_id', $businessId)->findOrFail($id);
        return view('arms_makes.show', compact('armsMake'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $businessId = session('active_business');
        $armsMake = ArmsMake::where('business_id', $businessId)->findOrFail($id);
        return view('arms_makes.edit', compact('armsMake'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $armsMake = ArmsMake::where('business_id', $businessId)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'arm_make' => 'required|string|max:255|unique:arms_makes,arm_make,'.$id.',id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('arms-makes.edit', $id)->withErrors($validator)->withInput();
        }
        $armsMake->update([
            'arm_make' => $request->arm_make,
            'status'=>$request->status,
        ]);
        return redirect()->route('arms-makes.index')->with('success', 'Arms Make updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    
}
