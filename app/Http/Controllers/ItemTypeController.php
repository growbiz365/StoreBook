<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemType;
use Illuminate\Support\Facades\Validator;

class ItemTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = ItemType::where('business_id', $businessId);

        // Apply status filter if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('item_type', 'like', '%' . $request->search . '%');
        }

        $itemTypes = $query->latest()->paginate(10)->withQueryString();
        return view('item_types.index', compact('itemTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessId = session('active_business');
        return view('item_types.create', compact('businessId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $businessId = session('active_business');
        $validator = Validator::make($request->all(), [
            'item_type' => 'required|string|max:255|unique:item_types,item_type,NULL,id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('item-types.create')->withErrors($validator)->withInput();
        }
        ItemType::create([
            'item_type' => $request->item_type,
            'business_id' => $businessId,
            'status'=>$request->status,
        ]);
        return redirect()->route('item-types.index')->with('success', 'Item Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = session('active_business');
        $itemType = ItemType::where('business_id', $businessId)->findOrFail($id);
        return view('item_types.show', compact('itemType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $businessId = session('active_business');
        $itemType = ItemType::where('business_id', $businessId)->findOrFail($id);
        return view('item_types.edit', compact('itemType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = session('active_business');
        $itemType = ItemType::where('business_id', $businessId)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'item_type' => 'required|string|max:255|unique:item_types,item_type,'.$id.',id,business_id,'.$businessId,
            'status' => 'nullable|in:1,0'
        ]);
        if ($validator->fails()) {
            return redirect()->route('item-types.edit', $id)->withErrors($validator)->withInput();
        }
        $itemType->update([
            'item_type' => $request->item_type,
            'status'=>$request->status,
        ]);
        return redirect()->route('item-types.index')->with('success', 'Item Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $businessId = session('active_business');
        $itemType = ItemType::where('business_id', $businessId)->findOrFail($id);
        $itemType->delete();
        return redirect()->route('item-types.index')->with('success', 'Item Type deleted successfully.');
    }
}
