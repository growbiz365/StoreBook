<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Business;

/**
 * Business View Composer
 * 
 * Automatically injects business settings (date format, timezone) into all views
 * This ensures consistent date/time formatting across the application
 */
class BusinessComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $businessId = session('active_business');
        
        if ($businessId) {
            try {
                $business = Business::with(['timezone', 'currency'])->find($businessId);
                
                if ($business) {
                    $view->with([
                        'businessDateFormat' => $business->date_format ?? 'd M Y',
                        'businessTimezone' => $business->timezone->timezone_name ?? 'Asia/Karachi',
                        'businessCurrency' => $business->currency,
                        'businessCurrencyCode' => $business->currency->currency_code ?? 'PKR',
                        'businessCurrencySymbol' => $business->currency->symbol ?? 'Rs',
                        'activeBusiness' => $business,
                    ]);
                    return;
                }
            } catch (\Exception $e) {
                \Log::error('Error in BusinessComposer: ' . $e->getMessage());
            }
        }
        
        // Fallback values if no business is active or error occurs
        $view->with([
            'businessDateFormat' => 'd M Y',
            'businessTimezone' => 'Asia/Karachi',
            'businessCurrency' => null,
            'businessCurrencyCode' => 'PKR',
            'businessCurrencySymbol' => 'Rs',
            'activeBusiness' => null,
        ]);
    }
}

