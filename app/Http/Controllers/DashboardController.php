<?php

namespace App\Http\Controllers;

use App\Models\SaleInvoice;
use App\Models\Purchase;
use App\Models\SaleReturn;
use App\Models\PurchaseReturn;
use App\Models\Party;
use App\Models\Bank;
use App\Models\GeneralItem;
// Arms model disabled - StoreBook is items-only
// use App\Models\Arm;
use App\Models\JournalEntry;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        $businessId = session('active_business');
        
        \Log::info('Dashboard loaded', [
            'user_id' => Auth::id(),
            'business_id' => $businessId,
            'session_id' => session()->getId(),
            'has_user' => Auth::check()
        ]);
        
        // Fallback: if no active business, try to set the user's first business
        if (!$businessId) {
            $user = Auth::user();
            if ($user) {
                $firstBusiness = $user->businesses()->first();
                if ($firstBusiness) {
                    $businessId = $firstBusiness->id;
                    session(['active_business' => $businessId]);
                    \Log::info('Fallback: Set active business in dashboard', [
                        'user_id' => $user->id,
                        'business_id' => $businessId,
                        'business_name' => $firstBusiness->business_name ?? 'Unknown'
                    ]);
                } else {
                    \Log::warning('User has no businesses', [
                        'user_id' => $user->id,
                        'user_businesses_count' => $user->businesses()->count()
                    ]);
                }
            } else {
                \Log::warning('No authenticated user in dashboard');
            }
        }
        
        // If still no business ID, return empty dashboard with proper structure
        if (!$businessId) {
            \Log::warning('No active business found for user', [
                'user_id' => Auth::id()
            ]);
            return view('dashboard', [
                'stats' => [
                    'total_sales' => 0,
                    'total_purchases' => 0,
                    'total_sale_returns' => 0,
                    'total_purchase_returns' => 0,
                    'total_parties' => 0,
                    'total_banks' => 0,
                    'total_general_items' => 0,
                    // 'total_arms' => 0, // Disabled - StoreBook is items-only
                    'total_journal_entries' => 0,
                ],
                'financialStats' => [
                    'total_sales_revenue' => 0,
                    'total_purchase_amount' => 0,
                    'total_sale_returns_amount' => 0,
                    'total_purchase_returns_amount' => 0,
                ],
                'netProfit' => 0,
                'recentActivities' => [
                    'recent_sales' => collect(),
                    'recent_purchases' => collect(),
                ],
                'monthlyTrends' => [],
                'topCustomers' => collect(),
                'topSuppliers' => collect(),
                'statusDistributions' => [
                    'sales' => [
                        'posted' => 0,
                        'draft' => 0,
                        'cancelled' => 0,
                    ],
                    'purchases' => [
                        'posted' => 0,
                        'draft' => 0,
                        'cancelled' => 0,
                    ],
                ],
                'inventoryStats' => [
                    'general_items_stock' => 0,
                    // 'arms_available' => 0, // Disabled - StoreBook is items-only
                    'low_stock_items' => 0,
                ],
                'todayStats' => [
                    'today_sales' => 0,
                    'today_purchases' => 0,
                    'today_sales_amount' => 0,
                ],
                'businessTimezone' => 'Asia/Karachi' // Default timezone
            ]);
        }
        
        // Get business timezone information
        $business = Business::with('timezone')->find($businessId);
        $businessTimezone = $business && $business->timezone ? $business->timezone->timezone_name : 'Asia/Karachi';
        
        // Get overall statistics
        $stats = [
            'total_sales' => SaleInvoice::where('business_id', $businessId)->count(),
            'total_purchases' => Purchase::where('business_id', $businessId)->count(),
            'total_sale_returns' => SaleReturn::where('business_id', $businessId)->count(),
            'total_purchase_returns' => PurchaseReturn::where('business_id', $businessId)->count(),
            'total_parties' => Party::where('business_id', $businessId)->count(),
            'total_banks' => Bank::where('business_id', $businessId)->count(),
            'total_general_items' => GeneralItem::where('business_id', $businessId)->count(),
            // 'total_arms' => Arm::where('business_id', $businessId)->count(), // Disabled - StoreBook is items-only
            'total_journal_entries' => JournalEntry::where('business_id', $businessId)->count(),
        ];

        // Get financial statistics
        $financialStats = [
            'total_sales_revenue' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->sum('total_amount'),
            'total_purchase_cost' => Purchase::where('business_id', $businessId)->where('status', 'posted')->sum('total_amount'),
            'total_sale_returns_amount' => SaleReturn::where('business_id', $businessId)->where('status', 'posted')->sum('total_amount'),
            'total_purchase_returns_amount' => PurchaseReturn::where('business_id', $businessId)->where('status', 'posted')->sum('total_amount'),
        ];

        // Calculate net profit
        $netProfit = $financialStats['total_sales_revenue'] - $financialStats['total_purchase_cost'] - $financialStats['total_sale_returns_amount'] + $financialStats['total_purchase_returns_amount'];

        // Get recent activities
        $recentActivities = [
            'recent_sales' => SaleInvoice::where('business_id', $businessId)
                ->with(['party', 'bank'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'recent_purchases' => Purchase::where('business_id', $businessId)
                ->with(['party', 'bank'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'recent_returns' => collect()
                ->merge(SaleReturn::where('business_id', $businessId)->with(['party', 'bank'])->orderBy('created_at', 'desc')->limit(3)->get())
                ->merge(PurchaseReturn::where('business_id', $businessId)->with(['party', 'bank'])->orderBy('created_at', 'desc')->limit(3)->get())
                ->sortByDesc('created_at')
                ->take(5),
        ];

        // Get inventory statistics
        $inventoryStats = [
            'general_items_stock' => DB::table('general_batches')
                ->where('business_id', $businessId)
                ->where('status', 'active')
                ->sum('qty_remaining'),
            // Arms statistics disabled - StoreBook is items-only
            // 'arms_available' => Arm::where('business_id', $businessId)->where('status', 'available')->count(),
            // 'arms_sold' => Arm::where('business_id', $businessId)->where('status', 'sold')->count(),
            'low_stock_items' => DB::table('general_items')
                ->select('general_items.id')
                ->leftJoin('general_batches', function($join) {
                    $join->on('general_items.id', '=', 'general_batches.item_id')
                         ->where('general_batches.status', 'active')
                         ->where('general_batches.qty_remaining', '>', 0);
                })
                ->where('general_items.business_id', $businessId)
                ->whereNotNull('general_items.min_stock_limit')
                ->groupBy('general_items.id', 'general_items.min_stock_limit')
                ->havingRaw('COALESCE(SUM(general_batches.qty_remaining), 0) <= general_items.min_stock_limit')
                ->count(),
        ];

        // Get monthly trends for current year
        $currentYear = now()->year;
        $monthlyTrends = [
            'sales' => SaleInvoice::where('business_id', $businessId)
                ->where('status', 'posted')
                ->whereYear('created_at', $currentYear)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'purchases' => Purchase::where('business_id', $businessId)
                ->where('status', 'posted')
                ->whereYear('created_at', $currentYear)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        // Get today's activities
        $todayStats = [
            'today_sales' => SaleInvoice::where('business_id', $businessId)->whereDate('created_at', today())->count(),
            'today_purchases' => Purchase::where('business_id', $businessId)->whereDate('created_at', today())->count(),
            'today_sales_amount' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->whereDate('created_at', today())->sum('total_amount'),
            'today_purchases_amount' => Purchase::where('business_id', $businessId)->where('status', 'posted')->whereDate('created_at', today())->sum('total_amount'),
        ];


        // Get top customers and suppliers
        $topCustomers = SaleInvoice::where('business_id', $businessId)
            ->where('status', 'posted')
            ->with('party')
            ->selectRaw('party_id, COUNT(*) as invoice_count, SUM(total_amount) as total_amount')
            ->groupBy('party_id')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        $topSuppliers = Purchase::where('business_id', $businessId)
            ->where('status', 'posted')
            ->with('party')
            ->selectRaw('party_id, COUNT(*) as invoice_count, SUM(total_amount) as total_amount')
            ->groupBy('party_id')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        // Get status distributions
        $statusDistributions = [
            'sales' => [
                'posted' => SaleInvoice::where('business_id', $businessId)->where('status', 'posted')->count(),
                'draft' => SaleInvoice::where('business_id', $businessId)->where('status', 'draft')->count(),
                'cancelled' => SaleInvoice::where('business_id', $businessId)->where('status', 'cancelled')->count(),
            ],
            'purchases' => [
                'posted' => Purchase::where('business_id', $businessId)->where('status', 'posted')->count(),
                'draft' => Purchase::where('business_id', $businessId)->where('status', 'draft')->count(),
                'cancelled' => Purchase::where('business_id', $businessId)->where('status', 'cancelled')->count(),
            ],
        ];

        // Ensure all variables are properly set with defaults
        $viewData = [
            'stats' => $stats ?? [],
            'financialStats' => $financialStats ?? [],
            'netProfit' => $netProfit ?? 0,
            'recentActivities' => $recentActivities ?? ['recent_sales' => collect(), 'recent_purchases' => collect()],
            'monthlyTrends' => $monthlyTrends ?? [],
            'topCustomers' => $topCustomers ?? collect(),
            'topSuppliers' => $topSuppliers ?? collect(),
            'statusDistributions' => $statusDistributions ?? [
                'sales' => ['posted' => 0, 'draft' => 0, 'cancelled' => 0],
                'purchases' => ['posted' => 0, 'draft' => 0, 'cancelled' => 0]
            ],
            'inventoryStats' => $inventoryStats ?? [
                'general_items_stock' => 0,
                // 'arms_available' => 0, // Disabled - StoreBook is items-only
                'low_stock_items' => 0
            ],
            'todayStats' => $todayStats ?? [
                'today_sales' => 0,
                'today_purchases' => 0,
                'today_sales_amount' => 0
            ],
            'businessTimezone' => $businessTimezone ?? 'Asia/Karachi'
        ];

        return view('dashboard', $viewData);
    }
}
