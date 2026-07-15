<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss - {{ $business->business_name ?? 'StoreBook' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.standalone-report-styles')
    <style>
        .highlight-row td { background: #eff6ff; font-weight: 700; }
        .profit-row td { background: #f0fdf4; font-weight: 700; }
        .loss-row td { background: #fef2f2; font-weight: 700; }
        @media print {
            .highlight-row td { background: #eff6ff !important; }
            .profit-row td { background: #f0fdf4 !important; }
            .loss-row td { background: #fef2f2 !important; }
        }
    </style>
</head>
<body>

<div class="page-container">

    <div class="filters no-print">
        <form action="{{ route('profit-loss.index') }}" method="GET" class="filter-form">
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
            <div class="button-group">
                <button type="submit" class="btn-primary">Search</button>
                <button type="button" class="btn-print" onclick="window.print()">Print</button>
                <a href="{{ route('finance.index') }}" class="btn-secondary btn-link">Back</a>
            </div>
        </form>
    </div>

    <x-report-header :business="$business" title="Profit & Loss Statement">
        <div><strong>Basis:</strong> {{ ucfirst($basis) }}</div>
        <div><strong>Period:</strong> <span class="meta-pill">@businessDate($fromDate) to @businessDate($toDate)</span></div>
    </x-report-header>

    <div class="summary-grid no-print">
        <div class="summary-card">
            <h4>Total Revenue</h4>
            <div class="summary-value credit-val">{{ number_format($profitLossData['totals']['revenue'], 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Expenses</h4>
            <div class="summary-value expense-val">{{ number_format($profitLossData['totals']['operating_expenses'] + $profitLossData['totals']['cost_of_goods_sold'] + $profitLossData['totals']['other_expenses'], 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Net Profit</h4>
            <div class="summary-value {{ $profitLossData['totals']['net_profit'] >= 0 ? 'credit-val' : 'debit-val' }}">
                {{ number_format($profitLossData['totals']['net_profit'], 2) }}
            </div>
        </div>
    </div>

    @if($basis === 'cash' && !empty($profitLossData['excluded_accounts']))
        <div class="notice-box no-print">
            <h4>Cash Basis Notice</h4>
            <p>The following accounts are excluded in cash basis accounting:</p>
            <ul>
                @foreach($profitLossData['excluded_accounts'] as $excludedAccount)
                    <li>{{ $excludedAccount }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($diagnostics) && $diagnostics)
        <div class="notice-box info no-print">
            <h4>Journal Entry Diagnostics</h4>
            @if(isset($diagnostics['revenue']))
                <p><strong>Revenue:</strong> {{ $diagnostics['revenue']['account_name'] }} — Entries: {{ $diagnostics['revenue']['total_entries'] }}, Credits: {{ number_format($diagnostics['revenue']['total_credits'], 2) }}</p>
            @endif
            @if(isset($diagnostics['cogs']))
                <p><strong>COGS:</strong> {{ $diagnostics['cogs']['account_name'] }} — Entries: {{ $diagnostics['cogs']['total_entries'] }}, Debits: {{ number_format($diagnostics['cogs']['total_debits'], 2) }}</p>
            @endif
            <p style="margin-top:8px;font-size:11px">Add <code>?debug=1</code> to the URL to view diagnostics.</p>
        </div>
    @endif

    <div class="table-container">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="amount">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr class="category-row"><td colspan="2">Operating Income</td></tr>
                @foreach($profitLossData['revenue'] as $account)
                    <tr><td class="sub-row">{{ $account['name'] }}</td><td class="amount">{{ number_format($account['balance'], 2) }}</td></tr>
                @endforeach
                <tr class="total-row"><td>Total for Operating Income</td><td class="amount credit-val">{{ number_format($profitLossData['totals']['revenue'], 2) }}</td></tr>

                <tr class="category-row"><td colspan="2">Cost of Goods Sold</td></tr>
                @foreach($profitLossData['cost_of_goods_sold'] as $account)
                    <tr><td class="sub-row">{{ $account['name'] }}</td><td class="amount">{{ number_format($account['balance'], 2) }}</td></tr>
                @endforeach
                <tr class="total-row"><td>Total for Cost of Goods Sold</td><td class="amount">{{ number_format($profitLossData['totals']['cost_of_goods_sold'], 2) }}</td></tr>

                <tr class="highlight-row"><td><strong>Gross Profit</strong></td><td class="amount"><strong>{{ number_format($profitLossData['totals']['gross_profit'], 2) }}</strong></td></tr>

                <tr class="category-row"><td colspan="2">Operating Expense</td></tr>
                @foreach($profitLossData['operating_expenses'] as $account)
                    <tr><td class="sub-row">{{ $account['name'] }}</td><td class="amount">{{ number_format($account['balance'], 2) }}</td></tr>
                @endforeach
                <tr class="total-row"><td>Total for Operating Expense</td><td class="amount">{{ number_format($profitLossData['totals']['operating_expenses'], 2) }}</td></tr>

                <tr class="highlight-row"><td><strong>Operating Profit</strong></td><td class="amount"><strong>{{ number_format($profitLossData['totals']['operating_profit'], 2) }}</strong></td></tr>

                @if(count($profitLossData['other_income']) > 0 || $profitLossData['totals']['other_income'] > 0)
                    <tr class="category-row"><td colspan="2">Non Operating Income</td></tr>
                    @foreach($profitLossData['other_income'] as $account)
                        <tr><td class="sub-row">{{ $account['name'] }}</td><td class="amount">{{ number_format($account['balance'], 2) }}</td></tr>
                    @endforeach
                    <tr class="total-row"><td>Total for Non Operating Income</td><td class="amount">{{ number_format($profitLossData['totals']['other_income'], 2) }}</td></tr>
                @endif

                @if(count($profitLossData['other_expenses']) > 0 || $profitLossData['totals']['other_expenses'] > 0)
                    <tr class="category-row"><td colspan="2">Non Operating Expense</td></tr>
                    @foreach($profitLossData['other_expenses'] as $account)
                        <tr><td class="sub-row">{{ $account['name'] }}</td><td class="amount">{{ number_format($account['balance'], 2) }}</td></tr>
                    @endforeach
                    <tr class="total-row"><td>Total for Non Operating Expense</td><td class="amount">{{ number_format($profitLossData['totals']['other_expenses'], 2) }}</td></tr>
                @endif

                <tr class="{{ $profitLossData['totals']['net_profit'] < 0 ? 'loss-row' : 'profit-row' }}">
                    <td><strong>Net Profit/Loss</strong></td>
                    <td class="amount"><strong class="{{ $profitLossData['totals']['net_profit'] >= 0 ? 'credit-val' : 'debit-val' }}">{{ number_format($profitLossData['totals']['net_profit'], 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="report-footer">
        <span>Generated by: {{ auth()->user()->name ?? 'User' }}</span>
        <span>Printed: @businessDateTime(now())</span>
    </div>

</div>

</body>
</html>
