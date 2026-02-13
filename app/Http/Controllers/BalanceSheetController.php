<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Services\BalanceSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BalanceSheetController extends Controller
{
    protected $balanceSheetService;

    public function __construct(BalanceSheetService $balanceSheetService)
    {
        $this->balanceSheetService = $balanceSheetService;
    }

    /**
     * Display the balance sheet report
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to view the balance sheet.');
        }

        $business = Business::with(['country', 'timezone', 'currency', 'package'])->findOrFail($businessId);
        
        // Get the as of date from request or default to today
        $asOfDate = $request->get('as_of_date', Carbon::now()->format('Y-m-d'));
        
        // Get the accounting basis from request or default to accrual
        $basis = $request->get('basis', 'accrual'); // 'accrual' or 'cash'
        
        try {
            // Get balance sheet data
            $balanceSheetData = $this->balanceSheetService->generateBalanceSheet($businessId, $asOfDate, $basis);
            
            // Ensure all required keys exist
            if (!isset($balanceSheetData['warnings'])) {
                $balanceSheetData['warnings'] = [];
            }
            if (!isset($balanceSheetData['negative_balances'])) {
                $balanceSheetData['negative_balances'] = [];
            }
            if (!isset($balanceSheetData['inventory_adjustment'])) {
                $balanceSheetData['inventory_adjustment'] = null;
            }
            if (!isset($balanceSheetData['excluded_accounts'])) {
                $balanceSheetData['excluded_accounts'] = [];
            }
            
            return view('finance.balance-sheet.index', compact(
                'business',
                'balanceSheetData',
                'asOfDate',
                'basis'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Balance Sheet Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'business_id' => $businessId,
                'as_of_date' => $asOfDate,
                'basis' => $basis
            ]);
            
            return redirect()->back()
                ->with('error', 'Error generating balance sheet: ' . $e->getMessage());
        }
    }

    /**
     * Export balance sheet to PDF
     */
    public function exportPdf(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to export the balance sheet.');
        }

        $business = Business::with(['country', 'timezone', 'currency', 'package'])->findOrFail($businessId);
        $asOfDate = $request->get('as_of_date', Carbon::now()->format('Y-m-d'));
        $basis = $request->get('basis', 'accrual');
        
        try {
            $balanceSheetData = $this->balanceSheetService->generateBalanceSheet($businessId, $asOfDate, $basis);
            
            // Generate PDF using a PDF library like DomPDF or similar
            // For now, we'll return the view for printing
            return view('finance.balance-sheet.pdf', compact(
                'business',
                'balanceSheetData',
                'asOfDate',
                'basis'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error exporting balance sheet: ' . $e->getMessage());
        }
    }
}
