<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeneralItemController extends Controller
{
    /**
     * Search general items with pagination
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

            $query = GeneralItem::where('business_id', $businessId)
                ->where(function($q) use ($searchTerm) {
                    $q->where('item_name', 'like', "%{$searchTerm}%")
                      ->orWhere('item_code', 'like', "%{$searchTerm}%");
                });

            // Exclude already selected general items if provided
            if ($request->filled('exclude_ids')) {
                $excludeIds = is_array($request->exclude_ids) ? $request->exclude_ids : explode(',', $request->exclude_ids);
                $excludeIds = array_filter($excludeIds, function($id) { return !empty($id) && is_numeric($id); });
                if (!empty($excludeIds)) {
                    $query->whereNotIn('id', $excludeIds);
                }
            }

            $query->orderBy('item_name');

            $items = $query->with(['batches' => function($q) {
                $q->where('status', 'active');
            }])->paginate($limit, ['*'], 'page', $page);

            // Add available stock to each item (including zero stock items)
            $items->getCollection()->transform(function ($item) {
                $item->available_stock = $item->batches->where('status', 'active')->sum('qty_remaining');
                return $item;
            });

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
            \Log::error('GeneralItem Search API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Search failed',
                'message' => 'An error occurred while searching items',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get paginated general items
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

            $query = GeneralItem::where('business_id', $businessId);

            // Exclude already selected general items if provided
            if ($request->filled('exclude_ids')) {
                $excludeIds = is_array($request->exclude_ids) ? $request->exclude_ids : explode(',', $request->exclude_ids);
                $excludeIds = array_filter($excludeIds, function($id) { return !empty($id) && is_numeric($id); });
                if (!empty($excludeIds)) {
                    $query->whereNotIn('id', $excludeIds);
                }
            }

            $query->orderBy('item_name');

            $items = $query->with(['batches' => function($q) {
                $q->where('status', 'active');
            }])->paginate($limit, ['*'], 'page', $page);

            // Add available stock to each item (including zero stock items)
            $items->getCollection()->transform(function ($item) {
                $item->available_stock = $item->batches->where('status', 'active')->sum('qty_remaining');
                return $item;
            });

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
            \Log::error('GeneralItem API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load items',
                'message' => 'An error occurred while loading items'
            ], 500);
        }
    }

    /**
     * Get a specific general item
     */
    public function show(GeneralItem $generalItem): JsonResponse
    {
        $businessId = session('active_business');
        
        if ($generalItem->business_id !== $businessId) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json($generalItem);
    }
}
