<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed General Ledger - {{ $business->business_name ?? 'StoreBook' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.standalone-report-styles')
</head>
<body>

@php
    $selectedAccount = $accountId ? $accounts->firstWhere('id', (int) $accountId) : null;
@endphp

<div class="page-container">

    <div class="filters no-print">
        <form action="{{ route('finance.detailed-general-ledger.index') }}" method="GET" class="filter-form">
            <div class="form-group">
                <label for="from_date">From Date</label>
                <input type="date" id="from_date" name="from_date" value="{{ $fromDate }}">
            </div>
            <div class="form-group">
                <label for="to_date">To Date</label>
                <input type="date" id="to_date" name="to_date" value="{{ $toDate }}">
            </div>
            <div class="form-group wide">
                <label for="account_id">Account</label>
                <select id="account_id" name="account_id">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ (string) $accountId === (string) $acc->id ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="button-group">
                <button type="submit" class="btn-primary">Search</button>
                @if(count($ledgerData['accounts']) > 0)
                    <button type="button" class="btn-print" onclick="window.print()">Print</button>
                @endif
                <a href="{{ route('finance.index') }}" class="btn-secondary btn-link">Back</a>
            </div>
        </form>
    </div>

    <x-report-header :business="$business" title="Detailed General Ledger">
        <div><strong>Period:</strong> <span class="meta-pill">@businessDate($fromDate) to @businessDate($toDate)</span></div>
        @if($selectedAccount)
            <div><strong>Account:</strong> {{ $selectedAccount->code }} - {{ $selectedAccount->name }}</div>
        @else
            <div><strong>Accounts:</strong> {{ count($ledgerData['accounts']) }}</div>
        @endif
        <div>
            <strong>Totals:</strong>
            Debit {{ number_format($ledgerData['totals']['debit'], 2) }} |
            Credit {{ number_format($ledgerData['totals']['credit'], 2) }}
        </div>
    </x-report-header>

    @forelse($ledgerData['accounts'] as $account)
        <div class="account-block">
            <div class="account-block-header">
                <div>
                    <div>{{ $account['code'] }} - {{ $account['name'] }}</div>
                    <div class="account-block-meta">Type: {{ ucfirst($account['type']) }}</div>
                </div>
                <div class="account-block-meta">
                    Debit: <span class="debit-val">{{ number_format($account['total_debit'], 2) }}</span> |
                    Credit: <span class="credit-val">{{ number_format($account['total_credit'], 2) }}</span> |
                    Balance: <span class="{{ $account['ending_balance'] >= 0 ? 'credit-val' : 'debit-val' }}">{{ number_format($account['ending_balance'], 2) }}</span>
                </div>
            </div>
            <div class="table-container" style="margin:0;border:none;border-top:1px solid #333">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Voucher</th>
                            <th>Description</th>
                            <th class="amount">Debit</th>
                            <th class="amount">Credit</th>
                            <th class="amount">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($account['rows'] as $row)
                            <tr>
                                <td>@businessDate($row['date'])</td>
                                <td>
                                    @if(!empty($row['voucher_url']))
                                        <a href="{{ $row['voucher_url'] }}" class="voucher-link">
                                            {{ $row['voucher_type'] }} #{{ $row['voucher_id'] }}
                                        </a>
                                    @elseif($row['voucher_type'])
                                        {{ $row['voucher_type'] }}@if($row['voucher_id']) #{{ $row['voucher_id'] }}@endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $row['comments'] ?: '—' }}</td>
                                <td class="amount">@if($row['debit'] > 0)<span class="debit-val">{{ number_format($row['debit'], 2) }}</span>@else — @endif</td>
                                <td class="amount">@if($row['credit'] > 0)<span class="credit-val">{{ number_format($row['credit'], 2) }}</span>@else — @endif</td>
                                <td class="amount">
                                    <span class="{{ $row['balance'] >= 0 ? 'credit-val' : 'debit-val' }}">{{ number_format($row['balance'], 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <p class="empty-state">No journal entries found for the selected filters.</p>
    @endforelse

    @if(count($ledgerData['accounts']) > 0)
        <div class="record-count no-print">
            Total accounts: <strong>{{ count($ledgerData['accounts']) }}</strong>
        </div>
    @endif

    <div class="report-footer">
        <span>Generated by: {{ auth()->user()->name ?? 'User' }}</span>
        <span>Printed: @businessDateTime(now())</span>
    </div>

</div>

</body>
</html>
