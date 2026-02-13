<?php

namespace App\Http\Controllers;

use App\Models\Arm;
use App\Models\ArmHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArmsStockLedgerController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')->with('error', 'No active business selected.');
        }

        // Get filter parameters
        $armId = $request->get('arm_id');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type');
        $perPage = $request->get('per_page', 25);

        // Get all arms for filter dropdown
        $arms = Arm::where('business_id', $businessId)
            ->orderBy('arm_title')
            ->get();

        // Build the query for arms stock ledger
        $query = ArmHistory::with(['arm'])
            ->where('business_id', $businessId);

        // Apply filters
        if ($armId) {
            $query->where('arm_id', $armId);
        }

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        if ($transactionType) {
            $query->where('action', $transactionType);
        }

        // Get paginated results
        $ledgerEntries = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Calculate running balance for each entry
        $runningBalance = 0;
        $ledgerEntries->getCollection()->transform(function ($entry) use (&$runningBalance) {
            // Stock in transactions: purchase, opening
            if (in_array($entry->action, ['opening', 'purchase'])) {
                $runningBalance += 1; // Each arm is counted as 1
            } 
            // Stock out transactions: sale, transfer
            else if (in_array($entry->action, ['sale', 'transfer'])) {
                $runningBalance -= 1; // Each arm is counted as 1
            }
            // Other transactions: repair, decommission, price_adjustment, edit, delete
            else {
                // These don't affect quantity, just status changes
                $runningBalance += 0;
            }
            $entry->running_balance = $runningBalance;
            return $entry;
        });

        // Get summary statistics
        $summary = $this->getSummary($businessId, $armId, $dateFrom, $dateTo);

        // Get business information
        $business = \App\Models\Business::find($businessId);

        return view('arms.stock-ledger', compact(
            'ledgerEntries',
            'arms',
            'armId',
            'dateFrom',
            'dateTo',
            'transactionType',
            'perPage',
            'summary',
            'business'
        ));
    }

    private function getSummary($businessId, $armId = null, $dateFrom = null, $dateTo = null)
    {
        $query = ArmHistory::where('business_id', $businessId);

        if ($armId) {
            $query->where('arm_id', $armId);
        }

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        $summary = $query->selectRaw('
            SUM(CASE WHEN action IN ("purchase", "opening") THEN 1 ELSE 0 END) as total_in,
            SUM(CASE WHEN action IN ("sale", "transfer") THEN 1 ELSE 0 END) as total_out,
            COUNT(*) as total_transactions
        ')->first();

        // Get current stock levels
        $currentStockQuery = ArmHistory::where('business_id', $businessId);
        if ($armId) {
            $currentStockQuery->where('arm_id', $armId);
        }

        $currentStock = $currentStockQuery->selectRaw('
            SUM(CASE WHEN action IN ("purchase", "opening") THEN 1 ELSE 0 END) - 
            SUM(CASE WHEN action IN ("sale", "transfer") THEN 1 ELSE 0 END) as current_stock
        ')->first();

        return [
            'total_in' => $summary->total_in ?? 0,
            'total_out' => $summary->total_out ?? 0,
            'total_transactions' => $summary->total_transactions ?? 0,
            'current_stock' => $currentStock->current_stock ?? 0,
            'net_movement' => ($summary->total_in ?? 0) - ($summary->total_out ?? 0)
        ];
    }

    public function export(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->back()->with('error', 'No active business selected.');
        }

        // Get filter parameters (same as index)
        $armId = $request->get('arm_id');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type');

        // Build the query
        $query = ArmHistory::with(['arm'])
            ->where('business_id', $businessId);

        if ($armId) {
            $query->where('arm_id', $armId);
        }

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        if ($transactionType) {
            $query->where('action', $transactionType);
        }

        $ledgerEntries = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Calculate running balance
        $runningBalance = 0;
        $ledgerEntries->transform(function ($entry) use (&$runningBalance) {
            // Stock in transactions: purchase, opening
            if (in_array($entry->action, ['opening', 'purchase'])) {
                $runningBalance += 1; // Each arm is counted as 1
            } 
            // Stock out transactions: sale, transfer
            else if (in_array($entry->action, ['sale', 'transfer'])) {
                $runningBalance -= 1; // Each arm is counted as 1
            }
            // Other transactions: repair, decommission, price_adjustment, edit, delete
            else {
                // These don't affect quantity, just status changes
                $runningBalance += 0;
            }
            $entry->running_balance = $runningBalance;
            return $entry;
        });

        // Get business information for filename
        $business = \App\Models\Business::find($businessId);
        $businessName = $business ? $business->name : 'Business';
        
        $filename = $businessName . '_arms_stock_ledger_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($ledgerEntries) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date',
                'Arm Name',
                'Arm Type',
                'Transaction Type',
                'Quantity',
                'Unit Cost',
                'Total Cost',
                'Reference',
                'Description',
                'Running Balance'
            ]);

            // CSV data
            foreach ($ledgerEntries as $entry) {
                fputcsv($file, [
                    $entry->transaction_date,
                    $entry->arm->arm_title ?? 'N/A',
                    $entry->arm->armType->arm_type ?? 'N/A',
                    ucfirst($entry->action),
                    1, // Each arm is counted as 1
                    $entry->price ?? 0,
                    $entry->price ?? 0, // Total cost is same as unit cost for arms
                    $entry->action, // Use action as reference
                    $entry->remarks ?? 'N/A',
                    $entry->running_balance
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
