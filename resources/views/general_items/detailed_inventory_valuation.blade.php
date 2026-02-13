<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Valuation for {{ $item->item_name }} - {{ $business->name ?? 'Business' }} - General Items Management - StoreBook</title>
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

        thead th:nth-child(1) { width: 12%; } /* Date column */
        thead th:nth-child(2) { width: 35%; } /* Transaction Details column */
        thead th:nth-child(3) { width: 12%; } /* Quantity column */
        thead th:nth-child(4) { width: 12%; } /* Unit Cost column */
        thead th:nth-child(5) { width: 12%; } /* Stock On Hand column */
        thead th:nth-child(6) { width: 17%; } /* Inventory Asset Value column */

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

        td:nth-child(1) { width: 12%; } /* Date column */
        td:nth-child(2) { width: 35%; } /* Transaction Details column */
        td:nth-child(3) { width: 12%; text-align: right; } /* Quantity column */
        td:nth-child(4) { width: 12%; text-align: right; } /* Unit Cost column */
        td:nth-child(5) { width: 12%; text-align: right; } /* Stock On Hand column */
        td:nth-child(6) { width: 17%; text-align: right; } /* Inventory Asset Value column */

        .amount {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
        }

        /* Transaction Type Colors */
        .quantity-positive {
            color: #059669;
            font-weight: 600;
        }

        .quantity-negative {
            color: #dc2626;
            font-weight: 600;
        }

        .quantity-zero {
            color: #6b7280;
            font-weight: 500;
        }

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

        .transaction-opening {
            font-weight: 700;
            color: #0d6efd;
            background: #e7f1ff;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .transaction-purchase {
            color: #059669;
            font-weight: 600;
        }

        .transaction-sale {
            color: #dc2626;
            font-weight: 600;
        }

        .transaction-adjustment {
            color: #d97706;
            font-weight: 600;
        }

        .transaction-return {
            color: #7c3aed;
            font-weight: 600;
        }

        .transaction-reversal {
            color: #ea580c;
            font-weight: 600;
        }

        /* Clickable Transaction Links */
        .clickable-value {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px dotted currentColor;
            display: inline-block;
        }

        .clickable-value:hover {
            text-decoration: underline;
            border-bottom-color: transparent;
            opacity: 0.8;
        }

        .transaction-purchase .clickable-value:hover {
            color: #047857;
        }

        .transaction-sale .clickable-value:hover {
            color: #b91c1c;
        }

        .transaction-return .clickable-value:hover {
            color: #6d28d9;
        }

        .transaction-reversal .clickable-value:hover {
            color: #c2410c;
        }

        .transaction-adjustment .clickable-value:hover {
            color: #b45309;
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
            div[style*="Total Records"] {
                float: right !important;
                margin: 15px 0 10px 0 !important;
            }

            thead th {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .clickable-value {
                color: #1f2937 !important;
                text-decoration: none !important;
                border-bottom: none !important;
            }
            
            .clickable-value:hover {
                text-decoration: none !important;
            }

            .report-footer::after {
                content: counter(page);
                position: fixed;
                bottom: 0;
                right: 0;
            }

            /* Ensure page breaks */
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
            <x-report-header :business="$business" title="Inventory Valuation for {{ $item->item_name }}">
                <div>From {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} To {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</div>
            </x-report-header>

            <div class="filters">
                <form action="{{ route('general-items.detailed-inventory-valuation', $item->id) }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="item_id">All Items</label>
                        <select name="item_id" id="item_id" aria-label="Select item">
                            @foreach($allItems as $allItem)
                                <option value="{{ $allItem->id }}" {{ $item->id == $allItem->id ? 'selected' : '' }}>
                                    {{ $allItem->item_name }} ({{ $allItem->item_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_from">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" aria-label="Select from date">
                    </div>

                    <div class="form-group">
                        <label for="date_to">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" aria-label="Select to date">
                    </div>

                    <div class="form-group buttons">
                        <button type="submit" aria-label="Search inventory">Search</button>
                        <button type="button" onclick="window.print()" class="btn-secondary" aria-label="Print report">Print</button>
                        <a href="{{ route('general-items.inventory-valuation-summary') }}" class="btn btn-secondary" aria-label="Go back to summary">Back</a>
                    </div>
                </form>
            </div>

            @if($transactions && count($transactions) > 0)
                <!-- Total Records Info -->
                <div style="margin: 15px 0 10px 0; text-align: right; font-size: 13px; color: #444; font-weight: 600; padding: 8px 12px; background-color: #f8f9fa; border-radius: 4px; display: inline-block; float: right;">
                    Total Records: <strong>{{ count($transactions) }}</strong>
                </div>
                <div style="clear: both;"></div>

                <div class="table-container" style="margin-top: 10px;">
                    <div class="table-wrapper">
                        <table role="table" aria-label="Detailed inventory valuation">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Transaction Details</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Unit Cost</th>
                                    <th scope="col">Stock On Hand</th>
                                    <th scope="col">Inventory Asset Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>@businessDate($transaction['date'])</td>
                                        <td class="@php
                                            $details = $transaction['transaction_details'];
                                            if ($details == '*** Opening Stock ***') {
                                                echo 'transaction-opening';
                                            } elseif (strpos($details, 'Purchase') !== false) {
                                                echo 'transaction-purchase';
                                            } elseif (strpos($details, 'Sale') !== false) {
                                                echo 'transaction-sale';
                                            } elseif (strpos($details, 'Return') !== false) {
                                                echo 'transaction-return';
                                            } elseif (strpos($details, 'Reversal') !== false) {
                                                echo 'transaction-reversal';
                                            } else {
                                                echo 'transaction-adjustment';
                                            }
                                        @endphp">
                                            @if(!empty($transaction['transaction_link']))
                                                <a href="{{ $transaction['transaction_link'] }}" 
                                                   class="clickable-value" 
                                                   target="_blank"
                                                   title="Click to view transaction details">
                                                    {{ $transaction['transaction_details'] }}
                                                </a>
                                            @else
                                                {{ $transaction['transaction_details'] }}
                                            @endif
                                        </td>
                                        <td class="amount {{ $transaction['quantity'] > 0 ? 'quantity-positive' : ($transaction['quantity'] < 0 ? 'quantity-negative' : 'quantity-zero') }}">
                                            {{ $transaction['quantity'] > 0 ? '+' : '' }}{{ number_format(round($transaction['quantity']), 0) }}
                                        </td>
                                        <td class="amount">{{ number_format(round($transaction['unit_cost']), 0) }}</td>
                                        <td class="amount {{ $transaction['stock_on_hand'] > 0 ? 'stock-positive' : ($transaction['stock_on_hand'] == 0 ? 'stock-zero' : 'stock-negative') }}">
                                            {{ number_format(round($transaction['stock_on_hand']), 0) }}
                                        </td>
                                        <td class="amount">{{ number_format(round($transaction['inventory_asset_value']), 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="report-footer">
                    <p>Generated by: {{ auth()->user()->name }} | Print time: @businessDateTime(now())</p>
                    <p style="margin-top: 5px;">Powered By GrowBusiness 365</p>
                </div>
            @else
                <div class="no-data">
                    <h3>No Transactions Found</h3>
                    <p>No transactions found for {{ $item->item_name }} in the selected date range.</p>
                    <p>Try adjusting your date range or select a different item.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filters = ['item_id'];
            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', function() {
                        if (this.value !== '{{ $item->id }}') {
                            // Redirect to different item
                            window.location.href = "{{ route('general-items.detailed-inventory-valuation', '') }}/" + this.value + "?date_from={{ $dateFrom }}&date_to={{ $dateTo }}";
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
