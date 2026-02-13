<x-app-layout>
    @section('title', 'Create Quotation - Quotation Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/sales-dashboard', 'label' => 'Sales Dashboard'],
        ['url' => '/quotations', 'label' => 'Quotations'],
        ['url' => '#', 'label' => 'Create Quotation']
    ]" />

    <x-dynamic-heading 
        :title="'Create Quotation'" 
        :subtitle="'Add new quotation with general items and arms'"
        :icon="'fas fa-file-invoice'"
    />
    <style>
        
        .w-full {
            width: 100% !important;
            max-width: 100% !important;
        }
        
        /* Override for form inputs to maintain proper padding */
        .w-full input, .w-full select {
            padding: 0.75rem 1rem !important;
        }
        
        .bg-white.shadow-lg.rounded-lg {
            width: 100% !important;
            margin: 0 !important;
        }
        
        .bg-white.shadow-lg {
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
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
        
        #arms_table {
            width: 100%;
            min-width: 600px;
            table-layout: fixed;
        }
        
        #arms_table th,
        #arms_table td {
            padding: 1rem;
            vertical-align: middle;
            white-space: nowrap;
        }
        
        #arms_table th:nth-child(1), #arms_table td:nth-child(1) { width: 50%; min-width: 300px; } /* Arm */
        #arms_table th:nth-child(2), #arms_table td:nth-child(2) { width: 30%; min-width: 200px; } /* Sale Price */
        #arms_table th:nth-child(3), #arms_table td:nth-child(3) { width: 20%; min-width: 100px; } /* Actions */
        
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
        
        /* General form inputs - but not header inputs */
        .form-section input, .form-section select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        /* Header section inputs - clean and enhanced styling */
        .bg-gradient-to-r input, .bg-gradient-to-r select {
            width: 100%;
            padding: 0.625rem 1rem !important;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #ffffff;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            color: #374151;
        }
        
        .bg-gradient-to-r input:focus, .bg-gradient-to-r select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            background-color: #ffffff;
        }
        
        .bg-gradient-to-r input:hover, .bg-gradient-to-r select:hover {
            border-color: #9ca3af;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        /* Enhanced labels for header section */
        .bg-gradient-to-r label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.375rem;
            font-size: 0.875rem;
        }
        
        /* Placeholder styling */
        .bg-gradient-to-r input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }
        
        /* Select dropdown arrow styling */
        .bg-gradient-to-r select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1rem;
            padding-right: 2.5rem;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        label {
            display: block;
            font-weight: 500;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        button {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        
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
        
        .bg-gray-50 {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 1rem;
        }
        
        .flex.justify-end {
            gap: 0.75rem;
        }
        
        @media (max-width: 1400px) {
            .grid-cols-5 {
                grid-template-columns: repeat(3, 1fr);
            }
            #arms_table {
                min-width: 500px;
            }
        }
        
        @media (max-width: 1024px) {
            .grid-cols-5 {
                grid-template-columns: repeat(2, 1fr);
            }
            #arms_table {
                min-width: 450px;
            }
        }
        
        @media (max-width: 768px) {
            .grid-cols-5 {
                grid-template-columns: 1fr;
            }
            .w-full {
                padding: 0 0.5rem;
            }
            #arms_table {
                min-width: 400px;
            }
        }
        
        /* Searchable Dropdown Styles */
        .searchable-select-container {
            position: relative;
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
            position: absolute;
            z-index: 50;
            top: 100%;
            left: 0;
            right: 0;
            width: 100%;
            margin-top: 0.25rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            max-height: 12rem;
            overflow: hidden;
        }
        
        .general-item-row td {
            position: relative !important;
            overflow: visible !important;
        }
        
        #general_items_table {
            overflow: visible !important;
        }
        
        #general_items_container {
            overflow: visible !important;
        }
        
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
    </style>

    @if (Session::has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ Session::get('success') }}</span>
        </div>
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

    <form method="POST" action="{{ route('quotations.store') }}" id="quotationForm">
        @csrf

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        <!-- Main Content -->
        <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-6">
                                         <!-- Quotation Header Information -->
                     <div class="bg-gradient-to-r from-slate-50 to-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                         <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                             <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                             </svg>
                             Quotation Header Information
                         </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            <div>
                                <label for="party_search_input" class="block text-sm font-medium text-gray-700 mb-2">
                                    Party <span class="text-red-500">*</span>
                                </label>
                                <div class="searchable-select-container relative">
                                    <input type="text" 
                                           id="party_search_input" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-green-500 focus:ring-green-500 searchable-input bg-white @error('party_id') border-red-500 @enderror" 
                                           placeholder="Search parties..."
                                           autocomplete="off"
                                           value="{{ old('party_search_input') }}">
                                    <input type="hidden" name="party_id" id="party_id" class="selected-item-id" value="{{ old('party_id') }}">
                                    
                                    <!-- Dropdown -->
                                    <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                                        <div class="search-results-container max-h-40 overflow-y-auto">
                                            <!-- Results will be populated here -->
                                        </div>
                                        <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                                            <div class="flex justify-between items-center text-xs">
                                                <button type="button" class="prev-page text-green-500 hover:text-green-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-green-50 transition-colors">Previous</button>
                                                <span class="page-info text-gray-500 text-xs"></span>
                                                <button type="button" class="next-page text-green-500 hover:text-green-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-green-50 transition-colors">Next</button>
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
                                <p class="mt-1 text-xs text-gray-500" id="party_help_text">Required for credit sales, optional for cash sales</p>
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
                                     class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-green-500 focus:ring-green-500 @error('payment_type') border-red-500 @enderror">
                                    <option value="credit" {{ old('payment_type') == 'credit' ? 'selected' : '' }}>Credit (Party)</option>
                                    <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                                @error('payment_type')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="bank_field" style="display: none;">
                                <label for="bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank <span class="text-red-500">*</span>
                                </label>
                                                                 <select name="bank_id" id="bank_id" 
                                     class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-green-500 focus:ring-green-500 @error('bank_id') border-red-500 @enderror">
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
                                @error('bank_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quotation Date <span class="text-red-500">*</span>
                                </label>
                                                                 <input type="date" name="quotation_date" id="quotation_date" value="{{ old('quotation_date', date('Y-m-d')) }}" 
                                    required class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-green-500 focus:ring-green-500 @error('quotation_date') border-red-500 @enderror">
                                @error('quotation_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Valid Until Date -->
                            <div>
                                <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">
                                    Valid Until <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" 
                                    required class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-green-500 focus:ring-green-500 @error('valid_until') border-red-500 @enderror">
                                @error('valid_until')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="shipping_charges" class="block text-sm font-medium text-gray-700 mb-2">
                                    Shipping Charges
                                </label>
                                                                 <input type="number" name="shipping_charges" id="shipping_charges" value="{{ old('shipping_charges', 0) }}" 
                                        step="1" min="0" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-green-500 focus:ring-green-500 @error('shipping_charges') border-red-500 @enderror">
                                @error('shipping_charges')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details Section (for Cash sales) -->
                    <div id="customer_details_field" style="display: none;" class="mb-4">
                        <div class="bg-gray-50 border rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Customer Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
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

                    <!-- Party Details Section (for Credit sales when party is selected) -->
                    <div id="party_details_field" style="display: none;" class="mb-4">
                        <div class="bg-gray-50 border rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Party Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
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
                                    <label for="party_dc_date" class="block text-xs font-medium text-gray-600 mb-1">DC Date</label>
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
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">General Items (FIFO batches)</h2>
                            <button type="button" id="add_general_item" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Line
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="general_items_table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
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
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Arms (serial-based)</h2>
                            </div>
                            <button type="button" id="add_arm" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Line
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200" id="arms_table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arm</th>
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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('quotations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" name="action" value="save" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Save Draft
                        </button>
                        <button type="submit" name="action" value="post" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Quotation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Templates for dynamic content -->
    <template id="general_item_template">
        <tr class="general-item-row">
            <td class="px-4 py-4 whitespace-nowrap">
                <div class="relative">
                    <div class="searchable-select-container relative">
                        <input type="text" 
                               class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-green-400 focus:ring-1 focus:ring-green-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
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
                                    <button type="button" class="prev-page text-green-500 hover:text-green-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-green-50 transition-colors">Previous</button>
                                    <span class="page-info text-gray-500 text-xs"></span>
                                    <button type="button" class="next-page text-green-500 hover:text-green-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-green-50 transition-colors">Next</button>
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
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-qty focus:border-green-500 focus:ring-green-500"
                       placeholder="0" value="1">
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <input type="number" name="general_lines[INDEX][sale_price]" required step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-sale-price focus:border-green-500 focus:ring-green-500"
                       placeholder="0">
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <span class="text-sm font-medium general-line-total text-gray-900">0</span>
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
             <td class="px-4 py-4 whitespace-nowrap">
                 <div class="searchable-select-container relative">
                     <input type="text" 
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
                            placeholder="Search arms..."
                            autocomplete="off"
                            data-index="INDEX">
                     <input type="hidden" name="arm_lines[INDEX][arm_id]" class="selected-arm-id">
                     
                     <!-- Error display for arm selection -->
                     <div class="arm-error mt-1 text-xs text-red-600 hidden"></div>
                     
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
             </td>
             <td class="px-4 py-4 whitespace-nowrap">
                 <input type="number" name="arm_lines[INDEX][sale_price]" required step="0.01" min="0" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm arm-sale-price focus:border-blue-500 focus:ring-blue-500"
                        placeholder="0">
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
        const wasSubmitting = sessionStorage.getItem('quotation_form_submitting');
        const hasFailedSubmissionFlag = localStorage.getItem('quotation_form_failed_submission');
        
        console.log('Immediate check on page load:', {
            wasSubmitting: wasSubmitting,
            hasFailedSubmissionFlag: hasFailedSubmissionFlag,
            url: window.location.href,
            referrer: document.referrer
        });
        
        // Check if this is a fresh navigation to create page (not coming from a form submission)
        const isCreatePage = window.location.href.includes('/quotations/create');
        const hasVisibleErrors = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const hasVisibleSuccess = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        
        // Check if we're coming from a different page (not from form submission)
        const isFromDifferentPage = document.referrer && 
            !document.referrer.includes('/quotations/create') && 
            !document.referrer.includes('quotations/create');
        
        console.log('Error detection:', {
            isCreatePage: isCreatePage,
            hasVisibleErrors: !!hasVisibleErrors,
            hasVisibleSuccess: !!hasVisibleSuccess,
            wasSubmitting: wasSubmitting,
            hasFailedSubmissionFlag: hasFailedSubmissionFlag,
            isFromDifferentPage: isFromDifferentPage,
            referrer: document.referrer
        });
        
        // Clear data if:
        // 1. Fresh page load (no flags), OR
        // 2. Success message is visible (regardless of flags), OR
        // 3. Coming from different page with no errors (likely successful navigation)
        if ((!wasSubmitting && !hasFailedSubmissionFlag) || 
            hasVisibleSuccess ||
            (isFromDifferentPage && !hasVisibleErrors)) {
            console.log('Clearing data - fresh page/success/different page navigation detected');
            localStorage.removeItem('quotation_form_data');
            localStorage.removeItem('quotation_form_failed_submission');
            sessionStorage.removeItem('quotation_form_submitting');
        } else {
            console.log('Preserving data - submission flags detected, letting main logic handle restoration');
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        let generalItemIndex = 0;
        let armIndex = 0;

        // Sale type change handler
        document.getElementById('payment_type').addEventListener('change', function() {
            const bankField = document.getElementById('bank_field');
            const bankSelect = document.getElementById('bank_id');
            const customerField = document.getElementById('party_id');
            const customerLabel = document.querySelector('label[for="party_id"]');
            const customerHelpText = document.getElementById('party_help_text');
            const customerDetailsField = document.getElementById('customer_details_field');
            const partyDetailsField = document.getElementById('party_details_field');

            // Clear any existing party error when sale type changes
            const partyError = document.getElementById('party_error');
            if (partyError) {
                partyError.remove();
            }
            
            if (this.value === 'cash') {
                bankField.style.display = 'block';
                bankSelect.required = true;
                customerDetailsField.style.display = 'block';
                partyDetailsField.style.display = 'none';
                
                customerField.required = false;
                customerLabel.innerHTML = 'Party';
                customerHelpText.textContent = 'Optional for cash sales - you can leave this blank';
            } else {
                bankField.style.display = 'none';
                bankSelect.required = false;
                bankSelect.value = '';
                customerDetailsField.style.display = 'none';
                
                customerField.required = true;
                customerLabel.innerHTML = 'Party <span class="text-red-500">*</span>';
                customerHelpText.textContent = 'Required for credit sales';
                
                // Show party details if party is selected
                if (customerField.value) {
                    loadPartyDetails(customerField.value);
                    partyDetailsField.style.display = 'block';
                } else {
                    partyDetailsField.style.display = 'none';
                }
            }
        });

        // Party selection change handler
        document.getElementById('party_id').addEventListener('change', function() {
            // Clear any existing party error when party is selected
            const partyError = document.getElementById('party_error');
            if (partyError) {
                partyError.remove();
            }
            
            if (this.value) {
                fetchCustomerBalance(this.value);
                
                // If sale type is credit, show party details
                const paymentType = document.getElementById('payment_type').value;
                if (paymentType === 'credit') {
                    loadPartyDetails(this.value);
                    document.getElementById('party_details_field').style.display = 'block';
                }
            } else {
                hideCustomerBalance();
                
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
        function fetchCustomerBalance(customerId) {
            console.log(`Fetching balance for party ${customerId}`);
            
            const currentDate = document.getElementById('quotation_date').value;
            const url = `/parties/${customerId}/balance${currentDate ? `?date=${currentDate}` : ''}`;
            
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
                        showCustomerBalance(data.formatted_balance, data.status);
                    } else {
                        hideCustomerBalance();
                    }
                })
                .catch(error => {
                    console.error('Error fetching party balance:', error);
                    hideCustomerBalance();
                });
        }

        // Function to fetch bank balance
        function fetchBankBalance(bankId) {
            console.log(`Fetching balance for bank ${bankId}`);
            
            const currentDate = document.getElementById('quotation_date').value;
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
        function showCustomerBalance(balance, status) {
            const balanceDiv = document.getElementById('party_balance');
            const balanceSpan = document.getElementById('party_balance_amount');
            
            balanceSpan.textContent = balance;
            
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
        function hideCustomerBalance() {
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
        const quotationDateInput = document.getElementById('quotation_date');
        if (quotationDateInput) {
            quotationDateInput.addEventListener('change', function() {
            const customerSelect = document.getElementById('party_id');
            const bankSelect = document.getElementById('bank_id');
            
            if (customerSelect.value) {
                fetchCustomerBalance(customerSelect.value);
            }
            if (bankSelect.value) {
                fetchBankBalance(bankSelect.value);
            }
        });
        }

        // Validate stock before form submission
        function validateStock() {
            const generalItemRows = document.querySelectorAll('.general-item-row');
            let hasInsufficientStock = false;
            let stockErrors = [];
            
            generalItemRows.forEach((row, index) => {
                const itemInput = row.querySelector('.searchable-input');
                const selectedItemId = row.querySelector('.selected-item-id').value;
                const quantityInput = row.querySelector('input[name*="[qty]"]');
                const quantity = parseFloat(quantityInput?.value || 0);
                
                if (selectedItemId && itemInput && quantity > 0) {
                    // Get available stock from the item data
                    const stockInfo = row.querySelector('.item-info');
                    if (stockInfo) {
                        const stockText = stockInfo.textContent;
                        const stockMatch = stockText.match(/Stock:\s*(\d+(?:\.\d+)?)/);
                        if (stockMatch) {
                            const availableStock = parseFloat(stockMatch[1]);
                            if (quantity > availableStock) {
                                hasInsufficientStock = true;
                                const itemName = itemInput.value;
                                stockErrors.push(`Line ${index + 1}: Insufficient stock for '${itemName}'. Available: ${availableStock}, Required: ${quantity}`);
                                
                                // Highlight the quantity field
                                quantityInput.style.borderColor = '#ef4444';
                                quantityInput.style.backgroundColor = '#fef2f2';
                            } else {
                                // Reset styling if stock is sufficient
                                quantityInput.style.borderColor = '';
                                quantityInput.style.backgroundColor = '';
                            }
                        }
                    }
                }
            });
            
            if (hasInsufficientStock) {
                alert('⚠️ Stock Validation Error:\n\n' + stockErrors.join('\n') + '\n\nPlease adjust quantities or remove items with insufficient stock.');
                return false;
            }
            
            return true;
        }

        // Validate quantity for a specific row
        function validateQuantityForRow(quantityInput) {
            const row = quantityInput.closest('.general-item-row');
            const itemInput = row.querySelector('.searchable-input');
            const selectedItemId = row.querySelector('.selected-item-id').value;
            const quantity = parseFloat(quantityInput.value || 0);
            
            if (selectedItemId && itemInput && quantity > 0) {
                const stockInfo = row.querySelector('.item-info');
                if (stockInfo) {
                    const stockText = stockInfo.textContent;
                    const stockMatch = stockText.match(/Stock:\s*(\d+(?:\.\d+)?)/);
                    if (stockMatch) {
                        const availableStock = parseFloat(stockMatch[1]);
                        if (quantity > availableStock) {
                            // Show error styling
                            quantityInput.style.borderColor = '#ef4444';
                            quantityInput.style.backgroundColor = '#fef2f2';
                            
                            // Show error message
                            let errorDiv = row.querySelector('.quantity-error');
                            if (!errorDiv) {
                                errorDiv = document.createElement('div');
                                errorDiv.className = 'quantity-error text-xs text-red-600 mt-1';
                                quantityInput.parentNode.appendChild(errorDiv);
                            }
                            errorDiv.textContent = `Insufficient stock. Available: ${availableStock}`;
                        } else {
                            // Reset styling and remove error message
                            quantityInput.style.borderColor = '';
                            quantityInput.style.backgroundColor = '';
                            const errorDiv = row.querySelector('.quantity-error');
                            if (errorDiv) {
                                errorDiv.remove();
                            }
                        }
                    }
                }
            } else {
                // Reset styling if no item selected or quantity is 0
                quantityInput.style.borderColor = '';
                quantityInput.style.backgroundColor = '';
                const errorDiv = row.querySelector('.quantity-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
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
            const salePriceInput = newRow.querySelector('input[name*="[sale_price]"]');
            
            // Initialize searchable dropdown for general items
            if (searchableContainer) {
                new GeneralItemSearchableDropdown(searchableContainer, {
                    itemsPerPage: 15,
                    debounceDelay: 300,
                    minSearchLength: 2
                });
                searchableContainer.dataset.initialized = 'true';
            }
            
            // Add input event listeners for calculations
            [qtyInput, salePriceInput].forEach(input => {
                input.addEventListener('input', function() {
                    calculateLineTotal(this);
                    calculateTotals();
                });
            });
            
            // Add quantity validation for stock checking
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    validateQuantityForRow(this);
                });
            }
            
            generalItemIndex++;
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
            
            container.appendChild(clone);
            
            // Add event listeners
            const newRow = container.lastElementChild;
            const searchableContainer = newRow.querySelector('.searchable-select-container');
            const salePriceInput = newRow.querySelector('input[name*="[sale_price]"]');
            
            // Initialize searchable dropdown for arms
            if (searchableContainer) {
                new ArmSearchableDropdown(searchableContainer, {
                    itemsPerPage: 15,
                    debounceDelay: 300,
                    minSearchLength: 2
                });
                searchableContainer.dataset.initialized = 'true';
            }
            
            // Add calculation listener
            salePriceInput.addEventListener('input', function() {
                calculateTotals();
            });
            
            armIndex++;
        });

        // Remove buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-general-item') || e.target.closest('.remove-general-item')) {
                const generalItemRow = e.target.closest('.general-item-row');
                const selectedGeneralItemId = generalItemRow.querySelector('.selected-item-id')?.value;
                
                // Remove general item ID from global array if it exists
                if (selectedGeneralItemId && window.selectedGeneralItemIds && Array.isArray(window.selectedGeneralItemIds) && window.selectedGeneralItemIds.includes(selectedGeneralItemId)) {
                    window.selectedGeneralItemIds = window.selectedGeneralItemIds.filter(id => id !== selectedGeneralItemId);
                }
                
                generalItemRow.remove();
                calculateTotals();
            }
            
            if (e.target.classList.contains('remove-arm') || e.target.closest('.remove-arm')) {
                const armRow = e.target.closest('.arm-row');
                const selectedArmId = armRow.querySelector('.selected-arm-id')?.value;
                
                // Remove arm ID from global array if it exists
                if (selectedArmId && window.selectedArmIds && Array.isArray(window.selectedArmIds) && window.selectedArmIds.includes(selectedArmId)) {
                    window.selectedArmIds = window.selectedArmIds.filter(id => id !== selectedArmId);
                }
                
                armRow.remove();
                calculateTotals();
            }
        });

        // Form validation
        document.getElementById('quotationForm').addEventListener('submit', function(e) {
            // Validate party selection for credit sales
            const paymentType = document.getElementById('payment_type').value;
            const partyId = document.getElementById('party_id').value;
            const partyError = document.getElementById('party_error');
            
            // Remove existing error message
            if (partyError) {
                partyError.remove();
            }
            
            // Validate party selection for credit sales
            if (paymentType === 'credit' && (!partyId || partyId === '')) {
                e.preventDefault();
                
                // Create error message
                const errorDiv = document.createElement('p');
                errorDiv.id = 'party_error';
                errorDiv.className = 'mt-1 text-xs text-red-600';
                errorDiv.textContent = 'Party selection is required for credit sales.';
                
                // Insert error message after the party field
                const partyField = document.getElementById('party_id').closest('div');
                partyField.appendChild(errorDiv);
                
                // Scroll to the error
                partyField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                return false;
            }
            
            if (!validateStock()) {
                e.preventDefault();
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
                const selectedArmId = row.querySelector('.selected-arm-id').value;
                if (!selectedArmId || selectedArmId === '') {
                    showArmError(row, 'Please select an arm.');
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
            document.querySelectorAll('.arm-error').forEach(error => {
                error.classList.add('hidden');
                error.textContent = '';
            });
            
            // Remove red borders
            document.querySelectorAll('.searchable-input').forEach(input => {
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
        
        // Function to show arm error
        function showArmError(row, message) {
            const errorDiv = row.querySelector('.arm-error');
            const input = row.querySelector('.searchable-input');
            
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
            }
            
            if (input) {
                input.classList.add('border-red-500');
            }
        }

        // Calculation functions
        function calculateLineTotal(element) {
            const row = element.closest('.general-item-row');
            const qty = parseFloat(row.querySelector('.general-qty').value) || 0;
            const salePrice = parseFloat(row.querySelector('.general-sale-price').value) || 0;
            
            const lineTotal = qty * salePrice;
            row.querySelector('.general-line-total').textContent = lineTotal.toFixed(2);
        }

        function calculateTotals() {
            let subtotal = 0;
            
            // Calculate general items
            document.querySelectorAll('.general-item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.general-qty').value) || 0;
                const salePrice = parseFloat(row.querySelector('.general-sale-price').value) || 0;
                
                subtotal += qty * salePrice;
            });
            
            // Calculate arms
            document.querySelectorAll('.arm-row').forEach(row => {
                const salePrice = parseFloat(row.querySelector('.arm-sale-price').value) || 0;
                subtotal += salePrice;
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

        // General Item Searchable Dropdown
        class GeneralItemSearchableDropdown {
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
                this.input.addEventListener('focus', () => {
                    this.showDropdown();
                    this.performSearch();
                });
                
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
                    const url = new URL('/api/general-items/search', window.location.origin);
                    url.searchParams.set('q', this.searchTerm);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    
                    // Add excluded general item IDs if any
                    if (window.selectedGeneralItemIds && Array.isArray(window.selectedGeneralItemIds) && window.selectedGeneralItemIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedGeneralItemIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
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
                    const url = new URL('/api/general-items', window.location.origin);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    
                    // Add excluded general item IDs if any
                    if (window.selectedGeneralItemIds && Array.isArray(window.selectedGeneralItemIds) && window.selectedGeneralItemIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedGeneralItemIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
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
                
                items.forEach((item, index) => {
                    if (!item || !item.id || !item.item_name) {
                        return;
                    }
                    
                    const resultItem = document.createElement('div');
                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                    resultItem.dataset.itemId = item.id;
                    resultItem.dataset.itemName = item.item_name;
                    resultItem.dataset.salePrice = this.safeNumber(item.sale_price);
                    resultItem.dataset.availableStock = this.safeNumber(item.available_stock);
                    
                    resultItem.innerHTML = `
                        <div class="font-medium text-gray-900">${item.item_name}</div>
                    `;
                    
                    resultItem.addEventListener('click', () => {
                        this.selectItem(item);
                    });
                    
                    this.resultsContainer.appendChild(resultItem);
                });
                
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
                
                // Add selected general item ID to global array to exclude from other dropdowns
                if (!window.selectedGeneralItemIds || !Array.isArray(window.selectedGeneralItemIds)) {
                    window.selectedGeneralItemIds = [];
                }
                if (!window.selectedGeneralItemIds.includes(item.id)) {
                    window.selectedGeneralItemIds.push(item.id);
                }
                
                // Show available stock and stock warning if needed
                const availableStock = item.available_stock || 0;
                const row = this.container.closest('.general-item-row');
                
                // Remove any existing stock warning and info
                const existingWarning = row.querySelector('.stock-warning');
                const existingInfo = row.querySelector('.item-info');
                if (existingWarning) {
                    existingWarning.remove();
                }
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                // Add item info (available stock) - positioned absolutely to not affect layout
                const infoDiv = document.createElement('div');
                infoDiv.className = 'item-info absolute text-xs text-gray-500 top-full left-0 mt-1 z-10 bg-white px-1 rounded';
                infoDiv.innerHTML = `
                    <span>Stock: <span class="font-medium">${availableStock}</span></span>
                `;
                this.container.style.position = 'relative';
                this.container.appendChild(infoDiv);
                
                // Add stock warning if stock is low or zero - positioned absolutely
                if (availableStock <= 0) {
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'stock-warning absolute text-red-600 text-xs font-medium top-full left-0 mt-1 z-10 bg-white px-1 rounded';
                    warningDiv.textContent = '⚠️ No stock!';
                    this.container.appendChild(warningDiv);
                } else if (availableStock <= 5) {
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'stock-warning absolute text-orange-600 text-xs font-medium top-full left-0 mt-1 z-10 bg-white px-1 rounded';
                    warningDiv.textContent = `⚠️ Low: ${availableStock}`;
                    this.container.appendChild(warningDiv);
                }
                
                // Populate sale price
                const salePriceInput = row.querySelector('.general-sale-price');
                
                if (salePriceInput) {
                    salePriceInput.value = item.sale_price ? item.sale_price : '';
                }
                
                // Trigger calculation
                if (salePriceInput) {
                    calculateLineTotal(salePriceInput);
                    calculateTotals();
                }
                
                this.hideDropdown();
            }
            
            selectFirstResult() {
                const firstResult = this.resultsContainer.querySelector('.result-item');
                if (firstResult) {
                    const item = {
                        id: firstResult.dataset.itemId,
                        item_name: firstResult.dataset.itemName,
                        sale_price: this.safeNumber(firstResult.dataset.salePrice),
                        available_stock: this.safeNumber(firstResult.dataset.availableStock)
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
                        sale_price: this.safeNumber(highlightedResult.dataset.salePrice),
                        available_stock: this.safeNumber(highlightedResult.dataset.availableStock)
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
                
                results.forEach(item => item.classList.remove('selected', 'bg-green-100'));
                
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-green-100');
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
            
            clearSelection() {
                // Remove the previously selected general item ID from global array
                if (this.selectedItem && this.selectedItem.id && window.selectedGeneralItemIds && Array.isArray(window.selectedGeneralItemIds)) {
                    window.selectedGeneralItemIds = window.selectedGeneralItemIds.filter(id => id !== this.selectedItem.id);
                }
                
                // Clear the current selection
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
                
                // Clear sale price and qty
                const row = this.container.closest('.general-item-row');
                const salePriceInput = row.querySelector('.general-sale-price');
                const qtyInput = row.querySelector('.general-qty');
                
                if (salePriceInput) {
                    salePriceInput.value = '';
                }
                if (qtyInput) {
                    qtyInput.value = '1';
                }
                
                // Remove any stock warnings and item info
                const existingWarning = row.querySelector('.stock-warning');
                const existingInfo = row.querySelector('.item-info');
                if (existingWarning) {
                    existingWarning.remove();
                }
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                // Trigger calculation
                calculateTotals();
            }
        }

        // Global array to track selected arm IDs
        window.selectedArmIds = window.selectedArmIds || [];

        // Global array to track selected general item IDs
        window.selectedGeneralItemIds = window.selectedGeneralItemIds || [];

        // Arm Searchable Dropdown
        class ArmSearchableDropdown {
            constructor(container, options = {}) {
                this.container = container;
                this.input = container.querySelector('.searchable-input');
                this.hiddenInput = container.querySelector('.selected-arm-id');
                this.dropdown = container.querySelector('.searchable-dropdown');
                this.resultsContainer = container.querySelector('.search-results-container');
                this.paginationContainer = container.querySelector('.pagination-container');
                this.loadingIndicator = container.querySelector('.loading-indicator');
                
                this.searchTimeout = null;
                this.currentPage = 1;
                this.searchTerm = '';
                this.selectedItem = null;
                
                this.itemsPerPage = options.itemsPerPage || 10;
                this.debounceDelay = options.debounceDelay || 300;
                this.minSearchLength = options.minSearchLength || 2;
                
                this.init();
            }
            
            init() {
                this.bindEvents();
                this.setupGlobalClickHandler();
                
                // Add blur event listener for clearing selection
                this.input.addEventListener('blur', () => {
                    if (!this.input.value.trim()) {
                        this.clearSelection();
                    }
                });
            }
            
            bindEvents() {
                this.input.addEventListener('focus', () => {
                    this.showDropdown();
                    this.performSearch();
                });
                
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
                    // Build URL with excluded arm IDs
                    const url = new URL('/api/arms/search', window.location.origin);
                    url.searchParams.set('q', this.searchTerm);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    
                    // Add excluded arm IDs if any
                    if (window.selectedArmIds && Array.isArray(window.selectedArmIds) && window.selectedArmIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedArmIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
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
                    // Build URL with excluded arm IDs
                    const url = new URL('/api/arms', window.location.origin);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    
                    // Add excluded arm IDs if any
                    if (window.selectedArmIds && Array.isArray(window.selectedArmIds) && window.selectedArmIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedArmIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
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
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial load error:', error);
                    this.showError(`Failed to load arms: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            displayResults(items, meta) {
                this.resultsContainer.innerHTML = '';
                
                if (!items || !Array.isArray(items) || items.length === 0) {
                    this.resultsContainer.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            ${this.searchTerm ? 'No arms found matching your search.' : 'No arms available.'}
                        </div>
                    `;
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                
                                 items.forEach((item, index) => {
                     if (!item || !item.id || !item.arm_title) {
                         return;
                     }
                     
                     const resultItem = document.createElement('div');
                     resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                     resultItem.dataset.itemId = item.id;
                     resultItem.dataset.itemName = item.arm_title;
                     resultItem.dataset.salePrice = this.safeNumber(item.sale_price);
                     
                     resultItem.innerHTML = `
                         <div class="font-medium text-gray-900">${item.arm_title}</div>
                     `;
                     
                     resultItem.addEventListener('click', () => {
                         this.selectItem(item);
                     });
                     
                     this.resultsContainer.appendChild(resultItem);
                 });
                
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
                 this.input.value = item.arm_title;
                 this.hiddenInput.value = item.id;
                 
                 // Add selected arm ID to global array to exclude from other dropdowns
                 if (!window.selectedArmIds || !Array.isArray(window.selectedArmIds)) {
                     window.selectedArmIds = [];
                 }
                 if (!window.selectedArmIds.includes(item.id)) {
                     window.selectedArmIds.push(item.id);
                 }
                 
                 // Populate sale price
                 const row = this.container.closest('.arm-row');
                 const salePriceInput = row.querySelector('.arm-sale-price');
                 
                 if (salePriceInput) {
                     salePriceInput.value = item.sale_price || '';
                 }
                 
                 // Trigger calculation
                 if (salePriceInput) {
                     calculateTotals();
                 }
                 
                 this.hideDropdown();
             }
            
                         selectFirstResult() {
                 const firstResult = this.resultsContainer.querySelector('.result-item');
                 if (firstResult) {
                     const item = {
                         id: firstResult.dataset.itemId,
                         arm_title: firstResult.dataset.itemName,
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
                         arm_title: highlightedResult.dataset.itemName,
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
                
                results.forEach(item => item.classList.remove('selected', 'bg-blue-100'));
                
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
                if (value === null || value === undefined || value === '') {
                    return 0;
                }
                
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }
            
            showError(message) {
                this.resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-red-500 text-center">
                        <div class="font-medium">Error Loading Arms</div>
                        <div class="mt-1">${message}</div>
                        <div class="mt-2 text-xs text-gray-500">
                            Please check your browser console for more details.
                        </div>
                    </div>
                `;
            }
            
            clearSelection() {
                // Remove the previously selected arm ID from global array
                if (this.selectedItem && this.selectedItem.id && window.selectedArmIds && Array.isArray(window.selectedArmIds)) {
                    window.selectedArmIds = window.selectedArmIds.filter(id => id !== this.selectedItem.id);
                }
                
                // Clear the current selection
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
                
                // Clear sale price
                const row = this.container.closest('.arm-row');
                const salePriceInput = row.querySelector('.arm-sale-price');
                if (salePriceInput) {
                    salePriceInput.value = '';
                    calculateTotals();
                }
            }
        }

        // Party Searchable Dropdown
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
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial load error:', error);
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
                
                parties.forEach((party, index) => {
                    if (!party || !party.id || !party.name) {
                        return;
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
                        this.selectParty(party);
                    });
                    
                    this.resultsContainer.appendChild(resultItem);
                });
                
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
            
            selectParty(party) {
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
                    this.selectParty(party);
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
                    this.selectParty(party);
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
                
                results.forEach(item => item.classList.remove('selected', 'bg-green-100'));
                
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-green-100');
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
                }
                container.dataset.initialized = 'true';
            }
        });

        // Handle pre-selected party from form validation errors
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
                        fetchCustomerBalance(party.id);
                    }
                })
                .catch(error => {
                    console.error('Error fetching pre-selected party:', error);
                });
        }

        // Initialize calculation listeners
        document.getElementById('shipping_charges').addEventListener('input', function() {
            calculateTotals();
        });

        // Input mask functions
        function applyInputMasks() {
            const cnicInput = document.getElementById('cnic');
            if (cnicInput) {
                cnicInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
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

            const contactInput = document.getElementById('contact');
            if (contactInput) {
                contactInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 0) {
                        if (!value.startsWith('03')) {
                            if (value.startsWith('3')) {
                                value = '0' + value;
                            } else if (!value.startsWith('0')) {
                                value = '03' + value;
                            }
                        }
                        
                        if (value.length > 4) {
                            value = value.substring(0, 4) + '-' + value.substring(4);
                        }
                        
                        if (value.length > 12) {
                            value = value.substring(0, 12);
                        }
                    }
                    e.target.value = value;
                });
            }
        }

        // Initialize input masks on page load
        applyInputMasks();

        // Initialize payment type state
        const paymentTypeSelect = document.getElementById('payment_type');
        if (paymentTypeSelect && paymentTypeSelect.value === 'cash') {
            paymentTypeSelect.dispatchEvent(new Event('change'));
        }
    });

    // ===== PARTY RESTORATION FUNCTION =====
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

    // ===== DATA PERSISTENCE FOR QUOTATION FORM =====

    // Function to save quotation form data to localStorage
    function saveQuotationFormData() {
        try {
            const formData = {
                payment_type: document.getElementById('payment_type').value,
                party_id: document.getElementById('party_id').value,
                party_display: document.getElementById('party_search_input')?.value || '',
                bank_id: document.getElementById('bank_id').value,
                quotation_date: document.getElementById('quotation_date').value,
                valid_until: document.getElementById('valid_until').value,
                // Party license details
                party_license_no: document.getElementById('party_license_no')?.value || '',
                party_license_issue_date: document.getElementById('party_license_issue_date')?.value || '',
                party_license_valid_upto: document.getElementById('party_license_valid_upto')?.value || '',
                party_license_issued_by: document.getElementById('party_license_issued_by')?.value || '',
                party_re_reg_no: document.getElementById('party_re_reg_no')?.value || '',
                party_dc: document.getElementById('party_dc')?.value || '',
                party_dc_date: document.getElementById('party_dc_date')?.value || '',
                general_items: [],
                arms: []
            };

            // Collect general items data
            const generalRows = document.querySelectorAll('.general-item-row');
            generalRows.forEach((row, index) => {
                const generalItemId = row.querySelector('.selected-item-id')?.value || '';
                const generalItemDisplay = row.querySelector('.searchable-input')?.value || '';
                const qty = row.querySelector('.general-qty')?.value || '';
                const unitPrice = row.querySelector('.general-price')?.value || '';
                const description = row.querySelector('.general-description')?.value || '';

                if (generalItemId || qty || unitPrice) {
                    formData.general_items.push({
                        general_item_id: generalItemId,
                        general_item_display: generalItemDisplay,
                        qty: qty,
                        unit_price: unitPrice,
                        description: description
                    });
                }
            });

            // Collect arms data
            const armRows = document.querySelectorAll('.arm-row');
            armRows.forEach((row, index) => {
                const armId = row.querySelector('.selected-arm-id')?.value || '';
                const armDisplay = row.querySelector('.searchable-input')?.value || '';
                const salePrice = row.querySelector('.arm-sale-price')?.value || '';

                if (armId || salePrice) {
                    formData.arms.push({
                        arm_id: armId,
                        arm_display: armDisplay,
                        sale_price: salePrice
                    });
                }
            });

            // Only save if there's meaningful data
            if (formData.sale_type || formData.party_id || formData.bank_id || 
                formData.party_license_no || formData.party_license_issue_date || formData.party_license_valid_upto ||
                formData.party_license_issued_by || formData.party_re_reg_no || formData.party_dc || formData.party_dc_date ||
                formData.general_items.length > 0 || formData.arms.length > 0) {
                localStorage.setItem('quotation_form_data', JSON.stringify(formData));
                console.log('Quotation form data saved:', formData);
            }
        } catch (error) {
            console.error('Error saving quotation form data:', error);
        }
    }

    // Function to load saved quotation form data from localStorage
    function loadSavedQuotationData() {
        try {
            const savedData = localStorage.getItem('quotation_form_data');
            if (!savedData) {
                console.log('No saved quotation data found');
                return;
            }

            const formData = JSON.parse(savedData);
            console.log('Loading saved quotation data:', formData);

            // Restore header data
            if (formData.sale_type) {
                document.getElementById('sale_type').value = formData.sale_type;
                console.log('Restored sale_type:', formData.sale_type);
            }
            if (formData.party_id) {
                document.getElementById('party_id').value = formData.party_id;
                console.log('Restored party_id:', formData.party_id);
                
                // Use the party restoration function if available
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
                console.log('Restored invoice_date:', formData.invoice_date);
            }

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
                restoreGeneralItems(formData.general_items);
            }

            // Restore arms
            if (formData.arms && formData.arms.length > 0) {
                console.log('Restoring arms:', formData.arms);
                restoreArms(formData.arms);
            }

            console.log('Quotation form data restored successfully');
        } catch (error) {
            console.error('Error loading saved quotation form data:', error);
        }
    }

    // Function to clear saved quotation form data
    function clearQuotationFormData() {
        localStorage.removeItem('quotation_form_data');
        localStorage.removeItem('quotation_form_failed_submission');
        sessionStorage.removeItem('quotation_form_submitting');
        console.log('Quotation form data cleared');
    }

    // Function to restore general items
    function restoreGeneralItems(generalItems) {
        console.log('restoreGeneralItems called with:', generalItems);
        
        // Ensure we have at least one general item row
        const existingRows = document.querySelectorAll('.general-item-row');
        if (existingRows.length === 0) {
            console.log('No existing rows found, creating first row');
            document.getElementById('add_general_item').click();
            
            // Wait for the row to be added
            setTimeout(() => {
                restoreGeneralItems(generalItems);
            }, 100);
            return;
        }

        generalItems.forEach((item, index) => {
            const rows = document.querySelectorAll('.general-item-row');
            const targetRow = rows[index];
            
            if (targetRow) {
                console.log(`Restoring item ${index}, found ${rows.length} rows, targeting row:`, targetRow);
                
                // Restore general item selection
                if (item.general_item_id) {
                    const hiddenInput = targetRow.querySelector('.selected-item-id');
                    const searchInput = targetRow.querySelector('.searchable-input');
                    
                    if (hiddenInput) {
                        hiddenInput.value = item.general_item_id;
                    }
                    if (searchInput && item.general_item_display) {
                        searchInput.value = item.general_item_display;
                        // Trigger searchable dropdown update
                        triggerSearchableDropdownUpdate(searchInput);
                    }
                }
                
                // Restore quantity
                if (item.qty) {
                    const qtyInput = targetRow.querySelector('.general-qty');
                    if (qtyInput) {
                        qtyInput.value = item.qty;
                        console.log('Restored qty:', item.qty);
                    }
                }
                
                // Restore sale price
                if (item.sale_price) {
                    const priceInput = targetRow.querySelector('.general-sale-price');
                    if (priceInput) {
                        priceInput.value = item.sale_price;
                        console.log('Restored sale_price:', item.sale_price);
                    }
                }
                
                // Restore description
                if (item.description) {
                    const descInput = targetRow.querySelector('.general-description');
                    if (descInput) {
                        descInput.value = item.description;
                    }
                }
                
                console.log('Successfully restored general item:', item);
            }
        });
        
        console.log('Finished processing all general items');
    }

    // Function to restore arms
    function restoreArms(arms) {
        console.log('restoreArms called with:', arms);
        
        // Ensure we have at least one arm row
        const existingRows = document.querySelectorAll('.arm-row');
        if (existingRows.length === 0) {
            console.log('No existing arm rows found, creating first row');
            document.getElementById('add_arm').click();
            
            // Wait for the row to be added
            setTimeout(() => {
                restoreArms(arms);
            }, 100);
            return;
        }
        
        arms.forEach((arm, index) => {
            const rows = document.querySelectorAll('.arm-row');
            const targetRow = rows[index];
            
            if (targetRow) {
                console.log(`Restoring arm ${index}, found ${rows.length} rows, targeting row:`, targetRow);
                
                // Restore arm selection
                if (arm.arm_id) {
                    const hiddenInput = targetRow.querySelector('.selected-arm-id');
                    const searchInput = targetRow.querySelector('.searchable-input');
                    
                    if (hiddenInput) {
                        hiddenInput.value = arm.arm_id;
                    }
                    if (searchInput && arm.arm_display) {
                        searchInput.value = arm.arm_display;
                        // Trigger searchable dropdown update
                        triggerSearchableDropdownUpdate(searchInput);
                    }
                }
                
                // Restore sale price
                if (arm.sale_price) {
                    const priceInput = targetRow.querySelector('.arm-sale-price');
                    if (priceInput) {
                        priceInput.value = arm.sale_price;
                        console.log('Restored arm sale_price:', arm.sale_price);
                    }
                }
                
                console.log('Successfully restored arm:', arm);
            }
        });
        
        console.log('Finished processing all arms');
    }

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

    // Function to add data persistence listeners
    function addQuotationDataPersistenceListeners() {
        // Add listeners to form fields
        const formFields = [
            'sale_type', 'party_id', 'bank_id', 'invoice_date'
        ];
        
        formFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', saveQuotationFormData);
                field.addEventListener('change', saveQuotationFormData);
            }
        });
        
        // Add listeners to party search input
        const partySearchInput = document.getElementById('party_search_input');
        if (partySearchInput) {
            partySearchInput.addEventListener('input', saveQuotationFormData);
            partySearchInput.addEventListener('change', saveQuotationFormData);
        }
        
        // Add listeners to dynamic content (general items and arms)
        // These will be added when rows are created
        document.addEventListener('input', function(e) {
            if (e.target.closest('.general-item-row') || e.target.closest('.arm-row')) {
                saveQuotationFormData();
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.closest('.general-item-row') || e.target.closest('.arm-row')) {
                saveQuotationFormData();
            }
        });
    }

    // Set up data persistence on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add data persistence listeners
        addQuotationDataPersistenceListeners();
        
        // Set up form submission tracking
        document.getElementById('quotationForm').addEventListener('submit', function(e) {
            sessionStorage.setItem('quotation_form_submitting', 'true');
            localStorage.setItem('quotation_form_failed_submission', 'true');
        });
        
        // Clear flags when leaving the page (handles successful submissions)
        window.addEventListener('beforeunload', function(e) {
            // Only clear if we're not on the create page (successful navigation)
            if (!window.location.href.includes('/quotations/create')) {
                console.log('Leaving quotation create page - clearing flags');
                sessionStorage.removeItem('quotation_form_submitting');
                localStorage.removeItem('quotation_form_failed_submission');
            }
        });
        
        // Handle page show event for data persistence
        window.addEventListener('pageshow', function(event) {
            const wasSubmitting = sessionStorage.getItem('quotation_form_submitting');
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
            const isCreatePage = window.location.href.includes('/quotations/create');
            
            console.log('Quotation pageshow event:', {
                wasSubmitting: wasSubmitting,
                hasSuccessMessage: !!successMessage,
                hasErrorMessages: !!errorMessages,
                isCreatePage: isCreatePage
            });
            
            if (wasSubmitting && successMessage && isCreatePage) {
                console.log('Clearing quotation form data due to successful submission');
                clearQuotationFormData();
            } else if (!wasSubmitting && !errorMessages) {
                console.log('Fresh page load - clearing flags');
                sessionStorage.removeItem('quotation_form_submitting');
                localStorage.removeItem('quotation_form_failed_submission');
            } else if (errorMessages) {
                console.log('Error messages detected - preserving flags');
            } else if (wasSubmitting && !successMessage) {
                console.log('Failed submission but no error messages - letting restoration logic decide');
            }
        });
        
        // Load saved data after all initialization is complete
        setTimeout(() => {
            const wasSubmitting = sessionStorage.getItem('quotation_form_submitting');
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
            const hasSavedData = localStorage.getItem('quotation_form_data');
            const hasFailedSubmissionFlag = localStorage.getItem('quotation_form_failed_submission');
            
            console.log('Quotation restoration check:', {
                wasSubmitting: wasSubmitting,
                hasSuccessMessage: !!successMessage,
                hasErrorMessages: !!errorMessages,
                hasSavedData: !!hasSavedData,
                hasFailedSubmissionFlag: !!hasFailedSubmissionFlag,
                successMessageText: successMessage ? successMessage.textContent : 'none'
            });
            
            // Restore data if:
            // 1. There was a submission attempt but no success message AND there are error messages, OR
            // 2. There are error messages on the page AND saved data exists, OR
            // 3. There was a submission attempt but no success message AND saved data exists (error messages might have disappeared)
            if ((wasSubmitting && !successMessage && errorMessages) || 
                (!successMessage && errorMessages && hasSavedData) ||
                (wasSubmitting && !successMessage && hasSavedData)) {
                console.log('Restoring quotation data after failed submission');
                setTimeout(() => {
                    loadSavedQuotationData();
                    
                    // Clear flags after restoration if there are no error messages
                    if (!errorMessages) {
                        console.log('Clearing flags after restoration (no error messages)');
                        sessionStorage.removeItem('quotation_form_submitting');
                        localStorage.removeItem('quotation_form_failed_submission');
                    }
                }, 500);
            } else if (!wasSubmitting && !errorMessages && !hasFailedSubmissionFlag) {
                console.log('Fresh page load - not restoring quotation data');
            } else {
                console.log('Successful submission - not restoring quotation data');
            }
        }, 1500);
    
    // Additional fallback: Clear data after 3 seconds if no errors are detected
    setTimeout(() => {
        const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const wasSubmitting = sessionStorage.getItem('quotation_form_submitting');
        
        console.log('Quotation fallback check after 3 seconds:', {
            wasSubmitting: wasSubmitting,
            hasSuccessMessage: !!successMessage,
            hasErrorMessages: !!errorMessages,
            successMessageText: successMessage ? successMessage.textContent : 'none'
        });
        
        // If there's a success message OR no error messages and we were submitting, clear everything
        if (successMessage || (!errorMessages && wasSubmitting)) {
            console.log('Fallback: Clearing quotation data due to success or no errors after submission');
            clearQuotationFormData();
        }
    }, 3000);
    
    // Additional aggressive fallback: Clear data if we're on create page with no errors
    setTimeout(() => {
        const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        const wasSubmitting = sessionStorage.getItem('quotation_form_submitting');
        const hasFailedSubmissionFlag = localStorage.getItem('quotation_form_failed_submission');
        const isCreatePage = window.location.href.includes('/quotations/create');
        
        console.log('Quotation aggressive fallback check after 5 seconds:', {
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
            console.log('Aggressive fallback: Clearing quotation data - success or no errors on create page');
            clearQuotationFormData();
        } else {
            console.log('Aggressive fallback: Preserving quotation data - errors detected');
        }
    }, 5000);
    });

    // Debug functions (can be removed in production)
    window.debugQuotationForm = {
        save: saveQuotationFormData,
        load: loadSavedQuotationData,
        clear: clearQuotationFormData,
        show: () => {
            const data = localStorage.getItem('quotation_form_data');
            console.log('Current saved quotation data:', data ? JSON.parse(data) : 'No data');
            return data ? JSON.parse(data) : null;
        },
        triggerDropdown: triggerSearchableDropdownUpdate
    };
    </script>

    <script>
        // Function to display validation errors for general items and arms
        function displayValidationErrors() {
            console.log('displayValidationErrors called');
            
            // Get all validation errors from the page
            const errorMessages = @json($errors->all());
            const errorKeys = @json($errors->keys());
            
            console.log('Error messages:', errorMessages);
            console.log('Error keys:', errorKeys);
            
            // If there are no errors, return early
            if (errorMessages.length === 0) {
                console.log('No validation errors found');
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
                
                if (key.startsWith('arm_lines.') && key.includes('.arm_id')) {
                    const match = key.match(/arm_lines\.(\d+)\.arm_id/);
                    if (match) {
                        const rowIndex = parseInt(match[1]);
                        const errorMessage = errorMessages[index];
                        
                        // Find the corresponding row
                        const armRows = document.querySelectorAll('.arm-row');
                        if (armRows[rowIndex]) {
                            showArmError(armRows[rowIndex], errorMessage);
                        }
                    }
                }
            });
        }
        
        // Call displayValidationErrors when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Small delay to ensure all elements are loaded
            setTimeout(displayValidationErrors, 100);
        });
        
        // Also try to run after a short delay to ensure all elements are loaded
        setTimeout(function() {
            displayValidationErrors();
        }, 500);
        
        // Additional attempts to display errors after longer delays
        setTimeout(function() {
            displayValidationErrors();
        }, 1000);
        
        setTimeout(function() {
            displayValidationErrors();
        }, 2000);
    </script>
</x-app-layout>

