<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arms Stock Ledger Report - {{ $business ? $business->name : 'Business' }}</title>
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
                <h2>Arms Stock Ledger Report</h2>
                <div>{{ $dateFrom }} to {{ $dateTo }}</div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Arms</h4>
                    <div class="summary-value">{{ number_format($summary['total_arms'] ?? 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Available</h4>
                    <div class="summary-value stock-in">{{ number_format($summary['available'] ?? 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Sold</h4>
                    <div class="summary-value stock-out">{{ number_format($summary['sold'] ?? 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Transferred</h4>
                    <div class="summary-value">{{ number_format($summary['transferred'] ?? 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Transactions</h4>
                    <div class="summary-value">{{ number_format($summary['total_transactions'] ?? 0) }}</div>
                </div>
            </div>

            <div class="filters">
                <form action="{{ route('arms-stock-ledger') }}" method="GET" class="filter-form">
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
                            <option value="sale" {{ $transactionType == 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="transfer" {{ $transactionType == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="repair" {{ $transactionType == 'repair' ? 'selected' : '' }}>Repair</option>
                            <option value="decommission" {{ $transactionType == 'decommission' ? 'selected' : '' }}>Decommission</option>
                            <option value="price_adjustment" {{ $transactionType == 'price_adjustment' ? 'selected' : '' }}>Price Adjustment</option>
                            <option value="edit" {{ $transactionType == 'edit' ? 'selected' : '' }}>Edit</option>
                            <option value="delete" {{ $transactionType == 'delete' ? 'selected' : '' }}>Delete</option>
                        </select>
                    </div>

                    <button type="submit">Apply Filters</button>
                    <a href="{{ route('arms-stock-ledger.export', request()->query()) }}">
                        <button type="button" class="btn-success">Export CSV</button>
                    </a>
                    <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                    <a href="{{ route('arms.dashboard') }}">
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
                                <th>Arm Name</th>
                                <th>Arm Type</th>
                                <th>Transaction Type</th>
                                <th>Price</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ledgerEntries as $entry)
                                <tr>
                                    <td>@businessDate($entry->transaction_date)</td>
                                    <td>{{ $entry->arm->arm_title ?? 'N/A' }}</td>
                                    <td>{{ $entry->arm->armType->arm_type ?? 'N/A' }}</td>
                                    <td>
                                        <span class="transaction-type {{ in_array($entry->action, ['opening', 'purchase']) ? 'in' : (in_array($entry->action, ['sale', 'transfer']) ? 'out' : 'other') }}">
                                            {{ ucfirst($entry->action) }}
                                        </span>
                                    </td>
                                    <td class="amount">{{ number_format($entry->price ?? 0, 2) }}</td>
                                    <td>{{ $entry->action ?? 'N/A' }}</td>
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
            const filters = ['transaction_type'];
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
