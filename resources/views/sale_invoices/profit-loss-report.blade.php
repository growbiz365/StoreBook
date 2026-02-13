<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item/Arm Profit and Loss Report - {{ $business ? $business->business_name : 'Business' }} - Sales Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            background: #f8fafc;
        }

        .page-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
            box-sizing: border-box;
            background: white;
        }

        .report-container {
            width: 100%;
            max-width: 100%;
            position: relative;
            overflow-x: visible;
        }

        /* Standard report header layout (business left, report details right) */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            gap: 24px;
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
            height: 35px;
            margin-bottom: 2px;
        }

        .business-info h2 {
            margin: 2px 0;
            font-size: 14px;
            font-weight: 600;
        }

        .business-info-details {
            font-size: 10px;
            color: #444;
            line-height: 1.3;
        }

        .report-title {
            text-align: right;
        }

        .report-title h2 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .report-title div {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
            background: #fff;
        }

        .summary-card h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .summary-card.profit .summary-value {
            color: #059669;
        }

        .summary-card.loss .summary-value {
            color: #dc2626;
        }

        .filters {
            margin: 12px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-inputs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 12px;
            flex: 1;
        }

        .filter-buttons {
            display: flex;
            gap: 6px;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
            min-width: 130px;
            max-width: 160px;
            flex: 1 1 130px;
        }

        label {
            display: block;
            margin-bottom: 3px;
            font-weight: 500;
            font-size: 11px;
            color: #444;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 5px 8px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            font-size: 11px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            height: 28px;
        }

        button {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 500;
            color: white;
            background-color: #0d6efd;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.2s;
            height: 28px;
        }

        button:hover {
            background-color: #0b5ed7;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .table-container {
            margin-top: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow-x: auto;
            width: 100%;
            max-width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            background: #fff;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 11px;
            color: #1a1a1a;
            border-bottom: 2px solid #333;
            padding: 8px 6px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 1;
            white-space: nowrap;
        }

        th.text-right {
            text-align: right;
        }

        td {
            border-bottom: 1px solid #f0f0f0;
            padding: 6px;
            background-color: #fff;
            vertical-align: middle;
            white-space: nowrap;
        }
        
        /* Column width adjustments for compact display */
        th:nth-child(1), td:nth-child(1) { width: 7%; } /* Date */
        th:nth-child(2), td:nth-child(2) { width: 7.5%; } /* Invoice */
        th:nth-child(3), td:nth-child(3) { 
            width: 18.5%; 
            white-space: normal; 
            word-wrap: break-word; 
            overflow-wrap: break-word;
            line-height: 1.3;
        } /* Item */
        th:nth-child(4), td:nth-child(4) { width: 6%; } /* Type */
        th:nth-child(5), td:nth-child(5) { width: 6.5%; } /* Qty */
        th:nth-child(6), td:nth-child(6) { width: 9%; } /* Unit Cost */
        th:nth-child(7), td:nth-child(7) { width: 9%; } /* Total Cost */
        th:nth-child(8), td:nth-child(8) { width: 9%; } /* Sale Rate */
        th:nth-child(9), td:nth-child(9) { width: 9%; } /* Total Sales */
        th:nth-child(10), td:nth-child(10) { width: 9.5%; } /* PNL */

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .amount {
            font-family: 'Inter', monospace;
            font-weight: 600;
            font-size: 11px;
        }

        .profit {
            color: #059669;
        }

        .loss {
            color: #dc2626;
        }

        tfoot td {
            background: #f8f9fa;
            font-weight: 700;
            border-top: 3px solid #333;
            font-size: 11px;
            padding: 8px 6px;
        }

        .invoice-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }

        .invoice-link:hover {
            text-decoration: underline;
        }

        .item-type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .item-type-general {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .item-type-arm {
            background-color: #fef3c7;
            color: #92400e;
        }

        @media print {
            body {
                background: white;
            }
            .page-container {
                padding: 0;
                max-width: 100%;
            }
            .filters {
                display: none;
            }
            table {
                width: 100%;
                font-size: 10px;
            }
            th, td {
                padding: 4px;
            }
            th {
                background-color: #f8f8f8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .invoice-link {
                color: #000;
                text-decoration: none;
            }
        }

        @media screen and (max-width: 768px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-group {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Item/Arm Profit and Loss Report">
                <div>Generated on @businessDateTime(now())</div>
            </x-report-header>

            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Sales</h4>
                    <div class="summary-value">{{ number_format($totalSales, 2) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Cost</h4>
                    <div class="summary-value">{{ number_format($totalCost, 2) }}</div>
                </div>
                <div class="summary-card {{ $totalProfit >= 0 ? 'profit' : 'loss' }}">
                    <h4>Net Profit / Loss</h4>
                    <div class="summary-value">{{ number_format($totalProfit, 2) }}</div>
                </div>
            </div>

            <div class="filters">
                <form action="{{ route('sale-invoices.profit-loss-report') }}" method="GET" class="filter-form">
                    <div class="filter-inputs">
                        <div class="form-group">
                            <label for="invoice_number">Invoice Number</label>
                            <input type="text" name="invoice_number" id="invoice_number" value="{{ $invoiceNumber }}" placeholder="SI-1234">
                        </div>
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}">
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" name="to_date" id="to_date" value="{{ $toDate }}">
                        </div>
                    </div>
                    <div class="filter-buttons">
                        <button type="submit">Apply Filters</button>
                        <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                        <a href="{{ route('sale-invoices.index') }}">
                            <button type="button" class="btn-secondary">Back to Sales</button>
                        </a>
                    </div>
                </form>
            </div>

            @if(count($reportData) > 0)
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Item</th>
                                <th class="text-center">Type</th>
                                <th class="text-right">Qty Sold</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Total Cost</th>
                                <th class="text-right">Sale Rate</th>
                                <th class="text-right">Total Sales</th>
                                <th class="text-right">PNL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $row)
                                <tr>
                                    <td>@businessDate($row['date'])</td>
                                    <td>
                                        <a href="{{ route('sale-invoices.show', $row['invoice_id']) }}" target="_blank" class="invoice-link">
                                            {{ $row['invoice_number'] }}
                                        </a>
                                    </td>
                                    <td style="white-space: normal; word-wrap: break-word;">{{ $row['item_name'] }}</td>
                                    <td class="text-center">
                                        <span class="item-type-badge item-type-{{ $row['item_type'] }}">
                                            {{ ucfirst($row['item_type']) }}
                                        </span>
                                    </td>
                                    <td class="text-right amount">{{ number_format(round($row['quantity']), 0) }}</td>
                                    <td class="text-right amount">{{ number_format($row['unit_cost'], 2) }}</td>
                                    <td class="text-right amount">{{ number_format($row['total_cost'], 2) }}</td>
                                    <td class="text-right amount">{{ number_format($row['sale_rate'], 2) }}</td>
                                    <td class="text-right amount">{{ number_format($row['total_sales'], 2) }}</td>
                                    <td class="text-right amount {{ $row['profit_loss'] >= 0 ? 'profit' : 'loss' }}">
                                        {{ number_format($row['profit_loss'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Totals:</strong></td>
                                <td class="text-right amount">-</td>
                                <td class="text-right amount">{{ number_format($totalUnitCost, 2) }}</td>
                                <td class="text-right amount">{{ number_format($totalCost, 2) }}</td>
                                <td class="text-right amount">{{ number_format($totalSaleRate, 2) }}</td>
                                <td class="text-right amount">{{ number_format($totalSales, 2) }}</td>
                                <td class="text-right amount {{ $totalProfit >= 0 ? 'profit' : 'loss' }}">
                                    {{ number_format($totalProfit, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="margin-top: 12px; text-align: right; font-size: 10px; color: #666;">
                    <p>Generated by: {{ auth()->user() ? auth()->user()->name : 'System' }} | Print time: @businessDateTime(now())</p>
                    <p>Total Records: {{ count($reportData) }}</p>
                </div>
            @else
                <div style="text-align: center; padding: 20px; color: #666;">
                    <p style="font-size: 13px; margin-bottom: 8px;">No data found for the selected criteria.</p>
                    <p style="font-size: 11px;">Try adjusting your filters or date range.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>

