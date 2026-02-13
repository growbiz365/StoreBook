<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralItem;
use App\Models\GeneralBatch;
use App\Models\GeneralItemStockLedger;
use App\Models\InventoryTransaction;
use App\Models\Purchase;
use App\Models\ItemType;
use Illuminate\Support\Facades\DB;

class GeneralItemsDashboardController extends Controller
{
    public function index()
    {
        $businessId = session('active_business');

        // General Items Statistics
        $generalItemsStats = $this->getGeneralItemsStats($businessId);
        
        // General Items by Type Statistics
        $itemTypesStats = $this->getItemTypesStats($businessId);
        
        // General Items Stock Statistics
        $stockStats = $this->getStockStats($businessId);
        
        // Batch Statistics
        $batchStats = $this->getBatchStats($businessId);
        
        // Low Stock Alerts
        $lowStockAlerts = $this->getLowStockAlerts($businessId);

        return view('general_items.dashboard', compact(
            'generalItemsStats',
            'itemTypesStats',
            'stockStats',
            'batchStats',
            'lowStockAlerts'
        ));
    }

    private function getGeneralItemsStats($businessId)
    {
        $totalItems = GeneralItem::where('business_id', $businessId)->count();
        $totalOpeningStock = GeneralItem::where('business_id', $businessId)->sum('opening_stock');
        $totalOpeningValue = GeneralItem::where('business_id', $businessId)->sum('opening_total');
        $averageCostPrice = GeneralItem::where('business_id', $businessId)->avg('cost_price');
        $averageSalePrice = GeneralItem::where('business_id', $businessId)->avg('sale_price');
        
        // Items with low stock alerts (using current stock from batches)
        $lowStockItems = GeneralItem::select('general_items.id', 'general_items.min_stock_limit')
            ->leftJoin('general_batches', function($join) {
                $join->on('general_items.id', '=', 'general_batches.item_id')
                     ->where('general_batches.status', 'active')
                     ->where('general_batches.qty_remaining', '>', 0);
            })
            ->where('general_items.business_id', $businessId)
            ->whereNotNull('general_items.min_stock_limit')
            ->groupBy('general_items.id', 'general_items.min_stock_limit')
            ->havingRaw('COALESCE(SUM(general_batches.qty_remaining), 0) <= general_items.min_stock_limit')
            ->count();
        
        // Items added this month
        $thisMonthItems = GeneralItem::where('business_id', $businessId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return [
            'total_items' => $totalItems,
            'total_opening_stock' => $totalOpeningStock,
            'total_opening_value' => $totalOpeningValue,
            'average_cost_price' => $averageCostPrice,
            'average_sale_price' => $averageSalePrice,
            'low_stock_items' => $lowStockItems,
            'this_month_items' => $thisMonthItems,
        ];
    }

    private function getItemTypesStats($businessId)
    {
        $topItemTypes = GeneralItem::select('item_types.item_type', DB::raw('count(*) as count'))
            ->join('item_types', 'general_items.item_type_id', '=', 'item_types.id')
            ->where('general_items.business_id', $businessId)
            ->groupBy('item_types.id', 'item_types.item_type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
            
        $totalItemTypes = ItemType::where('business_id', $businessId)->where('status', true)->count();
        
        return [
            'top_types' => $topItemTypes,
            'total_types' => $totalItemTypes,
        ];
    }

    private function getStockStats($businessId)
    {
        $currentStock = GeneralBatch::where('business_id', $businessId)->sum('qty_remaining');
        $totalReceived = GeneralBatch::where('business_id', $businessId)->sum('qty_received');
        $totalConsumed = $totalReceived - $currentStock;
        
        $currentStockValue = GeneralBatch::where('business_id', $businessId)
            ->sum(DB::raw('qty_remaining * unit_cost'));
            
        $activeBatches = GeneralBatch::where('business_id', $businessId)
            ->where('status', 'active')
            ->where('qty_remaining', '>', 0)
            ->count();
        
        return [
            'current_stock' => $currentStock,
            'total_received' => $totalReceived,
            'total_consumed' => $totalConsumed,
            'current_stock_value' => $currentStockValue,
            'active_batches' => $activeBatches,
        ];
    }



    private function getBatchStats($businessId)
    {
        $totalBatches = GeneralBatch::where('business_id', $businessId)->count();
        $activeBatches = GeneralBatch::where('business_id', $businessId)->where('status', 'active')->count();
        $reversedBatches = GeneralBatch::where('business_id', $businessId)->where('status', 'reversed')->count();
        $totalBatchValue = GeneralBatch::where('business_id', $businessId)->sum('total_cost');
        
        return [
            'total_batches' => $totalBatches,
            'active_batches' => $activeBatches,
            'reversed_batches' => $reversedBatches,
            'total_batch_value' => $totalBatchValue,
        ];
    }



    private function getLowStockAlerts($businessId)
    {
        // Get items with their current stock (opening stock + purchased stock from batches)
        $lowStockItems = GeneralItem::select(
                'general_items.id',
                'general_items.item_name', 
                'general_items.min_stock_limit',
                'general_items.item_type_id',
                'general_items.opening_stock',
                DB::raw('COALESCE(SUM(general_batches.qty_remaining), 0) as current_stock')
            )
            ->leftJoin('general_batches', function($join) {
                $join->on('general_items.id', '=', 'general_batches.item_id')
                     ->where('general_batches.status', 'active')
                     ->where('general_batches.qty_remaining', '>', 0);
            })
            ->where('general_items.business_id', $businessId)
            ->whereNotNull('general_items.min_stock_limit')
            ->groupBy('general_items.id', 'general_items.item_name', 'general_items.min_stock_limit', 'general_items.item_type_id', 'general_items.opening_stock')
            ->havingRaw('current_stock <= general_items.min_stock_limit')
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();
            
        $expiringSoonBatches = GeneralBatch::where('business_id', $businessId)
            ->where('status', 'active')
            ->where('qty_remaining', '>', 0)
            ->orderBy('received_date', 'asc')
            ->limit(5)
            ->get();
        
        return [
            'low_stock_items' => $lowStockItems,
            'expiring_soon_batches' => $expiringSoonBatches,
        ];
    }
}
