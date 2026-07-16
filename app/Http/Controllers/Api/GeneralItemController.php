<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralItem;
use App\Models\GeneralItemStockLedger;
use App\Support\GeneralItemBarcode;
use App\Support\StockQuantity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class GeneralItemController extends Controller
{
    /**
     * Search general items with pagination
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $businessId = session('active_business');
            if (!$businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select an active business'
                ], 400);
            }

            $request->validate([
                'q' => 'required|string|min:2',
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1|max:100',
                'item_type_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('item_types', 'id')->where('business_id', $businessId),
                ],
            ]);

            $searchTerm = $request->get('q');
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);

            $query = GeneralItem::where('business_id', $businessId)
                ->where(function($q) use ($searchTerm) {
                    $q->where('item_name', 'like', "%{$searchTerm}%")
                      ->orWhere('item_code', 'like', "%{$searchTerm}%");
                });

            $forSaleReturn = $request->boolean('for_sale_return');
            if ($forSaleReturn) {
                $query->activeOrHistoricallySold((int) $businessId);
            } else {
                $query->active();
            }

            // Filter by item type if provided
            if ($request->filled('item_type_id')) {
                $query->where('item_type_id', $request->item_type_id);
            }

            // Exclude already selected general items if provided
            if ($request->filled('exclude_ids')) {
                $excludeIds = is_array($request->exclude_ids) ? $request->exclude_ids : explode(',', $request->exclude_ids);
                $excludeIds = array_filter($excludeIds, function($id) { return !empty($id) && is_numeric($id); });
                if (!empty($excludeIds)) {
                    $query->whereNotIn('id', $excludeIds);
                }
            }

            // Order by relevance: exact code match → code starts-with → item name starts-with → rest
            $query->orderByRaw("
                CASE
                    WHEN item_code = ? THEN 1
                    WHEN item_code LIKE ? THEN 2
                    WHEN item_name = ? THEN 3
                    WHEN item_name LIKE ? THEN 4
                    ELSE 5
                END, item_name
            ", [
                $searchTerm,
                $searchTerm . '%',
                $searchTerm,
                $searchTerm . '%',
            ]);

            $items = $query->with(['itemType', 'batches' => function($q) {
                $q->where('status', 'active');
            }])->paginate($limit, ['*'], 'page', $page);

            // Add available stock for goods only; services do not track inventory
            $items->getCollection()->transform(function ($item) {
                $item->tracks_inventory = $item->tracksInventory();
                if ($item->tracksInventory()) {
                    $balance = GeneralItemStockLedger::getStockBalance($item->id, $item->business_id);
                    $item->available_stock = StockQuantity::normalize($balance['balance']);
                } else {
                    $item->available_stock = null;
                }

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
            $businessId = session('active_business');
            if (!$businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select an active business'
                ], 400);
            }

            $request->validate([
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1|max:100',
                'item_type_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('item_types', 'id')->where('business_id', $businessId),
                ],
            ]);

            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);

            $query = GeneralItem::where('business_id', $businessId);

            $forSaleReturn = $request->boolean('for_sale_return');
            if ($forSaleReturn) {
                $query->activeOrHistoricallySold((int) $businessId);
            } else {
                $query->active();
            }

            // Filter by item type if provided
            if ($request->filled('item_type_id')) {
                $query->where('item_type_id', $request->item_type_id);
            }

            // Exclude already selected general items if provided
            if ($request->filled('exclude_ids')) {
                $excludeIds = is_array($request->exclude_ids) ? $request->exclude_ids : explode(',', $request->exclude_ids);
                $excludeIds = array_filter($excludeIds, function($id) { return !empty($id) && is_numeric($id); });
                if (!empty($excludeIds)) {
                    $query->whereNotIn('id', $excludeIds);
                }
            }

            $query->orderBy('item_name');

            $items = $query->with(['itemType', 'batches' => function($q) {
                $q->where('status', 'active');
            }])->paginate($limit, ['*'], 'page', $page);

            // Add available stock for goods only; services do not track inventory
            $items->getCollection()->transform(function ($item) {
                $item->tracks_inventory = $item->tracksInventory();
                if ($item->tracksInventory()) {
                    $balance = GeneralItemStockLedger::getStockBalance($item->id, $item->business_id);
                    $item->available_stock = StockQuantity::normalize($balance['balance']);
                } else {
                    $item->available_stock = null;
                }

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
     * Exact lookup by item code for barcode scanners (goods only).
     */
    public function lookupByCode(Request $request): JsonResponse
    {
        try {
            $businessId = session('active_business');
            if (! $businessId) {
                return response()->json([
                    'error' => 'No active business found',
                    'message' => 'Please select an active business',
                ], 400);
            }

            $request->validate([
                'code' => 'required|string|max:255',
                'item_type_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('item_types', 'id')->where('business_id', $businessId),
                ],
            ]);

            $code = GeneralItemBarcode::normalizeCode($request->get('code'));
            if ($code === '') {
                return response()->json([
                    'error' => 'Invalid code',
                    'message' => 'Please scan or enter a valid item code.',
                ], 422);
            }

            $item = GeneralItem::query()
                ->where('business_id', $businessId)
                ->active()
                ->goods()
                ->whereRaw('LOWER(item_code) = ?', [mb_strtolower($code)])
                ->first();

            if (! $item) {
                return response()->json([
                    'error' => 'Not found',
                    'message' => 'No active goods item matches this code.',
                ], 404);
            }

            if ($request->filled('item_type_id') && (int) $item->item_type_id !== (int) $request->item_type_id) {
                return response()->json([
                    'error' => 'Type mismatch',
                    'message' => 'This code does not match the selected item type.',
                ], 422);
            }

            return response()->json(GeneralItemBarcode::toScanPayload($item));
        } catch (\Exception $e) {
            \Log::error('GeneralItem barcode lookup error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => 'Lookup failed',
                'message' => 'An error occurred while looking up the item code.',
                'debug' => config('app.debug') ? $e->getMessage() : null,
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
