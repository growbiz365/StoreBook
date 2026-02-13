<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to view activity logs.');
        }

        $business = Business::with('timezone')->find($businessId);
        
        // Get business timezone or default - handle null cases properly
        $timezone = 'Asia/Karachi'; // Default
        if ($business && $business->timezone && !empty($business->timezone->timezone_name)) {
            $timezone = $business->timezone->timezone_name;
        }
        
        // Date filters (default to current month)
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Filters
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 25);
        
        // Query
        $query = ActivityLog::where('business_id', $businessId)
            ->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ])
            ->with(['user', 'business']);
        
        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }
        
        // Get logs with pagination
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Convert logs timestamps to business timezone - ensure timezone is valid
        $logs->getCollection()->transform(function ($log) use ($timezone) {
            if ($log->created_at && !empty($timezone)) {
                try {
                    $log->created_at_tz = Carbon::parse($log->created_at)->setTimezone($timezone);
                } catch (\Exception $e) {
                    // If timezone is invalid, use default
                    $log->created_at_tz = Carbon::parse($log->created_at)->setTimezone('Asia/Karachi');
                }
            } else {
                $log->created_at_tz = $log->created_at;
            }
            return $log;
        });
        
        return view('activity-logs.index', compact(
            'business',
            'logs',
            'dateFrom',
            'dateTo',
            'search',
            'perPage',
            'timezone'
        ));
    }

    public function export(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business.');
        }

        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        
        // Query
        $query = ActivityLog::where('business_id', $businessId)
            ->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ])
            ->with(['user', 'business']);
        
        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Create CSV
        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Date', 'Activity Details', 'Description', 'User', 'IP Address']);
            
            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('d M Y h:i A'),
                    $log->log_name ? ucfirst($log->log_name) : 'System',
                    $log->description,
                    $log->user ? $log->user->name : 'System',
                    $log->ip_address ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
