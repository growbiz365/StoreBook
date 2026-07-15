<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Party Balances - {{ $business->business_name ?? 'StoreBook' }}</title>
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

        .report-title h2 {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .report-title .meta {
            font-size: 12px;
            color: #555;
            line-height: 1.7;
        }

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

        .filter-form .form-group {
            flex: 1;
            min-width: 140px;
        }

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

        .tables-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 4px;
        }

        .tables-container.single-table {
            grid-template-columns: 1fr;
        }

        .table-container {
            overflow-x: auto;
            border: 1px solid #333;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .section-title {
            text-align: center;
            background: #f1f5f9;
            font-weight: 700;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #333;
            padding: 7px 8px;
            vertical-align: top;
        }

        th {
            background: #f1f5f9;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
        }

        .urdu {
            font-size: 10px;
            color: #666;
            display: block;
            font-weight: 400;
        }

        .amount { text-align: right; white-space: nowrap; }

        .balances-table tbody td,
        .balances-table tfoot td {
            font-weight: 600;
        }

        .credit-val {
            color: #15803d;
            font-weight: 600;
        }

        .debit-val {
            color: #b91c1c;
            font-weight: 600;
        }

        .total-row td {
            font-weight: 700;
            border-top: 2px solid #333;
            background: #f9fafb;
        }

        .inactive-tag {
            color: #dc2626;
            font-size: 10px;
            margin-left: 4px;
            font-weight: 500;
        }

        .grand-total {
            margin-top: 16px;
            border: 1px solid #333;
            padding: 14px 16px;
            text-align: right;
            background: #f9fafb;
        }

        .grand-total-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .grand-total-amount {
            font-size: 18px;
            font-weight: 700;
        }

        .record-count {
            margin-top: 10px;
            font-size: 12px;
            color: #6b7280;
        }

        .report-footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
        }

        .empty-state {
            text-align: center;
            color: #6b7280;
            padding: 24px 12px;
        }

        @media screen and (max-width: 768px) {
            .page-container { padding: 12px 10px; }
            .tables-container { grid-template-columns: 1fr; }
            .filter-form { flex-direction: column; align-items: stretch; }
            .filter-form .form-group { min-width: 100%; width: 100%; }
            .button-group { width: 100%; flex-wrap: wrap; }
            .button-group button, .button-group .btn-link {
                flex: 1;
                justify-content: center;
                min-width: min(100%, 120px);
            }
            table { font-size: 10px; }
            th, td { padding: 5px 4px; }
        }

        @media print {
            html, body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .page-container {
                padding: 0 !important;
                margin: 0 !important;
                max-width: none !important;
                width: 100% !important;
                background: white !important;
            }

            .no-print { display: none !important; }

            .report-header {
                display: flex !important;
                flex-direction: row !important;
                align-items: flex-start !important;
                justify-content: space-between !important;
                gap: 24px !important;
                margin-bottom: 14px;
                padding-bottom: 10px;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .report-header-left { flex: 1 !important; text-align: left !important; }
            .report-header-right { flex: 1 !important; text-align: right !important; }

            .business-info { text-align: left !important; }
            .report-title { text-align: right !important; }

            .business-info h2,
            .report-title h2 { font-size: 16px; }

            .business-info-details,
            .report-title .meta { font-size: 11px; }

            .meta-pill {
                background: #eef2ff !important;
                color: #3730a3 !important;
            }

            .tables-container {
                gap: 12px;
            }

            .table-container {
                overflow: visible !important;
                border: 1px solid #000 !important;
                page-break-inside: auto;
                break-inside: auto;
            }

            table {
                font-size: 10.5px;
                width: 100% !important;
            }

            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }

            th {
                background: #f1f5f9 !important;
                border: 1px solid #000 !important;
                padding: 5px 6px;
            }

            td {
                border: 1px solid #000 !important;
                padding: 4px 6px;
                font-weight: 600 !important;
            }

            .section-title { background: #f1f5f9 !important; }

            .total-row td {
                background: #f3f4f6 !important;
                border-top: 2px solid #000 !important;
            }

            .grand-total {
                border: 1px solid #000 !important;
                background: #f3f4f6 !important;
                page-break-inside: avoid;
            }

            .credit-val { color: #15803d !important; }
            .debit-val { color: #b91c1c !important; }

            .report-footer {
                margin-top: 12px;
                font-size: 9px;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

@php
    $reportDate = request('date', now()->format('Y-m-d'));
    $balanceType = request('balance_type');
    $debitTotal = $debitParties->sum(fn ($party) => abs((float) $party->balance));
    $creditTotal = $creditParties->sum(fn ($party) => (float) $party->balance);
    $netBalance = $creditTotal - $debitTotal;
    $totalRecords = $debitParties->count() + $creditParties->count();
@endphp

<div class="page-container">

    <div class="filters no-print">
        <form action="{{ route('parties.balances-report') }}" method="GET" class="filter-form">
            <div class="form-group">
                <label for="date">As of Date</label>
                <input type="date" name="date" id="date" value="{{ $reportDate }}">
            </div>
            <div class="form-group">
                <label for="balance_type">Balance Type</label>
                <select name="balance_type" id="balance_type">
                    <option value="">Both (Debit & Credit)</option>
                    <option value="debit" {{ $balanceType === 'debit' ? 'selected' : '' }}>Debit Only</option>
                    <option value="credit" {{ $balanceType === 'credit' ? 'selected' : '' }}>Credit Only</option>
                </select>
            </div>
            <div class="button-group">
                <button type="submit" class="btn-primary">Search</button>
                <button type="button" class="btn-print" onclick="window.print()">Print</button>
                <a href="{{ route('party-management.dashboard') }}" class="btn-secondary btn-link">Back</a>
            </div>
        </form>
    </div>

    <div class="report-header">
        <div class="report-header-left">
            <div class="business-info">
                @php
                    $logo = $business->logo ?? $business->business_logo ?? false;
                @endphp
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Business Logo" class="business-logo">
                @endif
                <h2>{{ $business->business_name ?? 'StoreBook' }}</h2>
                <div class="business-info-details">
                    @if($business?->address)
                        <div>{{ $business->address }}</div>
                    @endif
                    @if($business?->contact_no)
                        <div>Contact: {{ $business->contact_no }}</div>
                    @endif
                    @if($business?->email)
                        <div>Email: {{ $business->email }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="report-header-right">
            <div class="report-title">
                <h2>Party Balances</h2>
                <div class="meta">
                    <div><strong>As of:</strong> <span class="meta-pill">@businessDate($reportDate)</span></div>
                    <div><strong>Filter:</strong>
                        @if($balanceType === 'debit')
                            Debit parties only
                        @elseif($balanceType === 'credit')
                            Credit parties only
                        @else
                            Debit & credit parties
                        @endif
                    </div>
                    <div><strong>Parties with balance:</strong> {{ number_format($totalRecords) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="tables-container {{ $balanceType ? 'single-table' : '' }}">
        @if(!$balanceType || $balanceType === 'debit')
            <div class="table-container">
                <table class="balances-table">
                    <thead>
                        <tr>
                            <th colspan="3" class="section-title">
                                Debit Parties
                                <span class="urdu">(بنام)</span>
                            </th>
                        </tr>
                        <tr>
                            <th>Sr.</th>
                            <th>Party Name</th>
                            <th class="amount">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debitParties as $index => $party)
                            <tr @if($party->status == 0) style="opacity: 0.65;" @endif>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $party->name }}@if($party->pcode) ({{ $party->pcode }})@endif
                                    @if($party->status == 0)
                                        <span class="inactive-tag">(Inactive)</span>
                                    @endif
                                </td>
                                <td class="amount">
                                    <span class="debit-val">{{ number_format(abs($party->balance), 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty-state">No debit parties found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($debitParties->count() > 0)
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" style="text-align: right"><strong>Total Debit</strong></td>
                                <td class="amount"><strong class="debit-val">{{ number_format($debitTotal, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @endif

        @if(!$balanceType || $balanceType === 'credit')
            <div class="table-container">
                <table class="balances-table">
                    <thead>
                        <tr>
                            <th colspan="3" class="section-title">
                                Credit Parties
                                <span class="urdu">(جمع)</span>
                            </th>
                        </tr>
                        <tr>
                            <th>Sr.</th>
                            <th>Party Name</th>
                            <th class="amount">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creditParties as $index => $party)
                            <tr @if($party->status == 0) style="opacity: 0.65;" @endif>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $party->name }}@if($party->pcode) ({{ $party->pcode }})@endif
                                    @if($party->status == 0)
                                        <span class="inactive-tag">(Inactive)</span>
                                    @endif
                                </td>
                                <td class="amount">
                                    <span class="credit-val">{{ number_format($party->balance, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty-state">No credit parties found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($creditParties->count() > 0)
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" style="text-align: right"><strong>Total Credit</strong></td>
                                <td class="amount"><strong class="credit-val">{{ number_format($creditTotal, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @endif
    </div>

    @if($totalRecords > 0)
        <div class="record-count no-print">
            Total parties: <strong>{{ $totalRecords }}</strong>
            @if(!$balanceType)
                | Debit: <strong>{{ $debitParties->count() }}</strong>
                | Credit: <strong>{{ $creditParties->count() }}</strong>
            @endif
        </div>
    @endif

    <div class="grand-total">
        @if(!$balanceType)
            <div class="grand-total-label">Net Balance (Credit - Debit)</div>
            <div class="grand-total-amount {{ $netBalance >= 0 ? 'credit-val' : 'debit-val' }}">
                {{ number_format($netBalance, 2) }}
            </div>
        @elseif($balanceType === 'debit')
            <div class="grand-total-label">Total Debit Balance</div>
            <div class="grand-total-amount debit-val">{{ number_format($debitTotal, 2) }}</div>
        @else
            <div class="grand-total-label">Total Credit Balance</div>
            <div class="grand-total-amount credit-val">{{ number_format($creditTotal, 2) }}</div>
        @endif
    </div>

    <div class="report-footer">
        <span>Generated by: {{ auth()->user()->name ?? 'User' }}</span>
        <span>Printed: @businessDateTime(now())</span>
    </div>

</div>

</body>
</html>
