<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Ledger - {{ $business->business_name ?? 'StoreBook' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.standalone-report-styles')
</head>
<body>

<div class="page-container">

    <div class="filters no-print">
        <form action="{{ route('general-ledger.index') }}" method="GET" class="filter-form">
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

    <x-report-header :business="$business" title="General Ledger">
        <div><strong>Basis:</strong> {{ ucfirst($basis) }}</div>
        <div><strong>Period:</strong> <span class="meta-pill">@businessDate($fromDate) to @businessDate($toDate)</span></div>
    </x-report-header>

    <div class="summary-grid no-print">
        <div class="summary-card">
            <h4>Total Debit</h4>
            <div class="summary-value debit-val">{{ number_format($generalLedgerData['totals']['total_debit'], 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Credit</h4>
            <div class="summary-value credit-val">{{ number_format($generalLedgerData['totals']['total_credit'], 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Balance</h4>
            <div class="summary-value {{ $generalLedgerData['totals']['total_balance'] >= 0 ? 'credit-val' : 'debit-val' }}">
                {{ number_format($generalLedgerData['totals']['total_balance'], 2) }}
            </div>
        </div>
    </div>

    @if($basis === 'cash' && !empty($generalLedgerData['excluded_accounts']))
        <div class="notice-box no-print">
            <h4>Cash Basis Notice</h4>
            <p>The following accounts are excluded in cash basis accounting:</p>
            <ul>
                @foreach($generalLedgerData['excluded_accounts'] as $excludedAccount)
                    <li>{{ $excludedAccount }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-container">
        <table class="report-table" id="general-ledger-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="amount">Debit</th>
                    <th class="amount">Credit</th>
                    <th class="amount">Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($generalLedgerData['accounts'] as $account)
                    <tr>
                        <td>
                            <strong>{{ $account['code'] }}</strong> {{ $account['name'] }}
                        </td>
                        <td class="amount">{{ $account['debit'] > 0 ? number_format($account['debit'], 2) : '—' }}</td>
                        <td class="amount">{{ $account['credit'] > 0 ? number_format($account['credit'], 2) : '—' }}</td>
                        <td class="amount">
                            <span class="{{ $account['balance'] >= 0 ? 'credit-val' : 'debit-val' }}">
                                {{ number_format($account['balance'], 2) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No accounts found for the selected period.</td></tr>
                @endforelse
            </tbody>
            @if(!empty($generalLedgerData['accounts']))
                <tfoot>
                    <tr class="total-row">
                        <td style="text-align:center"><strong>Total</strong></td>
                        <td class="amount"><strong class="debit-val">{{ number_format($generalLedgerData['totals']['total_debit'], 2) }}</strong></td>
                        <td class="amount"><strong class="credit-val">{{ number_format($generalLedgerData['totals']['total_credit'], 2) }}</strong></td>
                        <td class="amount">
                            <strong class="{{ $generalLedgerData['totals']['total_balance'] >= 0 ? 'credit-val' : 'debit-val' }}">
                                {{ number_format($generalLedgerData['totals']['total_balance'], 2) }}
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    @if(!empty($generalLedgerData['accounts']))
        <div class="record-count no-print">
            Total accounts: <strong>{{ count($generalLedgerData['accounts']) }}</strong>
        </div>
    @endif

    <div class="report-footer">
        <span>Generated by: {{ auth()->user()->name ?? 'User' }}</span>
        <span>Printed: @businessDateTime(now())</span>
    </div>

</div>

</body>
</html>
