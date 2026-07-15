<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report - {{ $business->business_name ?? 'StoreBook' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.standalone-report-styles')
</head>
<body>

@php
    $fromDate = request('from_date', now()->startOfMonth()->format('Y-m-d'));
    $toDate = request('to_date', now()->format('Y-m-d'));
@endphp

<div class="page-container">

    <div class="filters no-print">
        <form action="{{ route('expenses.report') }}" method="GET" class="filter-form">
            <div class="form-group">
                <label for="from_date">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}">
            </div>
            <div class="form-group">
                <label for="to_date">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}">
            </div>
            <div class="form-group wide">
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
            <div class="button-group">
                <button type="submit" class="btn-primary">Search</button>
                @if($expenseEntries->count() > 0)
                    <button type="button" class="btn-print" onclick="window.print()">Print</button>
                @endif
                <a href="{{ route('expenses.dashboard') }}" class="btn-secondary btn-link">Back</a>
            </div>
        </form>
    </div>

    <x-report-header :business="$business" title="Expense Report">
        <div><strong>Period:</strong> <span class="meta-pill">@businessDate($fromDate) to @businessDate($toDate)</span></div>
        @if(request('expense_head_id'))
            @php $selHead = $expenseHeads->firstWhere('id', (int) request('expense_head_id')); @endphp
            @if($selHead)
                <div><strong>Expense Head:</strong> {{ $selHead->expense_head }}</div>
            @endif
        @endif
        <div><strong>Total entries:</strong> {{ number_format($totalEntries) }}</div>
    </x-report-header>

    @if($expenseEntries->count() > 0)
        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th colspan="4" class="section-title-row" style="text-align:center">Summary by Expense Head</th>
                    </tr>
                    <tr>
                        <th>Expense Head</th>
                        <th class="amount">Count</th>
                        <th class="amount">Total Amount</th>
                        <th class="amount">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenseByHead as $headName => $data)
                        <tr>
                            <td>{{ $headName }}</td>
                            <td class="amount">{{ $data['count'] }}</td>
                            <td class="amount expense-val">{{ number_format($data['total'], 2) }}</td>
                            <td class="amount">{{ $totalExpense > 0 ? number_format(($data['total'] / $totalExpense) * 100, 1) : '0.0' }}%</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td><strong>Total</strong></td>
                        <td class="amount"><strong>{{ $totalEntries }}</strong></td>
                        <td class="amount"><strong class="expense-val">{{ number_format($totalExpense, 2) }}</strong></td>
                        <td class="amount"><strong>100%</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="table-container" style="margin-top:16px">
            <table class="report-table" id="expense-detail-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Expense Head</th>
                        <th>Bank Account</th>
                        <th>Comments</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenseEntries as $entry)
                        <tr>
                            <td>@businessDate($entry->date_added)</td>
                            <td>{{ $entry->account->expenseHead->first() ? $entry->account->expenseHead->first()->expense_head : 'N/A' }}</td>
                            <td>
                                @if($entry->voucher && $entry->voucher->bank)
                                    {{ $entry->voucher->bank->chartOfAccount->name ?? $entry->voucher->bank->account_name }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $entry->voucher->details ?? ($entry->comments ?: '—') }}</td>
                            <td class="amount expense-val">{{ number_format($entry->debit_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" style="text-align:center"><strong>Total</strong></td>
                        <td class="amount"><strong class="expense-val">{{ number_format($totalExpense, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="record-count no-print">
            Total records: <strong>{{ $expenseEntries->count() }}</strong>
        </div>
    @else
        <p class="empty-state">No expense entries found for the selected criteria.</p>
    @endif

    <div class="report-footer">
        <span>Generated by: {{ auth()->user()->name ?? 'User' }}</span>
        <span>Printed: @businessDateTime(now())</span>
    </div>

</div>

</body>
</html>
