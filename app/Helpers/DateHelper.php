<?php

/**
 * Business Date Formatting Helpers
 * 
 * These functions ensure all dates in the system respect the business's
 * configured date format and timezone settings.
 */

if (!function_exists('getBusinessDateFormat')) {
    /**
     * Get the date format for the active business
     * 
     * @return string Date format (e.g., 'Y-m-d', 'd/m/Y', 'm/d/Y')
     */
    function getBusinessDateFormat()
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return 'd M Y'; // Safe fallback
        }
        
        try {
            $business = \App\Models\Business::find($businessId);
            $format = $business ? $business->date_format : null;
            
            // Ensure we always return a valid format string
            if (empty($format)) {
                return 'd M Y';
            }
            
            return $format;
        } catch (\Exception $e) {
            \Log::error('Error getting business date format: ' . $e->getMessage());
            return 'd M Y';
        }
    }
}

if (!function_exists('getBusinessTimezone')) {
    /**
     * Get the timezone for the active business
     * 
     * @return string Timezone name (e.g., 'Asia/Karachi', 'America/New_York')
     */
    function getBusinessTimezone()
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return 'Asia/Karachi'; // Safe fallback
        }
        
        try {
            $business = \App\Models\Business::with('timezone')->find($businessId);
            
            if ($business && $business->timezone && !empty($business->timezone->timezone_name)) {
                return $business->timezone->timezone_name;
            }
            
            return 'Asia/Karachi';
        } catch (\Exception $e) {
            \Log::error('Error getting business timezone: ' . $e->getMessage());
            return 'Asia/Karachi';
        }
    }
}

if (!function_exists('formatBusinessDate')) {
    /**
     * Format a date according to business settings
     * 
     * @param mixed $date Date to format (Carbon, string, or DateTime)
     * @param bool $includeTime Whether to include time in the output
     * @return string Formatted date string
     */
    function formatBusinessDate($date, $includeTime = false)
    {
        if (!$date) {
            return '-';
        }
        
        try {
            $timezone = getBusinessTimezone();
            $format = getBusinessDateFormat();
            
            // Ensure format is never null
            if (empty($format)) {
                $format = 'd M Y';
            }
            
            // Add time format if requested
            if ($includeTime) {
                $format .= ' H:i';
            }
            
            // Parse and format the date
            return \Carbon\Carbon::parse($date)
                ->setTimezone($timezone)
                ->format($format);
                
        } catch (\Exception $e) {
            \Log::error('Error formatting business date: ' . $e->getMessage());
            
            // Fallback: try to return something usable
            try {
                return \Carbon\Carbon::parse($date)->format('d M Y');
            } catch (\Exception $e2) {
                return (string) $date;
            }
        }
    }
}

if (!function_exists('formatBusinessDateTime')) {
    /**
     * Format a date with time according to business settings
     * 
     * @param mixed $date Date to format
     * @return string Formatted date and time string
     */
    function formatBusinessDateTime($date)
    {
        return formatBusinessDate($date, true);
    }
}

if (!function_exists('convertToBusinessTimezone')) {
    /**
     * Convert a Carbon date to the business timezone
     * 
     * @param mixed $date Date to convert
     * @return \Carbon\Carbon|null Converted date or null
     */
    function convertToBusinessTimezone($date)
    {
        if (!$date) {
            return null;
        }
        
        try {
            $timezone = getBusinessTimezone();
            return \Carbon\Carbon::parse($date)->setTimezone($timezone);
        } catch (\Exception $e) {
            \Log::error('Error converting to business timezone: ' . $e->getMessage());
            return \Carbon\Carbon::parse($date);
        }
    }
}

if (!function_exists('getBusinessCurrency')) {
    /**
     * Get the currency for the active business
     * 
     * @return \App\Models\Currency|null Currency object or null
     */
    function getBusinessCurrency()
    {
        $businessId = session('active_business');
        
        if (!$businessId) {
            return null;
        }
        
        try {
            $business = \App\Models\Business::with('currency')->find($businessId);
            return $business ? $business->currency : null;
        } catch (\Exception $e) {
            \Log::error('Error getting business currency: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getBusinessCurrencyCode')) {
    /**
     * Get the currency code for the active business
     * 
     * @return string Currency code (e.g., 'PKR', 'USD')
     */
    function getBusinessCurrencyCode()
    {
        $currency = getBusinessCurrency();
        return $currency ? $currency->currency_code : 'PKR';
    }
}

if (!function_exists('getBusinessCurrencySymbol')) {
    /**
     * Get the currency symbol for the active business
     * 
     * @return string Currency symbol (e.g., 'Rs', '$')
     */
    function getBusinessCurrencySymbol()
    {
        $currency = getBusinessCurrency();
        return $currency ? $currency->symbol : 'Rs';
    }
}

if (!function_exists('formatBusinessCurrency')) {
    /**
     * Format an amount according to business currency settings
     * 
     * @param float|int $amount Amount to format
     * @param bool $showSymbol Whether to show currency symbol
     * @param int $decimals Number of decimal places
     * @return string Formatted currency string
     */
    function formatBusinessCurrency($amount, $showSymbol = true, $decimals = 2)
    {
        if ($amount === null || $amount === '') {
            return '-';
        }
        
        try {
            $currencyCode = getBusinessCurrencyCode();
            $currencySymbol = getBusinessCurrencySymbol();
            
            $formatted = number_format((float)$amount, $decimals);
            
            if ($showSymbol) {
                return $currencyCode . ' ' . $formatted;
            }
            
            return $formatted;
        } catch (\Exception $e) {
            \Log::error('Error formatting business currency: ' . $e->getMessage());
            return number_format((float)$amount, $decimals);
        }
    }
}

if (!function_exists('formatBusinessAmount')) {
    /**
     * Format an amount with business currency (alias for formatBusinessCurrency)
     * 
     * @param float|int $amount Amount to format
     * @return string Formatted currency string
     */
    function formatBusinessAmount($amount)
    {
        return formatBusinessCurrency($amount, true, 2);
    }
}

