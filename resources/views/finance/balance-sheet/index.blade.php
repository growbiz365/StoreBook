<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet - {{ $business->business_name }}</title>
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
        
        .balance-sheet-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            }

        .column {
            border: 1px solid #2c3e50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .column-header {
            padding: 12px;
            background-color: #34495e;
            color: white;
            border-bottom: 2px solid #2c3e50;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .section {
            border-bottom: 1px solid #333;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section-title {
            padding: 8px 12px;
            background-color: #ecf0f1;
            border-bottom: 1px solid #bdc3c7;
            font-size: 12px;
            font-weight: 600;
            color: #2c3e50;
        }

        .account-list {
            padding: 0;
            margin: 0;
        }

        .account-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 12px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }

        .account-item:last-child {
            border-bottom: none;
        }

        .account-name {
            flex: 1;
            padding-left: 15px;
        }

        .account-amount {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 500;
            min-width: 100px;
        }

        .subtotal-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            background-color: #ecf0f1;
            border-top: 1px solid #bdc3c7;
            border-bottom: 1px solid #bdc3c7;
            font-weight: 600;
            font-size: 11px;
            color: #2c3e50;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
            background-color: #d5dbdb;
            border-top: 2px solid #2c3e50;
            font-weight: 700;
            font-size: 12px;
            color: #1e3a5f;
        }

        .grand-total {
            margin-top: 20px;
            border: 2px solid #1e3a5f;
            background-color: #34495e;
            color: white;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .grand-total-item {
            padding: 14px;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
        }

        .grand-total-item:first-child {
            border-right: 2px solid #1e3a5f;
        }

        .footer {
            margin-top: 20px;
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

        .warnings-notice {
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        .warnings-notice h4 {
            margin: 0 0 10px 0;
            color: #721c24;
            font-size: 14px;
            font-weight: 600;
        }

        .warnings-notice ul {
            margin: 0;
            padding-left: 20px;
            color: #721c24;
            font-size: 13px;
        }

        .warnings-notice li {
            margin-bottom: 6px;
            line-height: 1.5;
        }

        .negative-balance {
            color: #dc3545;
            font-weight: 600;
        }

        .inventory-adjustment-notice {
            margin-bottom: 15px;
            padding: 15px;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
        }

        .inventory-adjustment-notice h4 {
            margin: 0 0 10px 0;
            color: #0c5460;
            font-size: 14px;
            font-weight: 600;
        }

        .inventory-adjustment-notice p {
            margin: 0 0 5px 0;
            color: #0c5460;
            font-size: 13px;
            line-height: 1.5;
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
            .column-header {
                background-color: #34495e !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .section-title,
            .subtotal-row {
                background-color: #ecf0f1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .total-row {
                background-color: #d5dbdb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .grand-total {
                background-color: #34495e !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Balance Sheet">
                <div>Basis: {{ ucfirst($basis) }}</div>
                <div>As of @businessDate($asOfDate)</div>
            </x-report-header>

            @if($basis === 'cash' && isset($balanceSheetData['excluded_accounts']) && !empty($balanceSheetData['excluded_accounts']))
            <div class="excluded-accounts-notice">
                <h4>Cash Basis Notice</h4>
                <p>
                    The following accounts are excluded in cash basis accounting as they represent non-cash transactions:
                </p>
                <ul>
                    @foreach($balanceSheetData['excluded_accounts'] as $excludedAccount)
                        <li>{{ $excludedAccount }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(isset($balanceSheetData['warnings']) && !empty($balanceSheetData['warnings']))
            <div class="warnings-notice">
                <h4>⚠️ Warnings</h4>
                <ul>
                    @foreach($balanceSheetData['warnings'] as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
            @endif


            <div class="filters">
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
                    <button type="submit">Apply Filter</button>
                    <button type="button" onclick="window.print()">Print Report</button>
                    <a href="{{ route('finance.index') }}" style="text-decoration: none;">
                            <button type="button" class="btn-secondary">Back to Finance</button>
                        </a>
                </form>
            </div>

            <div class="balance-sheet-grid">
                <!-- Left Column: Assets -->
                <div class="column">
                    <div class="column-header">Assets</div>

                    <!-- Current Assets Section -->
                    <div class="section">
                        <div class="section-title">Current Assets</div>
                        <div class="account-list">
                            @php
                                $currentAssets = collect($balanceSheetData['assets'])->filter(function($account) {
                                    $name = strtolower($account['name']);
                                    $code = $account['code'] ?? '';
                                    
                                    // Check by account code first - codes starting with 11xx or 12xx are Current Assets
                                    // 1110 = Cash in Hand, 1120 = Bank Accounts, 1200 = Receivables, etc.
                                    // This handles codes like 1100, 1110, 1120, 1121, 1200, 1210, etc.
                                    if (preg_match('/^11/', $code) || preg_match('/^12/', $code)) {
                                        return true;
                                    }
                                    
                                    // Fallback to name-based matching for accounts without proper codes
                                    return stripos($name, 'current') !== false ||
                                           stripos($name, 'cash') !== false ||
                                           stripos($name, 'receivable') !== false ||
                                           stripos($name, 'inventory') !== false ||
                                           stripos($name, 'prepaid') !== false ||
                                           stripos($name, 'bank') !== false ||
                                           stripos($name, 'short-term') !== false;
                                });
                                $currentAssetsTotal = $currentAssets->sum('balance');
                            @endphp
                            @if($currentAssets->count() > 0)
                                @foreach($currentAssets as $account)
                                    <div class="account-item">
                                        <span class="account-name">
                                            {{ $account['name'] }}
                                            @if(!empty($account['is_adjusted']))
                                                <span style="font-size: 9px; color: #0c5460;">(Adjusted)</span>
                                            @endif
                                        </span>
                                        <span class="account-amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">
                                            {{ number_format($account['balance'], 2) }}
                                        </span>
                                    </div>
                        @endforeach
                    @else
                                <div class="account-item" style="color: #999; font-style: italic;">
                                    <span class="account-name">No current assets found</span>
                                    <span class="account-amount">0.00</span>
                                </div>
                    @endif
                        </div>
                        <div class="subtotal-row">
                            <span>Total Current Assets</span>
                            <span>{{ number_format($currentAssetsTotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Long-term Assets Section -->
                    <div class="section">
                        <div class="section-title">Long-term Assets</div>
                        <div class="account-list">
                            @php
                                $longTermAssets = collect($balanceSheetData['assets'])->filter(function($account) {
                                    $name = strtolower($account['name']);
                                    $code = $account['code'] ?? '';
                                    
                                    // Exclude accounts with codes starting with 11xx or 12xx (Current Assets)
                                    // This handles codes like 1100, 1110, 1120, 1121, 1200, 1210, etc.
                                    if (preg_match('/^11/', $code) || preg_match('/^12/', $code)) {
                                        return false;
                                    }
                                    
                                    // Exclude by name keywords
                                    return stripos($name, 'current') === false &&
                                           stripos($name, 'cash') === false &&
                                           stripos($name, 'receivable') === false &&
                                           stripos($name, 'inventory') === false &&
                                           stripos($name, 'prepaid') === false &&
                                           stripos($name, 'bank') === false &&
                                           stripos($name, 'short-term') === false;
                                });
                                $longTermAssetsTotal = $longTermAssets->sum('balance');
                            @endphp
                            @if($longTermAssets->count() > 0)
                                @foreach($longTermAssets as $account)
                                    <div class="account-item">
                                        <span class="account-name">{{ $account['name'] }}</span>
                                        <span class="account-amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">
                                            {{ number_format($account['balance'], 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <div class="account-item" style="color: #999; font-style: italic;">
                                    <span class="account-name">No long-term assets found</span>
                                    <span class="account-amount">0.00</span>
                                </div>
                            @endif
                        </div>
                        <div class="subtotal-row">
                            <span>Total Long-term Assets</span>
                            <span>{{ number_format($longTermAssetsTotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Total Assets -->
                    <div class="total-row">
                        <span>Total Assets</span>
                        <span>{{ number_format($balanceSheetData['totals']['assets'] ?? 0, 2) }}</span>
                    </div>
                </div>

                <!-- Right Column: Liabilities and Equity -->
                <div class="column">
                    <div class="column-header">Liabilities</div>
                    
                    <!-- Current Liabilities Section -->
                    <div class="section">
                        <div class="section-title">Current Liabilities</div>
                        <div class="account-list">
                            @php
                                $currentLiabilities = collect($balanceSheetData['liabilities'])->filter(function($account) {
                                    $name = strtolower($account['name']);
                                    return stripos($name, 'current') !== false ||
                                           stripos($name, 'payable') !== false ||
                                           stripos($name, 'accrued') !== false ||
                                           stripos($name, 'unearned') !== false ||
                                           stripos($name, 'short-term') !== false;
                                });
                                $currentLiabilitiesTotal = $currentLiabilities->sum('balance');
                            @endphp
                            @if($currentLiabilities->count() > 0)
                                @foreach($currentLiabilities as $account)
                                    <div class="account-item">
                                        <span class="account-name">{{ $account['name'] }}</span>
                                        <span class="account-amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">
                                            {{ number_format($account['balance'], 2) }}
                                        </span>
                                    </div>
                        @endforeach
                    @else
                                <div class="account-item" style="color: #999; font-style: italic;">
                                    <span class="account-name">No current liabilities found</span>
                                    <span class="account-amount">0.00</span>
                                </div>
                    @endif
                        </div>
                        <div class="subtotal-row">
                            <span>Total Current Liabilities</span>
                            <span>{{ number_format($currentLiabilitiesTotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Long-term Liabilities Section -->
                    <div class="section">
                        <div class="section-title">Long-term Liabilities</div>
                        <div class="account-list">
                            @php
                                $longTermLiabilities = collect($balanceSheetData['liabilities'])->filter(function($account) {
                                    $name = strtolower($account['name']);
                                    return stripos($name, 'current') === false &&
                                           stripos($name, 'payable') === false &&
                                           stripos($name, 'accrued') === false &&
                                           stripos($name, 'unearned') === false &&
                                           stripos($name, 'short-term') === false;
                                });
                                $longTermLiabilitiesTotal = $longTermLiabilities->sum('balance');
                            @endphp
                            @if($longTermLiabilities->count() > 0)
                                @foreach($longTermLiabilities as $account)
                                    <div class="account-item">
                                        <span class="account-name">{{ $account['name'] }}</span>
                                        <span class="account-amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">
                                            {{ number_format($account['balance'], 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <div class="account-item" style="color: #999; font-style: italic;">
                                    <span class="account-name">No long-term liabilities found</span>
                                    <span class="account-amount">0.00</span>
                                </div>
                            @endif
                        </div>
                        <div class="subtotal-row">
                            <span>Total Long-term Liabilities</span>
                            <span>{{ number_format($longTermLiabilitiesTotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Total Liabilities -->
                    <div class="subtotal-row">
                        <span>Total Liabilities</span>
                        <span>{{ number_format($balanceSheetData['totals']['liabilities'] ?? 0, 2) }}</span>
                    </div>

                    <!-- Owner's Equity Section -->
                    <div class="section">
                        <div class="section-title" style="margin-top: 10px;">Owner's Equity</div>
                        <div class="account-list">
                    @if(!empty($balanceSheetData['equity']))
                        @foreach($balanceSheetData['equity'] as $account)
                                    <div class="account-item">
                                        <span class="account-name">{{ $account['name'] }}</span>
                                        <span class="account-amount {{ !empty($account['is_negative']) ? 'negative-balance' : '' }}">
                                            {{ number_format($account['balance'], 2) }}
                                        </span>
                                    </div>
                        @endforeach
                    @else
                                <div class="account-item" style="color: #999; font-style: italic;">
                                    <span class="account-name">No equity accounts found</span>
                                    <span class="account-amount">0.00</span>
                                </div>
                    @endif
                        </div>
                        <div class="subtotal-row">
                            <span>Total Owner's Equity</span>
                            <span>{{ number_format($balanceSheetData['totals']['equity'] ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <!-- Total Liabilities and Owner's Equity -->
                    <div class="total-row">
                        <span>Total Liabilities and Owner's Equity</span>
                        <span>{{ number_format($balanceSheetData['totals']['liabilities_and_equity'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Grand Total Row - Single Line with Left and Right -->
            <div class="grand-total">
                <div class="grand-total-item">
                    <span>Total Assets</span>
                    <span>{{ number_format($balanceSheetData['totals']['assets'] ?? 0, 2) }}</span>
                </div>
                <div class="grand-total-item">
                    <span>Total Liabilities and Owner's Equity</span>
                    <span>{{ number_format($balanceSheetData['totals']['liabilities_and_equity'] ?? 0, 2) }}</span>
                </div>
            </div>

            <div class="footer">
                <p>This is a computer generated report and does not require a signature.</p>
                <p>Generated by {{ config('app.name') }} on @businessDateTime(now())</p>
            </div>
        </div>
    </div>
</body>
</html>
