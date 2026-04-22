<x-invoice-fullscreen-layout :page-title="'Edit Sale Invoice — ' . config('app.name', 'StoreBook')">
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

        /* CI sale invoice layout (aligned with create) */
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
        .ci-invoice-page button.ci-btn-primary,
        .ci-invoice-page button.ci-btn-danger {
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

    <form method="POST" action="{{ route('sale-invoices.update', $saleInvoice) }}" id="saleInvoiceForm">
            @csrf
        @method('PUT')
        <input type="hidden" name="print_after_update" id="print_after_update" value="0">

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            <div class="invoice-fs-root min-h-full pos-sale-layout ci-invoice-page px-2 py-2 sm:px-3 sm:py-2">
            <div class="ci-white-box max-w-[1600px] mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end mb-1">
                        <div class="lg:col-span-3">
                            <h1 class="text-base font-bold text-gray-900 mb-0.5 tracking-tight">Edit sale invoice</h1>
                            <p class="text-xs text-gray-500 mb-0.5">#{{ $saleInvoice->invoice_number ?? ('SI-' . $saleInvoice->id) }} · {{ $saleInvoice->status }}</p>
                            <a href="{{ route('sale-invoices.show', $saleInvoice) }}" class="text-sm text-blue-600 hover:underline block">View invoice</a>
                            <a href="{{ route('sale-invoices.index') }}" class="text-sm text-blue-600 hover:underline">← Back to list</a>
                                        </div>
                        <div class="lg:col-span-9 pos-quick-add">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-end">
                                <div class="sm:col-span-3">
                                    <label for="pos_barcode" class="block text-sm font-semibold text-gray-800 mb-1">Barcode</label>
                                    <input type="text" id="pos_barcode" autocomplete="off" placeholder="Barcode" class="ci-form-control">
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
                                    <input type="text" id="pos_item_search" autocomplete="off" placeholder="Search by name or code (2+ chars), then Enter" class="ci-form-control">
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
                            <div id="sale_invoice_general_lines_error" class="mt-2 rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800" role="alert">{{ $message }}</div>
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
                                    <tbody id="general_items_container">
                                    @foreach($saleInvoice->generalLines as $index => $line)
                                        @php
                                            $gi = $line->generalItem;
                                            $avail = $gi->available_stock ?? 0;
                                            $dParts = [];
                                            if ($gi->item_code) { $dParts[] = 'Code: ' . $gi->item_code; }
                                            if ($gi->itemType) { $dParts[] = $gi->itemType->item_type; }
                                            $dParts[] = 'Stock: ' . $avail;
                                            $detailsText = implode(' · ', $dParts);
                                        @endphp
                                        <tr class="general-item-row">
                                            <td class="p-2 text-center align-middle">
                                                <input type="checkbox" class="line-check rounded border-gray-400" title="Select line">
                                            </td>
                                            <td class="p-2 align-top">
                                                <div class="line-item-name-wrap relative">
                                                    <input type="text" readonly class="item-display-name ci-form-control bg-gray-50 font-medium" placeholder="Item" value="{{ $gi->item_name }}" autocomplete="off">
                                                    <input type="hidden" name="general_lines[{{ $index }}][general_item_id]" class="line-item-id" value="{{ $line->general_item_id }}">
                                                    <div class="item-info absolute text-xs text-gray-500 left-0 mt-1 z-10 bg-white px-1 rounded" style="top:100%;">Stock: <span class="font-medium">{{ $avail }}</span></div>
                                                    <div class="general-item-error mt-1 text-xs text-red-600 hidden"></div>
                                </div>
                                            </td>
                                            <td class="p-2 general-details-cell align-middle text-sm text-gray-600">{{ $detailsText }}</td>
                                            <td class="p-2 align-middle">
                                                <input type="number" name="general_lines[{{ $index }}][sale_price]" required step="0.01" min="0" class="ci-form-control general-sale-price" placeholder="0" value="{{ $line->sale_price }}">
                                            </td>
                                            <td class="p-2 align-middle">
                                                <input type="number" name="general_lines[{{ $index }}][qty]" required step="1" min="1" class="ci-form-control general-qty" placeholder="0" value="{{ (float) $line->quantity == floor($line->quantity) ? (int) $line->quantity : $line->quantity }}">
                                            </td>
                                            <td class="p-2 align-middle">
                                                <span class="text-sm font-semibold general-line-total text-gray-900">{{ number_format($line->quantity * $line->sale_price, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
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
                                        <input type="number" name="shipping_charges" id="shipping_charges" value="{{ old('shipping_charges', $saleInvoice->shipping_charges) }}" step="1" min="0" class="ci-form-control @error('shipping_charges') border-red-500 @enderror">
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
                                <button type="submit" class="ci-btn-primary" id="btn_update_invoice">Update invoice</button>
                                <a href="{{ route('sale-invoices.show', $saleInvoice) }}" id="btn_cancel_edit" class="ci-btn-outline inline-block text-center no-underline leading-normal">Cancel</a>
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
                                    <option value="cash" {{ old('sale_type', $saleInvoice->sale_type) === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit" {{ old('sale_type', $saleInvoice->sale_type) === 'credit' ? 'selected' : '' }}>Credit (Party)</option>
                                </select>
                                @error('sale_type')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            <div class="form-group">
                                <label for="invoice_date">Date <span class="text-red-600">*</span></label>
                                <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', $saleInvoice->invoice_date->format('Y-m-d')) }}" required class="ci-form-control @error('invoice_date') border-red-500 @enderror">
                                @error('invoice_date')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            <div id="bank_field" class="form-group">
                                <label for="bank_id">Bank <span class="text-red-600">*</span></label>
                                <select name="bank_id" id="bank_id" class="ci-form-control @error('bank_id') border-red-500 @enderror">
                                    <option value="">Select bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ (string) old('bank_id', $saleInvoice->bank_id) === (string) $bank->id ? 'selected' : '' }}>
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
                                <label for="party_search_input"><span id="party_field_label">Party</span></label>
                                <div class="searchable-select-container relative">
                                    <input type="text" id="party_search_input" class="ci-form-control searchable-input bg-white @error('party_id') border-red-500 @enderror" placeholder="Search parties..." autocomplete="off" value="{{ old('party_search_input', $saleInvoice->party->name ?? '') }}">
                                    <input type="hidden" name="party_id" id="party_id" class="selected-item-id" value="{{ old('party_id', $saleInvoice->party_id) }}">
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
                                <td>Cancel</td>
                                <td>Update &amp; print</td>
                                <td>Focus barcode</td>
                                <td>Update invoice</td>
                                        </tr>
                                </tbody>
                            </table>

                    {{-- Persist customer / party licence fields without showing forms (matches create: party search only) --}}
                    <div class="sr-only" aria-hidden="true">
                        <input type="hidden" name="name_of_customer" value="{{ old('name_of_customer', $saleInvoice->name_of_customer) }}">
                        <input type="hidden" name="father_name" value="{{ old('father_name', $saleInvoice->father_name) }}">
                        <input type="hidden" name="contact" value="{{ old('contact', $saleInvoice->contact) }}">
                        <input type="hidden" name="cnic" value="{{ old('cnic', $saleInvoice->cnic) }}">
                        <input type="hidden" name="licence_no" value="{{ old('licence_no', $saleInvoice->licence_no) }}">
                        <input type="hidden" name="licence_issue_date" value="{{ old('licence_issue_date', $saleInvoice->licence_issue_date?->format('Y-m-d')) }}">
                        <input type="hidden" name="licence_valid_upto" value="{{ old('licence_valid_upto', $saleInvoice->licence_valid_upto?->format('Y-m-d')) }}">
                        <input type="hidden" name="licence_issued_by" value="{{ old('licence_issued_by', $saleInvoice->licence_issued_by) }}">
                        <input type="hidden" name="re_reg_no" value="{{ old('re_reg_no', $saleInvoice->re_reg_no) }}">
                        <input type="hidden" name="dc" value="{{ old('dc', $saleInvoice->dc) }}">
                        <input type="hidden" name="Date" value="{{ old('Date', $saleInvoice->Date?->format('Y-m-d')) }}">
                        <input type="hidden" name="address" value="{{ old('address', $saleInvoice->address) }}">
                        <input type="hidden" name="party_license_no" value="{{ old('party_license_no', $saleInvoice->party_license_no) }}">
                        <input type="hidden" name="party_license_issue_date" value="{{ old('party_license_issue_date', $saleInvoice->party_license_issue_date?->format('Y-m-d')) }}">
                        <input type="hidden" name="party_license_valid_upto" value="{{ old('party_license_valid_upto', $saleInvoice->party_license_valid_upto?->format('Y-m-d')) }}">
                        <input type="hidden" name="party_license_issued_by" value="{{ old('party_license_issued_by', $saleInvoice->party_license_issued_by) }}">
                        <input type="hidden" name="party_re_reg_no" value="{{ old('party_re_reg_no', $saleInvoice->party_re_reg_no) }}">
                        <input type="hidden" name="party_dc" value="{{ old('party_dc', $saleInvoice->party_dc) }}">
                        <input type="hidden" name="party_dc_date" value="{{ old('party_dc_date', $saleInvoice->party_dc_date?->format('Y-m-d')) }}">
                    </div>
                        </div>
                    </div>

                    <!-- Arms Section - Hidden: StoreBook is items-only (outside white box, still in form) -->
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
                                    @foreach($saleInvoice->armLines as $index => $line)
                                        <tr class="arm-row">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="searchable-select-container relative">
                                                    <input type="text" 
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-400 focus:outline-none transition-all duration-200 searchable-input bg-white" 
                                                           placeholder="Search arms..." 
                                                           value="{{ $line->arm->arm_title }}"
                                                           readonly>
                                                    <input type="hidden" name="arm_lines[{{ $index }}][arm_id]" class="selected-arm-id" value="{{ $line->arm_id }}">
                                                    <!-- Error display for arm selection -->
                                                    <div class="arm-error mt-1 text-xs text-red-600 hidden"></div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="number" name="arm_lines[{{ $index }}][sale_price]" required step="0.01" min="0" 
                                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm arm-sale-price focus:border-blue-500 focus:ring-blue-500"
                                                       placeholder="0" value="{{ $line->sale_price }}">
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <button type="button" class="text-red-600 hover:text-red-900 remove-arm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </form>

    <!-- Templates for dynamic content -->
    <template id="general_item_template">
        <tr class="general-item-row">
            <td class="p-2 text-center align-middle">
                <input type="checkbox" class="line-check rounded border-gray-400" title="Select line">
            </td>
            <td class="p-2 align-top">
                <div class="line-item-name-wrap relative">
                    <input type="text" readonly class="item-display-name ci-form-control bg-gray-50 font-medium" placeholder="Item" value="" autocomplete="off">
                    <input type="hidden" name="general_lines[INDEX][general_item_id]" class="line-item-id" value="">
                        <div class="general-item-error mt-1 text-xs text-red-600 hidden"></div>
                </div>
            </td>
            <td class="p-2 general-details-cell align-middle text-sm text-gray-600">—</td>
            <td class="p-2 align-middle">
                <input type="number" name="general_lines[INDEX][sale_price]" required step="0.01" min="0" class="ci-form-control general-sale-price" placeholder="0">
            </td>
            <td class="p-2 align-middle">
                <input type="number" name="general_lines[INDEX][qty]" required step="1" min="1" class="ci-form-control general-qty" placeholder="0" value="1">
            </td>
            <td class="p-2 align-middle">
                <span class="text-sm font-semibold general-line-total text-gray-900">0.00</span>
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
        const wasSubmitting = sessionStorage.getItem('sale_invoice_form_submitting');
        const hasFailedSubmissionFlag = localStorage.getItem('sale_invoice_form_failed_submission');
        
        console.log('Immediate check on page load:', {
            wasSubmitting: wasSubmitting,
            hasFailedSubmissionFlag: hasFailedSubmissionFlag,
            url: window.location.href,
            referrer: document.referrer
        });
        
        // Check if this is a fresh navigation to edit page (not coming from a form submission)
        const isEditPage = window.location.href.includes('/sale-invoices/') && window.location.href.includes('/edit');
        const hasVisibleErrors = document.querySelector('.bg-red-100, .text-red-600, .alert-danger, [class*="error"]');
        const hasVisibleSuccess = document.querySelector('.bg-green-100, .bg-green-50, [class*="success"], .alert-success');
        
        // Check if we're coming from a different page (not from form submission)
        const isFromDifferentPage = document.referrer && 
            !document.referrer.includes('/sale-invoices/') && 
            !document.referrer.includes('sale-invoices/');
        
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
            localStorage.removeItem('sale_invoice_form_data');
            localStorage.removeItem('sale_invoice_form_failed_submission');
            sessionStorage.removeItem('sale_invoice_form_submitting');
        } else {
            console.log('Preserving data - submission flags detected, letting main logic handle restoration');
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize global tracking array with existing arm selections
        if (!window.selectedArmIds || !Array.isArray(window.selectedArmIds)) {
            window.selectedArmIds = [];
        }
        document.querySelectorAll('.arm-row .selected-arm-id').forEach(hiddenInput => {
            if (hiddenInput.value && !window.selectedArmIds.includes(hiddenInput.value)) {
                window.selectedArmIds.push(hiddenInput.value);
            }
        });

        // Initialize global tracking array with existing general item selections
        if (!window.selectedGeneralItemIds || !Array.isArray(window.selectedGeneralItemIds)) {
            window.selectedGeneralItemIds = [];
        }
        document.querySelectorAll('.general-item-row .line-item-id').forEach(hiddenInput => {
            if (hiddenInput.value && !window.selectedGeneralItemIds.includes(String(hiddenInput.value))) {
                window.selectedGeneralItemIds.push(String(hiddenInput.value));
            }
        });
        
        let generalItemIndex = {{ $saleInvoice->generalLines->count() }};
        let armIndex = {{ $saleInvoice->armLines->count() }};

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

        // Sale type change handler (same behavior as create.blade sidebar)
        document.getElementById('sale_type').addEventListener('change', function(e) {
            const skipDestructiveUpdates = e && e.detail && e.detail.init === true;
            const bankField = document.getElementById('bank_field');
            const bankSelect = document.getElementById('bank_id');
            const customerField = document.getElementById('party_id');
            const partyFieldLabel = document.getElementById('party_field_label');
            const customerHelpText = document.getElementById('party_help_text');
            const creditPartyBlock = document.getElementById('credit_party_block');

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
                if (!skipDestructiveUpdates) {
                bankSelect.value = '';
                    hideBankBalance();
                }
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

        // Party selection change handler (same as create: balance only)
        document.getElementById('party_id').addEventListener('change', function() {
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
                const nameEl = row.querySelector('.item-display-name');
                const selectedItemId = row.querySelector('.line-item-id')?.value;
                const quantityInput = row.querySelector('input[name*="[qty]"]');
                const quantity = parseFloat(quantityInput?.value || 0);
                const detailsCell = row.querySelector('.general-details-cell');
                    const stockInfo = row.querySelector('.item-info');
                let stockText = '';
                if (detailsCell && detailsCell.textContent) {
                    stockText = detailsCell.textContent;
                } else if (stockInfo) {
                    stockText = stockInfo.textContent;
                }
                
                if (selectedItemId && quantity > 0) {
                        const stockMatch = stockText.match(/Stock:\s*(\d+(?:\.\d+)?)/);
                        if (stockMatch) {
                            const availableStock = parseFloat(stockMatch[1]);
                            if (quantity > availableStock) {
                                hasInsufficientStock = true;
                            const itemName = nameEl ? (nameEl.value || nameEl.textContent || 'Item') : 'Item';
                                stockErrors.push(`Line ${index + 1}: Insufficient stock for '${itemName}'. Available: ${availableStock}, Required: ${quantity}`);
                                quantityInput.style.borderColor = '#ef4444';
                                quantityInput.style.backgroundColor = '#fef2f2';
                            } else {
                                quantityInput.style.borderColor = '';
                                quantityInput.style.backgroundColor = '';
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
            const selectedItemId = row.querySelector('.line-item-id')?.value;
            const quantity = parseFloat(quantityInput.value || 0);
            const detailsCell = row.querySelector('.general-details-cell');
                const stockInfo = row.querySelector('.item-info');
            let stockText = '';
            if (detailsCell && detailsCell.textContent) {
                stockText = detailsCell.textContent;
            } else if (stockInfo) {
                stockText = stockInfo.textContent;
            }
            
            if (selectedItemId && quantity > 0) {
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
                            quantityInput.style.borderColor = '';
                            quantityInput.style.backgroundColor = '';
                            const errorDiv = row.querySelector('.quantity-error');
                            if (errorDiv) {
                                errorDiv.remove();
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

        // Remove arm rows (general lines use bulk delete + POS add)
        document.addEventListener('click', function(e) {
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
        document.getElementById('saleInvoiceForm').addEventListener('submit', function(e) {
            const printAfterEl = document.getElementById('print_after_update');
            const wantPrintAfterUpdate = !!window.__saleInvoicePrintAfterUpdate;
            window.__saleInvoicePrintAfterUpdate = false;
            if (printAfterEl) {
                printAfterEl.value = wantPrintAfterUpdate ? '1' : '0';
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
            
            document.querySelectorAll('.general-item-row .item-display-name').forEach(input => {
                input.classList.remove('border-red-500');
            });
            document.querySelectorAll('.arm-row .searchable-input').forEach(input => {
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

        window.clearValidationErrors = clearValidationErrors;
        window.showGeneralItemError = showGeneralItemError;
        window.showArmError = showArmError;

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
                document.getElementById('btn_cancel_edit')?.click();
            } else if (e.key === 'F4') {
                e.preventDefault();
                window.__saleInvoicePrintAfterUpdate = true;
                document.getElementById('btn_update_invoice')?.click();
            } else if (e.key === 'F8') {
                e.preventDefault();
                document.getElementById('pos_barcode')?.focus();
            } else if (e.key === 'F9') {
                e.preventDefault();
                window.__saleInvoicePrintAfterUpdate = false;
                document.getElementById('btn_update_invoice')?.click();
            }
        });

        new PosTopItemSearch();

        document.querySelectorAll('#general_items_container .general-item-row').forEach(attachGeneralRowListeners);

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
                     salePriceInput.value = item.sale_price ? item.sale_price : '';
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

        // Initialize calculation listeners
        document.getElementById('shipping_charges').addEventListener('input', function() {
            calculateTotals();
        });

        document.querySelectorAll('.arm-row').forEach(row => {
            const salePriceInput = row.querySelector('input[name*="[sale_price]"]');
            
            if (salePriceInput) {
                salePriceInput.addEventListener('input', function() {
                    calculateTotals();
                });
            }
        });

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

        // Handle pre-selected party from existing sale invoice data
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

        // Initialize sale type UI without clearing persisted values (e.g. bank on credit invoices)
        const saleTypeSelect = document.getElementById('sale_type');
        if (saleTypeSelect) {
            saleTypeSelect.dispatchEvent(new CustomEvent('change', { detail: { init: true }, bubbles: true }));
        }

        // Calculate initial totals
        calculateTotals();
    });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <style>
        /* Make Chosen match Tailwind input styles used in filters */
        .chosen-container { width: 100% !important; }
        .chosen-container-single .chosen-single {
            height: 40px;
            line-height: 38px;
            border: 1px solid #d1d5db; /* border-gray-300 */
            border-radius: 0.375rem; /* rounded-md */
            padding: 0 2.25rem 0 0.75rem;
            background: #fff;
            font-size: 0.875rem; /* text-sm */
            color: #111827; /* text-gray-900 */
            box-shadow: none;
        }
        .chosen-container-single .chosen-single div { right: 0.5rem; }
        .chosen-container-active .chosen-single {
            border-color: #a855f7; /* purple-500 */
            box-shadow: 0 0 0 1px rgba(168,85,247,0.2);
        }
        .chosen-container .chosen-drop {
            border-color: #e5e7eb; /* gray-200 */
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        .chosen-container .chosen-search input {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0 0.75rem;
            box-shadow: none;
        }
    </style>
    <script>
        $(function () {
            $('.chosen-select').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'All Item Types'
            });
        });
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
