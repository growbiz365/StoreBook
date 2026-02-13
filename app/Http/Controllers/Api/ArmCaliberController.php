<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArmsCaliber;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArmCaliberController extends Controller
{
    /**
     * Search arm calibers with pagination
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'q' => 'required|string|min:2',
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1|max:100'
            ]);

            $businessId = session('active_business');
            
            if (!$businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select an active business'
                ], 400);
            }

            $searchTerm = $request->get('q');
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);

            $query = ArmsCaliber::where('business_id', $businessId)
                ->where('arm_caliber', 'like', "%{$searchTerm}%");
            
            $query->orderBy('arm_caliber');

            $items = $query->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'data' => $items->items(),
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('ArmCaliber Search API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Search failed',
                'message' => 'An error occurred while searching arm calibers',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get paginated arm calibers
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1|max:100'
            ]);

            $businessId = session('active_business');
            
            if (!$businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select an active business'
                ], 400);
            }

            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);

            $query = ArmsCaliber::where('business_id', $businessId)
                ->orderBy('arm_caliber');

            $items = $query->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'data' => $items->items(),
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('ArmCaliber API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load arm calibers',
                'message' => 'An error occurred while loading arm calibers'
            ], 500);
        }
    }
}
