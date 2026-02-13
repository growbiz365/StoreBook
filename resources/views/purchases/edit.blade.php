<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Purchase #{{ $purchase->id }} - Purchases Management - StoreBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        
        /* Responsive Arms Table */
        @media (max-width: 1024px) {
            #arms_table {
                min-width: 1200px; /* Force horizontal scroll on tablets */
            }
            
            #arms_table th,
            #arms_table td {
                padding: 0.75rem 0.5rem;
            }
            
            #arms_table input,
            #arms_table select {
                font-size: 0.8125rem;
                padding: 0.5rem;
            }
        }
        
        @media (max-width: 768px) {
            #arms_table {
                min-width: 1000px; /* Maintain scroll on mobile */
            }
            
            #arms_table th,
            #arms_table td {
                padding: 0.625rem 0.375rem;
                font-size: 0.75rem;
            }
            
            #arms_table th {
                font-size: 0.625rem;
            }
            
            #arms_table input,
            #arms_table select {
                font-size: 13px;
                padding: 0.5rem;
            }
            
            #arms_table .remove-arm {
                padding: 0.375rem;
            }
            
            /* Adjust column minimum widths for mobile */
            #arms_table th:nth-child(1), #arms_table td:nth-child(1) { min-width: 140px; }
            #arms_table th:nth-child(2), #arms_table td:nth-child(2) { min-width: 140px; }
            #arms_table th:nth-child(3), #arms_table td:nth-child(3) { min-width: 130px; }
            #arms_table th:nth-child(4), #arms_table td:nth-child(4) { min-width: 130px; }
            #arms_table th:nth-child(5), #arms_table td:nth-child(5) { min-width: 130px; }
            #arms_table th:nth-child(6), #arms_table td:nth-child(6) { min-width: 120px; }
            #arms_table th:nth-child(7), #arms_table td:nth-child(7) { min-width: 100px; }
            #arms_table th:nth-child(8), #arms_table td:nth-child(8) { min-width: 100px; }
            #arms_table th:nth-child(9), #arms_table td:nth-child(9) { min-width: 70px; }
        }
        
        @media (max-width: 640px) {
            #arms_table {
                min-width: 900px;
            }
            
            #arms_table th,
            #arms_table td {
                padding: 0.5rem 0.25rem;
            }
            
            #arms_table input,
            #arms_table select {
                font-size: 12px;
                padding: 0.375rem;
            }
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
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Purchase #{{ $purchase->id }}</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('purchases.show', $purchase) }}" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <!-- Warning for Posted Purchases -->
    @if($purchase->isPosted())
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Warning:</strong> You are editing a posted purchase. Changes will automatically adjust inventory and create audit trails.
                    </p>
                </div>
            </div>
        </div>
    @endif

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
        (function(){
            function clean() {
                var keep = document.getElementById('page_error_banner');
                var selectors = [
                    '.bg-red-100',
                    '.border-red-400',
                    '.alert-danger',
                    '[role="alert"].bg-red-100',
                    '[role="alert"].bg-red-200'
                ];
                var nodes = document.querySelectorAll(selectors.join(', '));
                nodes.forEach(function(el){
                    if (!keep || (el !== keep && !keep.contains(el))) {
                        el.parentNode && el.parentNode.removeChild(el);
                    }
                });
                // Remove duplicate blocks that contain the exact same text as banner
                if (keep) {
                    var text = (keep.textContent || '').trim();
                    if (text) {
                        Array.from(document.querySelectorAll('div[role="alert"], .alert, .bg-red-100, .bg-red-50')).forEach(function(el){
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
        })();
    </script>
    @endif

    <form method="POST" action="{{ route('purchases.update', $purchase) }}" id="purchaseForm">
        @csrf
        @method('PUT')
        
        <!-- Main Content -->
        <div class="w-full px-2 sm:px-3 md:px-4 py-3 sm:py-4">
            <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-3 sm:p-4 md:p-6 w-full">
            <!-- Purchase Header Information -->
            <div class="pb-3 sm:pb-4 mb-3 sm:mb-4 border-b border-gray-200">
                <h2 class="text-sm sm:text-base font-semibold text-gray-900 mb-2">Purchase Header Information</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 md:gap-6">
                    <div>
                        <label for="party_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Party 
                            @if($purchase->payment_type == 'cash')
                                
                            @else
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <div class="relative">
                            <div class="searchable-select-container relative">
                                <input type="text" 
                                       class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
                                       placeholder="Search parties..."
                                       autocomplete="off"
                                       id="party_search_input">
                                <input type="hidden" name="party_id" id="party_id" class="selected-item-id" value="{{ old('party_id', $purchase->party_id) }}">
                                
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
                        <p class="mt-1 text-xs text-gray-500" id="party_help_text">
                            @if($purchase->payment_type == 'cash')
                                Optional for cash payments - you can leave this blank
                            @else
                                Required for credit payments
                            @endif
                        </p>
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
                            <option value="credit" {{ old('payment_type', $purchase->payment_type) == 'credit' ? 'selected' : '' }}>Credit (Party)</option>
                            <option value="cash" {{ old('payment_type', $purchase->payment_type) == 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                        @error('payment_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="bank_field" style="display: {{ $purchase->payment_type == 'cash' ? 'block' : 'none' }};">
                        <label for="bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Bank <span class="text-red-500">*</span>
                        </label>
                        <select name="bank_id" id="bank_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 @error('bank_id') border-red-500 @enderror">
                            <option value="">Select Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ old('bank_id', $purchase->bank_id) == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->chartOfAccount->name ?? $bank->account_name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="bank_balance" class="mt-1 text-sm hidden">
                            <span class="font-medium">Balance:</span>
                            <span id="bank_balance_amount" class="ml-1"></span>
                        </div>
                        @error('bank_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', $purchase->invoice_date->format('Y-m-d')) }}" 
                               required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 @error('invoice_date') border-red-500 @enderror">
                        @error('invoice_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shipping_charges" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Charges
                        </label>
                        <input type="number" name="shipping_charges" id="shipping_charges" value="{{ old('shipping_charges', $purchase->shipping_charges) }}" 
                               step="0.01" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 @error('shipping_charges') border-red-500 @enderror">
                        @error('shipping_charges')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Customer Details Section (for Cash payments) -->
            <div id="customer_details_field" style="display: {{ $purchase->payment_type == 'cash' ? 'block' : 'none' }};" class="mb-4">
                <div class="bg-gray-50 border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Customer Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        <div>
                            <label for="name_of_customer" class="block text-xs font-medium text-gray-600 mb-1">Customer Name</label>
                            <input type="text" name="name_of_customer" id="name_of_customer" value="{{ old('name_of_customer', $purchase->name_of_customer) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('name_of_customer') border-red-500 @enderror">
                            @error('name_of_customer')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="father_name" class="block text-xs font-medium text-gray-600 mb-1">Father Name</label>
                            <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $purchase->father_name) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('father_name') border-red-500 @enderror">
                            @error('father_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="contact" class="block text-xs font-medium text-gray-600 mb-1">Contact</label>
                            <input type="text" name="contact" id="contact" value="{{ old('contact', $purchase->contact) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('contact') border-red-500 @enderror"
                                   placeholder="03XX-XXXXXXX">
                            @error('contact')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="cnic" class="block text-xs font-medium text-gray-600 mb-1">CNIC</label>
                            <input type="text" name="cnic" id="cnic" value="{{ old('cnic', $purchase->cnic) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('cnic') border-red-500 @enderror"
                                   placeholder="12345-1234567-1" data-mask="00000-0000000-0">
                            @error('cnic')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_no" class="block text-xs font-medium text-gray-600 mb-1">Licence No</label>
                            <input type="text" name="licence_no" id="licence_no" value="{{ old('licence_no', $purchase->licence_no) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_no') border-red-500 @enderror">
                            @error('licence_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_issue_date" class="block text-xs font-medium text-gray-600 mb-1">Issue Date</label>
                            <input type="date" name="licence_issue_date" id="licence_issue_date" value="{{ old('licence_issue_date', $purchase->licence_issue_date?->format('Y-m-d')) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_issue_date') border-red-500 @enderror">
                            @error('licence_issue_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_valid_upto" class="block text-xs font-medium text-gray-600 mb-1">Valid Upto</label>
                            <input type="date" name="licence_valid_upto" id="licence_valid_upto" value="{{ old('licence_valid_upto', $purchase->licence_valid_upto?->format('Y-m-d')) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_valid_upto') border-red-500 @enderror">
                            @error('licence_valid_upto')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="licence_issued_by" class="block text-xs font-medium text-gray-600 mb-1">Issued By</label>
                            <input type="text" name="licence_issued_by" id="licence_issued_by" value="{{ old('licence_issued_by', $purchase->licence_issued_by) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('licence_issued_by') border-red-500 @enderror">
                            @error('licence_issued_by')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="re_reg_no" class="block text-xs font-medium text-gray-600 mb-1">Re-Reg No</label>
                            <input type="text" name="re_reg_no" id="re_reg_no" value="{{ old('re_reg_no', $purchase->re_reg_no) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('re_reg_no') border-red-500 @enderror">
                            @error('re_reg_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="dc" class="block text-xs font-medium text-gray-600 mb-1">DC</label>
                            <input type="text" name="dc" id="dc" value="{{ old('dc', $purchase->dc) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('dc') border-red-500 @enderror">
                            @error('dc')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="Date" class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                            <input type="date" name="Date" id="Date" value="{{ old('Date', $purchase->Date?->format('Y-m-d')) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('Date') border-red-500 @enderror">
                            @error('Date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="address" class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                            <textarea name="address" id="address" rows="1" 
                                      class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                                      placeholder="Enter customer address">{{ old('address', $purchase->address) }}</textarea>
                            @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Party Details Section (for Credit payments when party is selected) -->
            <div id="party_details_field" style="display: {{ $purchase->payment_type == 'credit' && $purchase->party_id ? 'block' : 'none' }};" class="mb-4">
                <div class="bg-gray-50 border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Party Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        <div>
                            <label for="party_name" class="block text-xs font-medium text-gray-600 mb-1">Party Name</label>
                            <input type="text" id="party_name" readonly 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600"
                                   value="{{ $purchase->party ? $purchase->party->name : '' }}">
                        </div>
                        
                        <div>
                            <label for="party_cnic" class="block text-xs font-medium text-gray-600 mb-1">Party CNIC</label>
                            <input type="text" id="party_cnic" readonly 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600"
                                   value="{{ $purchase->party ? $purchase->party->cnic : '' }}">
                        </div>
                        
                        <div>
                            <label for="party_contact" class="block text-xs font-medium text-gray-600 mb-1">Party Contact</label>
                            <input type="text" id="party_contact" readonly 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600"
                                   value="{{ $purchase->party ? $purchase->party->phone_no : '' }}">
                        </div>
                        
                        <div>
                            <label for="party_address" class="block text-xs font-medium text-gray-600 mb-1">Party Address</label>
                            <textarea id="party_address" rows="1" readonly 
                                      class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-100 text-gray-600">{{ $purchase->party ? $purchase->party->address : '' }}</textarea>
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_no" class="block text-xs font-medium text-gray-600 mb-1">Licence No</label>
                            <input type="text" name="party_license_no" id="party_license_no" value="{{ old('party_license_no', $purchase->party_license_no) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_no') border-red-500 @enderror">
                            @error('party_license_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_issue_date" class="block text-xs font-medium text-gray-600 mb-1">Issue Date</label>
                            <input type="date" name="party_license_issue_date" id="party_license_issue_date" value="{{ old('party_license_issue_date', $purchase->party_license_issue_date?->format('Y-m-d')) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_issue_date') border-red-500 @enderror">
                            @error('party_license_issue_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_valid_upto" class="block text-xs font-medium text-gray-600 mb-1">Valid Upto</label>
                            <input type="date" name="party_license_valid_upto" id="party_license_valid_upto" value="{{ old('party_license_valid_upto', $purchase->party_license_valid_upto?->format('Y-m-d')) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_valid_upto') border-red-500 @enderror">
                            @error('party_license_valid_upto')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_license_issued_by" class="block text-xs font-medium text-gray-600 mb-1">Issued By</label>
                            <input type="text" name="party_license_issued_by" id="party_license_issued_by" value="{{ old('party_license_issued_by', $purchase->party_license_issued_by) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_license_issued_by') border-red-500 @enderror">
                            @error('party_license_issued_by')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_re_reg_no" class="block text-xs font-medium text-gray-600 mb-1">Re-Reg No</label>
                            <input type="text" name="party_re_reg_no" id="party_re_reg_no" value="{{ old('party_re_reg_no', $purchase->party_re_reg_no) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_re_reg_no') border-red-500 @enderror">
                            @error('party_re_reg_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_dc" class="block text-xs font-medium text-gray-600 mb-1">DC</label>
                            <input type="text" name="party_dc" id="party_dc" value="{{ old('party_dc', $purchase->party_dc) }}" 
                                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('party_dc') border-red-500 @enderror">
                            @error('party_dc')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div style="display: none;">
                            <label for="party_dc_date" class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                            <input type="date" name="party_dc_date" id="party_dc_date" value="{{ old('party_dc_date', $purchase->party_dc_date?->format('Y-m-d')) }}" 
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
                        <span id="general_items_counter" class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-semibold text-white bg-purple-600 rounded-full min-w-[24px] h-6">{{ $purchase->generalLines->count() }}</span>
                    </div>
                    <button type="button" id="add_general_item" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Line</span>
                    </button>
                </div>
                
                <div class="overflow-x-auto">
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
                            @if($purchase->generalLines->count() == 0)
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                        No general items added yet. Click "Add Line" to add items.
                                    </td>
                                </tr>
                            @endif
                            @foreach($purchase->generalLines as $index => $line)
                            <tr class="general-item-row">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="relative">
                                        <div class="searchable-select-container relative">
                                            <input type="text" 
                                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
                                                   placeholder="Search items..."
                                                   autocomplete="off"
                                                   data-index="{{ $index }}"
                                                   value="{{ $line->generalItem ? $line->generalItem->item_name : '' }}">
                                            <input type="hidden" name="general_lines[{{ $index }}][general_item_id]" class="selected-item-id" value="{{ $line->general_item_id }}">
                                            
                                            <!-- Error display for general item selection -->
                                            <div class="general-item-error mt-1 text-xs text-red-600 hidden"></div>
                                            
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
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="number" name="general_lines[{{ $index }}][qty]" required step="1" min="1" 
                                           value="{{ round($line->qty) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-qty focus:border-blue-500 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="number" name="general_lines[{{ $index }}][unit_price]" required step="0.01" min="0" 
                                           value="{{ $line->unit_price }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-price focus:border-blue-500 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="number" name="general_lines[{{ $index }}][sale_price]" step="0.01" min="0" 
                                           value="{{ $line->sale_price }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-sale-price focus:border-blue-500 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium general-line-total text-gray-900">{{ number_format($line->line_total, 2) }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <button type="button" class="remove-general-item text-red-600 hover:text-red-800 text-sm p-1 rounded hover:bg-red-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Arms Section - Hidden: StoreBook is items-only -->
            <div class="mb-6" style="display: none;">
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mb-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Arms (serial-based)</h2>
                        <span id="arms_counter" class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-semibold text-white bg-blue-600 rounded-full min-w-[24px] h-6">{{ $purchase->armLines->count() }}</span>
                    </div>
                    <button type="button" id="add_arm" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out" title="Add new arm line (will clone data from previous line)">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            @php $oldArmLines = old('arm_lines'); @endphp
                            @if(is_array($oldArmLines))
                                @foreach($oldArmLines as $index => $line)
                                <tr class="arm-row">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_type_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Type</option>
                                            @foreach($armsTypes as $type)
                                                <option value="{{ $type->id }}" {{ (isset($line['arm_type_id']) && $line['arm_type_id']==$type->id) ? 'selected' : '' }}>
                                                    {{ $type->arm_type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_make_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Make</option>
                                            @foreach($armsMakes as $make)
                                                <option value="{{ $make->id }}" {{ (isset($line['arm_make_id']) && $line['arm_make_id']==$make->id) ? 'selected' : '' }}>
                                                    {{ $make->arm_make }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_caliber_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Caliber</option>
                                            @foreach($armsCalibers as $caliber)
                                                <option value="{{ $caliber->id }}" {{ (isset($line['arm_caliber_id']) && $line['arm_caliber_id']==$caliber->id) ? 'selected' : '' }}>
                                                    {{ $caliber->arm_caliber }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_category_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Category</option>
                                            @foreach($armsCategories as $category)
                                                <option value="{{ $category->id }}" {{ (isset($line['arm_category_id']) && $line['arm_category_id']==$category->id) ? 'selected' : '' }}>
                                                    {{ $category->arm_category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_condition_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Condition</option>
                                            @foreach($armsConditions as $condition)
                                                <option value="{{ $condition->id }}" {{ (isset($line['arm_condition_id']) && $line['arm_condition_id']==$condition->id) ? 'selected' : '' }}>
                                                    {{ $condition->arm_condition }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="text" name="arm_lines[{{ $index }}][serials]" 
                                               value="{{ $line['serials'] ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <div class="arm-serial-error mt-1 text-xs text-red-600 hidden"></div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="number" name="arm_lines[{{ $index }}][unit_price]" required step="0.01" min="0" 
                                               value="{{ isset($line['unit_price']) ? $line['unit_price'] : '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm arm-price focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="number" name="arm_lines[{{ $index }}][sale_price]" step="0.01" min="0" 
                                               value="{{ isset($line['sale_price']) ? $line['sale_price'] : '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm arm-sale-price focus:border-blue-500 focus:ring-blue-500">
                                        <input type="hidden" name="arm_lines[{{ $index }}][arm_title]" class="arm-title-input" value="{{ $line['arm_title'] ?? '' }}">
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <button type="button" class="remove-arm text-red-600 hover:text-red-800 text-sm p-2 rounded hover:bg-red-50 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                @foreach($purchase->armLines as $index => $line)
                                <tr class="arm-row">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_type_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Type</option>
                                            @foreach($armsTypes as $type)
                                                <option value="{{ $type->id }}" {{ $line->arm_type_id == $type->id ? 'selected' : '' }}>
                                                    {{ $type->arm_type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_make_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Make</option>
                                            @foreach($armsMakes as $make)
                                                <option value="{{ $make->id }}" {{ $line->arm_make_id == $make->id ? 'selected' : '' }}>
                                                    {{ $make->arm_make }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_caliber_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Caliber</option>
                                            @foreach($armsCalibers as $caliber)
                                                <option value="{{ $caliber->id }}" {{ $line->arm_caliber_id == $caliber->id ? 'selected' : '' }}>
                                                    {{ $caliber->arm_caliber }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_category_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Category</option>
                                            @foreach($armsCategories as $category)
                                                <option value="{{ $category->id }}" {{ $line->arm_category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->arm_category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <select name="arm_lines[{{ $index }}][arm_condition_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Condition</option>
                                            @foreach($armsConditions as $condition)
                                                <option value="{{ $condition->id }}" {{ $line->arm_condition_id == $condition->id ? 'selected' : '' }}>
                                                    {{ $condition->arm_condition }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="text" name="arm_lines[{{ $index }}][serials]" 
                                               value="{{ $line->armSerials->pluck('serial_no')->implode(', ') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <div class="arm-serial-error mt-1 text-xs text-red-600 hidden"></div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="number" name="arm_lines[{{ $index }}][unit_price]" required step="0.01" min="0" 
                                               value="{{ $line->unit_price }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm arm-price focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="number" name="arm_lines[{{ $index }}][sale_price]" step="0.01" min="0" 
                                               value="{{ $line->sale_price }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm arm-sale-price focus:border-blue-500 focus:ring-blue-500">
                                        <input type="hidden" name="arm_lines[{{ $index }}][arm_title]" class="arm-title-input" value="{{ $line->armSerials->first() ? $line->armSerials->first()->arm_title : '' }}">
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <button type="button" class="remove-arm text-red-600 hover:text-red-800 text-sm p-2 rounded hover:bg-red-50 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Summary -->
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Subtotal: <span id="subtotal" class="font-medium">0</span></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Shipping: <span id="shipping_display" class="font-medium">+ 0</span></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Amount: <span id="total_amount" class="font-medium text-lg">0</span></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('purchases.show', $purchase) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Purchase
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
                            
                            <!-- Error display for general item selection -->
                            <div class="general-item-error mt-1 text-xs text-red-600 hidden"></div>
                            
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
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="number" name="general_lines[INDEX][qty]" required step="1" min="1" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-qty focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0">
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

    <template id="arm_template">
        <tr class="arm-row">
            <td class="px-2 py-2 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_type_id]" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Type</option>
                    @foreach($armsTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->arm_type }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_make_id]" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Make</option>
                    @foreach($armsMakes as $make)
                        <option value="{{ $make->id }}">{{ $make->arm_make }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_caliber_id]" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Caliber</option>
                    @foreach($armsCalibers as $caliber)
                        <option value="{{ $caliber->id }}">{{ $caliber->arm_caliber }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_category_id]" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Category</option>
                    @foreach($armsCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->arm_category }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <select name="arm_lines[INDEX][arm_condition_id]" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Condition</option>
                    @foreach($armsConditions as $condition)
                        <option value="{{ $condition->id }}">{{ $condition->arm_condition }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <input type="text" name="arm_lines[INDEX][serials]" 
                       class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="SN1001, SN1002">
                <!-- Error display for arm serial numbers -->
                <div class="arm-serial-error mt-1 text-xs text-red-600 hidden"></div>
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <input type="number" name="arm_lines[INDEX][unit_price]" required step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm arm-price focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <input type="number" name="arm_lines[INDEX][sale_price]" step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm arm-sale-price focus:border-blue-500 focus:ring-blue-500"
                       placeholder="0.00">
                <input type="hidden" name="arm_lines[INDEX][arm_title]" class="arm-title-input">
            </td>
            <td class="px-2 py-2 whitespace-nowrap">
                <button type="button" class="remove-arm text-red-600 hover:text-red-800 text-sm p-1 rounded hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <script>
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
                // Use setTimeout to ensure the values are set before calculation
                setTimeout(() => {
                calculateLineTotal(unitPriceInput);
                calculateTotals();
                }, 50);
            }
            
            this.hideDropdown();
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

    document.addEventListener('DOMContentLoaded', function() {
        let generalItemIndex = {{ $purchase->generalLines->count() }};
        let armIndex = {{ $purchase->armLines->count() }};

        const deleteModal = document.getElementById('deleteConfirmationModal');
        const deleteModalMessage = document.getElementById('deleteModalMessage');
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const cancelDeleteModal = document.getElementById('cancelDeleteModal');
        let rowPendingDeletion = null;
        let pendingDeletionType = null;

        function openDeleteModal(row, type) {
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

        function handlePlaceholderRows() {
            if (pendingDeletionType === 'general') {
                const container = document.getElementById('general_items_container');
                if (container && container.querySelectorAll('.general-item-row').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = '<td colspan="6" class="px-4 py-4 text-center text-gray-500">No general items added yet. Click &quot;Add Line&quot; to add items.</td>';
                    container.appendChild(emptyRow);
                }
            }
        }

        confirmDeleteModal.addEventListener('click', function() {
            if (rowPendingDeletion) {
                const row = rowPendingDeletion;
                row.remove();
                calculateTotals();
                updateAllocationPreview();
                updateCounters();
                handlePlaceholderRows();
            }
            closeDeleteModal();
        });

        cancelDeleteModal.addEventListener('click', function() {
            closeDeleteModal();
        });

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
            
            // Hide the "No general items added yet" message if it exists
            const noItemsMessage = container.querySelector('tr td[colspan="6"]');
            if (noItemsMessage && noItemsMessage.textContent.includes('No general items added yet')) {
                noItemsMessage.closest('tr').remove();
            }
            
            container.appendChild(clone);
            
            // Set default quantity to 1
            const qtyInput = container.lastElementChild.querySelector('.general-qty');
            if (qtyInput) {
                qtyInput.value = '1';
            }
            
            // Initialize searchable dropdown for the new row
            const searchableContainer = container.lastElementChild.querySelector('.searchable-select-container');
            if (searchableContainer) {
                new SearchableDropdown(searchableContainer, {
                    itemsPerPage: 15,        // Show 15 items per page
                    debounceDelay: 300,      // 300ms delay for search
                    minSearchLength: 2       // Start searching after 2 characters
                });
                searchableContainer.dataset.initialized = 'true';
            }
            
            generalItemIndex++;
            
            // Add event listeners for calculations
            addCalculationListeners();
            
            // Update counters
            updateCounters();
            
            // Calculate line total for the new row after a short delay to ensure DOM is updated
            setTimeout(() => {
                const newRow = container.lastElementChild;
                if (newRow) {
                    calculateLineTotal(newRow.querySelector('.general-qty'));
                }
            updateAllocationPreview();
            }, 100);
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
            updateAllocationPreview();
            
            armIndex++;
            updateCounters();
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
            // General items
            document.querySelectorAll('.general-qty, .general-price, .general-sale-price').forEach(element => {
                element.addEventListener('input', function() {
                    calculateLineTotal(this);
                    calculateTotals();
                    updateAllocationPreview();
                });
            });
            
            // Arms
            document.querySelectorAll('.arm-price, .arm-sale-price, input[name*="serials"]').forEach(element => {
                element.addEventListener('input', function() {
                    calculateTotals();
                    updateAllocationPreview();
                });
            });
            
            // Charges
            document.querySelectorAll('#shipping_charges').forEach(element => {
                element.addEventListener('input', function() {
                    calculateTotals();
                    updateAllocationPreview();
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

        function calculateLineTotal(element) {
            const row = element.closest('.general-item-row');
            const qtyInput = row.querySelector('.general-qty');
            const priceInput = row.querySelector('.general-price');
            const salePriceInput = row.querySelector('.general-sale-price');
            const lineTotalSpan = row.querySelector('.general-line-total');
            
            if (!qtyInput || !priceInput || !lineTotalSpan) {
                
                return;
            }
            
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const salePrice = parseFloat(salePriceInput?.value) || 0;
            
            const lineTotal = qty * price;
            lineTotalSpan.textContent = lineTotal.toFixed(2);
            
            
        }

        function calculateTotals() {
            let subtotal = 0;
            
            // Calculate general items
            document.querySelectorAll('.general-item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.general-qty').value) || 0;
                const price = parseFloat(row.querySelector('.general-price').value) || 0;
                const salePrice = parseFloat(row.querySelector('.general-sale-price').value) || 0;
                
                subtotal += qty * price;
            });
            
            // Calculate arms
            document.querySelectorAll('.arm-row').forEach(row => {
                const serials = row.querySelector('input[name*="serials"]').value;
                const serialCount = serials ? serials.split(',').filter(s => s.trim()).length : 0;
                const price = parseFloat(row.querySelector('.arm-price').value) || 0;
                const salePrice = parseFloat(row.querySelector('.arm-sale-price').value) || 0;
                
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

        function updateAllocationPreview() {
            const container = document.getElementById('allocation_preview_container');
            if (!container) { return; }
            container.innerHTML = '';
            
            let lineNumber = 1;
            
            // Add general items to preview
            document.querySelectorAll('.general-item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.general-qty').value) || 0;
                const price = parseFloat(row.querySelector('.general-price').value) || 0;
                const salePrice = parseFloat(row.querySelector('.general-sale-price').value) || 0;
                const itemSelect = row.querySelector('select');
                const itemName = itemSelect.options[itemSelect.selectedIndex]?.text || 'Unknown Item';
                
                const baseNet = qty * price;
                const effectiveUnitCost = qty > 0 ? baseNet / qty : 0;
                
                const previewRow = document.createElement('tr');
                previewRow.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">General #${lineNumber} - ${itemName}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(baseNet).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(salePrice).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">0</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${effectiveUnitCost.toFixed(2)}</td>
                `;
                container.appendChild(previewRow);
                lineNumber++;
            });
            
            // Add arms to preview
            document.querySelectorAll('.arm-row').forEach(row => {
                const serials = row.querySelector('input[name*="serials"]').value;
                const serialCount = serials ? serials.split(',').filter(s => s.trim()).length : 0;
                const price = parseFloat(row.querySelector('.arm-price').value) || 0;
                const salePrice = parseFloat(row.querySelector('.arm-sale-price').value) || 0;
                
                const baseNet = serialCount * price;
                const effectiveUnitCost = serialCount > 0 ? baseNet / serialCount : 0;
                
                const previewRow = document.createElement('tr');
                previewRow.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Arm #${lineNumber} - Serial-based</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(baseNet).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(salePrice).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">0</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${effectiveUnitCost.toFixed(2)}</td>
                `;
                container.appendChild(previewRow);
                lineNumber++;
            });
        }

        // Initialize searchable dropdown for existing general item rows
        document.querySelectorAll('.general-item-row .searchable-select-container').forEach(container => {
            if (!container.dataset.initialized) {
            new SearchableDropdown(container, {
                itemsPerPage: 15,        // Show 15 items per page
                debounceDelay: 300,      // 300ms delay for search
                minSearchLength: 2       // Start searching after 2 characters
            });
                container.dataset.initialized = 'true';
            }
        });

        // Initialize calculation listeners
        addCalculationListeners();
        updateAllocationPreview();
        
        // Initialize counters on page load
        updateCounters();
        
        // Payment type change handler
        document.getElementById('payment_type').addEventListener('change', function() {
            const bankField = document.getElementById('bank_field');
            const bankSelect = document.getElementById('bank_id');
            const customerDetailsField = document.getElementById('customer_details_field');
            const partyDetailsField = document.getElementById('party_details_field');
            const vendorField = document.getElementById('party_id');
            const vendorLabel = document.querySelector('label[for="party_id"]');
            const vendorHelpText = document.getElementById('party_help_text');

            // Clear any existing party error when payment type changes
            const partyError = document.getElementById('party_error');
            if (partyError) {
                partyError.remove();
            }
            
            if (this.value === 'cash') {
                bankField.style.display = 'block';
                bankSelect.required = true; // Bank is required for cash payments
                customerDetailsField.style.display = 'block'; // Show customer details for cash payments
                partyDetailsField.style.display = 'none'; // Hide party details for cash payments
                
                // Make vendor optional for cash payments
                vendorField.required = false;
                vendorLabel.innerHTML = 'Party';
                vendorHelpText.textContent = 'Optional for cash payments - you can leave this blank';
                
                // Clear party selection when switching to cash
                vendorField.value = '';
                const partySearchInput = document.getElementById('party_search_input');
                if (partySearchInput) {
                    partySearchInput.value = '';
                }
                hidePartyBalance();
            } else {
                bankField.style.display = 'none';
                bankSelect.required = false;
                bankSelect.value = '';
                customerDetailsField.style.display = 'none'; // Hide customer details for credit payments
                
                // Make vendor required for credit payments
                vendorField.required = true;
                vendorLabel.innerHTML = 'Party <span class="text-red-500">*</span>';
                vendorHelpText.textContent = 'Required for credit payments';
                
                // Show party details if party is selected
                if (vendorField.value) {
                    loadPartyDetails(vendorField.value);
                    partyDetailsField.style.display = 'block';
                } else {
                    partyDetailsField.style.display = 'none';
                }
            }
        });
        
        // Initialize payment type state if it's cash
        const paymentTypeSelect = document.getElementById('payment_type');
        if (paymentTypeSelect.value === 'cash') {
            // Trigger the change event to set the correct state
            paymentTypeSelect.dispatchEvent(new Event('change'));
        }

        // Form submission validation
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            const paymentType = document.getElementById('payment_type').value;
            const partyId = document.getElementById('party_id').value;
            const partyError = document.getElementById('party_error');
            
            // Remove existing error message
            if (partyError) {
                partyError.remove();
            }
            
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
            
            // Clear previous validation errors
            clearValidationErrors();
            
            let hasErrors = false;
            
            // Validate general items
            const generalItemRows = document.querySelectorAll('.general-item-row');
            generalItemRows.forEach((row, index) => {
                const selectedItemId = row.querySelector('.selected-item-id').value;
                if (!selectedItemId || selectedItemId === '') {
                    showGeneralItemError(row, 'Please select a general item.');
                    hasErrors = true;
                }
            });
            
            // Validate arms
            const armRows = document.querySelectorAll('.arm-row');
            armRows.forEach((row, index) => {
                const serialsInput = row.querySelector('input[name*="[serials]"]');
                if (!serialsInput.value || serialsInput.value.trim() === '') {
                    showArmSerialError(row, 'Please enter arm serial numbers.');
                    hasErrors = true;
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                return false;
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
            
            // Remove red borders
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
            const input = row.querySelector('.searchable-input');
            
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
            }
            
            if (input) {
                input.classList.add('border-red-500');
            }
        }
        
        // Function to show arm serial error
        function showArmSerialError(row, message) {
            const errorDiv = row.querySelector('.arm-serial-error');
            const input = row.querySelector('input[name*="[serials]"]');
            
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
            }
            
            if (input) {
                input.classList.add('border-red-500');
            }
        }

        // Party selection change handler
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
                    
                    if (data.balance !== undefined) {
                        showPartyBalance(data.formatted_balance, data.status);
                    } else {
                        hidePartyBalance();
                    }
                })
                .catch(error => {
                    
                    hidePartyBalance();
                });
        }

        // Function to fetch bank balance
        function fetchBankBalance(bankId) {
            
            
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
                    
                    if (data.balance !== undefined) {
                        showBankBalance(data.formatted_balance, data.status);
                    } else {
                        hideBankBalance();
                    }
                })
                .catch(error => {
                    
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
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    
                    
                    // Check if elements exist
                    const nameField = document.getElementById('party_name');
                    const cnicField = document.getElementById('party_cnic');
                    const contactField = document.getElementById('party_contact');
                    const addressField = document.getElementById('party_address');
                    
                    
                    
                    // Populate party form fields
                    if (nameField) nameField.value = data.name || '';
                    if (cnicField) cnicField.value = data.cnic || '';
                    if (contactField) contactField.value = data.phone_no || '';
                    if (addressField) addressField.value = data.address || '';
                    
                    
                })
                .catch(error => {
                    
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

        // Load initial balances if party/bank are already selected
        const initialPartySelect = document.getElementById('party_id');
        const initialBankSelect = document.getElementById('bank_id');
        
        if (initialPartySelect.value) {
            fetchPartyBalance(initialPartySelect.value);
        }
        if (initialBankSelect.value) {
            fetchBankBalance(initialBankSelect.value);
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
            
            clearSelection() {
                // Clear the current selection
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
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

        // Handle pre-selected party from existing purchase data
        const partyIdInput = document.getElementById('party_id');
        if (partyIdInput && partyIdInput.value) {
            // Fetch party data and populate the search input
            fetch(`/api/parties/${partyIdInput.value}`)
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
                    
                });
        }

        // Initialize totals with existing purchase data
        calculateTotals();
        
        // Initialize allocation preview
        updateAllocationPreview();

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
        
        /* Responsive Arms Table (.arms-table class) */
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
        
        /* Column widths */
        .arms-table th:nth-child(1), .arms-table td:nth-child(1) { width: 10%; } /* Type */
        .arms-table th:nth-child(2), .arms-table td:nth-child(2) { width: 10%; } /* Make */
        .arms-table th:nth-child(3), .arms-table td:nth-child(3) { width: 10%; } /* Caliber */
        .arms-table th:nth-child(4), .arms-table td:nth-child(4) { width: 10%; } /* Category */
        .arms-table th:nth-child(5), .arms-table td:nth-child(5) { width: 10%; } /* Condition */
        .arms-table th:nth-child(6), .arms-table td:nth-child(6) { width: 20%; } /* Serials */
        .arms-table th:nth-child(7), .arms-table td:nth-child(7) { width: 12%; } /* Unit Price */
        .arms-table th:nth-child(8), .arms-table td:nth-child(8) { width: 12%; } /* Sale Price */
        .arms-table th:nth-child(9), .arms-table td:nth-child(9) { width: 6%; } /* Actions */
        
        /* Ensure table container doesn't overflow */
        .overflow-x-auto {
            max-width: 100%;
            overflow-x: auto;
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
        
        /* Responsive adjustments */
        @media (max-width: 1400px) {
            .arms-table th:nth-child(6), .arms-table td:nth-child(6) { width: 18%; } /* Serials */
        }
        
        @media (max-width: 1200px) {
            .arms-table th:nth-child(6), .arms-table td:nth-child(6) { width: 15%; } /* Serials */
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
        // Function to display validation errors for general items and arms
        function displayValidationErrors() {
            
            // Get all validation errors from the page
            const errorMessages = @json($errors->all());
            const errorKeys = @json($errors->keys());
            
            
            
            // If there are no errors, return early
            if (errorMessages.length === 0) {
                
                return;
            }
            
            // Clear any existing error displays first
            clearValidationErrors();
            
            // Display general item selection errors
            errorKeys.forEach((key, index) => {
                if (key.startsWith('general_lines.') && key.includes('.general_item_id')) {
                    const match = key.match(/general_lines\.(\d+)\.general_item_id/);
                    if (match) {
                        const rowIndex = parseInt(match[1]);
                        const errorMessage = errorMessages[index];
                        
                        // Find the corresponding row
                        const generalItemRows = document.querySelectorAll('.general-item-row');
                        if (generalItemRows[rowIndex]) {
                            showGeneralItemError(generalItemRows[rowIndex], errorMessage);
                        }
                    }
                }
                
                if (key.startsWith('arm_lines.') && key.includes('.serials')) {
                    const match = key.match(/arm_lines\.(\d+)\.serials/);
                    if (match) {
                        const rowIndex = parseInt(match[1]);
                        const errorMessage = errorMessages[index];
                        
                        // Find the corresponding row
                        const armRows = document.querySelectorAll('.arm-row');
                        if (armRows[rowIndex]) {
                            showArmSerialError(armRows[rowIndex], errorMessage);
                        }
                    }
                }
            });
        }
        
        // Call displayValidationErrors when the page loads
        document.addEventListener('DOMContentLoaded', displayValidationErrors);
        
        // Also try to run after a short delay to ensure all elements are loaded
        
    </script>
</html>