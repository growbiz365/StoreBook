<?php

namespace App\Http\Controllers;

use App\Models\ArmHistory;
use App\Models\Arm;
use App\Models\Business;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArmsHistoryController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->back()->with('error', 'No active business selected.');
        }

        // Get filter parameters
        $armId = $request->get('arm_id');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $action = $request->get('action');
        $perPage = $request->get('per_page', 25);

        // Get all arms for filter dropdown
        $arms = Arm::where('business_id', $businessId)
            ->orderBy('arm_title')
            ->get();

        // Build the query for arms history
        $query = ArmHistory::with(['arm', 'arm.armType', 'user'])
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

        if ($action) {
            $query->where('action', $action);
        }

        // Get paginated results
        $historyEntries = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Get summary statistics
        $summary = $this->getSummary($businessId, $armId, $dateFrom, $dateTo, $action);

        // Get business information
        $business = Business::find($businessId);

        return view('arms.history', compact(
            'historyEntries',
            'arms',
            'armId',
            'dateFrom',
            'dateTo',
            'action',
            'perPage',
            'summary',
            'business'
        ));
    }

    private function getSummary($businessId, $armId = null, $dateFrom = null, $dateTo = null, $action = null)
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

        if ($action) {
            $query->where('action', $action);
        }

        $summary = $query->selectRaw('
            COUNT(*) as total_entries,
            COUNT(DISTINCT arm_id) as unique_arms,
            COUNT(DISTINCT user_id) as unique_users,
            SUM(CASE WHEN action IN ("purchase", "opening") THEN 1 ELSE 0 END) as stock_in_actions,
            SUM(CASE WHEN action IN ("sale", "transfer") THEN 1 ELSE 0 END) as stock_out_actions,
            SUM(CASE WHEN action IN ("repair", "decommission", "price_adjustment", "edit", "delete") THEN 1 ELSE 0 END) as status_change_actions
        ')->first();

        return [
            'total_entries' => $summary->total_entries ?? 0,
            'unique_arms' => $summary->unique_arms ?? 0,
            'unique_users' => $summary->unique_users ?? 0,
            'stock_in_actions' => $summary->stock_in_actions ?? 0,
            'stock_out_actions' => $summary->stock_out_actions ?? 0,
            'status_change_actions' => $summary->status_change_actions ?? 0
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
        $action = $request->get('action');

        // Build the query
        $query = ArmHistory::with(['arm', 'arm.armType', 'user'])
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

        if ($action) {
            $query->where('action', $action);
        }

        $historyEntries = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Get business information for filename
        $business = Business::find($businessId);
        $businessName = $business ? $business->name : 'Business';
        
        $filename = $businessName . '_arms_history_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($historyEntries) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date',
                'Arm Name',
                'Arm Type',
                'Action',
                'Price',
                'Remarks',
                'User',
                'IP Address',
                'User Agent',
                'Created At'
            ]);

            // CSV data
            foreach ($historyEntries as $entry) {
                // Format changes for CSV
                $changes = '';
                if ($entry->old_values && $entry->new_values && is_array($entry->old_values) && is_array($entry->new_values)) {
                    $changeItems = [];
                    foreach ($entry->old_values as $field => $oldValue) {
                        if (isset($entry->new_values[$field]) && $entry->new_values[$field] !== $oldValue && $field !== 'updated_at') {
                            $fieldName = ucfirst(str_replace('_', ' ', $field));
                            $changeItems[] = "{$fieldName}: {$oldValue} → {$entry->new_values[$field]}";
                        }
                    }
                    $changes = implode('; ', $changeItems);
                }

                fputcsv($file, [
                    $entry->transaction_date,
                    $entry->arm->arm_title ?? 'N/A',
                    $entry->arm->armType->arm_type ?? 'N/A',
                    ucfirst($entry->action),
                    $entry->price ?? 0,
                    $changes ?: 'No changes recorded',
                    $entry->remarks ?? 'N/A',
                    $entry->user->name ?? 'N/A',
                    $entry->ip_address ?? 'N/A',
                    $entry->user_agent ?? 'N/A',
                    $entry->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
