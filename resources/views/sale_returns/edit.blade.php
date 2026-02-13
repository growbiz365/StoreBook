<x-app-layout>
    @section('title', 'Edit Sale Return - Sales Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/sale-returns', 'label' => 'Sale Returns'],
        ['url' => '#', 'label' => 'Edit Return #' . $saleReturn->id]
    ]" />

    <x-dynamic-heading 
        :title="'Edit Return #' . $saleReturn->id" 
        :subtitle="'Update return details and items'"
        :icon="'fas fa-undo'"
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

    <form method="POST" action="{{ route('sale-returns.update', $saleReturn) }}" id="saleReturnForm">
        @csrf
        @method('PUT')

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        <!-- Main Content -->
        <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-6">
            <!-- Sale Return Header Information -->
            <div class="bg-gradient-to-r from-slate-50 to-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 6v3a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6"/>
                    </svg>
                    Sale Return Header Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div>
                        <label for="party_search_input" class="block text-sm font-medium text-gray-700 mb-2">
                            Party <span class="text-red-500">*</span>
                        </label>
                        <div class="searchable-select-container relative">
                            <input type="text" 
                                   id="party_search_input" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-orange-500 focus:ring-orange-500 searchable-input bg-white @error('party_id') border-red-500 @enderror" 
                                   placeholder="Search parties..."
                                   autocomplete="off"
                                   value="{{ old('party_search_input', $saleReturn->party->name ?? '') }}">
                            <input type="hidden" name="party_id" id="party_id" class="selected-item-id" value="{{ old('party_id', $saleReturn->party_id) }}">
                            
                            <!-- Dropdown -->
                            <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                                <div class="search-results-container max-h-40 overflow-y-auto">
                                    <!-- Results will be populated here -->
                                </div>
                                <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                                    <div class="flex justify-between items-center text-xs">
                                        <button type="button" class="prev-page text-orange-500 hover:text-orange-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-orange-50 transition-colors">Previous</button>
                                        <span class="page-info text-gray-500 text-xs"></span>
                                        <button type="button" class="next-page text-orange-500 hover:text-orange-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-orange-50 transition-colors">Next</button>
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
                        <p class="mt-1 text-xs text-gray-500" id="party_help_text">Required for credit returns, optional for cash returns</p>
                        <div id="party_balance" class="mt-1 text-sm hidden">
                            <span class="font-medium">Balance:</span>
                            <span id="party_balance_amount" class="ml-1"></span>
                        </div>
                        @error('party_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="return_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Return Type <span class="text-red-500">*</span>
                        </label>
                        <select name="return_type" id="return_type" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-orange-500 focus:ring-orange-500 @error('return_type') border-red-500 @enderror">
                            <option value="credit" {{ old('return_type', $saleReturn->return_type) == 'credit' ? 'selected' : '' }}>Credit (Party)</option>
                            <option value="cash" {{ old('return_type', $saleReturn->return_type) == 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                        @error('return_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="bank_field" style="display: {{ $saleReturn->return_type === 'cash' ? 'block' : 'none' }};">
                        <label for="bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Bank <span class="text-red-500">*</span>
                        </label>
                        <select name="bank_id" id="bank_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-orange-500 focus:ring-orange-500 @error('bank_id') border-red-500 @enderror">
                            <option value="">Select Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ old('bank_id', $saleReturn->bank_id) == $bank->id ? 'selected' : '' }}>
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
                        <label for="return_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Return Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="return_date" id="return_date" value="{{ old('return_date', $saleReturn->return_date->format('Y-m-d')) }}" 
                            required class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-orange-500 focus:ring-orange-500 @error('return_date') border-red-500 @enderror">
                        @error('return_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="shipping_charges" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Charges
                        </label>
                        <input type="number" name="shipping_charges" id="shipping_charges" value="{{ old('shipping_charges', $saleReturn->shipping_charges) }}" 
                            step="0.01" min="0" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-orange-500 focus:ring-orange-500 @error('shipping_charges') border-red-500 @enderror">
                        @error('shipping_charges')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Return
                        </label>
                        <input type="text" name="reason" id="reason" value="{{ old('reason', $saleReturn->reason) }}" 
                            placeholder="e.g., Defective item, Wrong size, Customer changed mind"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:border-orange-500 focus:ring-orange-500 @error('reason') border-red-500 @enderror">
                        @error('reason')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Customer Details Section (for cash returns only) -->
            <div id="customer_details_field" style="display: {{ $saleReturn->return_type === 'cash' ? 'block' : 'none' }};" class="bg-white border border-gray-200 rounded-lg p-3 mb-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Customer Details
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label for="name_of_customer" class="block text-xs font-medium text-gray-700 mb-1">
                            Customer Name
                        </label>
                        <input type="text" name="name_of_customer" id="name_of_customer" value="{{ old('name_of_customer', $saleReturn->name_of_customer) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('name_of_customer') border-red-500 @enderror">
                        @error('name_of_customer')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="father_name" class="block text-xs font-medium text-gray-700 mb-1">
                            Father Name
                        </label>
                        <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $saleReturn->father_name) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('father_name') border-red-500 @enderror">
                        @error('father_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact" class="block text-xs font-medium text-gray-700 mb-1">
                            Contact
                        </label>
                        <input type="text" name="contact" id="contact" value="{{ old('contact', $saleReturn->contact) }}" 
                            placeholder="03XX-XXXXXXX"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('contact') border-red-500 @enderror">
                        @error('contact')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cnic" class="block text-xs font-medium text-gray-700 mb-1">
                            CNIC
                        </label>
                        <input type="text" name="cnic" id="cnic" value="{{ old('cnic', $saleReturn->cnic) }}" 
                            placeholder="00000-0000000-0"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('cnic') border-red-500 @enderror">
                        @error('cnic')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: none;">
                        <label for="licence_no" class="block text-xs font-medium text-gray-700 mb-1">
                            Licence No
                        </label>
                        <input type="text" name="licence_no" id="licence_no" value="{{ old('licence_no', $saleReturn->licence_no) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('licence_no') border-red-500 @enderror">
                        @error('licence_no')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: none;">
                        <label for="licence_issue_date" class="block text-xs font-medium text-gray-700 mb-1">
                            Licence Issue Date
                        </label>
                        <input type="date" name="licence_issue_date" id="licence_issue_date" value="{{ old('licence_issue_date', $saleReturn->licence_issue_date?->format('Y-m-d')) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('licence_issue_date') border-red-500 @enderror">
                        @error('licence_issue_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: none;">
                        <label for="licence_valid_upto" class="block text-xs font-medium text-gray-700 mb-1">
                            Licence Valid Upto
                        </label>
                        <input type="date" name="licence_valid_upto" id="licence_valid_upto" value="{{ old('licence_valid_upto', $saleReturn->licence_valid_upto?->format('Y-m-d')) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('licence_valid_upto') border-red-500 @enderror">
                        @error('licence_valid_upto')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: none;">
                        <label for="licence_issued_by" class="block text-xs font-medium text-gray-700 mb-1">
                            Licence Issued By
                        </label>
                        <input type="text" name="licence_issued_by" id="licence_issued_by" value="{{ old('licence_issued_by', $saleReturn->licence_issued_by) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('licence_issued_by') border-red-500 @enderror">
                        @error('licence_issued_by')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: none;">
                        <label for="re_reg_no" class="block text-xs font-medium text-gray-700 mb-1">
                            Re Reg No
                        </label>
                        <input type="text" name="re_reg_no" id="re_reg_no" value="{{ old('re_reg_no', $saleReturn->re_reg_no) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('re_reg_no') border-red-500 @enderror">
                        @error('re_reg_no')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: none;">
                        <label for="dc" class="block text-xs font-medium text-gray-700 mb-1">
                            DC
                        </label>
                        <input type="text" name="dc" id="dc" value="{{ old('dc', $saleReturn->dc) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('dc') border-red-500 @enderror">
                        @error('dc')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: none;">
                        <label for="Date" class="block text-xs font-medium text-gray-700 mb-1">
                            Date
                        </label>
                        <input type="date" name="Date" id="Date" value="{{ old('Date', $saleReturn->Date?->format('Y-m-d')) }}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('Date') border-red-500 @enderror">
                        @error('Date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 lg:col-span-4">
                        <label for="address" class="block text-xs font-medium text-gray-700 mb-1">
                            Address
                        </label>
                        <textarea name="address" id="address" rows="2" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('address') border-red-500 @enderror">{{ old('address', $saleReturn->address) }}</textarea>
                        @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Party Details Section (for Credit returns when party is selected) -->
            <div id="party_details_field" style="display: {{ $saleReturn->return_type == 'credit' && $saleReturn->party_id ? 'block' : 'none' }};" class="bg-white border border-gray-200 rounded-lg p-3 mb-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Party Details
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label for="party_name" class="block text-xs font-medium text-gray-700 mb-1">Party Name</label>
                        <input type="text" id="party_name" readonly 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-gray-100 text-gray-600"
                               value="{{ $saleReturn->party ? $saleReturn->party->name : '' }}">
                    </div>
                    
                    <div>
                        <label for="party_cnic" class="block text-xs font-medium text-gray-700 mb-1">Party CNIC</label>
                        <input type="text" id="party_cnic" readonly 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-gray-100 text-gray-600"
                               value="{{ $saleReturn->party ? $saleReturn->party->cnic : '' }}">
                    </div>
                    
                    <div>
                        <label for="party_contact" class="block text-xs font-medium text-gray-700 mb-1">Party Contact</label>
                        <input type="text" id="party_contact" readonly 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-gray-100 text-gray-600"
                               value="{{ $saleReturn->party ? $saleReturn->party->phone_no : '' }}">
                    </div>
                    
                    <div>
                        <label for="party_address" class="block text-xs font-medium text-gray-700 mb-1">Party Address</label>
                        <textarea id="party_address" rows="2" readonly 
                                  class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-gray-100 text-gray-600">{{ $saleReturn->party ? $saleReturn->party->address : '' }}</textarea>
                    </div>
                    
                    <div style="display: none;">
                        <label for="party_license_no" class="block text-xs font-medium text-gray-700 mb-1">Licence No</label>
                        <input type="text" name="party_license_no" id="party_license_no" value="{{ old('party_license_no', $saleReturn->party_license_no) }}" 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('party_license_no') border-red-500 @enderror">
                        @error('party_license_no')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div style="display: none;">
                        <label for="party_license_issue_date" class="block text-xs font-medium text-gray-700 mb-1">Issue Date</label>
                        <input type="date" name="party_license_issue_date" id="party_license_issue_date" value="{{ old('party_license_issue_date', $saleReturn->party_license_issue_date?->format('Y-m-d')) }}" 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('party_license_issue_date') border-red-500 @enderror">
                        @error('party_license_issue_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div style="display: none;">
                        <label for="party_license_valid_upto" class="block text-xs font-medium text-gray-700 mb-1">Valid Upto</label>
                        <input type="date" name="party_license_valid_upto" id="party_license_valid_upto" value="{{ old('party_license_valid_upto', $saleReturn->party_license_valid_upto?->format('Y-m-d')) }}" 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('party_license_valid_upto') border-red-500 @enderror">
                        @error('party_license_valid_upto')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div style="display: none;">
                        <label for="party_license_issued_by" class="block text-xs font-medium text-gray-700 mb-1">Issued By</label>
                        <input type="text" name="party_license_issued_by" id="party_license_issued_by" value="{{ old('party_license_issued_by', $saleReturn->party_license_issued_by) }}" 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('party_license_issued_by') border-red-500 @enderror">
                        @error('party_license_issued_by')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div style="display: none;">
                        <label for="party_re_reg_no" class="block text-xs font-medium text-gray-700 mb-1">Re-Reg No</label>
                        <input type="text" name="party_re_reg_no" id="party_re_reg_no" value="{{ old('party_re_reg_no', $saleReturn->party_re_reg_no) }}" 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('party_re_reg_no') border-red-500 @enderror">
                        @error('party_re_reg_no')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div style="display: none;">
                        <label for="party_dc" class="block text-xs font-medium text-gray-700 mb-1">DC</label>
                        <input type="text" name="party_dc" id="party_dc" value="{{ old('party_dc', $saleReturn->party_dc) }}" 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('party_dc') border-red-500 @enderror">
                        @error('party_dc')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div style="display: none;">
                        <label for="party_dc_date" class="block text-xs font-medium text-gray-700 mb-1">DC Date</label>
                        <input type="date" name="party_dc_date" id="party_dc_date" value="{{ old('party_dc_date', $saleReturn->party_dc_date?->format('Y-m-d')) }}" 
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:border-gray-400 focus:outline-none @error('party_dc_date') border-red-500 @enderror">
                        @error('party_dc_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="general_items_container">
                            <!-- General items will be added here dynamically -->
                        </tbody>
                    </table>
                    
                    <!-- Empty state message for general items -->
                    <div id="general_items_empty_message" class="text-center py-8 text-gray-500 bg-gray-50 border-t border-gray-200">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No general items added</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding a general item to this return.</p>
                        <div class="mt-6">
                            <button type="button" id="add_first_general_item" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add General Item
                    </button>
                </div>
                    </div>
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="arms_container">
                            <!-- Arms will be added here dynamically -->
                        </tbody>
                    </table>
                    
                    <!-- Empty state message for arms -->
                    <div id="arms_empty_message" class="text-center py-8 text-gray-500 bg-gray-50 border-t border-gray-200">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No arms added</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding an arm to this return.</p>
                        <div class="mt-6">
                            <button type="button" id="add_first_arm" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Arm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Summary -->
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('sale-returns.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" name="action" value="save" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save Draft
                </button>
                <button type="submit" name="action" value="post" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Post Sale Return
                </button>
            </div>
        </div>
    </form>
</div>

    <script>
    // Clear data immediately if this is a fresh page load (no flags set)
    (function() {
        const wasSubmitting = sessionStorage.getItem('sale_return_form_submitting');
        const hasFailedSubmissionFlag = localStorage.getItem('sale_return_form_failed_submission');
        
        console.log('Immediate check on page load:', {
            wasSubmitting: wasSubmitting,
            hasFailedSubmissionFlag: hasFailedSubmissionFlag,
            url: window.location.href,
            referrer: document.referrer
        });
        
        // Check if this is a fresh navigation to edit page (not coming from a form submission)
        const isEditPage = window.location.href.includes('/sale-returns/') && window.location.href.includes('/edit');
        const hasVisibleErrors = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const hasVisibleSuccess = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        
        // Check if we're coming from a different page (not from form submission)
        const isFromDifferentPage = document.referrer && 
            !document.referrer.includes('/sale-returns/') && 
            !document.referrer.includes('sale-returns/');
        
        console.log('Error detection:', {
            isEditPage: isEditPage,
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
            localStorage.removeItem('sale_return_form_data');
            localStorage.removeItem('sale_return_form_failed_submission');
            sessionStorage.removeItem('sale_return_form_submitting');
        } else {
            console.log('Preserving data - submission flags detected, letting main logic handle restoration');
        }
    })();

    // Define global variables and functions
        let generalItemIndex = {{ $saleReturn->generalLines->count() }};
        let armIndex = {{ $saleReturn->armLines->count() }};

    function addGeneralItem() {
        const container = document.getElementById('general_items_container');
        const template = document.getElementById('general_item_template');
        if (!template) {
            console.error('General item template not found!');
            return;
        }
        const clone = template.content.cloneNode(true);
        
        // Replace INDEX placeholder
        clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
            element.name = element.name.replace('INDEX', generalItemIndex);
        });
        
        container.appendChild(clone);
        
        // Hide empty state message
        const emptyMessage = document.getElementById('general_items_empty_message');
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }
        
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
                window.calculateLineTotal(this);
                window.calculateTotals();
            });
        });
        
        // Add quantity validation for stock checking
        if (qtyInput) {
            qtyInput.addEventListener('input', function() {
                validateQuantityForRow(this);
            });
        }
        
        generalItemIndex++;
    }

    function addArm() {
        const container = document.getElementById('arms_container');
        const template = document.getElementById('arm_template');
        if (!template) {
            console.error('Arm template not found!');
            return;
        }
        const clone = template.content.cloneNode(true);
        
        // Replace INDEX placeholder
        clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
            element.name = element.name.replace('INDEX', armIndex);
        });
        
        container.appendChild(clone);
        
        // Hide empty state message
        const emptyMessage = document.getElementById('arms_empty_message');
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }
        
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
            window.calculateTotals();
        });
        
        armIndex++;
    }

        // Calculation functions - moved to global scope so they can be accessed by dropdown classes
        window.calculateLineTotal = function(element) {
            const row = element.closest('.general-item-row');
            const qty = parseFloat(row.querySelector('.general-qty').value) || 0;
        const salePrice = parseFloat(row.querySelector('.general-sale-price').value) || 0;
            
        const lineTotal = qty * salePrice;
            const lineTotalElement = row.querySelector('.general-line-total');
            lineTotalElement.textContent = lineTotal.toFixed(2);
        };

        window.calculateTotals = function() {
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
        };

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize global selectedArmIds array with existing selections
        const existingArmRows = document.querySelectorAll('.arm-row');
        existingArmRows.forEach(row => {
            const hiddenInput = row.querySelector('.selected-arm-id');
            if (hiddenInput && hiddenInput.value) {
                if (!window.selectedArmIds || !Array.isArray(window.selectedArmIds)) {
                    window.selectedArmIds = [];
                }
                if (!window.selectedArmIds.includes(hiddenInput.value)) {
                    window.selectedArmIds.push(hiddenInput.value);
                }
            }
        });

        // Initialize global selectedGeneralItemIds array with existing selections
        const existingGeneralItemRows = document.querySelectorAll('.general-item-row');
        existingGeneralItemRows.forEach(row => {
            const hiddenInput = row.querySelector('.selected-item-id');
            if (hiddenInput && hiddenInput.value) {
                if (!window.selectedGeneralItemIds || !Array.isArray(window.selectedGeneralItemIds)) {
                    window.selectedGeneralItemIds = [];
                }
                if (!window.selectedGeneralItemIds.includes(hiddenInput.value)) {
                    window.selectedGeneralItemIds.push(hiddenInput.value);
                }
            }
            
            // Add quantity validation to existing rows
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    validateQuantityForRow(this);
                });
            }
        });

    // Return type change handler
    document.getElementById('return_type').addEventListener('change', function() {
        const bankField = document.getElementById('bank_field');
        const bankSelect = document.getElementById('bank_id');
        const customerField = document.getElementById('party_id');
        const customerLabel = document.querySelector('label[for="party_search_input"]');
        const customerHelpText = document.getElementById('party_help_text');
        const customerDetailsField = document.getElementById('customer_details_field');
        const partyDetailsField = document.getElementById('party_details_field');

        // Clear any existing party error when return type changes
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
            customerHelpText.textContent = 'Optional for cash returns - you can leave this blank';
            
            // Clear party selection when switching to cash
            customerField.value = '';
            const partySearchInput = document.getElementById('party_search_input');
            if (partySearchInput) {
                partySearchInput.value = '';
            }
            hidePartyBalance();
        } else {
            bankField.style.display = 'none';
            bankSelect.required = false;
            bankSelect.value = '';
            customerDetailsField.style.display = 'none';
            
            customerField.required = true;
            customerLabel.innerHTML = 'Party <span class="text-red-500">*</span>';
            customerHelpText.textContent = 'Required for credit returns';
            
            // Show party details if party is selected
            if (customerField.value) {
                loadPartyDetails(customerField.value);
                partyDetailsField.style.display = 'block';
            } else {
                partyDetailsField.style.display = 'none';
            }
        }
    });

    // Input masks for CNIC and Contact
    function initializeInputMasks() {
        const cnicInput = document.getElementById('cnic');
        const contactInput = document.getElementById('contact');

        // CNIC mask: 00000-0000000-0
        if (cnicInput) {
            cnicInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 13) value = value.substr(0, 13);
                
                let formatted = '';
                if (value.length > 0) {
                    formatted = value.substr(0, 5);
                }
                if (value.length > 5) {
                    formatted += '-' + value.substr(5, 7);
                }
                if (value.length > 12) {
                    formatted += '-' + value.substr(12, 1);
                }
                
                e.target.value = formatted;
            });
        }

        // Contact mask: 03XX-XXXXXXX
        if (contactInput) {
            contactInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.substr(0, 11);
                
                let formatted = '';
                if (value.length > 0) {
                    formatted = value.substr(0, 4);
                }
                if (value.length > 4) {
                    formatted += '-' + value.substr(4, 7);
                }
                
                e.target.value = formatted;
            });
        }
    }

    // Initialize input masks
    initializeInputMasks();

    // Initialize form state on page load
    function initializeFormState() {
        const returnType = document.getElementById('return_type').value;
        const customerDetailsField = document.getElementById('customer_details_field');
        
        if (returnType === 'cash') {
            customerDetailsField.style.display = 'block';
        } else {
            customerDetailsField.style.display = 'none';
        }
    }

    // Call on page load
    initializeFormState();

    // Party selection change handler
    document.getElementById('party_id').addEventListener('change', function() {
        // Clear any existing party error when party is selected
        const partyError = document.getElementById('party_error');
        if (partyError) {
            partyError.remove();
        }
        
        if (this.value) {
            fetchCustomerBalance(this.value);
        } else {
            hideCustomerBalance();
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
        
        const currentDate = document.getElementById('return_date').value;
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
        
        const currentDate = document.getElementById('return_date').value;
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

    // Add event listener for return date changes to refresh balances
    const returnDateInput = document.getElementById('return_date');
    returnDateInput.addEventListener('change', function() {
        const customerSelect = document.getElementById('party_id');
        const bankSelect = document.getElementById('bank_id');
        
        if (customerSelect.value) {
            fetchCustomerBalance(customerSelect.value);
        }
        if (bankSelect.value) {
            fetchBankBalance(bankSelect.value);
        }
    });

    // Validate stock before form submission
    function validateStock() {
        // For sale returns, we don't need to validate against stock
        // since returns can be for any items regardless of current stock
        return true;
    }

        // Validate quantity for a specific row
        function validateQuantityForRow(quantityInput) {
            // For sale returns, we don't need to validate against stock
            // since returns can be for any items regardless of current stock
            // Just reset any error styling
            quantityInput.style.borderColor = '';
            quantityInput.style.backgroundColor = '';
            const errorDiv = quantityInput.closest('.general-item-row').querySelector('.quantity-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        }

        // Add general item
        document.getElementById('add_general_item').addEventListener('click', function() {
        addGeneralItem();
    });

    // Add first general item from empty state
    document.getElementById('add_first_general_item').addEventListener('click', function() {
        addGeneralItem();
        });

        // Add arm
        document.getElementById('add_arm').addEventListener('click', function() {
        addArm();
    });

    // Add first arm from empty state
    document.getElementById('add_first_arm').addEventListener('click', function() {
        addArm();
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
            
            // Show empty state if no items left
            const container = document.getElementById('general_items_container');
            if (container.children.length === 0) {
                document.getElementById('general_items_empty_message').style.display = 'block';
                }
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
            
            // Show empty state if no items left
            const container = document.getElementById('arms_container');
            if (container.children.length === 0) {
                document.getElementById('arms_empty_message').style.display = 'block';
            }
        }
    });

    // Form validation
    document.getElementById('saleReturnForm').addEventListener('submit', function(e) {
        // Check if at least one item is added
        const generalItems = document.querySelectorAll('.general-item-row');
        const arms = document.querySelectorAll('.arm-row');
        
        if (generalItems.length === 0 && arms.length === 0) {
            e.preventDefault();
            alert('Please add at least one general item or arm to the return.');
            return false;
        }
        
        // Validate party selection for credit returns
        const returnType = document.getElementById('return_type').value;
        const partyId = document.getElementById('party_id').value;
        const partyError = document.getElementById('party_error');
        
        // Remove existing error message
        if (partyError) {
            partyError.remove();
        }
        
        // Validate party selection for credit returns
        if (returnType === 'credit' && (!partyId || partyId === '')) {
            e.preventDefault();
            
            // Create error message
            const errorDiv = document.createElement('p');
            errorDiv.id = 'party_error';
            errorDiv.className = 'mt-1 text-xs text-red-600';
            errorDiv.textContent = 'Party selection is required for credit returns.';
            
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


    // Add event listener for shipping charges
    document.getElementById('shipping_charges').addEventListener('input', window.calculateTotals);

    // Initialize existing data after a short delay to ensure DOM is ready
    setTimeout(() => {
        initializeExistingData();
    }, 100);

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

    // Handle pre-selected party from existing sale return data
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
});

// Initialize existing data for edit mode
function initializeExistingData() {
    // Get existing data from PHP
    const existingGeneralItems = @json($saleReturn->generalLines);
    const existingArms = @json($armLinesData);
    
    
    
    // Load existing general items
    if (existingGeneralItems && existingGeneralItems.length > 0) {
        existingGeneralItems.forEach((line, index) => {
            addGeneralItem();
            
            // Populate the data after a short delay to ensure the row is created
            setTimeout(() => {
                const generalRows = document.querySelectorAll('.general-item-row');
                const generalRow = generalRows[index];
                if (generalRow) {
                    const itemName = line.general_item?.item_name || line.generalItem?.item_name || '';
                    generalRow.querySelector('.searchable-input').value = itemName;
                    generalRow.querySelector('.selected-item-id').value = line.general_item_id;
                    generalRow.querySelector('.general-qty').value = Math.round(line.quantity);
                    generalRow.querySelector('.general-sale-price').value = line.return_price;
                    calculateLineTotal(generalRow.querySelector('.general-qty'));
                }
            }, 200 * (index + 1));
        });
    }

    // Load existing arms
    if (existingArms && existingArms.length > 0) {
        existingArms.forEach((line, index) => {
            addArm();
            
            // Populate the data after a short delay to ensure the row is created
            setTimeout(() => {
                const armRows = document.querySelectorAll('.arm-row');
                const armRow = armRows[index];
                if (armRow) {
                    // Get the data from the explicitly loaded relationships
                    const makeName = line.arm?.armMake?.arm_make || '';
                    const caliberName = line.arm?.armCaliber?.arm_caliber || '';
                    const typeName = line.arm?.armType?.arm_type || '';
                    const serialNo = line.arm?.serial_no || '';
                    const armDisplayName = `${makeName} ${caliberName} ${typeName} (SN: ${serialNo})`.trim();
                    
                    armRow.querySelector('.searchable-input').value = armDisplayName;
                    armRow.querySelector('.selected-arm-id').value = line.arm_id;
                    armRow.querySelector('.arm-sale-price').value = line.return_price;
                }
            }, 200 * (existingGeneralItems.length + index + 1));
        });
    }

    // Calculate initial totals after a delay to ensure all data is loaded
    setTimeout(() => {
                calculateTotals();
    }, 500);
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
        
        results.forEach(item => item.classList.remove('selected', 'bg-orange-100'));
        
        if (results[newIndex]) {
            results[newIndex].classList.add('selected', 'bg-orange-100');
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
        
        // Show current stock info for sale returns
        const currentStock = item.available_stock || 0;
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
        
        // Add item info (current stock) - positioned absolutely to not affect layout
        const infoDiv = document.createElement('div');
        infoDiv.className = 'item-info absolute text-xs text-gray-500 top-full left-0 mt-1 z-10 bg-white px-1 rounded';
        infoDiv.innerHTML = `
            <span>Current stock: <span class="font-medium">${currentStock}</span></span>
        `;
        this.container.style.position = 'relative';
        this.container.appendChild(infoDiv);
        
        // Add warning if current stock is low or zero
        if (currentStock <= 0) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'stock-warning text-red-600 text-sm font-medium mt-1';
            warningDiv.textContent = '⚠️ No current stock available!';
            this.container.appendChild(warningDiv);
        } else if (currentStock <= 5) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'stock-warning text-orange-600 text-sm font-medium mt-1';
            warningDiv.textContent = `⚠️ Low current stock: ${currentStock} remaining`;
            this.container.appendChild(warningDiv);
        }
        
        // Populate sale price
        const salePriceInput = row.querySelector('.general-sale-price');
        
        if (salePriceInput) {
            salePriceInput.value = item.sale_price ? item.sale_price : '';
        }
        
        // Trigger calculation
        if (salePriceInput) {
            window.calculateLineTotal(salePriceInput);
            window.calculateTotals();
        }
        
        this.hideDropdown();
    }
    
    selectFirstResult() {
        const firstResult = this.resultsContainer.querySelector('.result-item');
        if (firstResult) {
            const item = {
                id: firstResult.dataset.itemId,
                item_name: firstResult.dataset.itemName,
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
        
        // Remove any stock warnings
        const existingWarning = row.querySelector('.stock-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        
        // Trigger calculation
        window.calculateTotals();
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
             const url = new URL('/api/arms/search', window.location.origin);
             url.searchParams.set('q', this.searchTerm);
             url.searchParams.set('page', this.currentPage);
             url.searchParams.set('limit', this.itemsPerPage);
             url.searchParams.set('status', 'sold');
             
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
             const url = new URL('/api/arms', window.location.origin);
             url.searchParams.set('page', this.currentPage);
             url.searchParams.set('limit', this.itemsPerPage);
             url.searchParams.set('status', 'sold');
             
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
            if (!item || !item.id || !item.serial_no) {
                return;
            }
            
            const resultItem = document.createElement('div');
            resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
            resultItem.dataset.armId = item.id;
            resultItem.dataset.serialNo = item.serial_no;
            resultItem.dataset.salePrice = this.safeNumber(item.sale_price);
            resultItem.dataset.armMake = item.armsMake?.name || '';
            resultItem.dataset.armType = item.armsType?.name || '';
            
            resultItem.innerHTML = `
                <div class="font-medium text-gray-900">${item.armsMake?.name || ''} ${item.armsType?.name || ''} (SN: ${item.serial_no})</div>
                <div class="text-sm text-gray-500">${item.armsCategory?.name || ''} ${item.armsCondition?.name || ''}</div>
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
        const displayText = `${item.armsMake?.name || ''} ${item.armsType?.name || ''} (SN: ${item.serial_no})`.trim();
        this.input.value = displayText;
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
            salePriceInput.value = item.sale_price ? item.sale_price : '';
        }
        
        // Trigger calculation
        if (salePriceInput) {
            window.calculateTotals();
        }
        
        this.hideDropdown();
    }
    
    selectFirstResult() {
        const firstResult = this.resultsContainer.querySelector('.result-item');
        if (firstResult) {
            const item = {
                id: firstResult.dataset.armId,
                serial_no: firstResult.dataset.serialNo,
                sale_price: this.safeNumber(firstResult.dataset.salePrice),
                armsMake: { name: firstResult.dataset.armMake },
                armsType: { name: firstResult.dataset.armType }
            };
            this.selectItem(item);
        }
    }
    
    selectHighlightedResult() {
        const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
        if (highlightedResult) {
            const item = {
                id: highlightedResult.dataset.armId,
                serial_no: highlightedResult.dataset.serialNo,
                sale_price: this.safeNumber(highlightedResult.dataset.salePrice),
                armsMake: { name: highlightedResult.dataset.armMake },
                armsType: { name: highlightedResult.dataset.armType }
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
        }
        
        // Trigger calculation
        window.calculateTotals();
    }
}
    </script>

<!-- Templates for dynamic content -->
<template id="general_item_template">
    <tr class="general-item-row">
        <td class="px-4 py-4 whitespace-nowrap">
            <div class="relative">
                <div class="searchable-select-container relative">
                    <input type="text" 
                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
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
                                <button type="button" class="prev-page text-orange-500 hover:text-orange-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-orange-50 transition-colors">Previous</button>
                                <span class="page-info text-gray-500 text-xs"></span>
                                <button type="button" class="next-page text-orange-500 hover:text-orange-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-orange-50 transition-colors">Next</button>
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
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-qty focus:border-orange-500 focus:ring-orange-500"
                   placeholder="0" value="1">
        </td>
        <td class="px-4 py-4 whitespace-nowrap">
            <input type="number" name="general_lines[INDEX][sale_price]" required step="0.01" min="0" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm general-sale-price focus:border-orange-500 focus:ring-orange-500"
                   placeholder="0">
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