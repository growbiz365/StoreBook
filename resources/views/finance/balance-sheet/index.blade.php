<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet - {{ $business->business_name ?? 'StoreBook' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.standalone-report-styles')
    <style>
        .balance-sheet-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 4px;
        }
        .bs-column { border: 1px solid #333; }
        .bs-column-header {
            padding: 10px 12px;
            background: #f1f5f9;
            border-bottom: 1px solid #333;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
        }
        .bs-section-title {
            padding: 7px 10px;
            background: #e2e8f0;
            border-bottom: 1px solid #333;
            font-size: 12px;
            font-weight: 600;
        }
        .bs-account-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
            font-weight: 600;
        }
        .bs-account-row:last-child { border-bottom: none; }
        .bs-subtotal, .bs-total {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 10px;
            font-weight: 700;
            font-size: 12px;
            border-top: 1px solid #333;
            background: #f9fafb;
        }
        .bs-total { background: #f1f5f9; border-top: 2px solid #333; }
        .bs-grand-total {
            margin-top: 16px;
            border: 1px solid #333;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            background: #f9fafb;
        }
        .bs-grand-total > div {
            padding: 12px 14px;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }
        .bs-grand-total > div:first-child { border-right: 1px solid #333; }
        .negative-balance { color: #b91c1c; }
        @media screen and (max-width: 768px) { .balance-sheet-grid { grid-template-columns: 1fr; } }
        @media print {
            .balance-sheet-grid { gap: 12px; }
            .bs-column-header, .bs-section-title, .bs-subtotal, .bs-total, .bs-grand-total { background: #f1f5f9 !important; }
        }
    </style>
</head>
<body>

<div class="page-container">

    <div class="filters no-print">
        <form action="{{ route('balance-sheet.index') }}" method="GET" class="filter-form">
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
            <div class="button-group">
                <button type="submit" class="btn-primary">Search</button>
                <button type="button" class="btn-print" onclick="window.print()">Print</button>
                <a href="{{ route('finance.index') }}" class="btn-secondary btn-link">Back</a>
            </div>
        </form>
    </div>

    <x-report-header :business="$business" title="Balance Sheet">
        <div><strong>Basis:</strong> {{ ucfirst($basis) }}</div>
        <div><strong>As of:</strong> <span class="meta-pill">@businessDate($asOfDate)</span></div>
    </x-report-header>

    @if($basis === 'cash' && isset($balanceSheetData['excluded_accounts']) && !empty($balanceSheetData['excluded_accounts']))
        <div class="notice-box no-print">
            <h4>Cash Basis Notice</h4>
            <ul>
                @foreach($balanceSheetData['excluded_accounts'] as $excludedAccount)
                    <li>{{ $excludedAccount }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($balanceSheetData['warnings']) && !empty($balanceSheetData['warnings']))
        <div class="notice-box warn no-print">
            <h4>Warnings</h4>
            <ul>
                @foreach($balanceSheetData['warnings'] as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="balance-sheet-grid">
        <div class="bs-column">
            <div class="bs-column-header">Assets</div>

            @php
                $currentAssets = collect($balanceSheetData['assets'])->filter(function ($account) {
                    $name = strtolower($account['name']);
                    $code = $account['code'] ?? '';
                    if (preg_match('/^11/', $code) || preg_match('/^12/', $code)) return true;
                    return stripos($name, 'current') !== false || stripos($name, 'cash') !== false ||
                        stripos($name, 'receivable') !== false || stripos($name, 'inventory') !== false ||
                        stripos($name, 'prepaid') !== false || stripos($name, 'bank') !== false ||
                        stripos($name, 'short-term') !== false;
                });
                $currentAssetsTotal = $currentAssets->sum('balance');
            @endphp
            <div class="bs-section-title">Current Assets</div>
            @forelse($currentAssets as $account)
                <div class="bs-account-row">
                    <span>{{ $account['name'] }}@if(!empty($account['is_adjusted'])) <small>(Adjusted)</small>@endif</span>
                    <span class="amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">{{ number_format($account['balance'], 2) }}</span>
                </div>
            @empty
                <div class="bs-account-row" style="color:#6b7280;font-style:italic"><span>No current assets</span><span>0.00</span></div>
            @endforelse
            <div class="bs-subtotal"><span>Total Current Assets</span><span>{{ number_format($currentAssetsTotal, 2) }}</span></div>

            @php
                $longTermAssets = collect($balanceSheetData['assets'])->filter(function ($account) {
                    $name = strtolower($account['name']);
                    $code = $account['code'] ?? '';
                    if (preg_match('/^11/', $code) || preg_match('/^12/', $code)) return false;
                    return stripos($name, 'current') === false && stripos($name, 'cash') === false &&
                        stripos($name, 'receivable') === false && stripos($name, 'inventory') === false &&
                        stripos($name, 'prepaid') === false && stripos($name, 'bank') === false &&
                        stripos($name, 'short-term') === false;
                });
                $longTermAssetsTotal = $longTermAssets->sum('balance');
            @endphp
            <div class="bs-section-title">Long-term Assets</div>
            @forelse($longTermAssets as $account)
                <div class="bs-account-row">
                    <span>{{ $account['name'] }}</span>
                    <span class="amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">{{ number_format($account['balance'], 2) }}</span>
                </div>
            @empty
                <div class="bs-account-row" style="color:#6b7280;font-style:italic"><span>No long-term assets</span><span>0.00</span></div>
            @endforelse
            <div class="bs-subtotal"><span>Total Long-term Assets</span><span>{{ number_format($longTermAssetsTotal, 2) }}</span></div>
            <div class="bs-total"><span>Total Assets</span><span>{{ number_format($balanceSheetData['totals']['assets'] ?? 0, 2) }}</span></div>
        </div>

        <div class="bs-column">
            <div class="bs-column-header">Liabilities &amp; Equity</div>

            @php
                $currentLiabilities = collect($balanceSheetData['liabilities'])->filter(function ($account) {
                    $name = strtolower($account['name']);
                    return stripos($name, 'current') !== false || stripos($name, 'payable') !== false ||
                        stripos($name, 'accrued') !== false || stripos($name, 'unearned') !== false ||
                        stripos($name, 'short-term') !== false;
                });
                $currentLiabilitiesTotal = $currentLiabilities->sum('balance');
            @endphp
            <div class="bs-section-title">Current Liabilities</div>
            @forelse($currentLiabilities as $account)
                <div class="bs-account-row">
                    <span>{{ $account['name'] }}</span>
                    <span class="amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">{{ number_format($account['balance'], 2) }}</span>
                </div>
            @empty
                <div class="bs-account-row" style="color:#6b7280;font-style:italic"><span>No current liabilities</span><span>0.00</span></div>
            @endforelse
            <div class="bs-subtotal"><span>Total Current Liabilities</span><span>{{ number_format($currentLiabilitiesTotal, 2) }}</span></div>

            @php
                $longTermLiabilities = collect($balanceSheetData['liabilities'])->filter(function ($account) {
                    $name = strtolower($account['name']);
                    return stripos($name, 'current') === false && stripos($name, 'payable') === false &&
                        stripos($name, 'accrued') === false && stripos($name, 'unearned') === false &&
                        stripos($name, 'short-term') === false;
                });
                $longTermLiabilitiesTotal = $longTermLiabilities->sum('balance');
            @endphp
            <div class="bs-section-title">Long-term Liabilities</div>
            @forelse($longTermLiabilities as $account)
                <div class="bs-account-row">
                    <span>{{ $account['name'] }}</span>
                    <span class="amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">{{ number_format($account['balance'], 2) }}</span>
                </div>
            @empty
                <div class="bs-account-row" style="color:#6b7280;font-style:italic"><span>No long-term liabilities</span><span>0.00</span></div>
            @endforelse
            <div class="bs-subtotal"><span>Total Long-term Liabilities</span><span>{{ number_format($longTermLiabilitiesTotal, 2) }}</span></div>
            <div class="bs-subtotal"><span>Total Liabilities</span><span>{{ number_format($balanceSheetData['totals']['liabilities'] ?? 0, 2) }}</span></div>

            <div class="bs-section-title">Owner's Equity</div>
            @forelse($balanceSheetData['equity'] ?? [] as $account)
                <div class="bs-account-row">
                    <span>{{ $account['name'] }}</span>
                    <span class="amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">{{ number_format($account['balance'], 2) }}</span>
                </div>
            @empty
                <div class="bs-account-row" style="color:#6b7280;font-style:italic"><span>No equity accounts</span><span>0.00</span></div>
            @endforelse
            <div class="bs-subtotal"><span>Total Owner's Equity</span><span>{{ number_format($balanceSheetData['totals']['equity'] ?? 0, 2) }}</span></div>
            <div class="bs-total"><span>Total Liabilities and Owner's Equity</span><span>{{ number_format($balanceSheetData['totals']['liabilities_and_equity'] ?? 0, 2) }}</span></div>
        </div>
    </div>

    <div class="bs-grand-total">
        <div><span>Total Assets</span><span>{{ number_format($balanceSheetData['totals']['assets'] ?? 0, 2) }}</span></div>
        <div><span>Total Liabilities and Owner's Equity</span><span>{{ number_format($balanceSheetData['totals']['liabilities_and_equity'] ?? 0, 2) }}</span></div>
    </div>

    <div class="report-footer">
        <span>Generated by: {{ auth()->user()->name ?? 'User' }}</span>
        <span>Printed: @businessDateTime(now())</span>
    </div>

</div>

</body>
</html>
