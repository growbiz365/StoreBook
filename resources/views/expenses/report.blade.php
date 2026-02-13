<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report - {{ $business->business_name }} - Expense Management - StoreBook</title>
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

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
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

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 10px;
            border-radius: 4px;
        }

        .summary-card h4 {
            margin: 0 0 6px 0;
            font-size: 12px;
            font-weight: 500;
            color: #444;
        }

        .summary-value {
            font-size: 16px;
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
            min-width: 200px;
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

        .expense-amount {
            color: #cc0000;
            font-weight: 600;
        }

        .total-row td {
            font-weight: 600;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .summary-section {
            margin: 20px 0;
            border: 1px solid #333;
            border-radius: 4px;
        }

        .summary-section h3 {
            margin: 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #333;
            font-size: 14px;
            font-weight: 600;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .summary-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 12px;
        }

        .summary-table .amount {
            text-align: right;
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
            .total-row {
                background-color: #f8f8f8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Expense Report">
                <div><strong>Duration:</strong> @businessDate(request('from_date', now()->startOfMonth())) to @businessDate(request('to_date', now()))</div>
            </x-report-header>

            <!-- <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Expenses</h4>
                    <div class="summary-value">{{ number_format(round($totalExpense), 0) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Entries</h4>
                    <div class="summary-value">{{ $totalEntries }}</div>
                </div>
                <div class="summary-card">
                    <h4>Average per Entry</h4>
                    <div class="summary-value">{{ $totalEntries > 0 ? number_format(round($totalExpense / $totalEntries), 0) : '0' }}</div>
                </div>
            </div> -->

            <div class="filters">
                <form action="{{ route('expenses.report') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="from_date">From Date</label>
                        <input type="date" name="from_date" id="from_date"
                            value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label for="to_date">To Date</label>
                        <input type="date" name="to_date" id="to_date"
                            value="{{ request('to_date', now()->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label for="expense_head_id">Expense Head</label>
                        <select name="expense_head_id" id="expense_head_id">
                            <option value="">All Expense Heads</option>
                            @foreach($expenseHeads as $expenseHead)
                                <option value="{{ $expenseHead->id }}" {{ request('expense_head_id') == $expenseHead->id ? 'selected' : '' }}>
                                    {{ $expenseHead->expense_head }}
                                </option>
                            @endforeach
                        </select>
                    </div>



                    <button type="submit">Apply Filters</button>
                    @if($expenseEntries->count() > 0)
                        <button type="button" onclick="window.print()">Print Report</button>
                    @endif
                    <a href="{{ route('expenses.dashboard') }}" class="back-btn">
                        <button type="button" style="background-color: #6c757d;">Back</button>
                    </a>
                </form>
            </div>

            @if($expenseEntries->count() > 0)
                <!-- Summary by Expense Head -->
                <div class="summary-section">
                    <h3>Summary by Expense Head</h3>
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Expense Head</th>
                                <th>Count</th>
                                <th>Total Amount</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseByHead as $headName => $data)
                                <tr>
                                    <td>{{ $headName }}</td>
                                    <td>{{ $data['count'] }}</td>
                                    <td class="amount expense-amount">{{ number_format(round($data['total']), 0) }}</td>
                                    <td class="amount">{{ number_format(($data['total'] / $totalExpense) * 100, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Total Records Info -->
                <div style="margin: 15px 0 10px 0; text-align: right; font-size: 13px; color: #444; font-weight: 600; padding: 8px 12px; background-color: #f8f9fa; border-radius: 4px; display: inline-block; float: right;">
                    Total Records: <strong>{{ $expenseEntries->count() }}</strong>
                </div>
                <div style="clear: both;"></div>

                <!-- Detailed Expense Entries -->
                <div class="table-container" style="margin-top: 10px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Expense Head</th>
                                <th>Bank Account</th>
                                <th>Comments</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseEntries as $entry)
                                <tr>
                                    <td>{{ $entry->date_added->format('d-m-Y') }}</td>
                                    <td>{{ $entry->account->expenseHead->first() ? $entry->account->expenseHead->first()->expense_head : 'N/A' }}</td>
                                    <td>
                                        @if($entry->voucher && $entry->voucher->bank)
                                            {{ $entry->voucher->bank->chartOfAccount->name ?? $entry->voucher->bank->account_name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $entry->voucher->details ?? '-' }}</td>
                                    <td class="amount expense-amount">{{ number_format(round($entry->debit_amount), 0) }}</td>
                                </tr>
                            @endforeach

                            <!-- Total Row -->
                            <tr class="total-row">
                                <td colspan="4" style="text-align: center"><strong>Total</strong></td>
                                <td class="amount expense-amount"><strong>{{ number_format(round($totalExpense), 0) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Report Footer -->
                <div style="margin-top: 20px; text-align: right; font-size: 11px; color: #666;">
                    <p>Generated by: {{ auth()->user()->name }} | Print time: @businessDateTime(now())</p>
                    <p style="margin-top: 5px;">Powered By GrowBusiness 365</p>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <p>No expense entries found for the selected criteria.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html> 