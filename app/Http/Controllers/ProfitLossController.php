<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Services\ProfitLossService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfitLossController extends Controller
{
    protected $profitLossService;

    public function __construct(ProfitLossService $profitLossService)
    {
        $this->profitLossService = $profitLossService;
    }

    /**
     * Display the profit and loss report
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to view the profit and loss statement.');
        }

        $business = Business::with(['country', 'timezone', 'currency', 'package'])->findOrFail($businessId);
        
        // Get the date range from request or default to current month
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get the accounting basis from request or default to accrual
        $basis = $request->get('basis', 'accrual'); // 'accrual' or 'cash'
        
        try {
            // Get profit and loss data
            $profitLossData = $this->profitLossService->generateProfitLoss($businessId, $fromDate, $toDate, $basis);
            
            return view('finance.profit-loss.index', compact(
                'business',
                'profitLossData',
                'fromDate',
                'toDate',
                'basis'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating profit and loss statement: ' . $e->getMessage());
        }
    }

    /**
     * Export profit and loss to PDF
     */
    public function exportPdf(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to export the profit and loss statement.');
        }

        $business = Business::with(['country', 'timezone', 'currency', 'package'])->findOrFail($businessId);
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $basis = $request->get('basis', 'accrual');
        
        try {
            $profitLossData = $this->profitLossService->generateProfitLoss($businessId, $fromDate, $toDate, $basis);
            
            // Generate PDF using a PDF library like DomPDF or similar
            // For now, we'll return the view for printing
            return view('finance.profit-loss.pdf', compact(
                'business',
                'profitLossData',
                'fromDate',
                'toDate',
                'basis'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error exporting profit and loss statement: ' . $e->getMessage());
        }
    }
}
