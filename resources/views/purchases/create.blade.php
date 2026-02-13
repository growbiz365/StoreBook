<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>New Purchase - Purchases Management - StoreBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        // TEMP DEBUG: minimal logger that bypasses silencing
        function dbg(tag, payload) {
            try { console.error(`[PurchaseCreate][${tag}]`, payload); } catch(e) {}
        }
        (function(){
            var sd = @json(session('store_debug'));
            if (sd) dbg('store-debug', sd);
        })();
        (function(){
            if (window && window.console) {
                try {
                    console.log = function(){};
                    console.debug = function(){};
                    console.info = function(){};
                } catch(e) {}
            }
        })();
    </script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        
        /* Full width container */
        .w-full {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 0.5rem;
        }
        
        /* Main content card */
        .bg-white.shadow-lg.rounded-lg {
            width: 100% !important;
            margin: 0 !important;
        }
        
        /* Form container styling */
        .bg-white.shadow-lg {
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Form sections */
        .form-section {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-section h2 {
            color: #111827;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.125rem;
        }
        
        @media (max-width: 768px) {
            .form-section {
                padding: 1rem;
                margin-bottom: 1rem;
                border-radius: 6px;
            }
            
            .form-section h2 {
                font-size: 1rem;
                margin-bottom: 0.75rem;
            }
        }
        
        @media (max-width: 640px) {
            .form-section {
                padding: 0.75rem;
            }
        }
        
        /* Table styling */
        .overflow-x-auto {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: white;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #374151;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        
        /* Arms table specific styling */
        #arms_table {
            width: 100%;
            min-width: 1800px;
            table-layout: fixed;
        }
        
        #arms_table th,
        #arms_table td {
            padding: 1rem;
            vertical-align: middle;
            white-space: nowrap;
        }
        
        /* Column widths for better data display */
        #arms_table th:nth-child(1), #arms_table td:nth-child(1) { width: 16%; min-width: 200px; } /* Type */
        #arms_table th:nth-child(2), #arms_table td:nth-child(2) { width: 16%; min-width: 200px; } /* Make */
        #arms_table th:nth-child(3), #arms_table td:nth-child(3) { width: 14%; min-width: 180px; } /* Caliber */
        #arms_table th:nth-child(4), #arms_table td:nth-child(4) { width: 14%; min-width: 180px; } /* Category */
        #arms_table th:nth-child(5), #arms_table td:nth-child(5) { width: 14%; min-width: 180px; } /* Condition */
        #arms_table th:nth-child(6), #arms_table td:nth-child(6) { width: 12%; min-width: 160px; } /* Serials */
        #arms_table th:nth-child(7), #arms_table td:nth-child(7) { width: 10%; min-width: 140px; } /* Unit Price */
        #arms_table th:nth-child(8), #arms_table td:nth-child(8) { width: 10%; min-width: 140px; } /* Sale Price */
        #arms_table th:nth-child(9), #arms_table td:nth-child(9) { width: 8%; min-width: 100px; }  /* Actions */
        
        #arms_table select,
        #arms_table input {
            width: 100%;
            min-width: 0;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        
        #arms_table select:focus,
        #arms_table input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        #arms_table .remove-arm {
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
        }
        
        #arms_table .remove-arm:hover {
            background-color: #fef2f2;
            transform: scale(1.05);
        }
        
        /* Form fields */
        input, select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Labels */
        label {
            display: block;
            font-weight: 500;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 640px) {
            label {
                font-size: 0.8125rem;
                margin-bottom: 0.375rem;
            }
        }
        
        /* Buttons */
        button {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        /* Better touch targets for mobile */
        @media (max-width: 768px) {
            button, a.inline-flex {
                min-height: 44px; /* Recommended minimum touch target size */
                padding: 0.625rem 1rem;
            }
            
            input, select, textarea {
                min-height: 44px; /* Better touch targets */
                font-size: 16px; /* Prevent zoom on iOS */
            }
        }
        
        /* Grid layout */
        .grid-cols-5 {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5rem;
            align-items: end;
        }
        
        .grid-cols-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            align-items: end;
        }
        
        /* Summary section */
        .bg-gray-50 {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 1rem;
        }
        
        /* Action buttons */
        .flex.justify-end {
            gap: 0.75rem;
        }
        
        /* Responsive */
        @media (max-width: 1400px) {
            .grid-cols-5 {
                grid-template-columns: repeat(3, 1fr);
            }
            #arms_table {
                min-width: 1200px;
            }
        }
        
        @media (max-width: 1024px) {
            .grid-cols-5 {
                grid-template-columns: repeat(2, 1fr);
            }
            #arms_table {
                min-width: 1000px;
            }
            
            /* Better form section padding */
            .form-section {
                padding: 1rem;
            }
            
            /* Adjust table containers */
            .overflow-x-auto {
                margin: 0 -1rem;
                padding: 0 1rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                font-size: 14px;
            }
            
            .grid-cols-5 {
                grid-template-columns: 1fr;
            }
            
            .w-full {
                padding: 0 0.5rem;
            }
            
            #arms_table {
                min-width: 800px;
            }
            
            /* Header adjustments */
            .bg-white.shadow-sm .flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }
            
            .bg-white.shadow-sm h1 {
                font-size: 1.5rem;
            }
            
            /* Form section adjustments */
            .form-section {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }
            
            .form-section h2 {
                font-size: 1rem;
            }
            
            /* Grid adjustments for customer details */
            .grid-cols-4 {
                grid-template-columns: 1fr !important;
            }
            
            .md\:col-span-2 {
                grid-column: span 1 !important;
            }
            
            .lg\:col-span-3 {
                grid-column: span 1 !important;
            }
            
            /* Button container */
            .flex.justify-end {
                flex-direction: column;
                width: 100%;
            }
            
            .flex.justify-end button,
            .flex.justify-end a {
                width: 100%;
                justify-content: center;
            }
            
            /* Summary section */
            .bg-gray-50.rounded-lg .grid {
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            
            /* Table overflow */
            .overflow-x-auto {
                margin: 0 -0.75rem;
                padding: 0 0.75rem;
            }
            
            /* Counter badges - smaller on mobile */
            #general_items_counter,
            #arms_counter {
                font-size: 0.75rem;
                min-width: 20px;
                height: 20px;
                padding: 0.25rem 0.5rem;
            }
            
            /* Add button adjustments */
            #add_general_item,
            #add_arm {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            
            /* Table inputs - full width on mobile */
            table input,
            table select {
                font-size: 14px !important;
                padding: 0.5rem !important;
            }
            
            /* Remove buttons more tappable on mobile */
            .remove-general-item,
            .remove-arm {
                min-width: 36px;
                min-height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        @media (max-width: 640px) {
            /* Extra small devices */
            .bg-white.shadow-lg.rounded-lg {
                border-radius: 8px;
                padding: 1rem !important;
            }
            
            .grid.gap-6 {
                gap: 1rem !important;
            }
            
            /* Compact header */
            .max-w-7xl.mx-auto.px-4 {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            
            /* Modal adjustments */
            #deleteConfirmationModal .bg-white {
                width: 90% !important;
                max-width: 90% !important;
                margin: 1rem;
            }
            
            /* Tighter spacing */
            label {
                font-size: 0.8125rem;
                margin-bottom: 0.375rem;
            }
            
            input, select {
                font-size: 0.875rem;
                padding: 0.5rem;
            }
            
            /* Party details and customer details */
            .bg-gray-50.border.rounded-lg {
                padding: 0.75rem !important;
            }
            
            .bg-gray-50.border.rounded-lg h3 {
                font-size: 0.875rem;
                margin-bottom: 0.5rem !important;
            }
            
            /* Section headers with counters */
            .flex.justify-between.items-center {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .flex.items-center.gap-3 {
                gap: 0.5rem !important;
            }
        }
        
        @media (max-width: 480px) {
            /* Very small devices */
            .w-full.px-2 {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            .bg-white.shadow-lg.rounded-lg {
                padding: 0.75rem !important;
            }
            
            /* Stack everything */
            .grid {
                display: flex !important;
                flex-direction: column !important;
            }
            
            /* Compact tables */
            table th,
            table td {
                font-size: 0.75rem !important;
                padding: 0.5rem 0.25rem !important;
            }
            
            /* Smaller buttons */
            button {
                font-size: 0.8125rem !important;
                padding: 0.5rem 0.75rem !important;
            }
            
            /* Counter and add button container */
            .flex.justify-between.items-center.mb-4,
            .flex.justify-between.items-center.mb-3 {
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.75rem;
            }
            
            #add_general_item,
            #add_arm {
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
            <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="flex flex-row justify-between items-center py-3 sm:py-4">
                    <div class="flex items-center">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">New Purchase</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('purchases.index') }}" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                    </div>
                    </div>
                </div>
            </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops! Something went wrong.</strong>
            <ul class="mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('form_error') || $errors->any())
    <div id="page_error_banner" class="mb-3 px-3 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded" role="alert">
        {{ session('form_error') ?? $errors->first() }}
    </div>
    <script>
        // Hide any other generic error blocks so only this compact banner shows
        (function(){
            try {
                function clean() {
                    var keep = document.getElementById('page_error_banner');
                    var selectors = [
                        '.bg-red-100',
                        '.border-red-400',
                        '.alert-danger',
                        '.bg-red-50.border-red-200.text-red-700',
                        '[role="alert"].bg-red-100',
                        '[role="alert"].bg-red-200'
                    ];
                    var nodes = document.querySelectorAll(selectors.join(', '));
                    nodes.forEach(function(el){
                        if (!keep || (el !== keep && !keep.contains(el))) {
                            el.parentNode && el.parentNode.removeChild(el);
                        }
                    });
                    // Remove duplicates matching the same text as the banner
                    if (keep) {
                        var text = (keep.textContent || '').trim();
                        if (text) {
                            Array.from(document.querySelectorAll('div, p, span')).forEach(function(el){
                                if (el !== keep && (el.textContent || '').trim() === text) {
                                    el.parentNode && el.parentNode.removeChild(el);
                                }
                            });
                        }
                    }
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', clean);
                } else {
                    clean();
                }
                setTimeout(clean, 300);
                setTimeout(clean, 800);
            } catch(e) {}
        })();
    </script>
    @endif

    <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
        @csrf

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        <!-- Main Content -->
        <div class="w-full px-2 sm:px-3 md:px-4 py-3 sm:py-4">
            <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-3 sm:p-4 md:p-6 w-full">
            <!-- Purchase Header Information -->
            <div class="pb-3 sm:pb-4 mb-3 sm:mb-4 border-b border-gray-200">
                <h2 class="text-sm sm:text-base font-semibold text-gray-900 mb-2">Purchase Header Information</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 md:gap-6">
                    <div>
                        <label for="party_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Party <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="searchable-select-container relative">
                                <input type="text" 
                                       class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
                                       placeholder="Search parties..."
                                       autocomplete="off"
                                       id="party_search_input">
                                <input type="hidden" name="party_id" id="party_id" class="selected-item-id">
                                
                                <!-- Dropdown -->
                                <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                                    <div class="search-results-container max-h-40 overflow-y-auto">
                                        <!-- Results will be populated here -->
                                    </div>
                                    <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                                        <div class="flex justify-between items-center text-xs">
                                            <button type="button" class="prev-page text-blue-500 hover:text-blue-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-blue-50 transition-colors">Previous</button>
                                            <span class="page-info text-gray-500 text-xs"></span>
                                            <button type="button" class="next-page text-blue-500 hover:text-blue-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-blue-50 transition-colors">Next</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Loading indicator -->
                                <div class="loading-indicator hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                                    <svg class="animate-spin h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="party_help_text">Required for credit payments, optional for cash payments</p>
                        <div id="party_balance" class="mt-1 text-sm hidden">
                            <span class="font-medium">Balance:</span>
                            <span id="party_balance_amount" class="ml-1"></span>
                        </div>
                        @error('party_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Type <span class="text-red-500">*</span>
                        </label>
                            <select name="payment_type" id="payment_type" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 @error('payment_type') border-red-500 @enderror">
                                <option value="credit" {{ old('payment_type') == 'credit' ? 'selected' : '' }}>Credit (Party)</option>
                                <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            </select>
                            @error('payment_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    @if ($errors->has('bank_id'))
                        <div class="mb-2 p-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded">
                            {{ $errors->first('bank_id') }}
                        </div>
                    @endif
                    <div id="bank_field" style="{{ $errors->has('bank_id') || old('payment_type') == 'cash' ? '' : 'display: none;' }}">
                        <label for="bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Bank <span class="text-red-500">*</span>
                        </label>
                            <select name="bank_id" id="bank_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->chartOfAccount->name ?? $bank->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        <div id="bank_balance" class="mt-1 text-sm hidden">
                            <span class="font-medium">Balance:</span>
                            <span id="bank_balance_amount" class="ml-1"></span>
                        </div>
                            {{-- inline bank_id error suppressed: using compact top banner only --}}
                    </div>


                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Date <span class="text-red-500">*</span>
                        </label>
                            <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" 
                               required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 @error('invoice_date') border-red-500 @enderror">
                            @error('invoice_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <label for="shipping_charges" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Charges
                        </label>
                            <input type="number" name="shipping_charges" id="shipping_charges" value="{{ old('shipping_charges', 0) }}" 
                                   step="1" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 @error('shipping_charges') border-red-500 @enderror">
                            @error('shipping_charges')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                </div>
            </div>

            <!-- Customer Details Section (for Cash payments) -->
            <div id="customer_details_field" style="display: none;" class="mb-4">
                <div class="bg-gray-50 border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Customer Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        <div>
                            <label for="name_of_customer" class="block text-xs font-medium text-gray-600 mb-1">Customer Name</label>
                            <input type="text" name="name_of_customer" id="name_of_customer" value="{{ old('name_of_customer') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('name_of_customer') border-red-500 @enderror">
                            @error('name_of_customer')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="father_name" class="block text-xs font-medium text-gray-600 mb-1">Father Name</label>
                            <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('father_name') border-red-500 @enderror">
                            @error('father_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="contact" class="block text-xs font-medium text-gray-600 mb-1">Contact</label>
                            <input type="text" name="contact" id="contact" value="{{ old('contact') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('contact') border-red-500 @enderror"
                                   placeholder="03XX-XXXXXXX">
                            @error('contact')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="cnic" class="block text-xs font-medium text-gray-600 mb-1">CNIC</label>
                            <input type="text" name="cnic" id="cnic" value="{{ old('cnic') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('cnic') border-red-500 @enderror"
                                   placeholder="12345-1234567-1" data-mask="00000-0000000-0">
                            @error('cnic')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_no" class="block text-xs font-medium text-gray-600 mb-1">Licence No</label>
                            <input type="text" name="licence_no" id="licence_no" value="{{ old('licence_no') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_no') border-red-500 @enderror">
                            @error('licence_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_issue_date" class="block text-xs font-medium text-gray-600 mb-1">Issue Date</label>
                            <input type="date" name="licence_issue_date" id="licence_issue_date" value="{{ old('licence_issue_date') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_issue_date') border-red-500 @enderror">
                            @error('licence_issue_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_valid_upto" class="block text-xs font-medium text-gray-600 mb-1">Valid Upto</label>
                            <input type="date" name="licence_valid_upto" id="licence_valid_upto" value="{{ old('licence_valid_upto') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_valid_upto') border-red-500 @enderror">
                            @error('licence_valid_upto')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_issued_by" class="block text-xs font-medium text-gray-600 mb-1">Issued By</label>
                            <input type="text" name="licence_issued_by" id="licence_issued_by" value="{{ old('licence_issued_by') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_issued_by') border-red-500 @enderror">
                            @error('licence_issued_by')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="re_reg_no" class="block text-xs font-medium text-gray-600 mb-1">Re-Reg No</label>
                            <input type="text" name="re_reg_no" id="re_reg_no" value="{{ old('re_reg_no') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('re_reg_no') border-red-500 @enderror">
                            @error('re_reg_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="dc" class="block text-xs font-medium text-gray-600 mb-1">DC</label>
                            <input type="text" name="dc" id="dc" value="{{ old('dc') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('dc') border-red-500 @enderror">
                            @error('dc')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="Date" class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                            <input type="date" name="Date" id="Date" value="{{ old('Date') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('Date') border-red-500 @enderror">
                            @error('Date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="address" class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                            <textarea name="address" id="address" rows="1" 
                                      class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                                      placeholder="Enter customer address">{{ old('address') }}</textarea>
                            @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Party Details Section (for Credit payments when party is selected) -->
            <div id="party_details_field" style="display: none;" class="mb-4">
                <div class="bg-gray-50 border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Party Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        <div>
                            <label for="party_name" class="block text-xs font-medium text-gray-600 mb-1">Party Name</label>
                            <input type="text" id="party_name" readonly 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600">
                        </div>
                        
                        <div>
                            <label for="party_cnic" class="block text-xs font-medium text-gray-600 mb-1">Party CNIC</label>
                            <input type="text" id="party_cnic" readonly 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600">
                        </div>
                        
                        <div>
                            <label for="party_contact" class="block text-xs font-medium text-gray-600 mb-1">Party Contact</label>
                            <input type="text" id="party_contact" readonly 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600">
                        </div>
                        
                        <div>
                            <label for="party_address" class="block text-xs font-medium text-gray-600 mb-1">Party Address</label>
                            <textarea id="party_address" rows="1" readonly 
                                      class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600"></textarea>
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_no" class="block text-xs font-medium text-gray-600 mb-1">Licence No</label>
                            <input type="text" name="party_license_no" id="party_license_no" value="{{ old('party_license_no') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_no') border-red-500 @enderror">
                            @error('party_license_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_issue_date" class="block text-xs font-medium text-gray-600 mb-1">Issue Date</label>
                            <input type="date" name="party_license_issue_date" id="party_license_issue_date" value="{{ old('party_license_issue_date') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_issue_date') border-red-500 @enderror">
                            @error('party_license_issue_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_valid_upto" class="block text-xs font-medium text-gray-600 mb-1">Valid Upto</label>
                            <input type="date" name="party_license_valid_upto" id="party_license_valid_upto" value="{{ old('party_license_valid_upto') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_valid_upto') border-red-500 @enderror">
                            @error('party_license_valid_upto')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_issued_by" class="block text-xs font-medium text-gray-600 mb-1">Issued By</label>
                            <input type="text" name="party_license_issued_by" id="party_license_issued_by" value="{{ old('party_license_issued_by') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_issued_by') border-red-500 @enderror">
                            @error('party_license_issued_by')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_re_reg_no" class="block text-xs font-medium text-gray-600 mb-1">Re-Reg No</label>
                            <input type="text" name="party_re_reg_no" id="party_re_reg_no" value="{{ old('party_re_reg_no') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_re_reg_no') border-red-500 @enderror">
                            @error('party_re_reg_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_dc" class="block text-xs font-medium text-gray-600 mb-1">DC</label>
                            <input type="text" name="party_dc" id="party_dc" value="{{ old('party_dc') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_dc') border-red-500 @enderror">
                            @error('party_dc')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_dc_date" class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                            <input type="date" name="party_dc_date" id="party_dc_date" value="{{ old('party_dc_date') }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_dc_date') border-red-500 @enderror">
                            @error('party_dc_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Items Section -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mb-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">General Items (FIFO batches)</h2>
                        <span id="general_items_counter" class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-semibold text-white bg-purple-600 rounded-full min-w-[24px] h-6">0</span>
                    </div>
                    <button type="button" id="add_general_item" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Line</span>
                    </button>
                </div>
                
                <div class="overflow-x-auto relative">
                    <table class="min-w-full divide-y divide-gray-200" id="general_items_table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="general_items_container">
                            <!-- General items will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Arms Section - Hidden: StoreBook is items-only -->
            <div class="form-section" style="display: none;">
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mb-3">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <h2 class="text-base font-semibold text-gray-900">Arms (serial-based)</h2>
                        <span id="arms_counter" class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-semibold text-white bg-blue-600 rounded-full min-w-[24px] h-6">0</span>
                    </div>
                    <button type="button" id="add_arm" class="inline-flex items-center justify-center px-3 py-2 sm:py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out" title="Add new arm line (will clone data from previous line)">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Line</span>
                    </button>
                </div>
                
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="arms_table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Make</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caliber</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serials</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="arms_container">
                            <!-- Arms will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Summary -->
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Subtotal: <span id="subtotal" class="font-medium">0.00</span></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Shipping: <span id="shipping_display" class="font-medium">+ 0.00</span></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Amount: <span id="total_amount" class="font-medium text-lg">0.00</span></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('purchases.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" name="action" value="save" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save Draft
                </button>
                <button type="submit" name="action" value="post" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Post Purchase
                </button>
            </div>
        </div>
    </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 hidden items-center justify-center px-4" style="z-index: 10000;">
        <div class="bg-white rounded-xl shadow-lg w-full sm:w-1/2 max-w-sm border border-gray-100" style="z-index: 10001;">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Remove Line Item?</h3>
            </div>
            <div class="px-4 py-4">
                <p id="deleteModalMessage" class="text-sm text-gray-600 leading-relaxed">
                    Are you sure you want to remove this line? This action cannot be undone.
                </p>
            </div>
            <div class="px-4 py-3 bg-gray-50 flex justify-end gap-2 rounded-b-xl">
                <button type="button" id="cancelDeleteModal" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteModal" class="px-3 py-1.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500">
                    Remove
                </button>
            </div>
        </div>
    </div>
</body>
</html>

    <!-- Templates for dynamic content -->
    <template id="general_item_template">
        <tr class="general-item-row">
            <td class="px-4 py-4 whitespace-nowrap">
                <div class="relative">
                                            <div class="searchable-select-container relative">
                            <input type="text" 
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
                                   placeholder="Search items..."
                                   autocomplete="off"
                                   data-index="INDEX">
                            <input type="hidden" name="general_lines[INDEX][general_item_id]" class="selected-item-id">
                            
                            <!-- Dropdown -->
                            <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                                <div class="search-results-container max-h-40 overflow-y-auto">
                                    <!-- Results will be populated here -->
                                </div>
                                <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                                    <div class="flex justify-between items-center text-xs">
                                        <button type="button" class="prev-page text-blue-500 hover:text-blue-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-blue-50 transition-colors">Previous</button>
                                        <span class="page-info text-gray-500 text-xs"></span>
                                        <button type="button" class="next-page text-blue-500 hover:text-blue-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-blue-50 transition-colors">Next</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Loading indicator -->
                            <div class="loading-indicator hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                                <svg class="animate-spin h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                         </div>
                         <!-- Error display for general item selection -->
                         <div class="general-item-error mt-1 text-xs text-red-600 hidden"></div>
                        </div>
                </div>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="number" name="general_lines[INDEX][qty]" required step="1" min="1" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-qty focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0" value="1">
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="number" name="general_lines[INDEX][unit_price]" required step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-price focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="number" name="general_lines[INDEX][sale_price]" step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-sale-price focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <span class="text-sm font-medium general-line-total text-gray-900">0.00</span>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <button type="button" class="remove-general-item text-red-600 hover:text-red-800 text-sm p-1 rounded hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <!-- Arms template hidden: StoreBook is items-only -->
    <template id="arm_template" style="display: none;">
        <tr class="arm-row">
            <td class="px-4 py-4 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_type_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Type</option>
                    @foreach($armsTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->arm_type }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_make_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Make</option>
                    @foreach($armsMakes as $make)
                        <option value="{{ $make->id }}">{{ $make->arm_make }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_caliber_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Caliber</option>
                    @foreach($armsCalibers as $caliber)
                        <option value="{{ $caliber->id }}">{{ $caliber->arm_caliber }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_category_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Category</option>
                    @foreach($armsCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->arm_category }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_condition_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Condition</option>
                    @foreach($armsConditions as $condition)
                        <option value="{{ $condition->id }}">{{ $condition->arm_condition }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="text" name="arm_lines[INDEX][serials]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 arm-serial-input" 
                       placeholder="SN1001">
                <!-- Error display for arm serial numbers -->
                <div class="arm-serial-error mt-1 text-xs text-red-600 hidden"></div>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="number" name="arm_lines[INDEX][unit_price]" required step="0.01" min="0" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm arm-price focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="number" name="arm_lines[INDEX][sale_price]" step="0.01" min="0" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm arm-sale-price focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
                <input type="hidden" name="arm_lines[INDEX][arm_title]" class="arm-title-input">
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <button type="button" class="remove-arm text-red-600 hover:text-red-800 text-sm p-2 rounded hover:bg-red-50 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <script>
    // Clear data immediately if this is a fresh page load (no flags set)
    (function() {
        const wasSubmitting = sessionStorage.getItem('purchase_form_submitting');
        const hasFailedSubmissionFlag = localStorage.getItem('purchase_form_failed_submission');
        
        // Minimal diagnostics
        console.log('[PurchaseCreate] init', { wasSubmitting, hasFailedSubmissionFlag });
        
        // Check if this is a fresh navigation to create page (not coming from a form submission)
        const isCreatePage = window.location.href.includes('/purchases/create');
        const hasVisibleErrors = document.querySelector('.bg-red-100, .bg-red-50, .text-red-700, .text-red-600, .alert-danger, [class*="error"]');
        const hasVisibleSuccess = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        
        // Check if we're coming from a different page (not from form submission)
        const isFromDifferentPage = document.referrer && 
            !document.referrer.includes('/purchases/create') && 
            !document.referrer.includes('purchases/create');
        
        console.log('[PurchaseCreate] error-scan', { hasVisibleErrors: !!hasVisibleErrors, hasVisibleSuccess: !!hasVisibleSuccess });
        
        // Clear data if:
        // 1. Fresh page load (no flags), OR
        // 2. Success message is visible (regardless of flags), OR
        // 3. Coming from different page with no errors (likely successful navigation)
        if ((!wasSubmitting && !hasFailedSubmissionFlag) || 
            hasVisibleSuccess ||
            (isFromDifferentPage && !hasVisibleErrors)) {
            console.log('[PurchaseCreate] clearing saved state');
            localStorage.removeItem('purchase_form_data');
            localStorage.removeItem('purchase_form_failed_submission');
            sessionStorage.removeItem('purchase_form_submitting');
        } else {
            console.log('[PurchaseCreate] preserving saved state');
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        dbg('dom-ready', {
            hasFormErrorBanner: !!document.getElementById('form_error_banner'),
            hasErrorSummary: !!document.querySelector('.bg-red-50.border-red-200.text-red-700'),
            bankFieldVisible: (function(){ const el = document.getElementById('bank_field'); return !!el && el.style.display !== 'none'; })()
        });
        let generalItemIndex = 0;
        let armIndex = 0;

        // Load saved data from localStorage on page load - moved to end of initialization

        // Function to handle payment type changes
        function handlePaymentTypeChange() {
            const paymentTypeSelect = document.getElementById('payment_type');
            const bankField = document.getElementById('bank_field');
            const bankSelect = document.getElementById('bank_id');
            const customerDetailsField = document.getElementById('customer_details_field');
            const partyDetailsField = document.getElementById('party_details_field');
            const vendorField = document.getElementById('party_id');
            const vendorLabel = document.querySelector('label[for="party_id"]');
            const vendorHelpText = document.getElementById('party_help_text');
            const hasBankIdError = !!(@json($errors->has('bank_id')));

            if (!paymentTypeSelect || !bankField || !bankSelect) {
                return;
            }

            // Clear any existing party error when payment type changes
            const partyError = document.getElementById('party_error');
            if (partyError) {
                partyError.remove();
            }
            
            if (paymentTypeSelect.value === 'cash') {
                bankField.style.display = 'block';
                bankSelect.required = true; // Bank is required for cash payments
                if (customerDetailsField) {
                    customerDetailsField.style.display = 'block'; // Show customer details for cash payments
                }
                if (partyDetailsField) {
                    partyDetailsField.style.display = 'none'; // Hide party details for cash payments
                }
                
                // Make vendor optional for cash payments
                if (vendorField) {
                    vendorField.required = false;
                }
                if (vendorLabel) {
                    vendorLabel.innerHTML = 'Party';
                }
                if (vendorHelpText) {
                    vendorHelpText.textContent = 'Optional for cash payments - you can leave this blank';
                }
            } else {
                bankField.style.display = 'none';
                bankSelect.required = false;
                bankSelect.value = '';
                if (customerDetailsField) {
                    customerDetailsField.style.display = 'none'; // Hide customer details for credit payments
                }
                
                // Make vendor required for credit payments
                if (vendorField) {
                    vendorField.required = true;
                }
                if (vendorLabel) {
                    vendorLabel.innerHTML = 'Party <span class="text-red-500">*</span>';
                }
                if (vendorHelpText) {
                    vendorHelpText.textContent = 'Required for credit payments';
                }
                
                // Show party details if party is selected
                if (vendorField && vendorField.value && typeof loadPartyDetails === 'function') {
                    loadPartyDetails(vendorField.value);
                }
                if (partyDetailsField && vendorField && vendorField.value) {
                    partyDetailsField.style.display = 'block';
                } else if (partyDetailsField) {
                    partyDetailsField.style.display = 'none';
                }
            }

            // If server-side validation flagged bank_id, force-show the bank field
            if (hasBankIdError && bankField) {
                bankField.style.display = 'block';
                if (bankSelect) bankSelect.required = true;
            }
        }

        // Payment type change handler
        const paymentTypeSelectElement = document.getElementById('payment_type');
        if (paymentTypeSelectElement) {
            paymentTypeSelectElement.addEventListener('change', handlePaymentTypeChange);
            
            // Initialize on page load (for validation errors or saved state)
            handlePaymentTypeChange();
        }

        // Party selection change handler (for hidden input)
        document.getElementById('party_id').addEventListener('change', function() {
            // Clear any existing party error when party is selected
            const partyError = document.getElementById('party_error');
            if (partyError) {
                partyError.remove();
            }
            
            if (this.value) {
                fetchPartyBalance(this.value);
                
                // If payment type is credit, show party details
                const paymentType = document.getElementById('payment_type').value;
                if (paymentType === 'credit') {
                    loadPartyDetails(this.value);
                    document.getElementById('party_details_field').style.display = 'block';
                }
            } else {
                hidePartyBalance();
                
                // Hide party details if no party selected
                document.getElementById('party_details_field').style.display = 'none';
            }
        });

        // Bank selection change handler
        document.getElementById('bank_id').addEventListener('change', function() {
            if (this.value) {
                fetchBankBalance(this.value);
            } else {
                hideBankBalance();
            }
        });

        // Function to fetch party balance
        function fetchPartyBalance(partyId) {
            console.log(`Fetching balance for party ${partyId}`);
            
            // Get the current date from the invoice date field
            const currentDate = document.getElementById('invoice_date').value;
            
            // Build the URL with date parameter
            const url = `/parties/${partyId}/balance${currentDate ? `?date=${currentDate}` : ''}`;
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Party balance data:', data);
                    if (data.balance !== undefined) {
                        showPartyBalance(data.formatted_balance, data.status);
                    } else {
                        hidePartyBalance();
                    }
                })
                .catch(error => {
                    console.error('Error fetching party balance:', error);
                    hidePartyBalance();
                });
        }

        // Function to fetch bank balance
        function fetchBankBalance(bankId) {
            console.log(`Fetching balance for bank ${bankId}`);
            
            // Get the current date from the invoice date field
            const currentDate = document.getElementById('invoice_date').value;
            
            // Build the URL with date parameter
            const url = `/banks/${bankId}/balance${currentDate ? `?date=${currentDate}` : ''}`;
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Bank balance data:', data);
                    if (data.balance !== undefined) {
                        showBankBalance(data.formatted_balance, data.status);
                    } else {
                        hideBankBalance();
                    }
                })
                .catch(error => {
                    console.error('Error fetching bank balance:', error);
                    hideBankBalance();
                });
        }

        // Function to show party balance
        function showPartyBalance(balance, status) {
            const balanceDiv = document.getElementById('party_balance');
            const balanceSpan = document.getElementById('party_balance_amount');
            
            balanceSpan.textContent = balance;
            
            // Set color based on balance status
            balanceSpan.className = 'ml-1 font-semibold';
            if (status === 'positive') {
                balanceSpan.classList.add('text-green-600');
            } else if (status === 'negative') {
                balanceSpan.classList.add('text-red-600');
            } else {
                balanceSpan.classList.add('text-gray-600');
            }
            
            balanceDiv.classList.remove('hidden');
        }

        // Function to show bank balance
        function showBankBalance(balance, status) {
            const balanceDiv = document.getElementById('bank_balance');
            const balanceSpan = document.getElementById('bank_balance_amount');
            
            balanceSpan.textContent = balance;
            
            // Set color based on balance status
            balanceSpan.className = 'ml-1 font-semibold';
            if (status === 'positive') {
                balanceSpan.classList.add('text-green-600');
            } else if (status === 'negative') {
                balanceSpan.classList.add('text-red-600');
            } else {
                balanceSpan.classList.add('text-gray-600');
            }
            
            balanceDiv.classList.remove('hidden');
        }

        // Function to hide party balance
        function hidePartyBalance() {
            const balanceDiv = document.getElementById('party_balance');
            balanceDiv.classList.add('hidden');
        }

        // Function to load party details
        function loadPartyDetails(partyId) {
            console.log(`Loading details for party ${partyId}`);
            
            // Add CSRF token to the request
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/api/parties/${partyId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Party details data received:', data);
                    console.log('Data type:', typeof data);
                    console.log('Data keys:', Object.keys(data));
                    
                    // Check if elements exist
                    const nameField = document.getElementById('party_name');
                    const cnicField = document.getElementById('party_cnic');
                    const contactField = document.getElementById('party_contact');
                    const addressField = document.getElementById('party_address');
                    
                    console.log('Form fields found:', {
                        nameField: !!nameField,
                        cnicField: !!cnicField,
                        contactField: !!contactField,
                        addressField: !!addressField
                    });
                    
                    // Populate party form fields
                    if (nameField) nameField.value = data.name || '';
                    if (cnicField) cnicField.value = data.cnic || '';
                    if (contactField) contactField.value = data.phone_no || '';
                    if (addressField) addressField.value = data.address || '';
                    
                    console.log('Fields populated with values:', {
                        name: data.name,
                        cnic: data.cnic,
                        phone_no: data.phone_no,
                        address: data.address
                    });
                })
                .catch(error => {
                    console.error('Error loading party details:', error);
                    console.error('Error details:', error.message);
                });
        }

        // Function to hide bank balance
        function hideBankBalance() {
            const balanceDiv = document.getElementById('bank_balance');
            balanceDiv.classList.add('hidden');
        }

        // Add event listener for invoice date changes to refresh balances
        const invoiceDateInput = document.getElementById('invoice_date');
        invoiceDateInput.addEventListener('change', function() {
            // Refresh balances for both party and bank if they are selected
            const partySelect = document.getElementById('party_id');
            const bankSelect = document.getElementById('bank_id');
            
            if (partySelect.value) {
                fetchPartyBalance(partySelect.value);
            }
            if (bankSelect.value) {
                fetchBankBalance(bankSelect.value);
            }
        });

        // Function to update counters
        function updateCounters() {
            const generalItemsCount = document.querySelectorAll('.general-item-row').length;
            const armsCount = document.querySelectorAll('.arm-row').length;
            
            const generalItemsCounter = document.getElementById('general_items_counter');
            const armsCounter = document.getElementById('arms_counter');
            
            if (generalItemsCounter) {
                generalItemsCounter.textContent = generalItemsCount;
            }
            
            if (armsCounter) {
                armsCounter.textContent = armsCount;
            }
        }

        // Add general item
        document.getElementById('add_general_item').addEventListener('click', function() {
            const container = document.getElementById('general_items_container');
            const template = document.getElementById('general_item_template');
            const clone = template.content.cloneNode(true);
            
            // Replace INDEX placeholder
            clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
                element.name = element.name.replace('INDEX', generalItemIndex);
            });
            
            container.appendChild(clone);
            
            // Add event listeners for the new row
            const newRow = container.lastElementChild;
            const searchableContainer = newRow.querySelector('.searchable-select-container');
            const qtyInput = newRow.querySelector('input[name*="[qty]"]');
            const priceInput = newRow.querySelector('input[name*="[unit_price]"]');
            const salePriceInput = newRow.querySelector('input[name*="[sale_price]"]');
            
            // Initialize searchable dropdown with custom options
            if (searchableContainer) {
                new SearchableDropdown(searchableContainer, {
                    itemsPerPage: 15,        // Show 15 items per page
                    debounceDelay: 300,      // 300ms delay for search
                    minSearchLength: 2       // Start searching after 2 characters
                });
                searchableContainer.dataset.initialized = 'true';
            }
            
            // Add input event listeners for calculations
            [qtyInput, priceInput, salePriceInput].forEach(input => {
                input.addEventListener('input', function() {
                    calculateLineTotal(this);
                    calculateTotals();
                });
            });
            
            generalItemIndex++;
            updateCounters();
        });

        // Add arm
        document.getElementById('add_arm').addEventListener('click', function() {
            const container = document.getElementById('arms_container');
            const template = document.getElementById('arm_template');
            const clone = template.content.cloneNode(true);
            
            // Replace INDEX placeholder
            clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
                element.name = element.name.replace('INDEX', armIndex);
            });
            
            // Append the clone first to get the actual DOM element
            container.appendChild(clone);
            
            // Get the newly added row (last child)
            const newRow = container.lastElementChild;
            
            // Clone data from the last filled arm line if it exists
            const existingRows = container.querySelectorAll('.arm-row');
            if (existingRows.length > 1) { // More than 1 because we just added one
                const lastRow = existingRows[existingRows.length - 2]; // Get the previous row
                cloneArmData(lastRow, newRow);
            }
            
            // Add event listeners
            addCalculationListeners();
            
            armIndex++;
            updateCounters();
        });

        // Delete Confirmation Modal
        const deleteModal = document.getElementById('deleteConfirmationModal');
        const deleteModalMessage = document.getElementById('deleteModalMessage');
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const cancelDeleteModal = document.getElementById('cancelDeleteModal');
        let rowPendingDeletion = null;
        let pendingDeletionType = null;

        function openDeleteModal(row, type) {
            if (!deleteModal || !deleteModalMessage) {
                // Fallback to direct removal if modal elements don't exist
                if (row) {
                    row.remove();
                    calculateTotals();
                    updateCounters();
                }
                return;
            }

            // Close any open searchable dropdowns before opening the modal
            document.querySelectorAll('.searchable-dropdown').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
            
            // Remove focus from any searchable inputs
            document.querySelectorAll('.searchable-input').forEach(input => {
                input.blur();
            });

            rowPendingDeletion = row;
            pendingDeletionType = type;

            if (type === 'general') {
                deleteModalMessage.textContent = 'Are you sure you want to remove this general item line? This action cannot be undone.';
            } else {
                deleteModalMessage.textContent = 'Are you sure you want to remove this arm line? This action cannot be undone.';
            }

            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            if (confirmDeleteModal) {
                confirmDeleteModal.focus();
            }
        }

        function closeDeleteModal() {
            if (!deleteModal) {
                rowPendingDeletion = null;
                pendingDeletionType = null;
                return;
            }
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            rowPendingDeletion = null;
            pendingDeletionType = null;
        }

        if (confirmDeleteModal) {
            confirmDeleteModal.addEventListener('click', function() {
                if (rowPendingDeletion) {
                    const row = rowPendingDeletion;
                    row.remove();
                    calculateTotals();
                    updateCounters();
                }
                closeDeleteModal();
            });
        }

        if (cancelDeleteModal) {
            cancelDeleteModal.addEventListener('click', function() {
                closeDeleteModal();
            });
        }

        if (deleteModal) {
            deleteModal.addEventListener('click', function(event) {
                if (event.target === deleteModal) {
                    closeDeleteModal();
                }
            });
        }

        document.addEventListener('keydown', function(event) {
            if (!deleteModal) {
                return;
            }
            if (event.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                closeDeleteModal();
            }
        });

        // Remove buttons
        document.addEventListener('click', function(e) {
            const generalRemoveBtn = e.target.closest('.remove-general-item');
            if (generalRemoveBtn) {
                const row = generalRemoveBtn.closest('.general-item-row');
                if (row) {
                    openDeleteModal(row, 'general');
                }
                e.preventDefault();
                return;
            }
            
            const armRemoveBtn = e.target.closest('.remove-arm');
            if (armRemoveBtn) {
                const row = armRemoveBtn.closest('.arm-row');
                if (row) {
                    openDeleteModal(row, 'arm');
                }
                e.preventDefault();
                return;
            }
        });

        // Calculation functions
        function addCalculationListeners() {
            // General items - only for existing elements, new ones are handled in add_general_item
            document.querySelectorAll('.general-qty, .general-price, .general-sale-price').forEach(element => {
                element.addEventListener('input', function() {
                    calculateLineTotal(this);
                    calculateTotals();
                });
            });
            
            // Arms
            document.querySelectorAll('.arm-price, .arm-sale-price, input[name*="serials"]').forEach(element => {
                element.addEventListener('input', function() {
                    calculateTotals();
                });
            });
            
            // Charges
            document.getElementById('shipping_charges').addEventListener('input', function() {
                    calculateTotals();
            });

            // Add change event listeners for existing general item selection
            document.querySelectorAll('select[name*="[general_item_id]"]').forEach(select => {
                select.addEventListener('change', function() {
                    populateItemPrices(this);
                });
            });

            // Add arm title generation listeners
            addArmTitleListeners();
        }

        // Function to add arm title generation listeners
        function addArmTitleListeners() {
            document.querySelectorAll('.arm-row').forEach(row => {
                const typeSelect = row.querySelector('select[name*="[arm_type_id]"]');
                const makeSelect = row.querySelector('select[name*="[arm_make_id]"]');
                const caliberSelect = row.querySelector('select[name*="[arm_caliber_id]"]');
                const serialsInput = row.querySelector('input[name*="[serials]"]');
                
                // Remove existing listeners to prevent duplicates
                [typeSelect, makeSelect, caliberSelect, serialsInput].forEach(element => {
                    if (element) {
                        element.removeEventListener('change', updateArmTitle);
                        element.removeEventListener('input', updateArmTitle);
                        element.addEventListener('change', updateArmTitle);
                        element.addEventListener('input', updateArmTitle);
                    }
                });
            });
        }

        // Function to update arm title
        function updateArmTitle() {
            const row = this.closest('.arm-row');
            const typeSelect = row.querySelector('select[name*="[arm_type_id]"]');
            const makeSelect = row.querySelector('select[name*="[arm_make_id]"]');
            const caliberSelect = row.querySelector('select[name*="[arm_caliber_id]"]');
            const serialsInput = row.querySelector('input[name*="[serials]"]');
            const titleInput = row.querySelector('.arm-title-input');
            
            const make = makeSelect.options[makeSelect.selectedIndex]?.text || '';
            const caliber = caliberSelect.options[caliberSelect.selectedIndex]?.text || '';
            const type = typeSelect.options[typeSelect.selectedIndex]?.text || '';
            const serials = serialsInput.value.trim();
            
            let title = '';
            if (make && caliber && type && serials) {
                // If multiple serials, use the first one for the title
                const firstSerial = serials.split(',')[0].trim();
                title = `${make} ${caliber} ${type} (SN: ${firstSerial})`;
            } else if (make || caliber || type || serials) {
                const firstSerial = serials ? serials.split(',')[0].trim() : '';
                title = `${make || ''} ${caliber || ''} ${type || ''} ${firstSerial ? `(SN: ${firstSerial})` : ''}`.trim();
            } else {
                title = '';
            }
            
            // Update hidden input
            titleInput.value = title;
        }

        function calculateLineTotal(element) {
            const row = element.closest('.general-item-row');
            const qty = parseFloat(row.querySelector('.general-qty').value) || 0;
            const price = parseFloat(row.querySelector('.general-price').value) || 0;
            
            const lineTotal = qty * price;
            row.querySelector('.general-line-total').textContent = lineTotal.toFixed(2);
        }

        function calculateTotals() {
            let subtotal = 0;
            
            // Calculate general items
            document.querySelectorAll('.general-item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.general-qty').value) || 0;
                const price = parseFloat(row.querySelector('.general-price').value) || 0;
                
                subtotal += qty * price;
            });
            
            // Calculate arms
            document.querySelectorAll('.arm-row').forEach(row => {
                const serials = row.querySelector('input[name*="serials"]').value;
                const serialCount = serials ? serials.split(',').filter(s => s.trim()).length : 0;
                const price = parseFloat(row.querySelector('.arm-price').value) || 0;
                
                subtotal += serialCount * price;
            });
            
            // Get charges
            const shipping = parseFloat(document.getElementById('shipping_charges').value) || 0;
            
            // Calculate total
            const total = subtotal + shipping;
            
            // Update display
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('shipping_display').textContent = `+ ${shipping.toFixed(2)}`;
            document.getElementById('total_amount').textContent = total.toFixed(2);
        }

        // Searchable dropdown functionality
        class SearchableDropdown {
                    constructor(container, options = {}) {
            this.container = container;
            this.input = container.querySelector('.searchable-input');
            this.hiddenInput = container.querySelector('.selected-item-id');
            this.dropdown = container.querySelector('.searchable-dropdown');
            this.resultsContainer = container.querySelector('.search-results-container');
            this.paginationContainer = container.querySelector('.pagination-container');
            this.loadingIndicator = container.querySelector('.loading-indicator');
            
            this.searchTimeout = null;
            this.currentPage = 1;
            this.searchTerm = '';
            this.selectedItem = null;
            
            // Configurable options
            this.itemsPerPage = options.itemsPerPage || 10; // Default to 10 items
            this.debounceDelay = options.debounceDelay || 300; // Default to 300ms
            this.minSearchLength = options.minSearchLength || 2; // Default to 2 characters
            
            this.init();
        }
            
            init() {
                this.bindEvents();
                this.setupGlobalClickHandler();
            }
            
            bindEvents() {
                // Input focus
                this.input.addEventListener('focus', () => {
                    this.showDropdown();
                    this.performSearch();
                });
                
                // Input input
                this.input.addEventListener('input', (e) => {
                    this.searchTerm = e.target.value;
                    this.currentPage = 1;
                    this.showDropdown(); // Ensure dropdown is visible when typing
                    
                    // Clear selection if input becomes empty
                    if (!this.searchTerm.trim() && this.selectedItem) {
                        this.clearSelection();
                    }
                    
                    this.debounceSearch();
                });
                
                // Input keydown
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.selectHighlightedResult();
            } else if (e.key === 'Escape') {
                this.hideDropdown();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.navigateResults('down');
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.navigateResults('up');
            }
        });
            }
            
            setupGlobalClickHandler() {
                document.addEventListener('click', (e) => {
                    if (!this.container.contains(e.target)) {
                        this.hideDropdown();
                    }
                });
            }
            
            debounceSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, this.debounceDelay);
            }
            
            async performSearch() {
                if (this.searchTerm.length < this.minSearchLength) {
                    this.showInitialResults();
                    return;
                }
                
                this.showLoading();
                
                try {
                    const response = await fetch(`/api/general-items/search?q=${encodeURIComponent(this.searchTerm)}&page=${this.currentPage}&limit=${this.itemsPerPage}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    // Validate the data structure
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from search API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Search error:', error);
                    this.showError(`Search failed: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            async showInitialResults() {
                this.showLoading();
                
                try {
                    const response = await fetch(`/api/general-items?page=${this.currentPage}&limit=${this.itemsPerPage}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    // Validate the data structure
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial load error:', error);
                    this.showError(`Failed to load items: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            displayResults(items, meta) {
                this.resultsContainer.innerHTML = '';
                
                if (!items || !Array.isArray(items) || items.length === 0) {
                    this.resultsContainer.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            ${this.searchTerm ? 'No items found matching your search.' : 'No items available.'}
                        </div>
                    `;
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                
                // Create result items
                items.forEach((item, index) => {
                    // Validate item data
                    if (!item || !item.id || !item.item_name) {
                        return; // Skip invalid items
                    }
                    
                    const resultItem = document.createElement('div');
                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                    resultItem.dataset.itemId = item.id;
                    resultItem.dataset.itemName = item.item_name;
                    
                    // Safely handle numeric values
                    const costPrice = this.safeNumber(item.cost_price);
                    const salePrice = this.safeNumber(item.sale_price);
                    
                    resultItem.dataset.costPrice = costPrice;
                    resultItem.dataset.salePrice = salePrice;
                    
                    resultItem.innerHTML = `
                        <div class="font-medium text-gray-900">${item.item_name}</div>
                    `;
                    
                    resultItem.addEventListener('click', () => {
                        this.selectItem(item);
                    });
                    
                    this.resultsContainer.appendChild(resultItem);
                });
                
                // Show pagination if needed
                if (meta && meta.last_page > 1) {
                    this.showPagination(meta);
                } else {
                    this.paginationContainer.classList.add('hidden');
                }
            }
            
            showPagination(meta) {
                this.paginationContainer.classList.remove('hidden');
                
                const pageInfo = this.paginationContainer.querySelector('.page-info');
                const prevBtn = this.paginationContainer.querySelector('.prev-page');
                const nextBtn = this.paginationContainer.querySelector('.next-page');
                
                pageInfo.textContent = `Page ${meta.current_page} of ${meta.last_page}`;
                
                prevBtn.disabled = meta.current_page <= 1;
                nextBtn.disabled = meta.current_page >= meta.last_page;
                
                prevBtn.onclick = () => {
                    if (meta.current_page > 1) {
                        this.currentPage = meta.current_page - 1;
                        this.performSearch();
                    }
                };
                
                nextBtn.onclick = () => {
                    if (meta.current_page < meta.last_page) {
                        this.currentPage = meta.current_page + 1;
                        this.performSearch();
                    }
                };
            }
            
            selectItem(item) {
                this.selectedItem = item;
                this.input.value = item.item_name;
                this.hiddenInput.value = item.id;
                
                // Populate prices
                const row = this.container.closest('.general-item-row');
                const unitPriceInput = row.querySelector('.general-price');
                const salePriceInput = row.querySelector('.general-sale-price');
                
                if (unitPriceInput) {
                    unitPriceInput.value = item.cost_price ? item.cost_price : '';
                }
                if (salePriceInput) {
                    salePriceInput.value = item.sale_price ? item.sale_price : '';
                }
                
                // Trigger calculation
                if (unitPriceInput) {
                    calculateLineTotal(unitPriceInput);
                    calculateTotals();
                }
                
                this.hideDropdown();
            }
            
            clearSelection() {
                // Clear the current selection
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
                
                // Clear prices
                const row = this.container.closest('.general-item-row');
                const unitPriceInput = row.querySelector('.general-price');
                const salePriceInput = row.querySelector('.general-sale-price');
                
                if (unitPriceInput) {
                    unitPriceInput.value = '';
                }
                if (salePriceInput) {
                    salePriceInput.value = '';
                }
                
                // Trigger calculation
                if (unitPriceInput) {
                    calculateLineTotal(unitPriceInput);
                    calculateTotals();
                }
            }
            
            selectFirstResult() {
                const firstResult = this.resultsContainer.querySelector('.result-item');
                if (firstResult) {
                    const item = {
                        id: firstResult.dataset.itemId,
                        item_name: firstResult.dataset.itemName,
                        cost_price: this.safeNumber(firstResult.dataset.costPrice),
                        sale_price: this.safeNumber(firstResult.dataset.salePrice)
                    };
                    this.selectItem(item);
                }
            }
            
            selectHighlightedResult() {
                const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
                if (highlightedResult) {
                    const item = {
                        id: highlightedResult.dataset.itemId,
                        item_name: highlightedResult.dataset.itemName,
                        cost_price: this.safeNumber(highlightedResult.dataset.costPrice),
                        sale_price: this.safeNumber(highlightedResult.dataset.salePrice)
                    };
                    this.selectItem(item);
                } else {
                    // Fallback to first result if no item is highlighted
                    this.selectFirstResult();
                }
            }
            
            navigateResults(direction) {
                const results = this.resultsContainer.querySelectorAll('.result-item');
                const currentIndex = Array.from(results).findIndex(item => item.classList.contains('selected'));
                
                let newIndex;
                if (direction === 'down') {
                    newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                } else {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                }
                
                // Remove previous selection
                results.forEach(item => item.classList.remove('selected', 'bg-blue-100'));
                
                // Add new selection
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-blue-100');
                    results[newIndex].scrollIntoView({ block: 'nearest' });
                }
            }
            
            showDropdown() {
                this.dropdown.classList.remove('hidden');
            }
            
            hideDropdown() {
                this.dropdown.classList.add('hidden');
            }
            
            showLoading() {
                this.loadingIndicator.classList.remove('hidden');
            }
            
            hideLoading() {
                this.loadingIndicator.classList.add('hidden');
            }
            
            safeNumber(value) {
                // Convert value to a safe number, defaulting to 0 if invalid
                if (value === null || value === undefined || value === '') {
                    return 0;
                }
                
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }
            
            showError(message) {
                this.resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-red-500 text-center">
                        <div class="font-medium">Error Loading Items</div>
                        <div class="mt-1">${message}</div>
                        <div class="mt-2 text-xs text-gray-500">
                            Please check your browser console for more details.
                        </div>
                    </div>
                `;
            }
        }

        // Party Searchable Dropdown functionality
        class PartySearchableDropdown {
            constructor(container, options = {}) {
                this.container = container;
                this.input = container.querySelector('.searchable-input');
                this.hiddenInput = container.querySelector('.selected-item-id');
                this.dropdown = container.querySelector('.searchable-dropdown');
                this.resultsContainer = container.querySelector('.search-results-container');
                this.paginationContainer = container.querySelector('.pagination-container');
                this.loadingIndicator = container.querySelector('.loading-indicator');
                
                this.searchTimeout = null;
                this.currentPage = 1;
                this.searchTerm = '';
                this.selectedItem = null;
                
                // Configurable options
                this.itemsPerPage = options.itemsPerPage || 10;
                this.debounceDelay = options.debounceDelay || 300;
                this.minSearchLength = options.minSearchLength || 2;
                
                this.init();
            }
            
            init() {
                this.bindEvents();
                this.setupGlobalClickHandler();
            }
            
            bindEvents() {
                // Input focus
                this.input.addEventListener('focus', () => {
                    this.showDropdown();
                    this.performSearch();
                });
                
                // Input input
                this.input.addEventListener('input', (e) => {
                    this.searchTerm = e.target.value;
                    this.currentPage = 1;
                    this.showDropdown(); // Ensure dropdown is visible when typing
                    
                    // Clear selection if input becomes empty
                    if (!this.searchTerm.trim() && this.selectedItem) {
                        this.clearSelection();
                    }
                    
                    this.debounceSearch();
                });
                
                // Input keydown
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.selectHighlightedResult();
            } else if (e.key === 'Escape') {
                this.hideDropdown();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.navigateResults('down');
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.navigateResults('up');
            }
        });
            }
            
            setupGlobalClickHandler() {
                document.addEventListener('click', (e) => {
                    if (!this.container.contains(e.target)) {
                        this.hideDropdown();
                    }
                });
            }
            
            debounceSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, this.debounceDelay);
            }
            
            async performSearch() {
                if (this.searchTerm.length < this.minSearchLength) {
                    this.showInitialResults();
                    return;
                }
                
                this.showLoading();
                
                try {
                    const response = await fetch(`/api/parties/search?q=${encodeURIComponent(this.searchTerm)}&page=${this.currentPage}&limit=${this.itemsPerPage}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    // Validate the data structure
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from search API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Party search error:', error);
                    this.showError(`Search failed: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            async showInitialResults() {
                this.showLoading();
                
                try {
                    const response = await fetch(`/api/parties?page=${this.currentPage}&limit=${this.itemsPerPage}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    // Validate the data structure
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial party load error:', error);
                    this.showError(`Failed to load parties: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            displayResults(parties, meta) {
                this.resultsContainer.innerHTML = '';
                
                if (!parties || !Array.isArray(parties) || parties.length === 0) {
                    this.resultsContainer.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            <div class="mb-3">
                                ${this.searchTerm ? 'No parties found matching your search.' : 'No parties available.'}
                            </div>
                            <button type="button" 
                                    class="add-party-btn inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                    onclick="window.open('{{ route('parties.create') }}', '_blank')">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Party
                            </button>
                        </div>
                    `;
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                
                // Create result items
                parties.forEach((party, index) => {
                    // Validate party data
                    if (!party || !party.id || !party.name) {
                        return; // Skip invalid parties
                    }
                    
                    const resultItem = document.createElement('div');
                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                    resultItem.dataset.partyId = party.id;
                    resultItem.dataset.partyName = party.name;
                    resultItem.dataset.partyCnic = party.cnic || '';
                    
                    resultItem.innerHTML = `
                        <div class="font-medium text-gray-900">${party.name}</div>
                        <div class="text-sm text-gray-500">
                            ${party.cnic ? `CNIC: ${party.cnic}` : ''}
                        </div>
                    `;
                    
                    resultItem.addEventListener('click', () => {
                        this.selectItem(party);
                    });
                    
                    this.resultsContainer.appendChild(resultItem);
                });
                
                // Show pagination if needed
                if (meta && meta.last_page > 1) {
                    this.showPagination(meta);
                } else {
                    this.paginationContainer.classList.add('hidden');
                }
            }
            
            showPagination(meta) {
                this.paginationContainer.classList.remove('hidden');
                
                const pageInfo = this.paginationContainer.querySelector('.page-info');
                const prevBtn = this.paginationContainer.querySelector('.prev-page');
                const nextBtn = this.paginationContainer.querySelector('.next-page');
                
                pageInfo.textContent = `Page ${meta.current_page} of ${meta.last_page}`;
                
                prevBtn.disabled = meta.current_page <= 1;
                nextBtn.disabled = meta.current_page >= meta.last_page;
                
                prevBtn.onclick = () => {
                    if (meta.current_page > 1) {
                        this.currentPage = meta.current_page - 1;
                        this.performSearch();
                    }
                };
                
                nextBtn.onclick = () => {
                    if (meta.current_page < meta.last_page) {
                        this.currentPage = meta.current_page + 1;
                        this.performSearch();
                    }
                };
            }
            
            selectItem(party) {
                this.selectedItem = party;
                
                // Create display text with name and additional info
                let displayText = party.name;
                if (party.cnic) {
                    displayText += ` (CNIC: ${party.cnic})`;
                }
                
                this.input.value = displayText;
                this.hiddenInput.value = party.id;
                
                // Trigger the change event on the hidden input to fetch balance
                this.hiddenInput.dispatchEvent(new Event('change'));
                
                this.hideDropdown();
            }
            
            clearSelection() {
                // Clear the current selection
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
            }
            
            selectFirstResult() {
                const firstResult = this.resultsContainer.querySelector('.result-item');
                if (firstResult) {
                    const party = {
                        id: firstResult.dataset.partyId,
                        name: firstResult.dataset.partyName,
                        cnic: firstResult.dataset.partyCnic
                    };
                    this.selectItem(party);
                }
            }
            
            selectHighlightedResult() {
                const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
                if (highlightedResult) {
                    const party = {
                        id: highlightedResult.dataset.partyId,
                        name: highlightedResult.dataset.partyName,
                        cnic: highlightedResult.dataset.partyCnic
                    };
                    this.selectItem(party);
                } else {
                    // Fallback to first result if no item is highlighted
                    this.selectFirstResult();
                }
            }
            
            navigateResults(direction) {
                const results = this.resultsContainer.querySelectorAll('.result-item');
                const currentIndex = Array.from(results).findIndex(item => item.classList.contains('selected'));
                
                let newIndex;
                if (direction === 'down') {
                    newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                } else {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                }
                
                // Remove previous selection
                results.forEach(item => item.classList.remove('selected', 'bg-blue-100'));
                
                // Add new selection
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-blue-100');
                    results[newIndex].scrollIntoView({ block: 'nearest' });
                }
            }
            
            showDropdown() {
                this.dropdown.classList.remove('hidden');
            }
            
            hideDropdown() {
                this.dropdown.classList.add('hidden');
            }
            
            showLoading() {
                this.loadingIndicator.classList.remove('hidden');
            }
            
            hideLoading() {
                this.loadingIndicator.classList.add('hidden');
            }
            
            showError(message) {
                this.resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-red-500 text-center">
                        <div class="font-medium">Error Loading Parties</div>
                        <div class="mt-1">${message}</div>
                        <div class="mt-2 text-xs text-gray-500">
                            Please check your browser console for more details.
                        </div>
                    </div>
                `;
            }
        }

        // Function to populate unit price and sale price when general item is selected
        function populateItemPrices(selectElement) {
            const row = selectElement.closest('.general-item-row');
            const selectedItemId = selectElement.value;

            if (selectedItemId) {
                // Get the item data from the server
                fetch(`/api/general-items/${selectedItemId}`)
                    .then(response => response.json())
                    .then(item => {
                        if (item) {
                            const unitPriceInput = row.querySelector('input[name*="[unit_price]"]');
                            const salePriceInput = row.querySelector('input[name*="[sale_price]"]');

                            if (unitPriceInput) {
                                unitPriceInput.value = item.cost_price ? item.cost_price : '';
                            }
                            if (salePriceInput) {
                                salePriceInput.value = item.sale_price ? item.sale_price : '';
                            }

                            // Trigger calculation
                            calculateLineTotal(unitPriceInput);
                            calculateTotals();
                        }
                    })

                    .catch(error => {
                        console.error('Error fetching item data:', error);
                    });
            }
        }

        // Function to populate arm line prices with default values
        function populateArmPrices(armRow) {
            const unitPriceInput = armRow.querySelector('input[name*="[unit_price]"]');
            const salePriceInput = armRow.querySelector('input[name*="[sale_price]"]');

            if (unitPriceInput && !unitPriceInput.value) {
                unitPriceInput.value = '0';
            }
            if (salePriceInput && !salePriceInput.value) {
                salePriceInput.value = '0';
            }
        }

        // Function to clone arm data from the last row to the new row
        function cloneArmData(sourceRow, newRow) {
            // Add visual indicator that this row was cloned
            newRow.classList.add('cloned');
            
            // Get all select elements from source row
            const sourceSelects = sourceRow.querySelectorAll('select');
            const newSelects = newRow.querySelectorAll('select');
            
            // Clone select values
            sourceSelects.forEach((sourceSelect, index) => {
                if (newSelects[index] && sourceSelect.value) {
                    newSelects[index].value = sourceSelect.value;
                }
            });
            
            // Get price inputs from source row
            const sourceUnitPrice = sourceRow.querySelector('input[name*="[unit_price]"]');
            const sourceSalePrice = sourceRow.querySelector('input[name*="[sale_price]"]');
            
            // Get price inputs from new row
            const newUnitPrice = newRow.querySelector('input[name*="[unit_price]"]');
            const newSalePrice = newRow.querySelector('input[name*="[sale_price]"]');
            
            // Clone price values
            if (sourceUnitPrice && newUnitPrice && sourceUnitPrice.value) {
                newUnitPrice.value = sourceUnitPrice.value;
            }
            if (sourceSalePrice && newSalePrice && sourceSalePrice.value) {
                newSalePrice.value = sourceSalePrice.value;
            }
            
            // Clear the serials field (this is the only field we want to clear)
            const newSerials = newRow.querySelector('input[name*="[serials]"]');
            if (newSerials) {
                newSerials.value = '';
                // Focus on the serials field for immediate typing
                setTimeout(() => {
                    newSerials.focus();
                }, 100);
            }
            
            // Update arm title for the new row
            setTimeout(() => {
                updateArmTitle.call(newRow.querySelector('input[name*="[serials]"]'));
            }, 50);
            
            // Remove the cloned class after animation completes
            setTimeout(() => {
                newRow.classList.remove('cloned');
            }, 1000);
        }

        // Initialize calculation listeners
        addCalculationListeners();
        
        // Initialize counters on page load
        updateCounters();

        // Initialize searchable dropdowns for any existing elements
        document.querySelectorAll('.searchable-select-container').forEach(container => {
            if (!container.dataset.initialized) {
                // Check if this is the party dropdown
                if (container.querySelector('#party_search_input')) {
                    new PartySearchableDropdown(container, {
                        itemsPerPage: 15,
                        debounceDelay: 300,
                        minSearchLength: 2
                    });
                } else {
                    new SearchableDropdown(container, {
                        itemsPerPage: 15,
                        debounceDelay: 300,
                        minSearchLength: 2
                    });
                }
                container.dataset.initialized = 'true';
            }
        });

        // Payment type state is initialized by handlePaymentTypeChange() which is called on page load

        // Form submission validation
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            const paymentType = document.getElementById('payment_type').value;
            const partyId = document.getElementById('party_id').value;
            const partyError = document.getElementById('party_error');
            
            // Remove existing error message
            if (partyError) {
                partyError.remove();
            }
            
            // Clear previous validation errors
            clearValidationErrors();
            
            let hasErrors = false;
            
            // Validate party selection for credit payments
            if (paymentType === 'credit' && (!partyId || partyId === '')) {
                e.preventDefault();
                
                // Create error message
                const errorDiv = document.createElement('p');
                errorDiv.id = 'party_error';
                errorDiv.className = 'mt-1 text-xs text-red-600';
                errorDiv.textContent = 'Party selection is required for credit payments.';
                
                // Insert error message after the party field
                const partyField = document.getElementById('party_id').closest('div');
                partyField.appendChild(errorDiv);
                
                // Scroll to the error
                partyField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                return false;
            }
            
            // Validate general items
            const generalItemRows = document.querySelectorAll('.general-item-row');
            generalItemRows.forEach((row, index) => {
                const selectedItemId = row.querySelector('.selected-item-id').value;
                if (!selectedItemId || selectedItemId === '') {
                    showGeneralItemError(row, 'Please select a general item.');
                    hasErrors = true;
                }
            });
            
            // Validate arm serials
            const armRows = document.querySelectorAll('.arm-row');
            armRows.forEach((row, index) => {
                const serialsInput = row.querySelector('input[name*="[serials]"]');
                if (!serialsInput || !serialsInput.value || serialsInput.value.trim() === '') {
                    showArmSerialError(row, 'Serial numbers are required.');
                    hasErrors = true;
                }
            });
            
            // Check for duplicate serial numbers within the form
            const allSerials = [];
            armRows.forEach((row) => {
                const serialsInput = row.querySelector('input[name*="[serials]"]');
                if (serialsInput && serialsInput.value) {
                    const serials = serialsInput.value.split(',').map(s => s.trim()).filter(s => s);
                    allSerials.push(...serials);
                }
            });
            
            const duplicateSerials = allSerials.filter((serial, index) => allSerials.indexOf(serial) !== index);
            if (duplicateSerials.length > 0) {
                armRows.forEach((row) => {
                    const serialsInput = row.querySelector('input[name*="[serials]"]');
                    if (serialsInput && serialsInput.value) {
                        const rowSerials = serialsInput.value.split(',').map(s => s.trim()).filter(s => s);
                        const hasDuplicates = rowSerials.some(serial => duplicateSerials.includes(serial));
                        if (hasDuplicates) {
                            showArmSerialError(row, 'Duplicate serial numbers found: ' + [...new Set(duplicateSerials)].join(', '));
                            hasErrors = true;
                        }
                    }
                });
            }
            
            if (hasErrors) {
                e.preventDefault();
                return false;
            }
            
            // Save form data before submission
            savePurchaseFormData();
            
            // Set flags to indicate form is being submitted
            sessionStorage.setItem('purchase_form_submitting', 'true');
            localStorage.setItem('purchase_form_failed_submission', 'true');
        });
        
        // Clear flags when leaving the page (handles successful submissions)
        window.addEventListener('beforeunload', function(e) {
            // Only clear if we're not on the create page (successful navigation)
            if (!window.location.href.includes('/purchases/create')) {
                console.log('Leaving create page - clearing flags');
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
            }
        });

        // Clear localStorage on fresh page load (after successful submission)
        document.addEventListener('DOMContentLoaded', function() {
            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            const hasOldInput = {{ old('party_id') || old('bank_id') ? 'true' : 'false' }};
            
            // If no errors and no old input, this is a fresh page load after successful submission or first visit
            // Clear localStorage to prevent pre-filling from previous submission
            if (!hasErrors && !hasOldInput) {
                console.log('Fresh page load detected - clearing localStorage');
                clearPurchaseFormData();
            }
        });
        
        // Function to clear validation errors
        function clearValidationErrors() {
            document.querySelectorAll('.general-item-error').forEach(error => {
                error.classList.add('hidden');
                error.textContent = '';
            });
            document.querySelectorAll('.arm-serial-error').forEach(error => {
                error.classList.add('hidden');
                error.textContent = '';
            });
            document.querySelectorAll('.searchable-input').forEach(input => {
                input.classList.remove('border-red-500');
            });
            document.querySelectorAll('input[name*="[serials]"]').forEach(input => {
                input.classList.remove('border-red-500');
            });
        }
        
        // Function to show general item error
        function showGeneralItemError(row, message) {
            const errorDiv = row.querySelector('.general-item-error');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                
                const searchInput = row.querySelector('.searchable-input');
                if (searchInput) {
                    searchInput.classList.add('border-red-500');
                }
            }
        }
        
        // Function to show arm serial error
        function showArmSerialError(row, message) {
            const errorDiv = row.querySelector('.arm-serial-error');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                
                const serialInput = row.querySelector('input[name*="[serials]"]');
                if (serialInput) {
                    serialInput.classList.add('border-red-500');
                }
            }
        }

        // LocalStorage functions for data persistence
        function savePurchaseFormData() {
            try {
                const formData = {
                    business_id: '{{ session("active_business") }}', // Store business_id for validation
                    form_type: 'purchase', // Identify form type to prevent cross-form restoration
                    payment_type: document.getElementById('payment_type').value,
                    party_id: document.getElementById('party_id').value,
                    party_display: document.getElementById('party_search_input')?.value || '',
                    bank_id: document.getElementById('bank_id').value,
                    invoice_date: document.getElementById('invoice_date').value,
                    shipping_charges: document.getElementById('shipping_charges').value,
                    name_of_customer: document.getElementById('name_of_customer')?.value || '',
                    father_name: document.getElementById('father_name')?.value || '',
                    contact: document.getElementById('contact')?.value || '',
                    address: document.getElementById('address')?.value || '',
                    cnic: document.getElementById('cnic')?.value || '',
                    licence_no: document.getElementById('licence_no')?.value || '',
                    licence_issue_date: document.getElementById('licence_issue_date')?.value || '',
                    licence_valid_upto: document.getElementById('licence_valid_upto')?.value || '',
                    licence_issued_by: document.getElementById('licence_issued_by')?.value || '',
                    re_reg_no: document.getElementById('re_reg_no')?.value || '',
                    dc: document.getElementById('dc')?.value || '',
                    Date: document.getElementById('Date')?.value || '',
                    general_items: [],
                    arms: []
                };
                
                // Collect general items
                document.querySelectorAll('.general-item-row').forEach((row, index) => {
                    const itemData = {
                        general_item_id: row.querySelector('.selected-item-id')?.value || '',
                        general_item_display: row.querySelector('.searchable-input')?.value || '',
                        qty: row.querySelector('.general-qty')?.value || '',
                        unit_price: row.querySelector('.general-price')?.value || '',
                        sale_price: row.querySelector('.general-sale-price')?.value || '',
                        description: row.querySelector('textarea[name*="[description]"]')?.value || ''
                    };
                    
                    // Debug logging
                    console.log(`General item ${index}:`, itemData);
                    
                    // Only save if there's meaningful data (even if no item selected, save the quantities)
                    if (itemData.qty || itemData.unit_price || itemData.sale_price || itemData.general_item_display) {
                        formData.general_items.push(itemData);
                    }
                });
                
                // Collect arms
                document.querySelectorAll('.arm-row').forEach((row, index) => {
                    const armData = {
                        arm_type_id: row.querySelector('select[name*="[arm_type_id]"]')?.value || '',
                        arm_make_id: row.querySelector('select[name*="[arm_make_id]"]')?.value || '',
                        arm_caliber_id: row.querySelector('select[name*="[arm_caliber_id]"]')?.value || '',
                        arm_category_id: row.querySelector('select[name*="[arm_category_id]"]')?.value || '',
                        arm_condition_id: row.querySelector('select[name*="[arm_condition_id]"]')?.value || '',
                        serials: row.querySelector('input[name*="[serials]"]')?.value || '',
                        unit_price: row.querySelector('input[name*="[unit_price]"]')?.value || '',
                        sale_price: row.querySelector('input[name*="[sale_price]"]')?.value || ''
                    };
                    // Only save if there's meaningful data
                    if (armData.serials || armData.unit_price) {
                        formData.arms.push(armData);
                    }
                });
                
                // Only save if there's meaningful data
                if (formData.payment_type || formData.party_id || formData.bank_id || 
                    formData.general_items.length > 0 || formData.arms.length > 0) {
                    localStorage.setItem('purchase_form_data', JSON.stringify(formData));
                    console.log('Purchase form data saved:', formData);
                }
            } catch (error) {
                console.error('Error saving form data:', error);
            }
        }
        
        function loadSavedPurchaseData() {
            try {
                const savedData = localStorage.getItem('purchase_form_data');
                if (!savedData) {
                    console.log('No saved purchase data found');
                    return;
                }
                
                console.log('Loading saved purchase data:', savedData);
                const formData = JSON.parse(savedData);
                
                // Validate business_id and form_type - clear data if validation fails
                const currentBusinessId = '{{ session("active_business") }}';
                if (!formData.business_id || formData.business_id !== currentBusinessId) {
                    console.log('Saved data belongs to different business or missing business_id, clearing...', {
                        savedBusinessId: formData.business_id || 'missing',
                        currentBusinessId: currentBusinessId
                    });
                    clearPurchaseFormData();
                    return;
                }
                
                // Validate form_type to prevent cross-form restoration
                if (!formData.form_type || formData.form_type !== 'purchase') {
                    console.log('Saved data is from different form type, clearing...', {
                        savedFormType: formData.form_type || 'missing',
                        currentFormType: 'purchase'
                    });
                    clearPurchaseFormData();
                    return;
                }
                
                // Restore header data
                if (formData.payment_type) {
                    document.getElementById('payment_type').value = formData.payment_type;
                    console.log('Restored payment_type:', formData.payment_type);
                }
                if (formData.party_id) {
                    document.getElementById('party_id').value = formData.party_id;
                    console.log('Restored party_id:', formData.party_id);
                    
                    // Use the new function to restore party display
                    if (window.restorePartyDisplay) {
                        window.restorePartyDisplay(formData.party_id);
                        console.log('Restored party_display via API');
                    }
                }
                if (formData.bank_id) {
                    document.getElementById('bank_id').value = formData.bank_id;
                    console.log('Restored bank_id:', formData.bank_id);
                }
                if (formData.invoice_date) {
                    document.getElementById('invoice_date').value = formData.invoice_date;
                }
                if (formData.shipping_charges) {
                    document.getElementById('shipping_charges').value = formData.shipping_charges;
                }
                
                // Restore customer details
                const customerFields = ['name_of_customer', 'father_name', 'contact', 'address', 'cnic', 
                                     'licence_no', 'licence_issue_date', 'licence_valid_upto', 'licence_issued_by', 
                                     're_reg_no', 'dc', 'Date'];
                customerFields.forEach(field => {
                    if (formData[field] && document.getElementById(field)) {
                        document.getElementById(field).value = formData[field];
                    }
                });
                
                // Restore party license details
                const partyLicenseFields = ['party_license_no', 'party_license_issue_date', 'party_license_valid_upto', 
                                          'party_license_issued_by', 'party_re_reg_no', 'party_dc', 'party_dc_date'];
                partyLicenseFields.forEach(field => {
                    if (formData[field] && document.getElementById(field)) {
                        document.getElementById(field).value = formData[field];
                    }
                });
                
                // Restore general items
                if (formData.general_items && formData.general_items.length > 0) {
                    console.log('Restoring general items:', formData.general_items);
                    console.log('General items count:', formData.general_items.length);
                    formData.general_items.forEach((item, i) => {
                        console.log(`General item ${i}:`, item);
                    });
                    
                    // Ensure we have at least one row by clicking add button if needed
                    const existingRows = document.querySelectorAll('.general-item-row');
                    console.log('Existing rows found:', existingRows.length);
                    
                    if (existingRows.length === 0) {
                        console.log('No existing rows found, creating first row');
                        document.getElementById('add_general_item').click();
                        // Add a longer delay to ensure the row is fully added to DOM
                        setTimeout(() => {
                            const newRows = document.querySelectorAll('.general-item-row');
                            console.log('Rows after creation:', newRows.length);
                            if (newRows.length > 0) {
                                restoreGeneralItems(formData.general_items);
                            } else {
                                console.error('Failed to create general item row');
                            }
                        }, 200);
                    } else {
                        restoreGeneralItems(formData.general_items);
                    }
                }
                
                function restoreGeneralItems(generalItems) {
                    console.log('restoreGeneralItems called with:', generalItems);
                    generalItems.forEach((item, index) => {
                        console.log(`Processing general item ${index}:`, item);
                        if (index > 0) {
                            console.log(`Creating additional row for item ${index}`);
                            document.getElementById('add_general_item').click();
                        }
                        setTimeout(() => {
                            const rows = document.querySelectorAll('.general-item-row');
                            const row = rows[index]; // Use index instead of last row
                            console.log(`Restoring item ${index}, found ${rows.length} rows, targeting row:`, row);
                            if (row) {
                                // Restore basic fields first
                                if (item.qty) {
                                    const qtyInput = row.querySelector('.general-qty');
                                    if (qtyInput) {
                                        qtyInput.value = item.qty;
                                        console.log(`Restored qty: ${item.qty}`);
                                    } else {
                                        console.log('Qty input not found');
                                    }
                                }
                                if (item.unit_price) {
                                    const priceInput = row.querySelector('.general-price');
                                    if (priceInput) {
                                        priceInput.value = item.unit_price;
                                        console.log(`Restored unit_price: ${item.unit_price}`);
                                    } else {
                                        console.log('Price input not found');
                                    }
                                }
                                if (item.sale_price) {
                                    const salePriceInput = row.querySelector('.general-sale-price');
                                    if (salePriceInput) {
                                        salePriceInput.value = item.sale_price;
                                        console.log(`Restored sale_price: ${item.sale_price}`);
                                    } else {
                                        console.log('Sale price input not found');
                                    }
                                }
                                if (item.description) {
                                    const descInput = row.querySelector('textarea[name*="[description]"]');
                                    if (descInput) {
                                        descInput.value = item.description;
                                        console.log(`Restored description: ${item.description}`);
                                    }
                                }
                                
                                // Handle searchable dropdown
                                if (item.general_item_display) {
                                    const searchInput = row.querySelector('.searchable-input');
                                    const hiddenInput = row.querySelector('.selected-item-id');
                                    
                                    console.log('Search input found:', searchInput);
                                    console.log('Hidden input found:', hiddenInput);
                                    
                                    if (searchInput) {
                                        searchInput.value = item.general_item_display;
                                        console.log(`Restored display value: ${item.general_item_display}`);
                                        
                                        // If we have the item ID, set it directly
                                        if (item.general_item_id && hiddenInput) {
                                            hiddenInput.value = item.general_item_id;
                                            console.log(`Restored item ID: ${item.general_item_id}`);
                                        }
                                        
                                        // Use helper function to properly trigger the dropdown
                                        setTimeout(() => {
                                            triggerSearchableDropdownUpdate(searchInput);
                                        }, 100);
                                    }
                                }
                                
                                // Trigger calculations
                                const qtyInput = row.querySelector('.general-qty');
                                const priceInput = row.querySelector('.general-price');
                                if (qtyInput && priceInput) {
                                    calculateLineTotal(qtyInput);
                                }
                                
                                console.log('Successfully restored general item:', item);
                            } else {
                                console.error(`Failed to find row for general item ${index}`);
                            }
                        }, 300 * index);
                    });
                    console.log('Finished processing all general items');
                }
                
                // Restore arms
                if (formData.arms && formData.arms.length > 0) {
                    console.log('Restoring arms:', formData.arms);
                    
                    // Ensure we have at least one arm row
                    const existingRows = document.querySelectorAll('.arm-row');
                    if (existingRows.length === 0) {
                        console.log('No existing arm rows found, creating first row');
                        document.getElementById('add_arm').click();
                        
                        // Wait for the row to be added, then continue with restoration
                        setTimeout(() => {
                            // Continue with the restoration logic below
                            formData.arms.forEach((arm, index) => {
                                // Create additional rows if needed
                                if (index > 0) {
                                    document.getElementById('add_arm').click();
                                }
                                
                                setTimeout(() => {
                                    const rows = document.querySelectorAll('.arm-row');
                                    const targetRow = rows[index];
                                    
                                    if (targetRow) {
                                        console.log(`Restoring arm ${index}, found ${rows.length} rows, targeting row:`, targetRow);
                                        
                                        if (arm.arm_type_id) {
                                            const typeSelect = targetRow.querySelector('select[name*="[arm_type_id]"]');
                                            if (typeSelect) {
                                                typeSelect.value = arm.arm_type_id;
                                                console.log('Restored arm_type_id:', arm.arm_type_id);
                                            }
                                        }
                                        if (arm.arm_make_id) {
                                            const makeSelect = targetRow.querySelector('select[name*="[arm_make_id]"]');
                                            if (makeSelect) {
                                                makeSelect.value = arm.arm_make_id;
                                                console.log('Restored arm_make_id:', arm.arm_make_id);
                                            }
                                        }
                                        if (arm.arm_caliber_id) {
                                            const caliberSelect = targetRow.querySelector('select[name*="[arm_caliber_id]"]');
                                            if (caliberSelect) {
                                                caliberSelect.value = arm.arm_caliber_id;
                                                console.log('Restored arm_caliber_id:', arm.arm_caliber_id);
                                            }
                                        }
                                        if (arm.arm_category_id) {
                                            const categorySelect = targetRow.querySelector('select[name*="[arm_category_id]"]');
                                            if (categorySelect) {
                                                categorySelect.value = arm.arm_category_id;
                                                console.log('Restored arm_category_id:', arm.arm_category_id);
                                            }
                                        }
                                        if (arm.arm_condition_id) {
                                            const conditionSelect = targetRow.querySelector('select[name*="[arm_condition_id]"]');
                                            if (conditionSelect) {
                                                conditionSelect.value = arm.arm_condition_id;
                                                console.log('Restored arm_condition_id:', arm.arm_condition_id);
                                            }
                                        }
                                        if (arm.serials) {
                                            const serialsInput = targetRow.querySelector('input[name*="[serials]"]');
                                            if (serialsInput) {
                                                serialsInput.value = arm.serials;
                                                console.log('Restored serials:', arm.serials);
                                            }
                                        }
                                        if (arm.unit_price) {
                                            const unitPriceInput = targetRow.querySelector('input[name*="[unit_price]"]');
                                            if (unitPriceInput) {
                                                unitPriceInput.value = arm.unit_price;
                                                console.log('Restored unit_price:', arm.unit_price);
                                            }
                                        }
                                        if (arm.sale_price) {
                                            const salePriceInput = targetRow.querySelector('input[name*="[sale_price]"]');
                                            if (salePriceInput) {
                                                salePriceInput.value = arm.sale_price;
                                                console.log('Restored sale_price:', arm.sale_price);
                                            }
                                        }
                                        console.log('Successfully restored arm:', arm);
                                    }
                                }, 200 * index);
                            });
                            console.log('Finished processing all arms');
                        }, 100);
                        return;
                    }
                    
                    formData.arms.forEach((arm, index) => {
                        // Create additional rows if needed
                        if (index > 0) {
                            document.getElementById('add_arm').click();
                        }
                        
                        setTimeout(() => {
                            const rows = document.querySelectorAll('.arm-row');
                            const targetRow = rows[index];
                            
                            if (targetRow) {
                                console.log(`Restoring arm ${index}, found ${rows.length} rows, targeting row:`, targetRow);
                                
                                if (arm.arm_type_id) {
                                    const typeSelect = targetRow.querySelector('select[name*="[arm_type_id]"]');
                                    if (typeSelect) {
                                        typeSelect.value = arm.arm_type_id;
                                        console.log('Restored arm_type_id:', arm.arm_type_id);
                                    }
                                }
                                if (arm.arm_make_id) {
                                    const makeSelect = targetRow.querySelector('select[name*="[arm_make_id]"]');
                                    if (makeSelect) {
                                        makeSelect.value = arm.arm_make_id;
                                        console.log('Restored arm_make_id:', arm.arm_make_id);
                                    }
                                }
                                if (arm.arm_caliber_id) {
                                    const caliberSelect = targetRow.querySelector('select[name*="[arm_caliber_id]"]');
                                    if (caliberSelect) {
                                        caliberSelect.value = arm.arm_caliber_id;
                                        console.log('Restored arm_caliber_id:', arm.arm_caliber_id);
                                    }
                                }
                                if (arm.arm_category_id) {
                                    const categorySelect = targetRow.querySelector('select[name*="[arm_category_id]"]');
                                    if (categorySelect) {
                                        categorySelect.value = arm.arm_category_id;
                                        console.log('Restored arm_category_id:', arm.arm_category_id);
                                    }
                                }
                                if (arm.arm_condition_id) {
                                    const conditionSelect = targetRow.querySelector('select[name*="[arm_condition_id]"]');
                                    if (conditionSelect) {
                                        conditionSelect.value = arm.arm_condition_id;
                                        console.log('Restored arm_condition_id:', arm.arm_condition_id);
                                    }
                                }
                                if (arm.serials) {
                                    const serialsInput = targetRow.querySelector('input[name*="[serials]"]');
                                    if (serialsInput) {
                                        serialsInput.value = arm.serials;
                                        console.log('Restored serials:', arm.serials);
                                    }
                                }
                                if (arm.unit_price) {
                                    const unitPriceInput = targetRow.querySelector('input[name*="[unit_price]"]');
                                    if (unitPriceInput) {
                                        unitPriceInput.value = arm.unit_price;
                                        console.log('Restored unit_price:', arm.unit_price);
                                    }
                                }
                                if (arm.sale_price) {
                                    const salePriceInput = targetRow.querySelector('input[name*="[sale_price]"]');
                                    if (salePriceInput) {
                                        salePriceInput.value = arm.sale_price;
                                        console.log('Restored sale_price:', arm.sale_price);
                                    }
                                }
                                console.log('Successfully restored arm:', arm);
                            }
                        }, 200 * index);
                    });
                    
                    console.log('Finished processing all arms');
                }
                
                // Trigger payment type change and recalculate totals
                setTimeout(() => {
                    document.getElementById('payment_type').dispatchEvent(new Event('change'));
                    calculateTotals();
                    console.log('Purchase form data restored successfully');
                    
                    // Re-display validation errors after data restoration
                    setTimeout(() => {
                        console.log('Re-displaying validation errors after data restoration');
                        displayValidationErrors();
                    }, 500);
                }, 1000);
            } catch (error) {
                console.error('Error loading saved data:', error);
            }
        }
        
        function clearPurchaseFormData() {
            localStorage.removeItem('purchase_form_data');
        }
        
        function addDataPersistenceListeners() {
            const formElement = document.getElementById('purchaseForm');
            formElement.addEventListener('input', savePurchaseFormData);
            formElement.addEventListener('change', savePurchaseFormData);
        }
        
        addDataPersistenceListeners();
        
        // Load saved data after all initialization is complete
        // Only restore data if there was a failed submission
        setTimeout(() => {
            const wasSubmitting = sessionStorage.getItem('purchase_form_submitting');
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const errorMessages = document.querySelector('.bg-red-100, .bg-red-50, .text-red-700, .text-red-600, .alert-danger, [class*="error"]');
            const hasSavedData = localStorage.getItem('purchase_form_data');
            const hasFailedSubmissionFlag = localStorage.getItem('purchase_form_failed_submission');
            
            // Check if this is a fresh page load (no flags set)
            const isFreshPageLoad = !wasSubmitting && !hasFailedSubmissionFlag;
            
            console.log('[PurchaseCreate] restore-check', { wasSubmitting, hasSavedData: !!hasSavedData, hasFailedSubmissionFlag: !!hasFailedSubmissionFlag, isFreshPageLoad });
            
            // If there's a success message, clear everything immediately
            if (successMessage) {
                console.log('[PurchaseCreate] restore: success detected, clearing state');
                clearPurchaseFormData();
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
                return;
            }
            
            // If this is a fresh page load, clear any existing data and flags
            if (isFreshPageLoad) {
                console.log('[PurchaseCreate] restore: fresh load, clearing state');
                clearPurchaseFormData();
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
                return;
            }
            
            // Restore data if there are error messages OR if there was a submission attempt
            // This is more permissive to ensure data is preserved when needed
            if ((errorMessages && hasSavedData) || 
                (wasSubmitting && !successMessage && hasSavedData)) {
                console.log('[PurchaseCreate] restore: applying saved form data');
                // Add additional delay to ensure all components are initialized
                setTimeout(() => {
                    loadSavedPurchaseData();
                    
                    // Re-display validation errors after data restoration
                    setTimeout(() => {
                        console.log('[PurchaseCreate] restore: redisplaying validation errors');
                        displayValidationErrors();
                    }, 1000);
                    
                    // Keep flags until user fixes errors and resubmits
                    console.log('[PurchaseCreate] restore: keeping flags');
                }, 500);
            } else if (wasSubmitting && !successMessage && !errorMessages) {
                // There was a submission but no success message and no errors - might be a success case
                console.log('[PurchaseCreate] restore: no errors, clearing flags');
                        sessionStorage.removeItem('purchase_form_submitting');
                        localStorage.removeItem('purchase_form_failed_submission');
            } else {
                console.log('No restoration needed - clearing flags');
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
            }
        }, 1500);
        
        // Additional fallback: Clear data after 3 seconds if no errors are detected
        // This handles cases where success messages might not be detected properly
        setTimeout(() => {
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const errorMessages = document.querySelector('.bg-red-100, .bg-red-50, .text-red-700, .text-red-600, .alert-danger, [class*="error"]');
            const wasSubmitting = sessionStorage.getItem('purchase_form_submitting');
            
            console.log('Fallback check after 3 seconds:', {
                wasSubmitting: wasSubmitting,
                hasSuccessMessage: !!successMessage,
                hasErrorMessages: !!errorMessages,
                successMessageText: successMessage ? successMessage.textContent : 'none'
            });
            
            // If there's a success message OR no error messages and we were submitting, clear everything
            if (successMessage || (!errorMessages && wasSubmitting)) {
                console.log('Fallback: Clearing data due to success or no errors after submission');
                clearPurchaseFormData();
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
            }
        }, 3000);
        
        // Additional aggressive fallback: Clear data if we're on create page with no errors
        // This handles cases where flags persist after successful navigation
        setTimeout(() => {
            const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const wasSubmitting = sessionStorage.getItem('purchase_form_submitting');
            const hasFailedSubmissionFlag = localStorage.getItem('purchase_form_failed_submission');
            const isCreatePage = window.location.href.includes('/purchases/create');
            
            console.log('Aggressive fallback check after 5 seconds:', {
                wasSubmitting: wasSubmitting,
                hasFailedSubmissionFlag: hasFailedSubmissionFlag,
                hasErrorMessages: !!errorMessages,
                hasSuccessMessage: !!successMessage,
                isCreatePage: isCreatePage,
                errorMessageText: errorMessages ? errorMessages.textContent : 'none',
                successMessageText: successMessage ? successMessage.textContent : 'none'
            });
            
            // Clear data if:
            // 1. Success message is visible, OR
            // 2. On create page with no errors (regardless of flags - might be from successful navigation)
            if (successMessage || (isCreatePage && !errorMessages)) {
                console.log('Aggressive fallback: Clearing data - success or no errors on create page');
                clearPurchaseFormData();
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
            } else {
                console.log('Aggressive fallback: Preserving data - errors detected');
            }
        }, 5000);
        
        // Watch for success messages that might appear after page load
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
                    if (successMessage) {
                        console.log('Success message detected via MutationObserver - clearing all data and flags');
                        clearPurchaseFormData();
                        sessionStorage.removeItem('purchase_form_submitting');
                        localStorage.removeItem('purchase_form_failed_submission');
                        observer.disconnect(); // Stop observing after clearing
                    }
                }
            });
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Stop observing after 10 seconds to avoid memory leaks
        setTimeout(() => {
            observer.disconnect();
        }, 10000);
        
        // Helper function to trigger searchable dropdown update
        function triggerSearchableDropdownUpdate(searchInput) {
            if (searchInput && searchInput.value) {
                // Trigger input event to make the searchable dropdown process the value
                searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                searchInput.dispatchEvent(new Event('change', { bubbles: true }));
                
                // Also try to trigger focus and blur to activate the dropdown
                searchInput.focus();
                setTimeout(() => {
                    searchInput.blur();
                }, 100);
            }
        }

        // Debug functions (can be removed in production)
        window.debugPurchaseForm = {
            save: savePurchaseFormData,
            load: loadSavedPurchaseData,
            clear: clearPurchaseFormData,
            show: () => {
                const data = localStorage.getItem('purchase_form_data');
                console.log('Current saved data:', data ? JSON.parse(data) : 'No data');
                return data ? JSON.parse(data) : null;
            },
            triggerDropdown: triggerSearchableDropdownUpdate,
            testRestoration: () => {
                console.log('Testing restoration manually...');
                const data = localStorage.getItem('purchase_form_data');
                if (data) {
                    const formData = JSON.parse(data);
                    console.log('Found saved data:', formData);
                    if (formData.general_items && formData.general_items.length > 0) {
                        console.log('Testing general items restoration...');
                        restoreGeneralItems(formData.general_items);
                    }
                } else {
                    console.log('No saved data found');
                }
            },
            clearAll: () => {
                console.log('Clearing all data and flags...');
                localStorage.removeItem('purchase_form_data');
                localStorage.removeItem('purchase_form_failed_submission');
                sessionStorage.removeItem('purchase_form_submitting');
                console.log('All data cleared');
            },
            checkState: () => {
                console.log('Current state:', {
                    hasSavedData: !!localStorage.getItem('purchase_form_data'),
                    hasFailedFlag: !!localStorage.getItem('purchase_form_failed_submission'),
                    hasSubmittingFlag: !!sessionStorage.getItem('purchase_form_submitting'),
                    hasSuccessMessage: !!document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success'),
                    hasErrorMessages: !!document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]')
                });
            }
        };
        
        window.addEventListener('pageshow', function(event) {
            // Only clear data if we're coming from a successful submission
            const wasSubmitting = sessionStorage.getItem('purchase_form_submitting');
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
            const isCreatePage = window.location.href.includes('/purchases/create');
            const hasFailedSubmissionFlag = localStorage.getItem('purchase_form_failed_submission');
            
            console.log('[PurchaseCreate] pageshow', { hasErrorMessages: !!errorMessages, hasSuccessMessage: !!successMessage });
            
            // If there's a success message, clear everything immediately
            if (successMessage && isCreatePage) {
                console.log('[PurchaseCreate] pageshow: success detected, clearing state');
                clearPurchaseFormData();
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
                return;
            }
            
            // If this is a fresh page load (no flags set), clear everything
            if (!wasSubmitting && !hasFailedSubmissionFlag && isCreatePage) {
                console.log('[PurchaseCreate] pageshow: fresh load, clearing state');
                clearPurchaseFormData();
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
                return;
            }
            
            if (wasSubmitting && successMessage && isCreatePage) {
                console.log('[PurchaseCreate] pageshow: clearing after success');
                clearPurchaseFormData();
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
            } else if (!wasSubmitting && !errorMessages && !hasFailedSubmissionFlag) {
                // Fresh page load with no errors - always clear flags
                console.log('[PurchaseCreate] pageshow: clearing flags');
                sessionStorage.removeItem('purchase_form_submitting');
                localStorage.removeItem('purchase_form_failed_submission');
            } else if (errorMessages) {
                // There are error messages - preserve flags for restoration
                console.log('[PurchaseCreate] pageshow: errors visible, preserving flags');
            } else if (wasSubmitting && !successMessage) {
                // There was a submission but no success message - let restoration logic handle it
                if (errorMessages) {
                    console.log('[PurchaseCreate] pageshow: failed submission with errors');
                } else {
                    console.log('[PurchaseCreate] pageshow: failed submission, no visible errors');
                    // Don't clear flags here - let the restoration logic handle it
                }
            }
            // Note: Don't clear sessionStorage for failed submissions here
            // Let the restoration logic handle it after checking
        });

        // Handle pre-selected party from old form data - only if we're restoring data
        // This will be called from the main restoration logic when needed
        window.restorePartyDisplay = function(partyId) {
            if (partyId) {
                fetch(`/api/parties/${partyId}`)
                .then(response => response.json())
                .then(party => {
                    if (party && !party.error) {
                        const partySearchInput = document.getElementById('party_search_input');
                        
                        // Create display text with name and additional info
                        let displayText = party.name;
                        if (party.cnic) {
                            displayText += ` (CNIC: ${party.cnic})`;
                        }
                        
                        partySearchInput.value = displayText;
                        // Trigger balance fetch
                        fetchPartyBalance(party.id);
                    }
                })
                .catch(error => {
                    console.error('Error fetching pre-selected party:', error);
                });
        }
        };

        // Input masks for Pakistan format
        function applyInputMasks() {
            // CNIC mask: 00000-0000000-0
            const cnicInput = document.getElementById('cnic');
            if (cnicInput) {
                cnicInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
                    if (value.length > 0) {
                        if (value.length <= 5) {
                            value = value;
                        } else if (value.length <= 12) {
                            value = value.substring(0, 5) + '-' + value.substring(5);
                        } else {
                            value = value.substring(0, 5) + '-' + value.substring(5, 12) + '-' + value.substring(12, 13);
                        }
                    }
                    e.target.value = value;
                });
            }

            // Contact mask: 03XX-XXXXXXX
            const contactInput = document.getElementById('contact');
            if (contactInput) {
                contactInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
                    if (value.length > 0) {
                        // Ensure it starts with 03
                        if (!value.startsWith('03')) {
                            if (value.startsWith('3')) {
                                value = '0' + value;
                            } else if (!value.startsWith('0')) {
                                value = '03' + value;
                            }
                        }
                        
                        // Format: 03XX-XXXXXXX
                        if (value.length > 4) {
                            value = value.substring(0, 4) + '-' + value.substring(4);
                        }
                        
                        // Limit to 12 characters total (03XX-XXXXXXX)
                        if (value.length > 12) {
                            value = value.substring(0, 12);
                        }
                    }
                    e.target.value = value;
                });
            }
        }

        // Apply masks when customer details section is shown
        const originalPaymentTypeHandler = document.getElementById('payment_type').onchange;
        document.getElementById('payment_type').addEventListener('change', function() {
            if (originalPaymentTypeHandler) {
                originalPaymentTypeHandler.call(this);
            }
            
            // Apply masks when customer details are shown
            if (this.value === 'cash') {
                setTimeout(applyInputMasks, 100);
            }
        });

        // Apply masks on page load if customer details are already visible
        applyInputMasks();
    });
    </script>
    <style>
        /* Enhanced table styles */
        .purchase-table {
            width: 100%;
        }

        .purchase-table th {
            white-space: nowrap;
            background-color: #f9fafb;
        }

        .purchase-table td {
            vertical-align: top;
        }

        /* Field enhancements */
        .purchase-table input,
        .purchase-table select {
            min-height: 36px;
            font-size: 14px;
        }

        .purchase-table input:focus,
        .purchase-table select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Responsive container */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: white;
        }

        /* Enhanced button styles */
        .remove-btn {
            transition: all 0.2s ease-in-out;
        }

        .remove-btn:hover {
            transform: scale(1.05);
        }

        /* Table row hover effects */
        .purchase-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Better spacing for form sections */
        .form-section {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .form-section h2 {
            color: #111827;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-section p {
            color: #6b7280;
            margin-bottom: 1rem;
        }

        /* Optimize table column widths */
        #general_items_table {
            width: 100%;
            table-layout: auto;
        }
        
        #general_items_table th:nth-child(1) { width: 25%; min-width: 200px; } /* Item */
        #general_items_table th:nth-child(2) { width: 10%; min-width: 100px; } /* Qty */
        #general_items_table th:nth-child(3) { width: 15%; min-width: 120px; } /* Unit Price */
        #general_items_table th:nth-child(4) { width: 15%; min-width: 120px; } /* Sale Price */
        #general_items_table th:nth-child(5) { width: 15%; min-width: 120px; } /* Line Total */
        #general_items_table th:nth-child(6) { width: 10%; min-width: 80px; } /* Actions */
        
        /* General items table inputs */
        #general_items_table input,
        #general_items_table select {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        
        @media (max-width: 768px) {
            #general_items_table {
                min-width: 800px; /* Enable horizontal scroll on mobile */
            }
            
            #general_items_table th,
            #general_items_table td {
                padding: 0.5rem 0.375rem;
                font-size: 0.8125rem;
            }
            
            #general_items_table input,
            #general_items_table select {
                padding: 0.5rem 0.5rem;
                font-size: 14px;
            }
        }
        
        @media (max-width: 640px) {
            #general_items_table {
                min-width: 700px;
            }
            
            #general_items_table th,
            #general_items_table td {
                padding: 0.375rem 0.25rem;
                font-size: 0.75rem;
            }
            
            #general_items_table input,
            #general_items_table select {
                padding: 0.5rem 0.375rem;
                font-size: 13px;
            }
        }

        /* Enhanced Arms Section - Better field visibility and sizing */
        .arms-table input,
        .arms-table select {
            min-height: 38px !important;
            font-size: 14px !important;
            padding: 8px 12px !important;
            border-width: 1px !important;
            width: 100% !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            box-sizing: border-box !important;
        }

        .arms-table input:focus,
        .arms-table select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            outline: none !important;
            background-color: #ffffff !important;
        }

        /* Specific field enhancements for arms */
        .arms-table input[type="text"] {
            font-weight: 400;
            color: #374151;
        }

        .arms-table input[type="number"] {
            font-weight: 500;
            color: #1f2937;
        }

        .arms-table select {
            font-weight: 500;
            color: #1f2937;
            background-color: #ffffff;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 8px center;
            background-repeat: no-repeat;
            background-size: 16px 12px;
            padding-right: 32px !important;
        }

        .arms-table select:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }

        .arms-table select option {
            font-size: 14px;
            padding: 8px 12px;
            background-color: #ffffff;
            color: #1f2937;
        }

        .arms-table select option:hover {
            background-color: #f3f4f6;
        }

        /* Better spacing for arms table */
        .arms-table td {
            padding: 8px 6px !important;
            vertical-align: middle !important;
        }

        .arms-table th {
            padding: 12px 6px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            color: #374151 !important;
        }

        /* Enhanced remove button for arms */
        .arms-table .remove-arm {
            padding: 4px !important;
            border-radius: 4px !important;
            transition: all 0.2s ease-in-out !important;
        }

        .arms-table .remove-arm:hover {
            background-color: #fef2f2 !important;
            transform: scale(1.05) !important;
        }

        .arms-table .remove-arm svg {
            width: 16px !important;
            height: 16px !important;
        }

        /* Ensure table doesn't wrap text */
        .arms-table {
            table-layout: fixed !important;
        }
        
        .arms-table td,
        .arms-table th {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        /* Responsive Arms Table */
        @media (max-width: 1024px) {
            .arms-table {
                min-width: 1200px; /* Force horizontal scroll on tablets */
            }
            
            .arms-table th,
            .arms-table td {
                padding: 6px 4px !important;
            }
            
            .arms-table input,
            .arms-table select {
                font-size: 13px !important;
                padding: 6px 8px !important;
                min-height: 36px !important;
            }
            
            .arms-table select {
                padding-right: 28px !important;
                background-size: 14px 10px;
            }
        }
        
        @media (max-width: 768px) {
            .arms-table {
                min-width: 1000px; /* Maintain scroll on mobile */
            }
            
            .arms-table th,
            .arms-table td {
                padding: 6px 3px !important;
                font-size: 0.75rem !important;
            }
            
            .arms-table th {
                font-size: 0.625rem !important;
                padding: 8px 3px !important;
            }
            
            .arms-table input,
            .arms-table select {
                font-size: 13px !important;
                padding: 6px 6px !important;
                min-height: 36px !important;
                border-radius: 4px !important;
            }
            
            .arms-table select {
                padding-right: 24px !important;
                background-size: 12px 8px;
                background-position: right 4px center;
            }
            
            .arms-table .remove-arm {
                padding: 2px !important;
            }
            
            .arms-table .remove-arm svg {
                width: 14px !important;
                height: 14px !important;
            }
        }
        
        @media (max-width: 640px) {
            .arms-table {
                min-width: 900px;
            }
            
            .arms-table input,
            .arms-table select {
                font-size: 12px !important;
                padding: 5px 5px !important;
                min-height: 34px !important;
            }
            
            .arms-table select {
                padding-right: 22px !important;
            }
        }
        
        /* Searchable Dropdown Styles */
        .searchable-select-container {
            position: relative;
            z-index: 1000;
        }
        
        /* Party dropdown specific styling */
        #party_search_input {
            cursor: pointer;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease-in-out;
        }
        
        #party_search_input:focus {
            cursor: text;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.1);
        }
        
        .searchable-input {
            cursor: pointer;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease-in-out;
        }
        
        .searchable-input:focus {
            cursor: text;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.1);
        }
        
        .searchable-dropdown {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            z-index: 9999 !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            min-width: 100% !important;
            max-height: 200px !important;
            overflow-y: auto !important;
        }
        
        @media (max-width: 640px) {
            .searchable-dropdown {
                max-height: 180px !important;
                font-size: 0.875rem;
            }
            
            .searchable-input {
                font-size: 14px !important; /* Prevent iOS zoom */
                padding: 0.5rem !important;
            }
            
            /* Searchable inputs within tables */
            #general_items_table .searchable-input {
                font-size: 13px !important;
                padding: 0.5rem 0.375rem !important;
            }
        }
        
        /* Ensure table cells don't clip the dropdown */
        .general-item-row td {
            position: relative !important;
            overflow: visible !important;
        }
        
        /* Ensure the table doesn't clip the dropdown */
        #general_items_table {
            overflow: visible !important;
        }
        
        #general_items_container {
            overflow: visible !important;
        }
        
        /* Ensure the table container doesn't clip the dropdown */
        .overflow-x-auto {
            overflow: visible !important;
        }
        
        .result-item {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }
        
        .result-item:last-child {
            border-bottom: none;
        }
        
        .result-item:hover {
            background-color: #f8fafc;
            transform: translateX(2px);
        }
        
        .result-item.selected {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding-left: 0.5rem;
        }
        
        .result-item .font-medium {
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: #1f2937;
        }
        
        .result-item .text-sm {
            font-size: 0.75rem;
            line-height: 1rem;
            color: #6b7280;
            margin-top: 0.125rem;
        }
        
        .loading-indicator {
            pointer-events: none;
        }
        
        .pagination-container {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        
        .pagination-container button {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.15s ease-in-out;
        }
        
        .pagination-container button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .pagination-container button:not(:disabled):hover {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        /* Arms table responsive styling */
        .arms-table {
            min-width: 100%;
            table-layout: fixed;
            width: 100%;
        }
        
        .arms-table th,
        .arms-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0.5rem 0.25rem;
        }
        
        /* Column widths with minimum widths for responsive */
        .arms-table th:nth-child(1), .arms-table td:nth-child(1) { width: 10%; min-width: 120px; } /* Type */
        .arms-table th:nth-child(2), .arms-table td:nth-child(2) { width: 10%; min-width: 120px; } /* Make */
        .arms-table th:nth-child(3), .arms-table td:nth-child(3) { width: 10%; min-width: 120px; } /* Caliber */
        .arms-table th:nth-child(4), .arms-table td:nth-child(4) { width: 10%; min-width: 120px; } /* Category */
        .arms-table th:nth-child(5), .arms-table td:nth-child(5) { width: 10%; min-width: 120px; } /* Condition */
        .arms-table th:nth-child(6), .arms-table td:nth-child(6) { width: 20%; min-width: 150px; } /* Serials */
        .arms-table th:nth-child(7), .arms-table td:nth-child(7) { width: 12%; min-width: 110px; } /* Unit Price */
        .arms-table th:nth-child(8), .arms-table td:nth-child(8) { width: 12%; min-width: 110px; } /* Sale Price */
        .arms-table th:nth-child(9), .arms-table td:nth-child(9) { width: 6%; min-width: 60px; } /* Actions */
        
        /* Ensure table container doesn't overflow */
        .overflow-x-auto {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        }
        
        /* Mobile table scroll indicator */
        @media (max-width: 768px) {
            .overflow-x-auto {
                position: relative;
            }
            
            .overflow-x-auto::before {
                content: '← Swipe to see more →';
                position: sticky;
                left: 50%;
                transform: translateX(-50%);
                display: block;
                text-align: center;
                background: linear-gradient(90deg, rgba(59, 130, 246, 0.95), rgba(37, 99, 235, 0.95));
                color: white;
                padding: 0.375rem 1rem;
                border-radius: 9999px;
                font-size: 0.6875rem;
                font-weight: 600;
                pointer-events: none;
                margin: 0.5rem auto;
                width: fit-content;
                box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
                animation: pulseGlow 2s ease-in-out infinite;
                z-index: 10;
            }
            
            @keyframes pulseGlow {
                0%, 100% { 
                    opacity: 0.8;
                    transform: translateX(-50%) scale(1);
                }
                50% { 
                    opacity: 1;
                    transform: translateX(-50%) scale(1.05);
                    box-shadow: 0 2px 12px rgba(59, 130, 246, 0.5);
                }
            }
            
            /* Hide indicator when scrolling */
            .overflow-x-auto:hover::before,
            .overflow-x-auto:active::before {
                animation: fadeOut 0.3s forwards;
            }
            
            @keyframes fadeOut {
                to { opacity: 0; }
            }
            
            .overflow-x-auto::-webkit-scrollbar {
                height: 8px;
            }
            
            .overflow-x-auto::-webkit-scrollbar-track {
                background: #f3f4f6;
                border-radius: 4px;
                margin: 0 0.5rem;
            }
            
            .overflow-x-auto::-webkit-scrollbar-thumb {
                background: #3b82f6;
                border-radius: 4px;
            }
            
            .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                background: #2563eb;
            }
        }
        
        @media (max-width: 640px) {
            .overflow-x-auto::before {
                font-size: 0.625rem;
                padding: 0.25rem 0.75rem;
            }
        }
        
        /* Enhanced form controls */
        .arms-table input,
        .arms-table select {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease-in-out;
        }

        .arms-table input:hover,
        .arms-table select:hover {
            border-color: #9ca3af;
        }

        .arms-table input:focus,
        .arms-table select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Responsive adjustments for column widths */
        @media (max-width: 1400px) {
            .arms-table th:nth-child(6), .arms-table td:nth-child(6) { width: 18%; min-width: 140px; } /* Serials */
        }
        
        @media (max-width: 1200px) {
            .arms-table th:nth-child(6), .arms-table td:nth-child(6) { width: 15%; min-width: 130px; } /* Serials */
        }
        
        @media (max-width: 768px) {
            /* Adjust all columns for mobile */
            .arms-table th:nth-child(1), .arms-table td:nth-child(1) { min-width: 100px; } /* Type */
            .arms-table th:nth-child(2), .arms-table td:nth-child(2) { min-width: 100px; } /* Make */
            .arms-table th:nth-child(3), .arms-table td:nth-child(3) { min-width: 100px; } /* Caliber */
            .arms-table th:nth-child(4), .arms-table td:nth-child(4) { min-width: 100px; } /* Category */
            .arms-table th:nth-child(5), .arms-table td:nth-child(5) { min-width: 100px; } /* Condition */
            .arms-table th:nth-child(6), .arms-table td:nth-child(6) { min-width: 120px; } /* Serials */
            .arms-table th:nth-child(7), .arms-table td:nth-child(7) { min-width: 90px; } /* Unit Price */
            .arms-table th:nth-child(8), .arms-table td:nth-child(8) { min-width: 90px; } /* Sale Price */
            .arms-table th:nth-child(9), .arms-table td:nth-child(9) { min-width: 50px; } /* Actions */
        }
        
        /* Form container responsive */
        .bg-white.shadow-lg {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Ensure form doesn't overflow */
        #purchaseForm {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Cloned arm row styling */
        .arm-row.cloned {
            background-color: #f0f9ff;
            border-left: 3px solid #3b82f6;
            animation: highlightClone 1s ease-out;
        }

        @keyframes highlightClone {
            0% {
                background-color: #dbeafe;
                transform: scale(1.02);
            }
            100% {
                background-color: #f0f9ff;
                transform: scale(1);
            }
        }

        /* Focus styling for serials input */
        .arm-row input[name*="[serials]"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background-color: #ffffff;
        }
    </style>

    <script>
        // Function to display validation errors for general items and arm serials
        function displayValidationErrors() {
            dbg('displayValidationErrors:start');
            
            // Get all validation errors from the page
            const errorMessages = @json($errors->all());
            const errorKeys = @json($errors->keys());
            dbg('validation-bag', { count: errorMessages.length, keys: errorKeys });
            
            console.log('Error messages:', errorMessages);
            console.log('Error keys:', errorKeys);
            
            // If there are no errors, return early
            if (errorMessages.length === 0) {
                dbg('displayValidationErrors:none');
                return;
            }
            
            // Clear any existing error displays first
            clearValidationErrors();
            
            // Ensure bank field is visible if bank_id has an error
            if (errorKeys.includes('bank_id')) {
                const bankField = document.getElementById('bank_field');
                if (bankField) {
                    bankField.style.display = 'block';
                    try { bankField.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(e) {}
                    dbg('bank-field-forced-visible', true);
                }
            }

            // Display general item selection errors
            errorKeys.forEach((key, index) => {
                dbg('processing-error', { key, message: errorMessages[index] });
                
                if (key.startsWith('general_lines.') && key.includes('.general_item_id')) {
                    const lineIndex = key.match(/general_lines\.(\d+)\.general_item_id/)[1];
                    const errorMessage = errorMessages[index];
                    
                    console.log('General item error - Line:', lineIndex, 'Message:', errorMessage);
                    
                    // Find the corresponding general item row
                    const generalItemRows = document.querySelectorAll('.general-item-row');
                    console.log('Found general item rows:', generalItemRows.length);
                    
                    if (generalItemRows[lineIndex]) {
                        const errorDiv = generalItemRows[lineIndex].querySelector('.general-item-error');
                        console.log('Error div found:', errorDiv);
                        
                        if (errorDiv) {
                            errorDiv.textContent = errorMessage;
                            errorDiv.classList.remove('hidden');
                            
                            // Add red border to the search input
                            const searchInput = generalItemRows[lineIndex].querySelector('.searchable-input');
                            if (searchInput) {
                                searchInput.classList.add('border-red-500');
                            }
                        }
                    } else {
                        console.log('General item row not found for index:', lineIndex);
                    }
                }
                
                if (key.startsWith('arm_lines.') && key.includes('.serials')) {
                    const lineIndex = key.match(/arm_lines\.(\d+)\.serials/)[1];
                    const errorMessage = errorMessages[index];
                    
                    console.log('Arm serial error - Line:', lineIndex, 'Message:', errorMessage);
                    
                    // Find the corresponding arm row
                    const armRows = document.querySelectorAll('.arm-row');
                    console.log('Found arm rows:', armRows.length);
                    
                    if (armRows[lineIndex]) {
                        const errorDiv = armRows[lineIndex].querySelector('.arm-serial-error');
                        console.log('Error div found:', errorDiv);
                        
                        if (errorDiv) {
                            errorDiv.textContent = errorMessage;
                            errorDiv.classList.remove('hidden');
                            
                            // Add red border to the serial input
                            const serialInput = armRows[lineIndex].querySelector('input[name*="[serials]"]');
                            if (serialInput) {
                                serialInput.classList.add('border-red-500');
                            }
                        }
                    } else {
                        console.log('Arm row not found for index:', lineIndex);
                    }
                }
            });
        }

        // Call the function when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, calling displayValidationErrors');
            displayValidationErrors();
        });
        
        // Also try to run after a short delay to ensure all elements are loaded
        setTimeout(function() {
            displayValidationErrors();
        }, 1000);
        
        // Additional attempts to display errors after longer delays
        setTimeout(function() {
            displayValidationErrors();
        }, 2000);
        
        setTimeout(function() {
            displayValidationErrors();
        }, 3000);
    </script>
</body>
</html>
