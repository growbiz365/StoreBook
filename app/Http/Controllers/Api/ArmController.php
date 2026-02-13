<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Arm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArmController extends Controller
{
    /**
     * Display a listing of arms with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $businessId = session('active_business');
            
            if (!$businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select a business first'
                ], 400);
            }
            

            $query = Arm::with(['armType', 'armCaliber', 'armCategory', 'armCondition', 'armMake'])
                ->where('business_id', $businessId);
            
            // Apply status filter if provided, otherwise default to available
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            } else {
                $query->where('status', 'available'); // Default to available arms for sale
            }

            // Exclude already selected arms if provided
            if ($request->filled('exclude_ids')) {
                $excludeIds = is_array($request->exclude_ids) ? $request->exclude_ids : explode(',', $request->exclude_ids);
                $excludeIds = array_filter($excludeIds, function($id) { return !empty($id) && is_numeric($id); });
                if (!empty($excludeIds)) {
                    $query->whereNotIn('id', $excludeIds);
                }
            }

            // Apply search filter if provided
            if ($request->filled('q')) {
                $searchTerm = $request->get('q');
                $query                ->where(function($q) use ($searchTerm) {
                    $q->where('arm_title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('serial_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('make', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('armCaliber', function($q) use ($searchTerm) {
                          $q->where('arm_caliber', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('armType', function($q) use ($searchTerm) {
                          $q->where('arm_type', 'like', '%' . $searchTerm . '%');
                      });
                });
            }

            // Get pagination parameters
            $perPage = $request->get('limit', 15);
            $page = $request->get('page', 1);

            $arms = $query->orderBy('arm_title')
                         ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $arms->items(),
                'meta' => [
                    'current_page' => $arms->currentPage(),
                    'last_page' => $arms->lastPage(),
                    'per_page' => $arms->perPage(),
                    'total' => $arms->total(),
                    'from' => $arms->firstItem(),
                    'to' => $arms->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load arms',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search arms with pagination
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $businessId = session('active_business');
            
            if (!$businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select a business first'
                ], 400);
            }

            $searchTerm = $request->get('q', '');
            
            if (empty($searchTerm)) {
                return response()->json([
                    'error' => 'Search term is required',
                    'message' => 'Please provide a search term'
                ], 400);
            }

            $query = Arm::with(['armType', 'armCaliber', 'armCategory', 'armCondition', 'armMake'])
                ->where('business_id', $businessId);
            
            // Apply status filter if provided, otherwise default to available
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            } else {
                $query->where('status', 'available'); // Default to available arms for sale
            }

            // Exclude already selected arms if provided
            if ($request->filled('exclude_ids')) {
                $excludeIds = is_array($request->exclude_ids) ? $request->exclude_ids : explode(',', $request->exclude_ids);
                $excludeIds = array_filter($excludeIds, function($id) { return !empty($id) && is_numeric($id); });
                if (!empty($excludeIds)) {
                    $query->whereNotIn('id', $excludeIds);
                }
            }
            
            $query->where(function($q) use ($searchTerm) {
                    $q->where('arm_title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('serial_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('make', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('armCaliber', function($q) use ($searchTerm) {
                          $q->where('arm_caliber', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('armType', function($q) use ($searchTerm) {
                          $q->where('arm_type', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('armCategory', function($q) use ($searchTerm) {
                          $q->where('arm_category', 'like', '%' . $searchTerm . '%');
                      });
                });

            // Get pagination parameters
            $perPage = $request->get('limit', 15);
            $page = $request->get('page', 1);

            $arms = $query->orderBy('arm_title')
                         ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $arms->items(),
                'meta' => [
                    'current_page' => $arms->currentPage(),
                    'last_page' => $arms->lastPage(),
                    'per_page' => $arms->perPage(),
                    'total' => $arms->total(),
                    'from' => $arms->firstItem(),
                    'to' => $arms->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a serial number already exists for the current business
     */
    public function checkSerial(Request $request): JsonResponse
    {
        try {
            $businessId = session('active_business');
            
            if (!$businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select a business first'
                ], 400);
            }

            $serialNo = $request->get('serial_no');
            
            if (empty($serialNo)) {
                return response()->json([
                    'error' => 'Serial number is required',
                    'message' => 'Please provide a serial number to check'
                ], 400);
            }

            $exists = Arm::where('business_id', $businessId)
                        ->where('serial_no', $serialNo)
                        ->exists();

            return response()->json([
                'exists' => $exists,
                'serial_no' => $serialNo,
                'business_id' => $businessId
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check serial number',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
