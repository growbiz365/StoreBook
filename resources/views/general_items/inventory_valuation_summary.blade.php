<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Valuation Summary - {{ $business->name ?? 'Business' }} - General Items Management - StoreBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #1f2937;
            background: #f8fafc;
            font-size: 14px;
        }

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
            min-height: 100vh;
        }

        .report-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 24px 24px 12px 24px;
            margin-bottom: 0;
            border-bottom: 2px solid #333;
            gap: 30px;
        }

        .report-header-left {
            flex: 1;
            text-align: left;
        }

        .report-header-right {
            flex: 1;
            text-align: right;
        }

        .business-info {
            text-align: left;
        }

        .business-logo {
            height: 55px;
            margin-bottom: 6px;
            display: block;
        }

        .business-info h2 {
            margin: 4px 0 6px 0;
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .business-info-details {
            font-size: 11px;
            color: #555;
            line-height: 1.6;
        }

        .report-title {
            text-align: right;
        }

        .report-title h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .report-title div {
            font-size: 13px;
            color: #555;
            margin-top: 6px;
            line-height: 1.8;
        }

        /* Filters Card */
        .filters {
            margin: 0;
            padding: 20px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .filter-form {
            display: flex;
            align-items: end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            min-width: 140px;
            flex: 1;
        }

        .form-group.buttons {
            display: flex !important;
            flex-direction: row !important;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: nowrap;
            align-items: end;
            min-width: auto;
            flex: none;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 12px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        select, input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            transition: all 0.2s ease;
            color: #1f2937;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        select:focus, input:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1), 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        select:hover, input:hover {
            border-color: #9ca3af;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        button, .btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 80px;
            white-space: nowrap;
            flex-shrink: 0;
            margin: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        button:focus, .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        }

        .btn-primary, button:not([class*="btn-"]) {
            background: #0d6efd;
            color: white;
        }

        .btn-primary:hover, button:not([class*="btn-"]):hover {
            background: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #198754;
            color: white;
        }

        .btn-success:hover {
            background: #157347;
            transform: translateY(-1px);
        }

        /* Table Styles */
        .table-container {
            position: relative;
            overflow: hidden;
        }

        .table-wrapper {
            overflow-x: auto;
            overflow-y: visible;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            background: white;
            table-layout: fixed;
        }

        thead th {
            background: #f8fafc;
            color: #374151;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px 12px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        thead th:nth-child(1) { width: 8%; }  /* No column */
        thead th:nth-child(2) { width: 45%; } /* Item Name column */
        thead th:nth-child(3) { width: 22%; } /* Stock On Hand column */
        thead th:nth-child(4) { width: 25%; } /* Inventory Asset Value column */

        tbody tr {
            transition: background-color 0.15s ease;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody tr:hover {
            background: #f3f4f6;
        }

        td {
            padding: 16px 12px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        td:nth-child(1) { width: 8%; text-align: center; }  /* No column */
        td:nth-child(2) { width: 45%; } /* Item Name column */
        td:nth-child(3) { width: 22%; text-align: right; } /* Stock On Hand column */
        td:nth-child(4) { width: 25%; text-align: right; } /* Inventory Asset Value column */

        .amount {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
        }

        /* Category Headers */
        .category-header {
            background: #f1f3f5 !important;
            font-weight: 700;
            font-size: 14px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .category-header td {
            padding: 20px 12px;
            border-bottom: 2px solid #e5e7eb;
            text-align: left;
        }

        /* Total Row */
        .total-row {
            background: #f8fafc !important;
            font-weight: 700;
            font-size: 15px;
            border-top: 2px solid #e5e7eb;
        }

        .total-row td {
            padding: 20px 12px;
            border-bottom: none;
        }

        /* Stock Status Colors */
        .stock-positive {
            color: #059669;
            font-weight: 600;
        }

        .stock-zero {
            color: #6b7280;
            font-weight: 500;
        }

        .stock-negative {
            color: #dc2626;
            font-weight: 600;
        }

        /* Clickable Values */
        .clickable-value {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
            position: relative;
        }

        .clickable-value:hover {
            background: #e7f1ff;
            color: #0b5ed7;
            transform: translateY(-1px);
        }

        .clickable-value:focus {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
        }

        /* Mobile Cards Layout */
        .mobile-cards {
            display: none;
            padding: 16px;
        }

        .mobile-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .mobile-card-header {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 12px;
            color: #1f2937;
        }

        .mobile-card-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .mobile-card-row:last-child {
            border-bottom: none;
        }

        .mobile-card-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
        }

        .mobile-card-value {
            font-weight: 600;
            text-align: right;
        }

        .category-card {
            background: #f1f3f5 !important;
            border-color: #e5e7eb !important;
        }

        .category-card .mobile-card-header {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            margin-bottom: 0;
        }

        .total-card {
            background: #f8fafc !important;
            border: 2px solid #e5e7eb !important;
            margin-top: 16px;
        }

        /* Report Footer */
        .report-footer {
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 80px 24px;
            color: #6b7280;
        }

        .no-data h3 {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 8px 0;
            color: #374151;
        }

        .no-data p {
            margin: 4px 0;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .filter-form {
                flex-wrap: wrap;
                gap: 12px;
            }

            .form-group {
                min-width: 120px;
            }
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 16px;
            }


            .filters {
                padding: 16px;
            }

            .filter-form {
                flex-direction: column;
                gap: 12px;
            }

            .form-group {
                min-width: auto;
                flex: none;
            }

            .form-group.buttons {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between;
                gap: 8px;
                margin-top: 4px;
            }

            button, .btn {
                padding: 10px 12px;
                font-size: 12px;
                min-width: 70px;
                flex: 1;
            }

            /* Mobile Card Layout */
            .table-container {
                display: none;
            }

            .mobile-cards {
                display: block;
            }
        }

        @media (min-width: 769px) {
            .mobile-cards {
                display: none;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                font-size: 12px;
            }

            .page-container {
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .report-container {
                box-shadow: none;
                border-radius: 0;
            }


            .filters {
                display: none;
            }

            thead th {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .category-header {
                background: #f1f3f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .total-row {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .clickable-value {
                color: #1f2937 !important;
                text-decoration: none !important;
            }

            .report-footer::after {
                content: counter(page);
                position: fixed;
                bottom: 0;
                right: 0;
            }

            /* Ensure page breaks */
            .category-header {
                page-break-inside: avoid;
                page-break-after: avoid;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        /* High Contrast for Accessibility */
        @media (prefers-contrast: high) {
            .clickable-value {
                text-decoration: underline;
            }

            button, .btn {
                border: 2px solid currentColor;
            }
        }

        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Inventory Valuation Summary">
                    @if($itemTypeId)
                        @php
                            $selectedItemType = $itemTypes->firstWhere('id', $itemTypeId);
                        @endphp
                        @if($selectedItemType)
                        <div>{{ $selectedItemType->item_type }}</div>
                    @endif
                @else
                    <div>All Item Types</div>
                @endif
                <div>As On @businessDate($asOnDate)</div>
            </x-report-header>

            <div class="filters">
                <form action="{{ route('general-items.inventory-valuation-summary') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="item_id">All Items</label>
                        <select name="item_id" id="item_id" aria-label="Select item">
                            <option value="">All Items</option>
                            @foreach($generalItems as $item)
                                <option value="{{ $item->id }}" {{ $itemId == $item->id ? 'selected' : '' }}>
                                    {{ $item->item_name }} ({{ $item->item_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="item_type_id">All Item Types</label>
                        <select name="item_type_id" id="item_type_id" aria-label="Select item type">
                            <option value="">All Item Types</option>
                            @foreach($itemTypes as $itemType)
                                <option value="{{ $itemType->id }}" {{ $itemTypeId == $itemType->id ? 'selected' : '' }}>
                                    {{ $itemType->item_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stock_filter">All Records</label>
                        <select name="stock_filter" id="stock_filter" aria-label="Select stock filter">
                            <option value="all" {{ $stockFilter == 'all' ? 'selected' : '' }}>All Records</option>
                            <option value="greater_than_zero" {{ $stockFilter == 'greater_than_zero' ? 'selected' : '' }}>Stock Greater Than 0</option>
                            <option value="equal_to_zero" {{ $stockFilter == 'equal_to_zero' ? 'selected' : '' }}>Stock Equal To 0</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="as_on_date">Date</label>
                        <input type="date" name="as_on_date" id="as_on_date" value="{{ $asOnDate }}" aria-label="Select date">
                    </div>

                    <div class="form-group buttons">
                        <button type="submit" aria-label="Search inventory">Search</button>
                        <button type="button" onclick="window.print()" class="btn-secondary" aria-label="Print report">Print</button>
                        <a href="{{ route('general-items.dashboard') }}" class="btn btn-secondary" aria-label="Go back to dashboard">Back</a>
                    </div>
                </form>
            </div>

            @if($groupedItems && $groupedItems->count() > 0)
                <div class="table-container">
                    <div class="table-wrapper">
                        <table role="table" aria-label="Inventory valuation summary">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Stock On Hand</th>
                                    <th scope="col">Inventory Asset Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rowNumber = 1;
                                @endphp
                                @foreach($groupedItems as $itemType => $items)
                                    <tr class="category-header">
                                        <td colspan="4" scope="colgroup"><strong>{{ strtoupper($itemType) }}</strong></td>
                                    </tr>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>{{ $rowNumber++ }}</td>
                                            <td>{{ $item['item_name'] }}</td>
                                            <td class="amount {{ $item['current_stock'] > 0 ? 'stock-positive' : ($item['current_stock'] == 0 ? 'stock-zero' : 'stock-negative') }}">
                                                {{ number_format(round($item['current_stock']), 0) }}
                                            </td>
                                            <td class="amount">
                                                <a href="{{ route('general-items.detailed-inventory-valuation', $item['id']) }}" 
                                                   class="clickable-value"
                                                   title="Click to view detailed inventory valuation for {{ $item['item_name'] }}"
                                                   aria-label="View detailed valuation for {{ $item['item_name'] }}">
                                                    {{ number_format(round($item['inventory_asset_value']), 0) }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="2" scope="row"><strong>Total</strong></td>
                                    <td class="amount"><strong>{{ number_format(round($totalStock), 0) }}</strong></td>
                                    <td class="amount"><strong>{{ number_format(round($totalValue), 0) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Cards Layout -->
                <div class="mobile-cards" aria-label="Inventory items">
                    @php
                        $mobileRowNumber = 1;
                    @endphp
                    @foreach($groupedItems as $itemType => $items)
                        <div class="mobile-card category-card">
                            <div class="mobile-card-header">{{ strtoupper($itemType) }}</div>
                        </div>
                        @foreach($items as $item)
                            <div class="mobile-card">
                                <div class="mobile-card-header">{{ $mobileRowNumber++ }}. {{ $item['item_name'] }}</div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">Stock On Hand:</span>
                                    <span class="mobile-card-value amount {{ $item['current_stock'] > 0 ? 'stock-positive' : ($item['current_stock'] == 0 ? 'stock-zero' : 'stock-negative') }}">
                                        {{ number_format(round($item['current_stock']), 0) }}
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">Asset Value:</span>
                                    <span class="mobile-card-value amount">
                                        <a href="{{ route('general-items.detailed-inventory-valuation', $item['id']) }}" 
                                           class="clickable-value"
                                           aria-label="View detailed valuation for {{ $item['item_name'] }}">
                                            {{ number_format(round($item['inventory_asset_value']), 0) }}
                                        </a>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                    
                    <div class="mobile-card total-card">
                        <div class="mobile-card-header">Total Summary</div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Total Stock:</span>
                            <span class="mobile-card-value amount"><strong>{{ number_format(round($totalStock), 0) }}</strong></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Total Value:</span>
                            <span class="mobile-card-value amount"><strong>{{ number_format(round($totalValue), 0) }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="report-footer">
                    <p>Print Date/Time :: {{ now()->format('d-m-Y H:i:s') }}</p>
                    
                </div>
            @else
                <div class="no-data">
                    <h3>No Data Found</h3>
                    <p>No inventory items found for the selected criteria.</p>
                    <p>Try adjusting your filters or date range.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filters = ['item_id', 'item_type_id', 'stock_filter'];
            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', function() {
                        document.querySelector('form').submit();
                    });
                }
            });
        });
    </script>
</body>
</html>
