<?php

namespace App\Http\Controllers;

use App\Models\GeneralBatch;
use App\Models\GeneralItem;
use Illuminate\Http\Request;

class GeneralBatchController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');

        $query = GeneralBatch::with('item')
            ->where('business_id', $businessId);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('received_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('received_date', '<=', $request->to_date);
        }



        $batches = $query->latest('received_date')->paginate(15)->withQueryString();

        $items = GeneralItem::where('business_id', $businessId)->orderBy('item_name')->get();
        return view('general_batches.index', compact('batches', 'items'));
    }

    public function show(GeneralBatch $generalBatch)
    {
        $this->authorizeBatch($generalBatch);
        $generalBatch->load('item');
        return view('general_batches.show', compact('generalBatch'));
    }

    public function edit(GeneralBatch $generalBatch)
    {
        $this->authorizeBatch($generalBatch);
        return view('general_batches.edit', compact('generalBatch'));
    }

    public function update(Request $request, GeneralBatch $generalBatch)
    {
        $this->authorizeBatch($generalBatch);

        $validated = $request->validate([
            'qty_received' => 'required|integer|min:0',
            'qty_remaining' => 'required|integer|min:0|max:' . max(0, (int)$request->qty_received),
            'unit_cost' => 'required|numeric|min:0',
            'received_date' => 'required|date',
        ]);

        $validated['total_cost'] = ($validated['qty_received'] ?? $generalBatch->qty_received) * ($validated['unit_cost'] ?? $generalBatch->unit_cost);

        $generalBatch->update($validated);

        return redirect()->route('general-batches.index')->with('success', 'Batch updated successfully.');
    }

    private function authorizeBatch(GeneralBatch $batch): void
    {
        if ($batch->business_id !== session('active_business')) {
            abort(403);
        }
    }
}


