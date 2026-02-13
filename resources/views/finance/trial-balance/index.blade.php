<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Balance - {{ $business->business_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
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

        .filters {
            margin: 20px 0;
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

        input[type="date"],
        select {
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
            background-color: #1e3a5f;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        button:hover {
            background-color: #2c5282;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .trial-balance-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }

        .trial-balance-table thead {
            background-color: #34495e;
            color: white;
        }

        .trial-balance-table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .trial-balance-table th:last-child,
        .trial-balance-table th:nth-child(2) {
            text-align: right;
        }

        .trial-balance-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .trial-balance-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .trial-balance-table td {
            padding: 10px 16px;
            vertical-align: top;
        }

        .trial-balance-table td:last-child,
        .trial-balance-table td:nth-child(2) {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 500;
        }

        .account-category {
            font-weight: 700;
            font-size: 14px;
            color: #1a1a1a;
            background-color: #ecf0f1;
            padding: 10px 16px;
        }

        .account-item {
            padding-left: 30px;
        }

        .account-item a {
            color: #1e3a5f;
            text-decoration: none;
            font-weight: 400;
        }

        .account-item a:hover {
            text-decoration: underline;
            color: #2c5282;
        }

        .total-row {
            font-weight: 700;
            background-color: #d5dbdb;
            border-top: 2px solid #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
        }

        .total-row td {
            padding: 12px 16px;
            font-size: 14px;
            color: #1e3a5f;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #bdc3c7;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }

        .excluded-accounts-notice {
            margin-bottom: 15px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
        }

        .excluded-accounts-notice h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 14px;
            font-weight: 600;
        }

        .excluded-accounts-notice p {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 13px;
            line-height: 1.5;
        }

        .excluded-accounts-notice ul {
            margin: 0;
            padding-left: 20px;
            color: #856404;
            font-size: 12px;
        }

        .excluded-accounts-notice li {
            margin-bottom: 4px;
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
            .trial-balance-table thead {
                background-color: #34495e !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .account-category {
                background-color: #ecf0f1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .total-row {
                background-color: #d5dbdb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Trial Balance">
                <div>Basis: {{ ucfirst($basis) }}</div>
                <div>As of @businessDate($asOfDate)</div>
            </x-report-header>

            @if($basis === 'cash' && !empty($trialBalanceData['excluded_accounts']))
            <div class="excluded-accounts-notice">
                <h4>Cash Basis Notice</h4>
                <p>
                    The following accounts are excluded in cash basis accounting as they represent non-cash transactions:
                </p>
                <ul>
                    @foreach($trialBalanceData['excluded_accounts'] as $excludedAccount)
                        <li>{{ $excludedAccount }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="filters">
                <form action="{{ route('trial-balance.index') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="as_of_date">As of Date</label>
                        <input type="date" id="as_of_date" name="as_of_date" value="{{ $asOfDate }}" required>
                    </div>
                    <div class="form-group">
                        <label for="basis">Accounting Basis</label>
                        <select id="basis" name="basis" required>
                            <option value="accrual" {{ $basis === 'accrual' ? 'selected' : '' }}>Accrual</option>
                            <option value="cash" {{ $basis === 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                    </div>
                    <button type="submit">Apply Filter</button>
                    <button type="button" onclick="window.print()">Print Report</button>
                    <a href="{{ route('finance.index') }}" style="text-decoration: none;">
                        <button type="button" class="btn-secondary">Back to Finance</button>
                    </a>
                </form>
            </div>

            <table class="trial-balance-table">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Net Debit</th>
                        <th>Net Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentType = null;
                        $typeLabels = [
                            'asset' => 'Assets',
                            'liability' => 'Liabilities',
                            'equity' => 'Equities',
                            'income' => 'Income',
                            'expense' => 'Expense'
                        ];
                    @endphp
                    @foreach($trialBalanceData['accounts'] as $account)
                        @if($currentType !== $account['type'])
                            @php $currentType = $account['type']; @endphp
                            <tr>
                                <td class="account-category" colspan="3">{{ $typeLabels[$account['type']] ?? ucfirst($account['type']) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="account-item">
                                <a href="{{ route('general-ledger.index', ['account_id' => $account['id'], 'as_of_date' => $asOfDate, 'basis' => $basis]) }}">
                                    {{ $account['name'] }}
                                </a>
                            </td>
                            <td>{{ number_format($account['debit'], 2) }}</td>
                            <td>{{ number_format($account['credit'], 2) }}</td>
                        </tr>
                    @endforeach
                    
                    @if(empty($trialBalanceData['accounts']))
                        <tr>
                            <td colspan="3" style="text-align: center; color: #999; font-style: italic; padding: 20px;">
                                No accounts found
                            </td>
                        </tr>
                    @endif

                    <tr class="total-row">
                        <td>Total for Trial Balance</td>
                        <td>{{ number_format($trialBalanceData['totals']['debit'], 2) }}</td>
                        <td>{{ number_format($trialBalanceData['totals']['credit'], 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <p>This is a computer generated report and does not require a signature.</p>
                <p>Generated by {{ config('app.name') }} on @businessDateTime(now())</p>
            </div>
        </div>
    </div>
</body>
</html>

