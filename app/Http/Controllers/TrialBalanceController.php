<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Services\TrialBalanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrialBalanceController extends Controller
{
    protected $trialBalanceService;

    public function __construct(TrialBalanceService $trialBalanceService)
    {
        $this->trialBalanceService = $trialBalanceService;
    }

    /**
     * Display the trial balance report
     */
    public function index(Request $request)
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select an active business to view the trial balance.');
        }

        $business = Business::with(['country', 'timezone', 'currency', 'package'])->findOrFail($businessId);
        
        // Get the as of date from request or default to today
        $asOfDate = $request->get('as_of_date', Carbon::now()->format('Y-m-d'));
        
        // Get the accounting basis from request or default to accrual
        $basis = $request->get('basis', 'accrual'); // 'accrual' or 'cash'
        
        try {
            // Get trial balance data
            $trialBalanceData = $this->trialBalanceService->generateTrialBalance($businessId, $asOfDate, $basis);
            
            return view('finance.trial-balance.index', compact(
                'business',
                'trialBalanceData',
                'asOfDate',
                'basis'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating trial balance: ' . $e->getMessage());
        }
    }
}

