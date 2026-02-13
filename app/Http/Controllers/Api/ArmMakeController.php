<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArmsMake;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArmMakeController extends Controller
{
    /**
     * Search arm makes with pagination
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

            $query = ArmsMake::where('business_id', $businessId)
                ->where('arm_make', 'like', "%{$searchTerm}%");
            
            $query->orderBy('arm_make');

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
            \Log::error('ArmMake Search API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Search failed',
                'message' => 'An error occurred while searching arm makes',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get paginated arm makes
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

            $query = ArmsMake::where('business_id', $businessId)
                ->orderBy('arm_make');

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
            \Log::error('ArmMake API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load arm makes',
                'message' => 'An error occurred while loading arm makes'
            ], 500);
        }
    }
}
