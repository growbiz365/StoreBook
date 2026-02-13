<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseGeneralLine;
use App\Models\PurchaseArmsLine;
use App\Models\Party;
use App\Models\Bank;
use Illuminate\Support\Facades\DB;

class PurchaseDashboardController extends Controller
{
    public function index()
    {
        $businessId = session('active_business');

        // Purchase Statistics
        $purchaseStats = $this->getPurchaseStats($businessId);
        
        // Purchase by Status Statistics
        $statusStats = $this->getStatusStats($businessId);
        
        // Purchase by Payment Type Statistics
        $paymentTypeStats = $this->getPaymentTypeStats($businessId);
        
        // Purchase Value Statistics
        $valueStats = $this->getValueStats($businessId);
        
        // Recent Activities
        $recentActivities = $this->getRecentActivities($businessId);
        
        // Party Statistics
        $partyStats = $this->getPartyStats($businessId);

        return view('purchases.dashboard', compact(
            'purchaseStats',
            'statusStats', 
            'paymentTypeStats',
            'valueStats',
            'recentActivities',
            'partyStats'
        ));
    }

    private function getPurchaseStats($businessId)
    {
        $totalPurchases = Purchase::where('business_id', $businessId)->count();
        $thisMonthPurchases = Purchase::where('business_id', $businessId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $thisWeekPurchases = Purchase::where('business_id', $businessId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $todayPurchases = Purchase::where('business_id', $businessId)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        
        return [
            'total_purchases' => $totalPurchases,
            'this_month_purchases' => $thisMonthPurchases,
            'this_week_purchases' => $thisWeekPurchases,
            'today_purchases' => $todayPurchases,
        ];
    }

    private function getStatusStats($businessId)
    {
        $draftPurchases = Purchase::where('business_id', $businessId)->where('status', 'draft')->count();
        $postedPurchases = Purchase::where('business_id', $businessId)->where('status', 'posted')->count();
        $cancelledPurchases = Purchase::where('business_id', $businessId)->where('status', 'cancelled')->count();
        
        return [
            'draft' => $draftPurchases,
            'posted' => $postedPurchases,
            'cancelled' => $cancelledPurchases,
        ];
    }

    private function getPaymentTypeStats($businessId)
    {
        $cashPurchases = Purchase::where('business_id', $businessId)->where('payment_type', 'cash')->count();
        $bankPurchases = Purchase::where('business_id', $businessId)->where('payment_type', 'bank')->count();
        $creditPurchases = Purchase::where('business_id', $businessId)->where('payment_type', 'credit')->count();
        
        return [
            'cash' => $cashPurchases,
            'bank' => $bankPurchases,
            'credit' => $creditPurchases,
        ];
    }

    private function getValueStats($businessId)
    {
        $totalValue = Purchase::where('business_id', $businessId)->sum('total_amount');
        $thisMonthValue = Purchase::where('business_id', $businessId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        $averageValue = Purchase::where('business_id', $businessId)->avg('total_amount');
        $highestValue = Purchase::where('business_id', $businessId)->max('total_amount');
        
        return [
            'total_value' => $totalValue,
            'this_month_value' => $thisMonthValue,
            'average_value' => $averageValue,
            'highest_value' => $highestValue,
        ];
    }

    private function getRecentActivities($businessId)
    {
        $recentPurchases = Purchase::where('business_id', $businessId)
            ->with(['party', 'bank'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return [
            'recent_purchases' => $recentPurchases,
        ];
    }

    private function getPartyStats($businessId)
    {
        $topParties = Purchase::select('party_id', DB::raw('count(*) as purchase_count'), DB::raw('sum(total_amount) as total_value'))
            ->where('business_id', $businessId)
            ->whereNotNull('party_id')
            ->with('party')
            ->groupBy('party_id')
            ->orderBy('total_value', 'desc')
            ->limit(5)
            ->get();
            
        $totalParties = Purchase::where('business_id', $businessId)
            ->whereNotNull('party_id')
            ->distinct('party_id')
            ->count();
        
        return [
            'top_parties' => $topParties,
            'total_parties' => $totalParties,
        ];
    }
}
