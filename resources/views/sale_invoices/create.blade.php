<x-invoice-fullscreen-layout :page-title="'Create Sale Invoice — ' . config('app.name', 'StoreBook')">
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
        }
        
        @media (max-width: 1024px) {
            .grid-cols-5 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .grid-cols-5 {
                grid-template-columns: 1fr;
            }
            .w-full {
                padding: 0 0.5rem;
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

        /* Legacy CI add-update-invoices.php style (white box, 9+3 grid, bordered table) */
        #invoice-fullscreen-root .invoice-fs-root {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
        .ci-invoice-page {
            background: linear-gradient(165deg, #eef1f8 0%, #f7f8fc 45%, #eef2f7 100%);
        }
        .ci-white-box {
            background: #fff;
            border: 1px solid #e2e6ef;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .ci-hr {
            border: 0;
            border-top: 1px solid #dee2e6;
            margin: 0.5rem 0;
        }
        .ci-form-control,
        .ci-invoice-page .pos-quick-add input,
        .ci-invoice-page .pos-quick-add select {
            width: 100%;
            min-height: 32px;
            padding: 0.28rem 0.55rem;
            font-size: 0.8125rem;
            line-height: 1.45;
            color: #495057;
            background-color: #fff;
            border: 1px solid #c5cad3;
            border-radius: 0.25rem;
            box-sizing: border-box;
        }
        .ci-invoice-page .pos-quick-add input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.15);
        }
        .ci-sidebar {
            border-left: 1px solid #dee2e6;
            padding-left: 1rem;
        }
        @media (max-width: 1023px) {
            .ci-sidebar {
                border-left: none;
                padding-left: 0;
                border-top: 1px solid #dee2e6;
                padding-top: 1rem;
                margin-top: 0.5rem;
            }
        }
        .ci-sidebar .form-group {
            margin-bottom: 0.5rem;
        }
        .ci-sidebar label {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            color: #333;
            margin-bottom: 0.35rem;
        }
        .pos-sale-type-btn {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.35rem 0.4rem;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            font-size: 0.8125rem;
            transition: background 0.15s, border-color 0.15s;
            background: #fff;
            color: #495057;
        }
        .pos-sale-type-btn.is-active-cash {
            border-color: #28a745;
            background: #e8f5e9;
            color: #155724;
        }
        .pos-sale-type-btn.is-active-credit {
            border-color: #007bff;
            background: #e7f1ff;
            color: #004085;
        }
        .ci-item-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dee2e6;
        }
        .ci-item-table thead tr {
            background: #fafafa !important;
        }
        .ci-item-table th,
        .ci-item-table td {
            border: 1px solid #dee2e6;
            padding: 0.3rem 0.45rem;
            vertical-align: middle;
        }
        .ci-item-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .ci-item-table th {
            font-weight: 600;
            font-size: 0.8125rem;
            color: #333;
            text-transform: none;
            letter-spacing: normal;
        }
        #general_items_table .general-details-cell {
            font-size: 0.8125rem;
            color: #4b5563;
            max-width: 14rem;
            white-space: normal;
        }
        .ci-total-readonly {
            border: 0 !important;
            background: transparent !important;
            font-weight: 600;
            font-size: 1rem;
            color: #212529;
            padding-left: 0 !important;
        }
        .ci-btn-primary {
            background: #007bff;
            border: 1px solid #007bff;
            color: #fff;
            padding: 0.45rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .ci-btn-primary:hover { background: #0069d9; border-color: #0062cc; color: #fff; }
        .ci-btn-success {
            background: #28a745;
            border: 1px solid #28a745;
            color: #fff;
            padding: 0.45rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .ci-btn-success:hover { background: #218838; border-color: #1e7e34; color: #fff; }
        .ci-btn-danger {
            background: #dc3545;
            border: 1px solid #dc3545;
            color: #fff;
            padding: 0.45rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .ci-btn-danger:hover { background: #c82333; border-color: #bd2130; color: #fff; }
        .ci-btn-outline {
            background: #fff;
            border: 1px solid #ced4da;
            color: #495057;
            padding: 0.45rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        .ci-btn-outline:hover { background: #f8f9fa; }
        .ci-btn-print {
            background: #5b21b6;
            border: 1px solid #5b21b6;
            color: #fff;
            padding: 0.38rem 0.85rem;
            border-radius: 0.25rem;
            font-size: 0.8125rem;
            font-weight: 600;
        }
        .ci-btn-print:hover { background: #4c1d95; border-color: #4c1d95; color: #fff; }
        .ci-invoice-page button.ci-btn-primary,
        .ci-invoice-page button.ci-btn-success,
        .ci-invoice-page button.ci-btn-danger,
        .ci-invoice-page button.ci-btn-print {
            padding: 0.38rem 0.85rem;
            border-radius: 0.25rem;
            font-size: 0.8125rem;
            font-weight: 600;
        }
        .ci-item-table input.ci-form-control,
        .ci-item-table .ci-form-control {
            min-height: 30px;
            font-size: 0.8125rem;
        }
        #shortcut_keys {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            overflow: hidden;
        }
        #shortcut_keys th,
        #shortcut_keys td {
            text-align: center;
            font-size: 0.6875rem;
            padding: 0.28rem 0.25rem;
            border: 1px solid #dee2e6;
        }
        #shortcut_keys .sk-f2 { background: #a3ff3f; }
        #shortcut_keys .sk-f4 { background: #93b9ff; color: #1e3a5f; }
        #shortcut_keys .sk-f8 { background: #00c92b; color: #fff; }
        #shortcut_keys .sk-f9 { background: #ffe3a5; }
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

    @php
        $posCatalog = $generalItems->map(function ($i) {
            return [
                'id' => $i->id,
                'item_name' => $i->item_name,
                'item_code' => $i->item_code,
                'sale_price' => (float) $i->sale_price,
                'available_stock' => (float) ($i->available_stock ?? 0),
                'item_type_id' => $i->item_type_id,
                'item_type' => $i->itemType ? ['item_type' => $i->itemType->item_type] : null,
            ];
        })->values();
    @endphp
    <script>
        window.__POS_ITEM_CATALOG = @json($posCatalog);
        window.__POS_ITEM_BY_CODE = {};
        (window.__POS_ITEM_CATALOG || []).forEach(function (it) {
            if (it.item_code && String(it.item_code).trim() !== '') {
                window.__POS_ITEM_BY_CODE[String(it.item_code).trim().toLowerCase()] = it;
            }
        });
    </script>

    <form method="POST" action="{{ route('sale-invoices.store') }}" id="saleInvoiceForm">
        @csrf

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        <!-- Main content: CI-style white box, title + entry row, 9 cols lines / 3 cols sidebar -->
        <div class="invoice-fs-root min-h-full pos-sale-layout ci-invoice-page px-2 py-2 sm:px-3 sm:py-2">
            <div class="ci-white-box max-w-[1600px] mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end mb-1">
                        <div class="lg:col-span-3">
                            <h1 class="text-base font-bold text-gray-900 mb-0.5 tracking-tight">Sale invoice</h1>
                            <a href="{{ route('sale-invoices.index') }}" class="text-sm text-blue-600 hover:underline">← Back to list</a>
                                        </div>
                        <div class="lg:col-span-9 pos-quick-add">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-end">
                                <div class="sm:col-span-3">
                                    <label for="pos_barcode" class="block text-sm font-semibold text-gray-800 mb-1">Barcode</label>
                                    <input type="text" id="pos_barcode" autocomplete="off" placeholder="Barcode"
                                        class="ci-form-control">
                                            </div>
                                <div class="sm:col-span-3">
                                    <label for="pos_item_type_id" class="block text-sm font-semibold text-gray-800 mb-1">Item type</label>
                                    <select id="pos_item_type_id" class="ci-form-control" autocomplete="off" title="Filter item search by type">
                                        <option value="">All types</option>
                                        @foreach($itemTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->item_type }}</option>
                                    @endforeach
                                </select>
                                </div>
                                <div class="sm:col-span-6 relative" id="pos_item_search_wrap">
                                    <label for="pos_item_search" class="block text-sm font-semibold text-gray-800 mb-1">Item</label>
                                    <input type="text" id="pos_item_search" autocomplete="off" placeholder="Search by name or code (2+ chars), then Enter"
                                        class="ci-form-control">
                                    <div id="pos_item_search_dropdown" class="searchable-dropdown hidden absolute z-[60] left-0 right-0 mt-1 bg-white border border-gray-200 rounded shadow-lg max-h-56 overflow-hidden">
                                        <div id="pos_item_search_results" class="max-h-44 overflow-y-auto"></div>
                                        <div id="pos_item_search_pager" class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-50">
                                            <div class="flex justify-between items-center text-xs">
                                                <button type="button" id="pos_item_prev" class="text-blue-600 hover:text-blue-800 px-2 py-1">Previous</button>
                                                <span id="pos_item_page_info" class="text-gray-500"></span>
                                                <button type="button" id="pos_item_next" class="text-blue-600 hover:text-blue-800 px-2 py-1">Next</button>
                            </div>
                            </div>
                            </div>
                                    <div id="pos_item_search_loading" class="hidden absolute right-3 top-9">
                                        <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                                </div>
                            @error('general_lines')
                            <div id="sale_invoice_general_lines_error" class="mt-2 rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800" role="alert">
                                {{ $message }}
                                </div>
                                    @enderror
                                </div>
                                </div>
                                
                    <hr class="ci-hr">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
                        <div class="lg:col-span-9 ci-main-col order-2 lg:order-1">
                            <div class="overflow-x-auto">
                                <table class="ci-item-table" id="general_items_table">
                                    <thead>
                                        <tr>
                                            <th style="width:3%"><input type="checkbox" id="check_all_lines" class="rounded border-gray-400 align-middle" title="Select all"></th>
                                            <th style="width:22%">Item</th>
                                            <th style="width:20%">Details</th>
                                            <th style="width:12%">Rate</th>
                                            <th style="width:12%">Quantity</th>
                                            <th style="width:15%">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="general_items_container"></tbody>
                                </table>
                                </div>
                                
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 mt-3 items-center">
                                <div class="sm:col-span-3">
                                    <button type="button" id="delete_selected_lines" class="ci-btn-danger w-full sm:w-auto">− Delete</button>
                                </div>
                                </div>
                                
                            <div class="mt-2 space-y-2">
                                <div class="grid grid-cols-12 gap-2 items-center">
                                    <div class="col-span-12 sm:col-span-4 text-sm font-semibold text-gray-800">Sub total</div>
                                    <div class="hidden sm:block sm:col-span-4"></div>
                                    <div class="col-span-12 sm:col-span-4">
                                        <span id="subtotal" class="ci-total-readonly">0.00</span>
                                </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2 items-end">
                                    <div class="col-span-12 sm:col-span-6 md:col-span-4">
                                        <label for="shipping_charges" class="block text-sm font-semibold text-gray-800 mb-1">Shipping charges</label>
                                        <input type="number" name="shipping_charges" id="shipping_charges" value="{{ old('shipping_charges', 0) }}"
                                            step="1" min="0"
                                            class="ci-form-control @error('shipping_charges') border-red-500 @enderror">
                                        @error('shipping_charges')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                </div>
                                <div class="grid grid-cols-12 gap-2 items-center pt-1 border-t border-gray-200">
                                    <div class="col-span-12 sm:col-span-4 text-sm font-bold text-gray-900">Total</div>
                                    <div class="hidden sm:block sm:col-span-4"></div>
                                    <div class="col-span-12 sm:col-span-4">
                                        <span id="total_amount" class="ci-total-readonly text-lg">0.00</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Shipping: <span id="shipping_display" class="font-medium">+ 0</span></p>
                            </div>
                        </div>
                    </div>

                            <div class="mt-2 pt-2 border-t border-gray-200 flex flex-wrap gap-1.5">
                                <button type="submit" name="action" value="save" id="btn_save_draft" class="ci-btn-primary">Save draft</button>
                                <button type="submit" name="action" value="post" id="btn_post_invoice" class="ci-btn-success">Post invoice</button>
                                <button type="submit" name="action" value="post_print" id="btn_post_print" class="ci-btn-print">Post &amp; print</button>
                                <a href="{{ route('sale-invoices.index') }}" class="ci-btn-outline inline-block text-center no-underline leading-normal">Cancel</a>
                                </div>
                                </div>
                                
                        <aside class="lg:col-span-3 ci-sidebar order-1 lg:order-2">
                            <div class="form-group">
                                <label>Sale type</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" id="pos_sale_credit" class="pos-sale-type-btn">Credit</button>
                                    <button type="button" id="pos_sale_cash" class="pos-sale-type-btn">Cash</button>
                                </div>
                                <select name="sale_type" id="sale_type" required class="sr-only @error('sale_type') border-red-500 @enderror">
                                    <option value="cash" {{ old('sale_type', 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit" {{ old('sale_type', 'cash') === 'credit' ? 'selected' : '' }}>Credit (Party)</option>
                                </select>
                                @error('sale_type')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            <div class="form-group">
                                <label for="invoice_date">Date <span class="text-red-600">*</span></label>
                                <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}"
                                    required class="ci-form-control @error('invoice_date') border-red-500 @enderror">
                                @error('invoice_date')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            <div id="bank_field" class="form-group">
                                <label for="bank_id">Bank <span class="text-red-600">*</span></label>
                                <select name="bank_id" id="bank_id" class="ci-form-control @error('bank_id') border-red-500 @enderror">
                                    <option value="">Select bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ (string) old('bank_id', $banks->first()?->id) === (string) $bank->id ? 'selected' : '' }}>
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
                            <div id="credit_party_block" class="form-group credit_block">
                                <label for="party_search_input">
                                    <span id="party_field_label">Party</span>
                                </label>
                                <div class="searchable-select-container relative">
                                    <input type="text"
                                           id="party_search_input"
                                           class="ci-form-control searchable-input bg-white @error('party_id') border-red-500 @enderror"
                                           placeholder="Search parties..."
                                           autocomplete="off"
                                           value="{{ old('party_search_input') }}">
                                    <input type="hidden" name="party_id" id="party_id" class="selected-item-id" value="{{ old('party_id') }}">
                                    <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                                        <div class="search-results-container max-h-40 overflow-y-auto"></div>
                                        <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                                            <div class="flex justify-between items-center text-xs">
                                                <button type="button" class="prev-page text-green-600 hover:text-green-800 disabled:opacity-40 px-2 py-1 rounded">Previous</button>
                                                <span class="page-info text-gray-500 text-xs"></span>
                                                <button type="button" class="next-page text-green-600 hover:text-green-800 disabled:opacity-40 px-2 py-1 rounded">Next</button>
                                </div>
                                </div>
                                </div>
                                    <div class="loading-indicator hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                                        <svg class="animate-spin h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                            </div>
                        </div>
                                <p class="mt-1 text-xs text-gray-600" id="party_help_text">Required for credit sales</p>
                                <div id="party_balance" class="mt-1 text-sm hidden">
                                    <span class="font-medium">Balance:</span>
                                    <span id="party_balance_amount" class="ml-1"></span>
                    </div>
                                @error('party_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                </div>
                        </aside>
                        </div>
                        
                    <table id="shortcut_keys">
                        <thead>
                            <tr>
                                <th class="sk-f2" scope="col">F2</th>
                                <th class="sk-f4" scope="col">F4</th>
                                <th class="sk-f8" scope="col">F8</th>
                                <th class="sk-f9" scope="col">F9</th>
                                    </tr>
                                </thead>
                        <tbody>
                            <tr>
                                <td>Save draft</td>
                                <td>Post &amp; print</td>
                                <td>Focus barcode</td>
                                <td>Post invoice</td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>

            </div>
        </form>

    <!-- Templates for dynamic content -->
    <template id="general_item_template">
        <tr class="general-item-row">
            <td class="p-2 text-center align-middle">
                <input type="checkbox" class="line-check rounded border-gray-400" title="Select line">
            </td>
            <td class="p-2 align-top">
                <div class="line-item-name-wrap relative">
                    <input type="text" readonly
                           class="item-display-name ci-form-control bg-gray-50 font-medium"
                           placeholder="Item" value="" autocomplete="off">
                    <input type="hidden" name="general_lines[INDEX][general_item_id]" class="line-item-id" value="">
                        <div class="general-item-error mt-1 text-xs text-red-600 hidden"></div>
                </div>
            </td>
            <td class="p-2 general-details-cell align-middle text-sm text-gray-600">—</td>
            <td class="p-2 align-middle">
                <input type="number" name="general_lines[INDEX][sale_price]" required step="0.01" min="0" 
                       class="ci-form-control general-sale-price"
                       placeholder="0">
            </td>
            <td class="p-2 align-middle">
                <input type="number" name="general_lines[INDEX][qty]" required step="1" min="1" 
                       class="ci-form-control general-qty"
                       placeholder="0" value="1">
            </td>
            <td class="p-2 align-middle">
                <span class="text-sm font-semibold general-line-total text-gray-900">0.00</span>
             </td>
         </tr>
     </template>

    <script>
    // Clear data immediately if this is a fresh page load (no flags set)
    (function() {
        const wasSubmitting = sessionStorage.getItem('sale_invoice_form_submitting');
        const hasFailedSubmissionFlag = localStorage.getItem('sale_invoice_form_failed_submission');
        
        console.log('Immediate check on page load:', {
            wasSubmitting: wasSubmitting,
            hasFailedSubmissionFlag: hasFailedSubmissionFlag,
            url: window.location.href,
            referrer: document.referrer
        });
        
        // Check if this is a fresh navigation to create page (not coming from a form submission)
        const isCreatePage = window.location.href.includes('/sale-invoices/create');
        const hasVisibleErrors = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const hasVisibleSuccess = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        
        // Check if we're coming from a different page (not from form submission)
        const isFromDifferentPage = document.referrer && 
            !document.referrer.includes('/sale-invoices/create') && 
            !document.referrer.includes('sale-invoices/create');
        
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
            localStorage.removeItem('sale_invoice_form_data');
            localStorage.removeItem('sale_invoice_form_failed_submission');
            sessionStorage.removeItem('sale_invoice_form_submitting');
        } else {
            console.log('Preserving data - submission flags detected, letting main logic handle restoration');
        }
    })();

    function getCatalogItemById(id) {
        if (id === undefined || id === null || id === '') {
            return null;
        }
        const list = window.__POS_ITEM_CATALOG || [];
        const sid = String(id);
        for (let i = 0; i < list.length; i++) {
            if (String(list[i].id) === sid) {
                return list[i];
            }
        }
        return null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        let generalItemIndex = 0;

        function updatePosSaleTypeButtons() {
            const v = document.getElementById('sale_type').value;
            const creditBtn = document.getElementById('pos_sale_credit');
            const cashBtn = document.getElementById('pos_sale_cash');
            if (!creditBtn || !cashBtn) return;
            creditBtn.classList.toggle('is-active-credit', v === 'credit');
            creditBtn.classList.remove('is-active-cash');
            cashBtn.classList.toggle('is-active-cash', v === 'cash');
            cashBtn.classList.remove('is-active-credit');
        }

        document.getElementById('pos_sale_credit')?.addEventListener('click', function(e) {
            e.preventDefault();
            const st = document.getElementById('sale_type');
            st.value = 'credit';
            st.dispatchEvent(new Event('change'));
        });
        document.getElementById('pos_sale_cash')?.addEventListener('click', function(e) {
            e.preventDefault();
            const st = document.getElementById('sale_type');
            st.value = 'cash';
            st.dispatchEvent(new Event('change'));
        });

        // Sale type change handler
        document.getElementById('sale_type').addEventListener('change', function() {
            const bankField = document.getElementById('bank_field');
            const bankSelect = document.getElementById('bank_id');
            const customerField = document.getElementById('party_id');
            const partyFieldLabel = document.getElementById('party_field_label');
            const customerHelpText = document.getElementById('party_help_text');
            const creditPartyBlock = document.getElementById('credit_party_block');

            // Clear any existing party error when sale type changes
            const partyError = document.getElementById('party_error');
            if (partyError) {
                partyError.remove();
            }
            
            if (this.value === 'cash') {
                bankField.style.display = 'block';
                bankSelect.required = true;
                customerField.required = false;
                if (partyFieldLabel) {
                    partyFieldLabel.textContent = 'Party';
                }
                if (customerHelpText) {
                    customerHelpText.textContent = 'Party is only used for credit sales.';
                }
                if (creditPartyBlock) {
                    creditPartyBlock.style.display = 'none';
                }
            } else {
                bankField.style.display = 'none';
                bankSelect.required = false;
                bankSelect.value = '';
                hideBankBalance();
                customerField.required = true;
                if (partyFieldLabel) {
                    partyFieldLabel.textContent = 'Party *';
                }
                if (customerHelpText) {
                customerHelpText.textContent = 'Required for credit sales';
                }
                if (creditPartyBlock) {
                    creditPartyBlock.style.display = 'block';
                }
            }
            updatePosSaleTypeButtons();
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
            
            const currentDate = document.getElementById('invoice_date').value;
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
            
            const currentDate = document.getElementById('invoice_date').value;
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

        // Function to hide bank balance
        function hideBankBalance() {
            const balanceDiv = document.getElementById('bank_balance');
            balanceDiv.classList.add('hidden');
        }

        // Add event listener for invoice date changes to refresh balances
        const invoiceDateInput = document.getElementById('invoice_date');
        invoiceDateInput.addEventListener('change', function() {
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
            const generalItemRows = document.querySelectorAll('.general-item-row');
            let hasInsufficientStock = false;
            let stockErrors = [];
            
            generalItemRows.forEach((row, index) => {
                const itemInput = row.querySelector('.item-display-name');
                const selectedItemId = row.querySelector('.line-item-id')?.value;
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
            const itemInput = row.querySelector('.item-display-name');
            const selectedItemId = row.querySelector('.line-item-id')?.value;
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

        class PosTopItemSearch {
            constructor() {
                this.wrap = document.getElementById('pos_item_search_wrap');
                this.input = document.getElementById('pos_item_search');
                this.itemTypeSelect = document.getElementById('pos_item_type_id');
                this.dropdown = document.getElementById('pos_item_search_dropdown');
                this.resultsContainer = document.getElementById('pos_item_search_results');
                this.paginationContainer = document.getElementById('pos_item_search_pager');
                this.loadingIndicator = document.getElementById('pos_item_search_loading');
                this.pageInfo = document.getElementById('pos_item_page_info');
                this.prevBtn = document.getElementById('pos_item_prev');
                this.nextBtn = document.getElementById('pos_item_next');
                this.searchTimeout = null;
                this.currentPage = 1;
                this.searchTerm = '';
                this.itemsPerPage = 15;
                this.debounceDelay = 300;
                this.minSearchLength = 2;
                this.init();
            }

            getSelectedItemTypeId() {
                if (!this.itemTypeSelect) {
                    return null;
                }
                const v = (this.itemTypeSelect.value || '').trim();
                return v !== '' ? v : null;
            }

            appendItemTypeToUrl(url) {
                const tid = this.getSelectedItemTypeId();
                if (tid) {
                    url.searchParams.set('item_type_id', tid);
                }
            }
            
            init() {
                this.bindEvents();
                document.addEventListener('click', (e) => {
                    if (this.wrap && !this.wrap.contains(e.target)) {
                        this.hideDropdown();
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
                    this.showDropdown();
                    this.debounceSearch();
                });
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                        this.commitSelection();
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
                if (this.itemTypeSelect) {
                    this.itemTypeSelect.addEventListener('change', () => {
                        this.currentPage = 1;
                        if (this.dropdown.classList.contains('hidden')) {
                            return;
                        }
                        if (this.searchTerm.length >= this.minSearchLength) {
                            this.performSearch();
                        } else {
                            this.showInitialResults();
                        }
                    });
                }
            }
            
            debounceSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => this.performSearch(), this.debounceDelay);
            }
            
            async performSearch() {
                if (this.searchTerm.length < this.minSearchLength) {
                    await this.showInitialResults();
                    return;
                }
                this.showLoading();
                try {
                    const url = new URL('/api/general-items/search', window.location.origin);
                    url.searchParams.set('q', this.searchTerm);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    this.appendItemTypeToUrl(url);
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
                        throw new Error('Invalid search response');
                    }
                    this.displayResults(data.data || [], data.meta || {});
                } catch (err) {
                    console.error(err);
                    this.resultsContainer.innerHTML = '<div class="px-4 py-3 text-sm text-red-500 text-center">' + err.message + '</div>';
                    this.paginationContainer.classList.add('hidden');
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
                    this.appendItemTypeToUrl(url);
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
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid list response');
                    }
                    this.displayResults(data.data || [], data.meta || {});
                } catch (err) {
                    console.error(err);
                    this.resultsContainer.innerHTML = '<div class="px-4 py-3 text-sm text-red-500 text-center">' + err.message + '</div>';
                    this.paginationContainer.classList.add('hidden');
                } finally {
                    this.hideLoading();
                }
            }
            
            displayResults(items, meta) {
                this.resultsContainer.innerHTML = '';
                if (!items || !items.length) {
                    this.resultsContainer.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No items found.</div>';
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                items.forEach((item) => {
                    if (!item || !item.id) {
                        return;
                    }
                    const el = document.createElement('div');
                    el.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer pos-top-result';
                    el.dataset.itemId = item.id;
                    el.dataset.itemName = item.item_name || '';
                    el.dataset.salePrice = this.safeNumber(item.sale_price);
                    el.dataset.availableStock = this.safeNumber(item.available_stock);
                    el.dataset.itemCode = item.item_code || '';
                    const tn = (item.item_type && item.item_type.item_type) ? item.item_type.item_type : '';
                    el.dataset.itemTypeName = tn;
                    el.innerHTML = '<div class="font-medium text-gray-900">' + item.item_name + '</div>' +
                        (item.item_code ? '<div class="text-xs text-gray-400">' + item.item_code + '</div>' : '') +
                        (tn ? '<div class="text-xs text-gray-500">' + tn + '</div>' : '');
                    el.addEventListener('mousedown', (ev) => {
                        ev.preventDefault();
                        this.pickItemFromDataset(el);
                    });
                    this.resultsContainer.appendChild(el);
                });
                if (meta && meta.last_page > 1) {
                this.paginationContainer.classList.remove('hidden');
                    this.pageInfo.textContent = 'Page ' + meta.current_page + ' of ' + meta.last_page;
                    this.prevBtn.disabled = meta.current_page <= 1;
                    this.nextBtn.disabled = meta.current_page >= meta.last_page;
                    this.prevBtn.onclick = () => {
                    if (meta.current_page > 1) {
                        this.currentPage = meta.current_page - 1;
                        this.performSearch();
                    }
                };
                    this.nextBtn.onclick = () => {
                    if (meta.current_page < meta.last_page) {
                        this.currentPage = meta.current_page + 1;
                        this.performSearch();
                    }
                };
                } else {
                    this.paginationContainer.classList.add('hidden');
                }
            }

            pickItemFromDataset(el) {
                    const item = {
                    id: el.dataset.itemId,
                    item_name: el.dataset.itemName,
                    item_code: el.dataset.itemCode,
                    sale_price: this.safeNumber(el.dataset.salePrice),
                    available_stock: this.safeNumber(el.dataset.availableStock),
                    item_type: el.dataset.itemTypeName ? { item_type: el.dataset.itemTypeName } : null
                };
                window.addOrBumpItemLine(item);
                this.input.value = '';
                this.hideDropdown();
            }

            commitSelection() {
                const hi = this.resultsContainer.querySelector('.pos-top-result.selected');
                if (hi) {
                    this.pickItemFromDataset(hi);
                    return;
                }
                const first = this.resultsContainer.querySelector('.pos-top-result');
                if (first) {
                    this.pickItemFromDataset(first);
                    return;
                }
                alert('No item to add. Type at least 2 characters and choose from the list.');
            }
            
            navigateResults(direction) {
                const results = this.resultsContainer.querySelectorAll('.pos-top-result');
                if (!results.length) {
                    return;
                }
                const currentIndex = Array.from(results).findIndex((item) => item.classList.contains('selected'));
                let newIndex;
                if (direction === 'down') {
                    newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                } else {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                }
                results.forEach((item) => item.classList.remove('selected', 'bg-emerald-50'));
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-emerald-50');
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
                if (this.loadingIndicator) {
                this.loadingIndicator.classList.remove('hidden');
                }
            }
            
            hideLoading() {
                if (this.loadingIndicator) {
                this.loadingIndicator.classList.add('hidden');
                }
            }
            
            safeNumber(value) {
                if (value === null || value === undefined || value === '') {
                    return 0;
                }
                const n = parseFloat(value);
                return isNaN(n) ? 0 : n;
            }
        }

        function findGeneralRowByItemId(itemId) {
            const sid = String(itemId);
            const rows = document.querySelectorAll('.general-item-row');
            for (let i = 0; i < rows.length; i++) {
                const hid = rows[i].querySelector('.line-item-id');
                if (hid && String(hid.value) === sid) {
                    return rows[i];
                }
            }
            return null;
        }

        function attachGeneralRowListeners(row) {
            const qtyInput = row.querySelector('.general-qty');
            const salePriceInput = row.querySelector('.general-sale-price');
            [qtyInput, salePriceInput].forEach((input) => {
                if (!input) {
                    return;
                }
                input.addEventListener('input', function() {
                    calculateLineTotal(this);
                    calculateTotals();
                });
            });
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    validateQuantityForRow(this);
                });
            }
        }

        function appendStaticGeneralRow() {
            const container = document.getElementById('general_items_container');
            const template = document.getElementById('general_item_template');
            const clone = template.content.cloneNode(true);
            clone.querySelectorAll('[name*="INDEX"]').forEach((element) => {
                element.name = element.name.replace('INDEX', generalItemIndex);
            });
            container.appendChild(clone);
            const newRow = container.lastElementChild;
            attachGeneralRowListeners(newRow);
            generalItemIndex++;
            return newRow;
        }

        function fillStaticGeneralRow(row, item) {
            if (!row || !item || !item.id) {
                return;
            }
            const hidden = row.querySelector('.line-item-id');
            const nameEl = row.querySelector('.item-display-name');
            if (hidden) {
                hidden.value = item.id;
            }
            if (nameEl) {
                nameEl.value = item.item_name || '';
            }
                const salePriceInput = row.querySelector('.general-sale-price');
                if (salePriceInput) {
                salePriceInput.value = item.sale_price != null && item.sale_price !== '' ? item.sale_price : '';
            }
            setGeneralLineDetailsCell(row, item);
            const availableStock = item.available_stock ?? 0;
            row.querySelectorAll('.stock-warning, .item-info').forEach((n) => n.remove());
            const wrap = row.querySelector('.line-item-name-wrap');
            if (wrap) {
                const infoDiv = document.createElement('div');
                infoDiv.className = 'item-info mt-1 text-xs leading-snug';
                if (availableStock <= 0) {
                    infoDiv.className += ' text-red-600 font-medium';
                    infoDiv.innerHTML = '<span>⚠️ Stock: <span class="font-medium">' + availableStock + '</span> — No stock</span>';
                } else if (availableStock <= 5) {
                    infoDiv.className += ' text-orange-700 font-medium';
                    infoDiv.innerHTML = '<span>⚠️ Stock: <span class="font-medium">' + availableStock + '</span> — Low stock</span>';
                } else {
                    infoDiv.className += ' text-gray-600';
                    infoDiv.innerHTML = '<span>Stock: <span class="font-medium">' + availableStock + '</span></span>';
                }
                wrap.appendChild(infoDiv);
            }
            if (salePriceInput) {
                calculateLineTotal(salePriceInput);
                calculateTotals();
            }
            const qtyInput = row.querySelector('.general-qty');
            if (qtyInput) {
                validateQuantityForRow(qtyInput);
            }
        }

        window.addOrBumpItemLine = function(item) {
            if (!item || !item.id) {
                return;
            }
            const existing = findGeneralRowByItemId(item.id);
            if (existing) {
                const qtyInput = existing.querySelector('.general-qty');
                const q = (parseFloat(qtyInput.value) || 0) + 1;
                qtyInput.value = q;
                calculateLineTotal(qtyInput);
                calculateTotals();
                validateQuantityForRow(qtyInput);
                document.getElementById('pos_barcode')?.focus();
                return;
            }
            const row = appendStaticGeneralRow();
            fillStaticGeneralRow(row, item);
            const rate = row.querySelector('.general-sale-price');
            if (rate) {
                rate.focus();
                rate.select();
            }
        };

        window._saleInvoiceAppendStaticRow = appendStaticGeneralRow;
        window._saleInvoiceFillStaticRow = fillStaticGeneralRow;
        window._saleInvoiceResetGeneralItemIndexForRestore = function() {
            generalItemIndex = 0;
        };

        document.getElementById('pos_barcode')?.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') {
                return;
            }
            e.preventDefault();
            const raw = (this.value || '').trim();
            if (!raw) {
                return;
            }
            const map = window.__POS_ITEM_BY_CODE || {};
            const item = map[raw.toLowerCase()];
            if (!item) {
                alert('No item matches this code.');
                return;
            }
            const typeSel = document.getElementById('pos_item_type_id');
            const selectedType = typeSel && typeSel.value ? String(typeSel.value) : '';
            if (selectedType && String(item.item_type_id ?? '') !== selectedType) {
                alert('This code does not match the selected item type. Change item type to "All types" or pick another item.');
                return;
            }
            window.addOrBumpItemLine(item);
            this.value = '';
            this.focus();
        });

        document.getElementById('check_all_lines')?.addEventListener('change', function() {
            document.querySelectorAll('.general-item-row .line-check').forEach(cb => {
                cb.checked = this.checked;
            });
        });

        document.getElementById('delete_selected_lines')?.addEventListener('click', function() {
            let removed = 0;
            document.querySelectorAll('.general-item-row').forEach(row => {
                const cb = row.querySelector('.line-check');
                if (cb && cb.checked) {
                    const sid = row.querySelector('.line-item-id')?.value;
                    if (sid && window.selectedGeneralItemIds && Array.isArray(window.selectedGeneralItemIds)) {
                        window.selectedGeneralItemIds = window.selectedGeneralItemIds.filter(x => String(x) !== String(sid));
                    }
                    row.remove();
                    removed++;
                }
            });
            const hall = document.getElementById('check_all_lines');
            if (hall) {
                hall.checked = false;
            }
            calculateTotals();
            if (!removed) {
                alert('Tick the boxes on the lines you want to remove.');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.altKey || e.ctrlKey || e.metaKey) {
                return;
            }
            const t = e.target;
            const tag = t && t.tagName;
            const isLineQtyPrice = t && t.classList && (t.classList.contains('general-qty') || t.classList.contains('general-sale-price'));
            if (tag && ['INPUT', 'TEXTAREA', 'SELECT'].includes(tag) && !isLineQtyPrice && t.id !== 'pos_barcode' && t.id !== 'pos_item_search' && t.id !== 'pos_item_type_id') {
                if (e.key === 'F2' || e.key === 'F4' || e.key === 'F8' || e.key === 'F9') {
                    return;
                }
            }
            if (e.key === 'F2') {
                e.preventDefault();
                document.getElementById('btn_save_draft')?.click();
            } else if (e.key === 'F4') {
                e.preventDefault();
                document.getElementById('btn_post_print')?.click();
            } else if (e.key === 'F8') {
                e.preventDefault();
                document.getElementById('pos_barcode')?.focus();
            } else if (e.key === 'F9') {
                e.preventDefault();
                document.getElementById('btn_post_invoice')?.click();
            }
        });

        new PosTopItemSearch();

        function showSaleInvoiceLinesBlockedMessage(message) {
            const text = message || 'Item selection is required. Add at least one line using the barcode or item search before posting.';
            let el = document.getElementById('sale_invoice_general_lines_error');
            if (!el) {
                el = document.createElement('div');
                el.id = 'sale_invoice_general_lines_error';
                el.className = 'mt-2 rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800';
                el.setAttribute('role', 'alert');
                const posQuick = document.querySelector('.pos-quick-add');
                if (posQuick) {
                    posQuick.appendChild(el);
                } else {
                    const hr = document.querySelector('.ci-white-box hr.ci-hr');
                    if (hr && hr.parentNode) {
                        hr.insertAdjacentElement('afterend', el);
                    }
                }
            }
            el.textContent = text;
            requestAnimationFrame(function() {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }

        // Form validation
        document.getElementById('saleInvoiceForm').addEventListener('submit', function(e) {
            const submitter = e.submitter;
            const submitAction = submitter && submitter.getAttribute('name') === 'action' ? submitter.value : null;
            if ((submitAction === 'post' || submitAction === 'post_print') && document.querySelectorAll('.general-item-row').length === 0) {
                e.preventDefault();
                showSaleInvoiceLinesBlockedMessage('Item selection is required. Add at least one line using the barcode or item search before posting.');
                return false;
            }

            // Validate party selection for credit sales
            const saleType = document.getElementById('sale_type').value;
            const partyId = document.getElementById('party_id').value;
            const partyError = document.getElementById('party_error');
            
            // Remove existing error message
            if (partyError) {
                partyError.remove();
            }
            
            // Validate party selection for credit sales
            if (saleType === 'credit' && (!partyId || partyId === '')) {
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
                const selectedItemId = row.querySelector('.line-item-id')?.value;
                if (!selectedItemId || selectedItemId === '') {
                    showGeneralItemError(row, 'Please add items using search or barcode.');
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
            document.querySelectorAll('.searchable-input, .item-display-name').forEach(input => {
                input.classList.remove('border-red-500');
            });
        }
        
        // Function to show general item error
        function showGeneralItemError(row, message) {
            const errorDiv = row.querySelector('.general-item-error');
            const input = row.querySelector('.item-display-name');
            
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
            
            // Get charges
            const shipping = parseFloat(document.getElementById('shipping_charges').value) || 0;
            
            // Calculate total
            const total = subtotal + shipping;
            
            // Update display
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('shipping_display').textContent = `+ ${shipping.toFixed(2)}`;
            document.getElementById('total_amount').textContent = total.toFixed(2);
        }

        function setGeneralLineDetailsCell(row, item) {
            const cell = row.querySelector('.general-details-cell');
            if (!cell) return;
            const parts = [];
            if (item.item_code) {
                parts.push('Code: ' + item.item_code);
            }
            const typeName = (item.item_type && item.item_type.item_type) ? item.item_type.item_type : (item.item_type_name || '');
            if (typeName) {
                parts.push(typeName);
            }
            const st = item.available_stock;
            if (st !== undefined && st !== null && st !== '') {
                parts.push('Stock: ' + st);
            }
            cell.textContent = parts.length ? parts.join(' · ') : '—';
        }

        window.clearValidationErrors = clearValidationErrors;
        window.showGeneralItemError = showGeneralItemError;
        window.showArmError = showArmError;
        window._saleInvoiceCalculateLineTotal = calculateLineTotal;
        window._saleInvoiceCalculateTotals = calculateTotals;
        window._saleInvoiceSetGeneralLineDetailsCell = setGeneralLineDetailsCell;
        window.fetchCustomerBalance = fetchCustomerBalance;

        // Global array to track selected general item IDs
        window.selectedGeneralItemIds = window.selectedGeneralItemIds || [];

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

        // Initialize sale type state (bank / party panels, POS buttons)
        const saleTypeSelect = document.getElementById('sale_type');
            saleTypeSelect.dispatchEvent(new Event('change'));
        const bankSelectInit = document.getElementById('bank_id');
        if (bankSelectInit && bankSelectInit.value) {
            fetchBankBalance(bankSelectInit.value);
        }

        @if (!$errors->any())
        requestAnimationFrame(function() {
            document.getElementById('pos_barcode')?.focus();
        });
        @endif

        @error('general_lines')
        requestAnimationFrame(function() {
            const el = document.getElementById('sale_invoice_general_lines_error');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        @enderror
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
                        if (window.fetchCustomerBalance) {
                            window.fetchCustomerBalance(party.id);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching pre-selected party:', error);
                });
        }
    };

    // ===== DATA PERSISTENCE FOR SALE INVOICE FORM =====

    // Function to save sale invoice form data to localStorage
    function saveSaleInvoiceFormData() {
        try {
            const formData = {
                business_id: '{{ session("active_business") }}', // Store business_id for validation
                form_type: 'sale_invoice', // Identify form type to prevent cross-form restoration
                sale_type: document.getElementById('sale_type').value,
                party_id: document.getElementById('party_id').value,
                party_display: document.getElementById('party_search_input')?.value || '',
                bank_id: document.getElementById('bank_id').value,
                invoice_date: document.getElementById('invoice_date').value,
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
                const generalItemId = row.querySelector('.line-item-id')?.value || '';
                const generalItemDisplay = row.querySelector('.item-display-name')?.value || '';
                const qty = row.querySelector('.general-qty')?.value || '';
                const unitPrice = row.querySelector('.general-sale-price')?.value || '';

                if (generalItemId || qty || unitPrice) {
                    formData.general_items.push({
                        general_item_id: generalItemId,
                        general_item_display: generalItemDisplay,
                        qty: qty,
                        unit_price: unitPrice,
                        description: ''
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
                localStorage.setItem('sale_invoice_form_data', JSON.stringify(formData));
                console.log('Sale invoice form data saved:', formData);
            }
        } catch (error) {
            console.error('Error saving sale invoice form data:', error);
        }
    }

    // Function to load saved sale invoice form data from localStorage
    function loadSavedSaleInvoiceData() {
        try {
            const savedData = localStorage.getItem('sale_invoice_form_data');
            if (!savedData) {
                console.log('No saved sale invoice data found');
                return;
            }

            const formData = JSON.parse(savedData);
            console.log('Loading saved sale invoice data:', formData);
            
            // Validate business_id and form_type - clear data if validation fails
            const currentBusinessId = '{{ session("active_business") }}';
            if (!formData.business_id || formData.business_id !== currentBusinessId) {
                console.log('Saved data belongs to different business or missing business_id, clearing...', {
                    savedBusinessId: formData.business_id || 'missing',
                    currentBusinessId: currentBusinessId
                });
                clearSaleInvoiceFormData();
                return;
            }
            
            // Validate form_type to prevent cross-form restoration
            if (!formData.form_type || formData.form_type !== 'sale_invoice') {
                console.log('Saved data is from different form type, clearing...', {
                    savedFormType: formData.form_type || 'missing',
                    currentFormType: 'sale_invoice'
                });
                clearSaleInvoiceFormData();
                return;
            }

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

            console.log('Sale invoice form data restored successfully');
        } catch (error) {
            console.error('Error loading saved sale invoice form data:', error);
        }
    }

    // Function to clear saved sale invoice form data
    function clearSaleInvoiceFormData() {
        localStorage.removeItem('sale_invoice_form_data');
        localStorage.removeItem('sale_invoice_form_failed_submission');
        sessionStorage.removeItem('sale_invoice_form_submitting');
        console.log('Sale invoice form data cleared');
    }

    // Function to restore general items
    function restoreGeneralItems(generalItems) {
        console.log('restoreGeneralItems called with:', generalItems);
        const appendRow = window._saleInvoiceAppendStaticRow;
        const fillRow = window._saleInvoiceFillStaticRow;
        if (!appendRow || !fillRow) {
            console.warn('POS row helpers not ready; retrying restore.');
            setTimeout(() => restoreGeneralItems(generalItems), 150);
            return;
        }

        document.getElementById('general_items_container').innerHTML = '';
        if (window._saleInvoiceResetGeneralItemIndexForRestore) {
            window._saleInvoiceResetGeneralItemIndexForRestore();
        }

        generalItems.forEach((item) => {
            const row = appendRow();
            const cat = getCatalogItemById(item.general_item_id);
            if (cat) {
                fillRow(row, cat);
            } else if (item.general_item_id) {
                const hidden = row.querySelector('.line-item-id');
                const nameEl = row.querySelector('.item-display-name');
                if (hidden) {
                    hidden.value = item.general_item_id;
                }
                if (nameEl && item.general_item_display) {
                    nameEl.value = item.general_item_display;
                }
                if (window._saleInvoiceSetGeneralLineDetailsCell) {
                    window._saleInvoiceSetGeneralLineDetailsCell(row, {});
                }
            }
            const qtyInput = row.querySelector('.general-qty');
            const priceInput = row.querySelector('.general-sale-price');
            if (qtyInput && item.qty) {
                        qtyInput.value = item.qty;
            }
            if (priceInput && item.unit_price) {
                        priceInput.value = item.unit_price;
            }
            if (priceInput && window._saleInvoiceCalculateLineTotal) {
                window._saleInvoiceCalculateLineTotal(priceInput);
            }
        });
        if (window._saleInvoiceCalculateTotals) {
            window._saleInvoiceCalculateTotals();
        }
        console.log('Finished processing all general items');
    }

    // Function to restore arms (UI removed — no-op to keep localStorage restore safe)
    function restoreArms(arms) {
        if (arms && arms.length) {
            console.log('restoreArms: arms UI removed, skipping', arms.length, 'saved arm row(s)');
        }
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
    function addSaleInvoiceDataPersistenceListeners() {
        // Add listeners to form fields
        const formFields = [
            'sale_type', 'party_id', 'bank_id', 'invoice_date'
        ];
        
        formFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', saveSaleInvoiceFormData);
                field.addEventListener('change', saveSaleInvoiceFormData);
            }
        });
        
        // Add listeners to party search input
        const partySearchInput = document.getElementById('party_search_input');
        if (partySearchInput) {
            partySearchInput.addEventListener('input', saveSaleInvoiceFormData);
            partySearchInput.addEventListener('change', saveSaleInvoiceFormData);
        }
        
        // Add listeners to dynamic content (general items and arms)
        // These will be added when rows are created
        document.addEventListener('input', function(e) {
            if (e.target.closest('.general-item-row')) {
                saveSaleInvoiceFormData();
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.closest('.general-item-row')) {
                saveSaleInvoiceFormData();
            }
        });
    }

    // Set up data persistence on page load
    document.addEventListener('DOMContentLoaded', function() {
        const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
        const hasOldInput = {{ old('party_id') || old('bank_id') ? 'true' : 'false' }};
        
        // If no errors and no old input, this is a fresh page load after successful submission or first visit
        // Clear localStorage to prevent pre-filling from previous submission
        if (!hasErrors && !hasOldInput) {
            console.log('Fresh page load detected - clearing localStorage');
            clearSaleInvoiceFormData();
        }
        
        // Add data persistence listeners
        addSaleInvoiceDataPersistenceListeners();
        
        // Set up form submission tracking
        document.getElementById('saleInvoiceForm').addEventListener('submit', function(e) {
            sessionStorage.setItem('sale_invoice_form_submitting', 'true');
            localStorage.setItem('sale_invoice_form_failed_submission', 'true');
        });
        
        // Clear flags when leaving the page (handles successful submissions)
        window.addEventListener('beforeunload', function(e) {
            // Only clear if we're not on the create page (successful navigation)
            if (!window.location.href.includes('/sale-invoices/create')) {
                console.log('Leaving sale invoice create page - clearing flags');
                sessionStorage.removeItem('sale_invoice_form_submitting');
                localStorage.removeItem('sale_invoice_form_failed_submission');
            }
        });
        
        // Handle page show event for data persistence
        window.addEventListener('pageshow', function(event) {
            const wasSubmitting = sessionStorage.getItem('sale_invoice_form_submitting');
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
            const isCreatePage = window.location.href.includes('/sale-invoices/create');
            
            console.log('Sale invoice pageshow event:', {
                wasSubmitting: wasSubmitting,
                hasSuccessMessage: !!successMessage,
                hasErrorMessages: !!errorMessages,
                isCreatePage: isCreatePage
            });
            
            if (wasSubmitting && successMessage && isCreatePage) {
                console.log('Clearing sale invoice form data due to successful submission');
                clearSaleInvoiceFormData();
            } else if (!wasSubmitting && !errorMessages) {
                console.log('Fresh page load - clearing flags');
                sessionStorage.removeItem('sale_invoice_form_submitting');
                localStorage.removeItem('sale_invoice_form_failed_submission');
            } else if (errorMessages) {
                console.log('Error messages detected - preserving flags');
            } else if (wasSubmitting && !successMessage) {
                console.log('Failed submission but no error messages - letting restoration logic decide');
            }
        });
        
        // Load saved data after all initialization is complete
        setTimeout(() => {
            const wasSubmitting = sessionStorage.getItem('sale_invoice_form_submitting');
            const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
            const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
            const hasSavedData = localStorage.getItem('sale_invoice_form_data');
            const hasFailedSubmissionFlag = localStorage.getItem('sale_invoice_form_failed_submission');
            
            console.log('Sale invoice restoration check:', {
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
                console.log('Restoring sale invoice data after failed submission');
                setTimeout(() => {
                    loadSavedSaleInvoiceData();
                    
                    // Clear flags after restoration if there are no error messages
                    if (!errorMessages) {
                        console.log('Clearing flags after restoration (no error messages)');
                        sessionStorage.removeItem('sale_invoice_form_submitting');
                        localStorage.removeItem('sale_invoice_form_failed_submission');
                    }
                }, 500);
            } else if (!wasSubmitting && !errorMessages && !hasFailedSubmissionFlag) {
                console.log('Fresh page load - not restoring sale invoice data');
            } else {
                console.log('Successful submission - not restoring sale invoice data');
            }
        }, 1500);
    
    // Additional fallback: Clear data after 3 seconds if no errors are detected
    setTimeout(() => {
        const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const wasSubmitting = sessionStorage.getItem('sale_invoice_form_submitting');
        
        console.log('Sale invoice fallback check after 3 seconds:', {
            wasSubmitting: wasSubmitting,
            hasSuccessMessage: !!successMessage,
            hasErrorMessages: !!errorMessages,
            successMessageText: successMessage ? successMessage.textContent : 'none'
        });
        
        // If there's a success message OR no error messages and we were submitting, clear everything
        if (successMessage || (!errorMessages && wasSubmitting)) {
            console.log('Fallback: Clearing sale invoice data due to success or no errors after submission');
            clearSaleInvoiceFormData();
        }
    }, 3000);
    
    // Additional aggressive fallback: Clear data if we're on create page with no errors
    setTimeout(() => {
        const errorMessages = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const successMessage = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        const wasSubmitting = sessionStorage.getItem('sale_invoice_form_submitting');
        const hasFailedSubmissionFlag = localStorage.getItem('sale_invoice_form_failed_submission');
        const isCreatePage = window.location.href.includes('/sale-invoices/create');
        
        console.log('Sale invoice aggressive fallback check after 5 seconds:', {
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
            console.log('Aggressive fallback: Clearing sale invoice data - success or no errors on create page');
            clearSaleInvoiceFormData();
        } else {
            console.log('Aggressive fallback: Preserving sale invoice data - errors detected');
        }
    }, 5000);
    });

    // Debug functions (can be removed in production)
    window.debugSaleInvoiceForm = {
        save: saveSaleInvoiceFormData,
        load: loadSavedSaleInvoiceData,
        clear: clearSaleInvoiceFormData,
        show: () => {
            const data = localStorage.getItem('sale_invoice_form_data');
            console.log('Current saved sale invoice data:', data ? JSON.parse(data) : 'No data');
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
            if (window.clearValidationErrors) {
                window.clearValidationErrors();
            }
            
            // Display general item selection errors
            errorKeys.forEach((key, index) => {
                if (key.startsWith('general_lines.') && key.includes('.general_item_id')) {
                    const match = key.match(/general_lines\.(\d+)\.general_item_id/);
                    if (match) {
                        const rowIndex = parseInt(match[1]);
                        const errorMessage = errorMessages[index];
                        
                        // Find the corresponding row
                        const generalItemRows = document.querySelectorAll('.general-item-row');
                        if (generalItemRows[rowIndex] && window.showGeneralItemError) {
                            window.showGeneralItemError(generalItemRows[rowIndex], errorMessage);
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
                        if (armRows[rowIndex] && window.showArmError) {
                            window.showArmError(armRows[rowIndex], errorMessage);
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
</x-invoice-fullscreen-layout>

