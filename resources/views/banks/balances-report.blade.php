<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Balances Report - {{ $business->business_name }} - Bank Management - StoreBook</title>
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

        input[type="date"] {
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
            background-color: #0d6efd;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        button:hover {
            background-color: #0b5ed7;
        }

        .tables-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .table-container {
            border: 1px solid #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background-color: #fff;
            font-weight: 600;
            text-transform: none;
            font-size: 13px;
            color: #000;
            border: 1px solid #333;
            padding: 8px;
        }

        td {
            border: 1px solid #333;
            padding: 8px;
            background-color: #fff;
        }

        .amount {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 500;
        }

        .negative-balance {
            color: #cc0000;
            font-weight: 600;
        }

        .positive-balance {
            color: #009900;
            font-weight: 600;
        }

        .total-row td {
            font-weight: 600;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .grand-total {
            margin-top: 20px;
            border: 1px solid #333;
            padding: 15px;
            text-align: right;
            background-color: #f8f9fa;
        }

        .grand-total-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .grand-total-amount {
            font-size: 18px;
            font-weight: 700;
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
            .report-header {
                display: flex !important;
                justify-content: space-between !important;
                gap: 30px !important;
            }
            .report-header-left {
                text-align: left !important;
            }
            .report-header-right {
                text-align: right !important;
            }
            .business-info {
                text-align: left !important;
            }
            .report-title {
                text-align: right !important;
            }
            th {
                background-color: #f8f8f8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .total-row td {
                background-color: #f8f8f8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media (max-width: 768px) {
            .tables-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Bank Balances Report">
                <div><strong>Duration:</strong> As of 
                    @php
                        // Use asOfDate from controller, fallback to request, then default
                        $displayDate = $asOfDate ?? request('date');
                        if (!$displayDate) {
                            $displayDate = now()->format('Y-m-d');
                        }
                        
                        // Format date directly without timezone conversion for date-only values
                        try {
                            $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $displayDate);
                            $businessDateFormat = getBusinessDateFormat();
                            $formattedDate = $dateObj->format($businessDateFormat);
                        } catch (\Exception $e) {
                            $formattedDate = formatBusinessDate($displayDate);
                        }
                    @endphp
                    {{ $formattedDate }}
                </div>
            </x-report-header>

            <div class="filters">
                <form action="{{ route('banks.balances-report') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="date">Select Date</label>
                        <input type="date" name="date" id="date" value="{{ request('date', now()->format('Y-m-d')) }}">
                    </div>

                    <button type="submit" style="margin-left: 8px;">Search</button>
                    <button type="button" onclick="window.print()">Print</button>
                    <a href="{{ route('bank-management') }}" class="back-btn">
                        <button type="button" style="background-color: #6c757d;">Back</button>
                    </a>
                </form>
            </div>

            <div class="tables-container">
                <!-- Bank Accounts Table -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="3" style="text-align: center; background-color: #f8f9fa;">Bank Accounts</th>
                            </tr>
                            <tr>
                                <th>Sr.</th>
                                <th>Account Name</th>
                                <th class="amount">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $bankTotal = 0; @endphp
                            @forelse($bankAccounts as $index => $account)
                                @php $bankTotal += $account->balance; @endphp
                                <tr style="{{ $account->status == 0 ? 'opacity: 0.6;' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ strtoupper($account->chartOfAccount->name ?? $account->account_name) }}
                                        @if($account->status == 0)
                                            <span style="color: #dc2626; font-size: 10px; margin-left: 5px;">(Inactive)</span>
                                        @endif
                                    </td>
                                    <td class="amount {{ $account->balance >= 0 ? 'positive-balance' : 'negative-balance' }}">
                                        {{ number_format(round($account->balance), 0) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No bank accounts found</td>
                                </tr>
                            @endforelse
                            <tr class="total-row">
                                <td colspan="2" style="text-align: right"><strong>Total Bank Balance</strong></td>
                                <td class="amount {{ $bankTotal >= 0 ? 'positive-balance' : 'negative-balance' }}">
                                    {{ number_format(round($bankTotal), 0) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Cash Accounts Table -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="3" style="text-align: center; background-color: #f8f9fa;">Cash Accounts</th>
                            </tr>
                            <tr>
                                <th>Sr.</th>
                                <th>Account Name</th>
                                <th class="amount">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $cashTotal = 0; @endphp
                            @forelse($cashAccounts as $index => $account)
                                @php $cashTotal += $account->balance; @endphp
                                <tr style="{{ $account->status == 0 ? 'opacity: 0.6;' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ strtoupper($account->chartOfAccount->name ?? $account->account_name) }}
                                        @if($account->status == 0)
                                            <span style="color: #dc2626; font-size: 10px; margin-left: 5px;">(Inactive)</span>
                                        @endif
                                    </td>
                                    <td class="amount {{ $account->balance >= 0 ? 'positive-balance' : 'negative-balance' }}">
                                        {{ number_format(round($account->balance), 0) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No cash accounts found</td>
                                </tr>
                            @endforelse
                            <tr class="total-row">
                                <td colspan="2" style="text-align: right"><strong>Total Cash Balance</strong></td>
                                <td class="amount {{ $cashTotal >= 0 ? 'positive-balance' : 'negative-balance' }}">
                                    {{ number_format(round($cashTotal), 0) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="grand-total">
                <div class="grand-total-label">Total Balance (Bank + Cash)</div>
                <div class="grand-total-amount" style="color: {{ ($bankTotal + $cashTotal) >= 0 ? '#009900' : '#cc0000' }}">
                    {{ number_format(round($bankTotal + $cashTotal), 0) }}
                </div>
            </div>

            <!-- Report Footer -->
            <div style="margin-top: 20px; text-align: right; font-size: 11px; color: #666;">
                <p>Generated by: {{ auth()->user()->name }} | Print time: @businessDateTime(now())</p>
                <p style="margin-top: 5px;">Powered By GrowBusiness 365</p>
            </div>
        </div>
    </div>
</body>
</html>