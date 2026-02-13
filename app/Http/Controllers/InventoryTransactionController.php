<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\GeneralItem;
use App\Models\GeneralBatch;
use Illuminate\Http\Request;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');
        $query = InventoryTransaction::with(['item', 'batch'])
            ->where('business_id', $businessId);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }
        if ($request->filled('tx_type')) {
            $query->where('tx_type', $request->tx_type);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $transactions = $query->orderByDesc('date')->paginate(15)->withQueryString();
        $items = GeneralItem::where('business_id', $businessId)->orderBy('item_name')->get();
        $batches = GeneralBatch::where('business_id', $businessId)->orderByDesc('received_date')->get();

        return view('inventory_transactions.index', compact('transactions', 'items', 'batches'));
    }

    public function show(InventoryTransaction $inventoryTransaction)
    {
        $this->authorizeTx($inventoryTransaction);
        $inventoryTransaction->load(['item', 'batch']);
        return view('inventory_transactions.show', compact('inventoryTransaction'));
    }

    public function edit(InventoryTransaction $inventoryTransaction)
    {
        $this->authorizeTx($inventoryTransaction);
        return view('inventory_transactions.edit', compact('inventoryTransaction'));
    }

    public function update(Request $request, InventoryTransaction $inventoryTransaction)
    {
        $this->authorizeTx($inventoryTransaction);
        $validated = $request->validate([
            'tx_type' => 'required|string',
            'qty' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $validated['total_cost'] = $validated['qty'] * $validated['unit_cost'];
        $inventoryTransaction->update($validated);
        return redirect()->route('inventory-transactions.index')->with('success', 'Inventory transaction updated successfully.');
    }

    private function authorizeTx(InventoryTransaction $tx): void
    {
        if ($tx->business_id !== session('active_business')) {
            abort(403);
        }
    }
}


