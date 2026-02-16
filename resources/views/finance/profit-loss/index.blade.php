<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Statement - {{ $business->business_name }}</title>
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
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            background: white;
            font-size: 14px;
        }

        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px;
            box-sizing: border-box;
            background: white;
        }

        .report-container {
            width: 100%;
            position: relative;
        }

        .back-button {
            margin-bottom: 30px;
        }

        .back-button a {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .back-button a:hover {
            background-color: #5a6268;
        }

        .back-button a svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .summary-card {
            background: white;
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .summary-card h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: 600;
            color: #666666;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            font-family: 'Inter', monospace;
        }

        .summary-value.positive {
            color: #28a745;
        }

        .summary-value.negative {
            color: #dc3545;
        }

        .filters {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
        }
        
        .filter-inputs {
            display: flex;
            flex-wrap: wrap;
            gap: 16px 24px;
            flex: 1;
        }
        
        .filter-buttons {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
        
        .filter-form .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
            min-width: 160px;
            max-width: 200px;
            flex: 0 0 auto;
        }
        
        .filter-form label {
            font-size: 13px;
            margin-bottom: 4px;
            font-weight: 500;
            color: #666666;
        }
        
        .filter-form input[type="date"] {
            font-size: 13px;
            padding: 8px 12px;
            height: 36px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        .filter-form select {
            font-size: 13px;
            padding: 8px 12px;
            height: 36px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            background-color: white;
            cursor: pointer;
        }
        
        .filter-buttons button,
        .filter-buttons .btn-secondary {
            font-size: 13px;
            padding: 8px 16px;
            height: 36px;
            min-width: 80px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .filter-buttons button {
            background-color: #007bff;
            color: white;
            border: none;
        }
        
        .filter-buttons button:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .filter-form a {
            text-decoration: none;
        }
        
        @media (max-width: 700px) {
            .filter-form {
                flex-direction: column;
                gap: 10px 0;
                align-items: stretch;
            }
            .filter-form .form-group {
                max-width: 100%;
            }
        }

        .profit-loss-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 30px;
            background: white;
        }

        .profit-loss-table th {
            background-color: white;
            font-weight: 700;
            font-size: 14px;
            color: #1a1a1a;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            font-family: 'Inter', sans-serif;
        }

        .profit-loss-table th:last-child {
            text-align: right;
        }

        .profit-loss-table td {
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
            font-family: 'Inter', sans-serif;
        }

        .profit-loss-table td:last-child {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 500;
        }

        .account-name {
            color: #1a1a1a;
            font-weight: 400;
        }

        .account-name.level-1 {
            font-weight: 700;
            font-size: 14px;
            padding-left: 0;
        }

        .account-name.level-4 {
            font-weight: 400;
            padding-left: 0;
            color: #1a1a1a;
        }

        .total-row {
            font-weight: 400;
            background-color: white;
        }

        .total-row td {
            padding: 12px 20px;
            font-weight: 400;
        }

        .total-row td:last-child {
            font-weight: 500;
        }

        .gross-profit-row {
            font-weight: 700;
            background-color: white;
        }

        .gross-profit-row td {
            padding: 12px 20px;
            font-weight: 700;
        }

        .gross-profit-row td:last-child {
            font-weight: 700;
        }

        .operating-profit-row {
            font-weight: 700;
            background-color: white;
        }

        .operating-profit-row td {
            padding: 12px 20px;
            font-weight: 700;
        }

        .operating-profit-row td:last-child {
            font-weight: 700;
        }

        .net-profit-row {
            font-weight: 700;
            background-color: white;
        }

        .net-profit-row.negative {
            background-color: white;
        }

        .net-profit-row td {
            padding: 12px 20px;
            font-weight: 700;
        }

        .net-profit-row td:last-child {
            font-weight: 700;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 12px;
            color: #666666;
        }

        .footer p {
            margin: 4px 0;
        }

        .excluded-accounts-notice {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
        }

        .excluded-accounts-notice h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
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
            .filters, .back-button {
                display: none;
            }
            .profit-loss-table th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .total-row {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .gross-profit-row {
                background-color: #e3f2fd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .operating-profit-row {
                background-color: #f3e5f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .net-profit-row {
                background-color: #e8f5e8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .net-profit-row.negative {
                background-color: #ffebee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            

            <x-report-header :business="$business" title="Profit & Loss Statement">
                <div>Basis: {{ ucfirst($basis) }}</div>
                <div>For the period {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}</div>
            </x-report-header>

                
            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Revenue</h4>
                    <div class="summary-value positive">Rs. {{ number_format($profitLossData['totals']['revenue'], 2) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Expenses</h4>
                    <div class="summary-value negative">Rs. {{ number_format($profitLossData['totals']['operating_expenses'] + $profitLossData['totals']['cost_of_goods_sold'] + $profitLossData['totals']['other_expenses'], 2) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Net Profit</h4>
                    <div class="summary-value {{ $profitLossData['totals']['net_profit'] >= 0 ? 'positive' : 'negative' }}">
                        Rs. {{ number_format($profitLossData['totals']['net_profit'], 2) }}
                    </div>
                </div>
            </div>

            @if($basis === 'cash' && !empty($profitLossData['excluded_accounts']))
            <div class="excluded-accounts-notice">
                <h4>
                    <svg style="width: 16px; height: 16px; margin-right: 6px; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Cash Basis Notice
                </h4>
                <p>
                    The following accounts are excluded in cash basis accounting as they represent non-cash transactions:
                </p>
                <ul>
                    @foreach($profitLossData['excluded_accounts'] as $excludedAccount)
                        <li>{{ $excludedAccount }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(isset($diagnostics) && $diagnostics)
            <div class="excluded-accounts-notice" style="background-color: #d1ecf1; border-color: #bee5eb;">
                <h4 style="color: #0c5460;">
                    <svg style="width: 16px; height: 16px; margin-right: 6px; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Journal Entry Diagnostics
                </h4>
                
                @if(isset($diagnostics['revenue']))
                <div style="margin-bottom: 15px;">
                    <strong style="color: #0c5460;">Revenue Account: {{ $diagnostics['revenue']['account_name'] }}</strong>
                    <ul style="color: #0c5460;">
                        <li>Total Entries: {{ $diagnostics['revenue']['total_entries'] }}</li>
                        <li>Total Credits: Rs. {{ number_format($diagnostics['revenue']['total_credits'], 2) }}</li>
                        <li>Total Debits: Rs. {{ number_format($diagnostics['revenue']['total_debits'], 2) }}</li>
                        <li>Net Balance: Rs. {{ number_format($diagnostics['revenue']['net_balance'], 2) }}</li>
                        <li>Normal Entries: {{ $diagnostics['revenue']['entries_by_type']['normal'] }}</li>
                        <li>Reversal Entries: {{ $diagnostics['revenue']['entries_by_type']['reversals'] }}</li>
                    </ul>
                </div>
                @endif
                
                @if(isset($diagnostics['cogs']))
                <div>
                    <strong style="color: #0c5460;">COGS Account: {{ $diagnostics['cogs']['account_name'] }}</strong>
                    <ul style="color: #0c5460;">
                        <li>Total Entries: {{ $diagnostics['cogs']['total_entries'] }}</li>
                        <li>Total Debits: Rs. {{ number_format($diagnostics['cogs']['total_debits'], 2) }}</li>
                        <li>Total Credits: Rs. {{ number_format($diagnostics['cogs']['total_credits'], 2) }}</li>
                        <li>Net Balance: Rs. {{ number_format($diagnostics['cogs']['net_balance'], 2) }}</li>
                        <li>Normal Entries: {{ $diagnostics['cogs']['entries_by_type']['normal'] }}</li>
                        <li>Reversal Entries: {{ $diagnostics['cogs']['entries_by_type']['reversals'] }}</li>
                    </ul>
                </div>
                @endif
                
                <p style="color: #0c5460; margin-top: 15px; font-size: 11px;">
                    To view diagnostics, add <code>?debug=1</code> to the URL.
                </p>
            </div>
            @endif

            <div class="filters">
                <form action="{{ route('profit-loss.index') }}" method="GET" class="filter-form">
                    <div class="filter-inputs">
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" required>
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" id="to_date" name="to_date" value="{{ $toDate }}" required>
                        </div>
                        <div class="form-group">
                            <label for="basis">Accounting Basis</label>
                            <select id="basis" name="basis" required>
                                <option value="accrual" {{ $basis === 'accrual' ? 'selected' : '' }}>Accrual</option>
                                <option value="cash" {{ $basis === 'cash' ? 'selected' : '' }}>Cash</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-buttons">
                        <button type="submit">Apply Filters</button>
                        <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                        <a href="{{ route('finance.index') }}">
                            <button type="button" class="btn-secondary">Back to Finance</button>
                        </a>
                    </div>
                </form>
            </div>

            <table class="profit-loss-table">
                <thead>
                    <tr>
                        <th>ACCOUNT</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Operating Income Section -->
                    <tr>
                        <td class="account-name level-1"><strong>Operating Income</strong></td>
                        <td></td>
                    </tr>
                    
                    @foreach($profitLossData['revenue'] as $account)
                    <tr>
                        <td class="account-name level-4">{{ $account['name'] }}</td>
                        <td>{{ number_format($account['balance'], 2) }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="total-row">
                        <td class="account-name">Total for Operating Income</td>
                        <td>{{ number_format($profitLossData['totals']['revenue'], 2) }}</td>
                    </tr>

                    <!-- Cost of Goods Sold Section -->
                    <tr>
                        <td class="account-name level-1"><strong>Cost of Goods Sold</strong></td>
                        <td></td>
                    </tr>
                    
                    @foreach($profitLossData['cost_of_goods_sold'] as $account)
                    <tr>
                        <td class="account-name level-4">{{ $account['name'] }}</td>
                        <td>{{ number_format($account['balance'], 2) }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="total-row">
                        <td class="account-name">Total for Cost of Goods Sold</td>
                        <td>{{ number_format($profitLossData['totals']['cost_of_goods_sold'], 2) }}</td>
                    </tr>

                    <!-- Gross Profit -->
                    <tr class="gross-profit-row">
                        <td class="account-name level-1"><strong>Gross Profit</strong></td>
                        <td>{{ number_format($profitLossData['totals']['gross_profit'], 2) }}</td>
                    </tr>

                    <!-- Operating Expenses Section -->
                    <tr>
                        <td class="account-name level-1"><strong>Operating Expense</strong></td>
                        <td></td>
                    </tr>
                    
                    @foreach($profitLossData['operating_expenses'] as $account)
                    <tr>
                        <td class="account-name level-4">{{ $account['name'] }}</td>
                        <td>{{ number_format($account['balance'], 2) }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="total-row">
                        <td class="account-name">Total for Operating Expense</td>
                        <td>{{ number_format($profitLossData['totals']['operating_expenses'], 2) }}</td>
                    </tr>

                    <!-- Operating Profit -->
                    <tr class="operating-profit-row">
                        <td class="account-name level-1"><strong>Operating Profit</strong></td>
                        <td>{{ number_format($profitLossData['totals']['operating_profit'], 2) }}</td>
                    </tr>

                    <!-- Non Operating Income Section -->
                    @if(count($profitLossData['other_income']) > 0 || $profitLossData['totals']['other_income'] > 0)
                    <tr>
                        <td class="account-name level-1"><strong>Non Operating Income</strong></td>
                        <td></td>
                    </tr>
                    
                    @foreach($profitLossData['other_income'] as $account)
                    <tr>
                        <td class="account-name level-4">{{ $account['name'] }}</td>
                        <td>{{ number_format($account['balance'], 2) }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="total-row">
                        <td class="account-name">Total for Non Operating Income</td>
                        <td>{{ number_format($profitLossData['totals']['other_income'], 2) }}</td>
                    </tr>
                    @endif

                    <!-- Non Operating Expenses Section -->
                    @if(count($profitLossData['other_expenses']) > 0 || $profitLossData['totals']['other_expenses'] > 0)
                    <tr>
                        <td class="account-name level-1"><strong>Non Operating Expense</strong></td>
                        <td></td>
                    </tr>
                    
                    @foreach($profitLossData['other_expenses'] as $account)
                    <tr>
                        <td class="account-name level-4">{{ $account['name'] }}</td>
                        <td>{{ number_format($account['balance'], 2) }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="total-row">
                        <td class="account-name">Total for Non Operating Expense</td>
                        <td>{{ number_format($profitLossData['totals']['other_expenses'], 2) }}</td>
                    </tr>
                    @endif

                    <!-- Net Profit/Loss -->
                    <tr class="net-profit-row {{ $profitLossData['totals']['net_profit'] < 0 ? 'negative' : '' }}">
                        <td class="account-name level-1"><strong>Net Profit/Loss</strong></td>
                        <td>{{ number_format($profitLossData['totals']['net_profit'], 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <p>This is a computer generated report and does not require a signature.</p>
                <p>Generated by {{ config('app.name') }} on {{ now()->format('d M, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
