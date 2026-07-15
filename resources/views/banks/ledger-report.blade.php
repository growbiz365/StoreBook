<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Ledger - {{ $business->business_name ?? 'StoreBook' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page { margin: 10mm; }
        * { box-sizing: border-box; }
        html, body { width: 100%; }
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            background: #fff;
        }
        .page-container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 16px 20px;
            background: white;
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            gap: 30px;
        }
        .report-header-left { flex: 1; text-align: left; }
        .report-header-right { flex: 1; text-align: right; }
        .business-logo { height: 55px; margin-bottom: 6px; display: block; }
        .business-info h2 { margin: 4px 0 6px 0; font-size: 18px; font-weight: 700; color: #1a1a1a; }
        .business-info-details { font-size: 11px; color: #555; line-height: 1.6; }
        .report-title h2 { margin: 0 0 8px 0; font-size: 18px; font-weight: 700; color: #1a1a1a; }
        .report-title .meta { font-size: 12px; color: #555; line-height: 1.7; }
        .report-title .meta strong { color: #1a1a1a; }
        .meta-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background: #eef2ff;
            color: #3730a3;
            font-size: 11px;
            font-weight: 600;
        }
        .filters {
            margin: 15px 0;
            padding: 16px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
        }
        .filter-form .form-group { flex: 1; min-width: 140px; }
        .filter-form .form-group.account-group { flex: 2; min-width: 220px; }
        .filter-form label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            font-weight: 500;
            color: #444;
        }
        .filter-form input[type="date"],
        .filter-form select {
            width: 100%;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            background: #fff;
        }
        .button-group {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
            flex-wrap: nowrap;
            align-items: center;
        }
        button, .btn-link {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            height: 36px;
            line-height: 1;
        }
        .btn-primary { background: #0d6efd; color: white; }
        .btn-primary:hover { background: #0b5ed7; }
        .btn-print { background: #1f2937; color: white; }
        .btn-print:hover { background: #111827; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5c636a; }
        .voucher-link { color: #0d6efd; font-weight: 600; text-decoration: none; }
        .voucher-link:hover { text-decoration: underline; }
        .table-container {
            overflow-x: auto;
            margin-top: 4px;
            border: 1px solid #333;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #333; padding: 7px 8px; vertical-align: top; }
        th { background: #f1f5f9; font-weight: 600; text-align: left; white-space: nowrap; }
        .urdu { font-size: 10px; color: #666; display: block; font-weight: 400; }
        .amount { text-align: right; white-space: nowrap; }
        #ledger-table tbody td, #ledger-table tfoot td { font-weight: 600; }
        .credit-val { color: #15803d; font-weight: 600; }
        .debit-val { color: #b91c1c; font-weight: 600; }
        .opening-balance-row td { background: #dcfce7 !important; font-weight: 600; }
        .opening-label {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 4px;
            background: #bbf7d0;
            color: #166534;
            font-size: 11px;
            font-weight: 600;
        }
        .total-row td { font-weight: 700; border-top: 2px solid #333; background: #f9fafb; }
        .record-count { margin-top: 10px; font-size: 12px; color: #6b7280; }
        .report-footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
        }
        .empty-state { text-align: center; color: #6b7280; padding: 40px 20px; }
        @media screen and (max-width: 768px) {
            .page-container { padding: 12px 10px; }
            .filter-form { flex-direction: column; align-items: stretch; }
            .filter-form .form-group { min-width: 100%; width: 100%; }
            .button-group { width: 100%; flex-wrap: wrap; }
            .button-group button, .button-group .btn-link {
                flex: 1; justify-content: center; min-width: min(100%, 120px);
            }
            table { font-size: 10px; }
            th, td { padding: 5px 4px; }
        }
        @media print {
            html, body { background: white !important; margin: 0 !important; padding: 0 !important; color: #000 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .page-container { padding: 0 !important; margin: 0 !important; max-width: none !important; width: 100% !important; background: white !important; }
            .no-print { display: none !important; }
            .report-header { display: flex !important; flex-direction: row !important; align-items: flex-start !important; justify-content: space-between !important; gap: 24px !important; margin-bottom: 14px; padding-bottom: 10px; page-break-inside: avoid; break-inside: avoid; }
            .report-header-left { flex: 1 !important; text-align: left !important; }
            .report-header-right { flex: 1 !important; text-align: right !important; }
            .business-info { text-align: left !important; }
            .report-title { text-align: right !important; }
            .business-info h2, .report-title h2 { font-size: 16px; }
            .business-info-details, .report-title .meta { font-size: 11px; }
            .meta-pill { background: #eef2ff !important; color: #3730a3 !important; }
            .table-container { overflow: visible !important; border: 1px solid #000 !important; width: 100% !important; }
            table { font-size: 10.5px; width: 100% !important; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            th { background: #f1f5f9 !important; border: 1px solid #000 !important; padding: 5px 6px; }
            td { border: 1px solid #000 !important; padding: 4px 6px; font-weight: 600 !important; }
            .opening-balance-row td { background: #dcfce7 !important; }
            .opening-label { background: #bbf7d0 !important; color: #166534 !important; }
            .total-row td { background: #f3f4f6 !important; border-top: 2px solid #000 !important; }
            .credit-val { color: #15803d !important; }
            .debit-val { color: #b91c1c !important; }
            .voucher-link { color: inherit !important; text-decoration: none !important; font-weight: 600 !important; }
            .report-footer { margin-top: 12px; font-size: 9px; page-break-inside: avoid; }
        }
    </style>
</head>
<body>

@php
    $fromDate = request('from_date', now()->startOfMonth()->format('Y-m-d'));
    $toDate = request('to_date', now()->format('Y-m-d'));
    $accountLabel = $selectedBank
        ? strtoupper($selectedBank->chartOfAccount->name ?? $selectedBank->account_name)
        : '';
@endphp

<div class="page-container">

    <div class="filters no-print">
        <form action="{{ route('banks.ledger-report') }}" method="GET" class="filter-form">
            <div class="form-group account-group">
                <label for="bank_id">Select Account</label>
                <select name="bank_id" id="bank_id" required>
                    <option value="">Select an account</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                            {{ strtoupper($bank->chartOfAccount->name ?? $bank->account_name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="from_date">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}">
            </div>
            <div class="form-group">
                <label for="to_date">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}">
            </div>
            <div class="button-group">
                <button type="submit" class="btn-primary">Search</button>
                @if($selectedBank)
                    <button type="button" class="btn-print" onclick="window.print()">Print</button>
                @endif
                <a href="{{ route('bank-management') }}" class="btn-secondary btn-link">Back</a>
            </div>
        </form>
    </div>

    @if($selectedBank)
        <div class="report-header">
            <div class="report-header-left">
                <div class="business-info">
                    @php $logo = $business->logo ?? $business->business_logo ?? false; @endphp
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="Business Logo" class="business-logo">
                    @endif
                    <h2>{{ $business->business_name ?? 'StoreBook' }}</h2>
                    <div class="business-info-details">
                        @if($business?->address)<div>{{ $business->address }}</div>@endif
                        @if($business?->contact_no)<div>Contact: {{ $business->contact_no }}</div>@endif
                        @if($business?->email)<div>Email: {{ $business->email }}</div>@endif
                    </div>
                </div>
            </div>
            <div class="report-header-right">
                <div class="report-title">
                    <h2>Bank Ledger</h2>
                    <div class="meta">
                        <div><strong>Account:</strong> <span class="meta-pill">{{ $accountLabel }}</span></div>
                        <div><strong>Period:</strong> @businessDate($fromDate) to @businessDate($toDate)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table id="ledger-table">
                <thead>
                    <tr>
                        <th>Date <span class="urdu">(تاریخ)</span></th>
                        <th>Voucher <span class="urdu">(واؤچر)</span></th>
                        <th>Description <span class="urdu">(تفصیلات)</span></th>
                        <th class="amount">Deposit <span class="urdu">(جمع)</span></th>
                        <th class="amount">Withdrawal <span class="urdu">(بنام)</span></th>
                        <th class="amount">Balance <span class="urdu">(بقیہ)</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="opening-balance-row">
                        <td>@businessDate($fromDate)</td>
                        <td colspan="2"><span class="opening-label">Opening Balance</span></td>
                        <td class="amount">—</td>
                        <td class="amount">—</td>
                        <td class="amount">
                            <span class="{{ $openingBalance > 0 ? 'credit-val' : ($openingBalance < 0 ? 'debit-val' : '') }}">
                                {{ number_format($openingBalance, 2) }}
                            </span>
                        </td>
                    </tr>

                    @forelse($ledgerEntries as $entry)
                        <tr>
                            <td>@businessDate($entry->date)</td>
                            <td>
                                @if($entry->voucher_url)
                                    <a href="{{ $entry->voucher_url }}" class="voucher-link">
                                        {{ $entry->voucher_type }} #{{ $entry->display_voucher_id }}
                                    </a>
                                @elseif($entry->voucher_type && $entry->voucher_id)
                                    {{ $entry->voucher_type }} #{{ $entry->display_voucher_id }}
                                @else
                                    {{ $entry->voucher_type ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $entry->details ?: ($entry->voucher_type ?: '—') }}</td>
                            <td class="amount">
                                @if($entry->deposit_amount > 0)
                                    <span class="credit-val">{{ number_format($entry->deposit_amount, 2) }}</span>
                                @else — @endif
                            </td>
                            <td class="amount">
                                @if($entry->withdrawal_amount > 0)
                                    <span class="debit-val">{{ number_format($entry->withdrawal_amount, 2) }}</span>
                                @else — @endif
                            </td>
                            <td class="amount">
                                <span class="{{ $entry->running_balance > 0 ? 'credit-val' : ($entry->running_balance < 0 ? 'debit-val' : '') }}">
                                    {{ number_format($entry->running_balance, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No transactions found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>

                @if($ledgerEntries->count() > 0)
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: center"><strong>Total</strong></td>
                            <td class="amount"><strong class="credit-val">{{ number_format($totals['deposits'], 2) }}</strong></td>
                            <td class="amount"><strong class="debit-val">{{ number_format($totals['withdrawals'], 2) }}</strong></td>
                            <td class="amount">
                                <strong class="{{ $totals['balance'] > 0 ? 'credit-val' : ($totals['balance'] < 0 ? 'debit-val' : '') }}">
                                    {{ number_format($totals['balance'], 2) }}
                                </strong>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($ledgerEntries->count() > 0)
            <div class="record-count no-print">
                Total records: <strong>{{ $ledgerEntries->count() }}</strong>
            </div>
        @endif

        <div class="report-footer">
            <span>Generated by: {{ auth()->user()->name ?? 'User' }}</span>
            <span>Printed: @businessDateTime(now())</span>
        </div>
    @else
        <p class="empty-state">Select an account, then click <strong>Search</strong> to view the ledger.</p>
    @endif

</div>

</body>
</html>
