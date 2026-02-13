<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Items Stock Ledger Report - {{ $business->name ?? 'Business' }} - General Items Management - StoreBook</title>
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
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            box-sizing: border-box;
            background: white;
        }

        .report-container {
            width: 100%;
            position: relative;
        }

        .business-info {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
        }

        .business-logo {
            height: 50px;
            margin-bottom: 4px;
        }

        .business-info h2 {
            margin: 4px 0;
            font-size: 18px;
            font-weight: 600;
        }

        .business-info-details {
            font-size: 11px;
            color: #444;
        }

        .report-title {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            border-bottom: 1px solid #333;
        }

        .report-title h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .report-title div {
            font-size: 13px;
            color: #444;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .summary-card h4 {
            margin: 0 0 6px 0;
            font-size: 11px;
            font-weight: 500;
            color: #444;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 600;
        }

        .filters {
            margin: 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            justify-content: center;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 180px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 12px;
            color: #444;
        }

        select, input {
            width: 100%;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
        }

        button {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            color: white;
            background-color: #0d6efd;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
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

        .btn-success {
            background-color: #198754;
        }

        .btn-success:hover {
            background-color: #157347;
        }

        .table-container {
            margin-top: 20px;
            overflow-x: auto;
            border: 1px solid #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background-color: #fff;
            font-weight: 600;
            text-transform: none;
            font-size: 13px;
            color: #000;
            border: 1px solid #333;
            padding: 8px;
        }

        td {
            border: 1px solid #333;
            padding: 8px;
            background-color: #fff;
        }

        .amount {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 500;
        }

        .stock-in {
            color: #009900;
            font-weight: 600;
        }

        .stock-out {
            color: #cc0000;
            font-weight: 600;
        }

        .running-balance {
            font-weight: 600;
        }

        .running-balance.positive {
            color: #009900;
        }

        .running-balance.negative {
            color: #cc0000;
        }

        .transaction-type {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-align: center;
        }

        .transaction-type.in {
            background-color: #d4edda;
            color: #155724;
        }

        .transaction-type.out {
            background-color: #f8d7da;
            color: #721c24;
        }

        .transaction-type.other {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .pagination-info {
            margin: 15px 0;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .per-page-selector {
            margin-left: 15px;
            padding: 4px 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 12px;
        }

        @media print {
            body {
                background: white;
            }
            .page-container {
                padding: 0;
            }
            .filters {
                display: none;
            }
            th {
                background-color: #f8f8f8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media screen and (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-group {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    
    <div class="page-container">
        <div class="report-container">
            <div class="business-info">
                @if($business && $business->logo)
                    <img src="{{ asset('storage/' . $business->logo) }}" alt="Business Logo" class="business-logo">
                @endif
                <h2>{{ $business ? $business->name : 'Business Name' }}</h2>
                <div class="business-info-details">
                    @if($business)
                        {{ $business->address ?? 'Address' }}<br>
                        Phone: {{ $business->phone ?? 'Phone' }} | Email: {{ $business->email ?? 'Email' }}
                    @else
                        Address: Not Available<br>
                        Phone: Not Available | Email: Not Available
                    @endif
                </div>
            </div>

            <div class="report-title">
                <h2>General Items Stock Ledger Report</h2>
                @if($itemId)
                    @php
                        $selectedItem = $generalItems->firstWhere('id', $itemId);
                    @endphp
                    @if($selectedItem)
                        <div>{{ $selectedItem->item_name }} ({{ $selectedItem->item_code }})</div>
                    @endif
                @endif
                <div>{{ $dateFrom }} to {{ $dateTo }}</div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total In</h4>
                    <div class="summary-value stock-in">{{ number_format(round($summary['total_in'] ?? 0), 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Out</h4>
                    <div class="summary-value stock-out">{{ number_format(round($summary['total_out'] ?? 0), 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Current Stock</h4>
                    <div class="summary-value">{{ number_format(round($summary['current_stock'] ?? 0), 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Net Movement</h4>
                    <div class="summary-value {{ ($summary['net_movement'] ?? 0) >= 0 ? 'stock-in' : 'stock-out' }}">
                        {{ number_format(round($summary['net_movement'] ?? 0), 0) }}
                    </div>
                </div>
                <div class="summary-card">
                    <h4>Transactions</h4>
                    <div class="summary-value">{{ number_format($summary['total_transactions'] ?? 0) }}</div>
                </div>
            </div>

            <div class="filters">
                <form action="{{ route('general-items-stock-ledger') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="item_id">Select Item</label>
                        <select name="item_id" id="item_id">
                            <option value="">All Items</option>
                            @foreach($generalItems as $item)
                                <option value="{{ $item->id }}" {{ $itemId == $item->id ? 'selected' : '' }}>
                                    {{ $item->item_name }} ({{ $item->item_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_from">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}">
                    </div>

                    <div class="form-group">
                        <label for="date_to">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}">
                    </div>

                    <div class="form-group">
                        <label for="transaction_type">Transaction Type</label>
                        <select name="transaction_type" id="transaction_type">
                            <option value="">All Types</option>
                            <option value="opening" {{ $transactionType == 'opening' ? 'selected' : '' }}>Opening Stock</option>
                            <option value="purchase" {{ $transactionType == 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="issue" {{ $transactionType == 'issue' ? 'selected' : '' }}>Issue</option>
                            <option value="sale" {{ $transactionType == 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="adjustment" {{ $transactionType == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="reversal" {{ $transactionType == 'reversal' ? 'selected' : '' }}>Reversal</option>
                            <option value="edit" {{ $transactionType == 'edit' ? 'selected' : '' }}>Edit</option>
                        </select>
                    </div>

                    <button type="submit">Apply Filters</button>
                    <a href="{{ route('general-items-stock-ledger.export', request()->query()) }}">
                        <button type="button" class="btn-success">Export CSV</button>
                    </a>
                    <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                    <a href="{{ route('general-items.dashboard') }}">
                        <button type="button" class="btn-secondary">Back</button>
                    </a>
                </form>
            </div>

            @if($ledgerEntries && $ledgerEntries->count() > 0)
                <div class="pagination-info">
                    Showing {{ $ledgerEntries ? ($ledgerEntries->firstItem() ?? 0) : 0 }} to {{ $ledgerEntries ? ($ledgerEntries->lastItem() ?? 0) : 0 }} 
                    of {{ $ledgerEntries ? $ledgerEntries->total() : 0 }} entries
                    <select name="per_page" id="per_page" onchange="changePerPage(this.value)" class="per-page-selector">
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item Name</th>
                                <th>Item Code</th>
                                <th>Transaction Type</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Total Cost</th>
                                
                                <th>Running Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ledgerEntries as $entry)
                                <tr>
                                    <td>@businessDate($entry->transaction_date)</td>
                                    <td>{{ $entry->item->item_name ?? 'N/A' }}</td>
                                    <td>{{ $entry->item->item_code ?? 'N/A' }}</td>
                                    <td>
                                        <span class="transaction-type {{ in_array($entry->transaction_type, ['opening', 'purchase']) ? 'in' : (in_array($entry->transaction_type, ['issue', 'sale']) ? 'out' : 'other') }}">
                                            {{ ucfirst($entry->transaction_type) }}
                                        </span>
                                    </td>
                                    <td class="amount {{ in_array($entry->transaction_type, ['opening', 'purchase']) ? 'stock-in' : (in_array($entry->transaction_type, ['issue', 'sale']) ? 'stock-out' : '') }}">
                                        {{ number_format(round($entry->quantity), 0) }}
                                    </td>
                                    <td class="amount">{{ number_format(round($entry->unit_cost ?? 0), 0) }}</td>
                                    <td class="amount">{{ number_format(round($entry->quantity * ($entry->unit_cost ?? 0)), 0) }}</td>
                                    
                                    <td class="amount running-balance {{ $entry->running_balance >= 0 ? 'positive' : 'negative' }}">
                                        {{ number_format(round($entry->running_balance), 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Report Footer -->
                <div style="margin-top: 20px; text-align: right; font-size: 11px; color: #666;">
                    <p>Generated by: {{ auth()->user() ? auth()->user()->name : 'System' }} | Print time: @businessDateTime(now())</p>
                    <p>Total Records: {{ $ledgerEntries ? $ledgerEntries->total() : 0 }}</p>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <p>No ledger entries found for the selected criteria.</p>
                    <p>Try adjusting your filters or date range.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function changePerPage(value) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', value);
            window.location.href = url.toString();
        }

        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filters = ['item_id', 'transaction_type'];
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
