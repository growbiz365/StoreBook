<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GeneralLedgerController extends Controller
{
    protected $generalLedgerService;

    public function __construct(GeneralLedgerService $generalLedgerService)
    {
        $this->generalLedgerService = $generalLedgerService;
    }

    /**
     * Display the general ledger report
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to view the general ledger.');
        }

        $business = Business::with(['country', 'timezone', 'currency', 'package'])->findOrFail($businessId);
        
        // Get the from date from request or default to start of current month
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        
        // Get the to date from request or default to today
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        
        // Get the accounting basis from request or default to accrual
        $basis = $request->get('basis', 'accrual'); // 'accrual' or 'cash'
        
        try {
            // Get general ledger data
            $generalLedgerData = $this->generalLedgerService->generateGeneralLedger($businessId, $fromDate, $toDate, $basis);
            
            return view('finance.general-ledger.index', compact(
                'business',
                'generalLedgerData',
                'fromDate',
                'toDate',
                'basis'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating general ledger: ' . $e->getMessage());
        }
    }

    /**
     * Export general ledger to PDF
     */
    public function exportPdf(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to export the general ledger.');
        }

        $business = Business::with(['country', 'timezone', 'currency', 'package'])->findOrFail($businessId);
        
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $basis = $request->get('basis', 'accrual');
        
        try {
            $generalLedgerData = $this->generalLedgerService->generateGeneralLedger($businessId, $fromDate, $toDate, $basis);
            
            // For now, return the view. In a real implementation, you would generate a PDF
            return view('finance.general-ledger.index', compact(
                'business',
                'generalLedgerData',
                'fromDate',
                'toDate',
                'basis'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error exporting general ledger: ' . $e->getMessage());
        }
    }
}
