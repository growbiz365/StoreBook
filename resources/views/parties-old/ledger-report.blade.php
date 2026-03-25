<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Party Ledger Report - {{ $business->business_name }} - Party Management - StoreBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chosen CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
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
            font-size: 18px; /* Match business name heading size */
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
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 10px;
            border-radius: 4px;
        }

        .summary-card h4 {
            margin: 0 0 6px 0;
            font-size: 12px;
            font-weight: 500;
            color: #444;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 600;
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

        .form-group.date-group {
            flex: 0 0 180px;
            min-width: 180px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 12px;
            color: #444;
        }

        select, input {
            width: 100%;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        /* Chosen Select Styling */
        .chosen-container {
            width: 100% !important;
        }

        .chosen-container-single .chosen-single {
            height: 36px;
            line-height: 34px;
            padding: 0 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            background: white;
        }

        .chosen-container-single .chosen-single:hover {
            border-color: #0d6efd;
        }

        .chosen-container-single .chosen-single:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .chosen-container-single .chosen-single div b {
            background-position: 0px 2px;
        }

        .chosen-container-active .chosen-single div b {
            background-position: -18px 2px;
        }

        .chosen-drop {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chosen-results {
            font-size: 13px;
            font-family: 'Inter', sans-serif;
        }

        .chosen-results li {
            padding: 8px 12px;
        }

        .chosen-results li.highlighted {
            background-color: #0d6efd;
            color: white;
        }

        input[type="date"] {
            padding-right: 30px;
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

        .table-container {
            margin-top: 20px;
            overflow-x: auto;
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

        .credit-amount {
            color: #009900;
            font-weight: 600;
        }

        .debit-amount {
            color: #cc0000;
            font-weight: 600;
        }

        .opening-balance-row {
            background-color: #e8ffe8 !important;
        }

        .opening-balance-row td {
            background-color: #e8ffe8 !important;
            font-weight: 600;
        }

        .total-row td {
            font-weight: 600;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .urdu {
            font-size: 11px;
            color: #666;
            display: block;
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
            div[style*="Total Records"] {
                float: right !important;
                margin: 15px 0 10px 0 !important;
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
            .total-row {
                background-color: #f8f8f8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Party Ledger Report">
                        @if($selectedParty)
                            <div><strong>Party Name:</strong> {{ $selectedParty->name }}</div>
                        @endif
                        <div><strong>Duration:</strong> @businessDate(request('from_date', now()->startOfMonth())) to @businessDate(request('to_date', now()))</div>
            </x-report-header>

            @if($selectedParty)
                <div class="summary-grid">
                    <div class="summary-card">
                        <h4>Total Debit</h4>
                        <div class="summary-value">{{ number_format($totals['debit'], 2) }}</div>
                    </div>
                    <div class="summary-card">
                        <h4>Total Credit</h4>
                        <div class="summary-value">{{ number_format($totals['credit'], 2) }}</div>
                    </div>
                    <div class="summary-card">
                        <h4>Balance</h4>
                        <div class="summary-value">{{ number_format($totals['balance'], 2) }}</div>
                    </div>
                </div>
            @endif

            <div class="filters">
                <form action="{{ route('parties.ledger-report') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="party_id">Select Party</label>
                        <select name="party_id" id="party_id" required class="chosen-select">
                            <option value="">Select a party</option>
                            @foreach($parties as $party)
                                <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>
                                    {{ $party->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group date-group">
                        <label for="from_date">From Date</label>
                        <input type="date" name="from_date" id="from_date"
                            value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group date-group">
                        <label for="to_date">To Date</label>
                        <input type="date" name="to_date" id="to_date"
                            value="{{ request('to_date', now()->format('Y-m-d')) }}">
                    </div>

                    <button type="submit">Apply</button>
                    @if($selectedParty)
                        <button type="button" onclick="window.print()">Print</button>
                    @endif
                    <a href="{{ route('party-management.dashboard') }}" class="back-btn">
                        <button type="button" style="background-color: #6c757d;">Back</button>
                    </a>
                </form>
            </div>

            @if($selectedParty)
                <!-- Total Records Info -->
                <div style="margin: 15px 0 10px 0; text-align: right; font-size: 13px; color: #444; font-weight: 600; padding: 8px 12px; background-color: #f8f9fa; border-radius: 4px; display: inline-block; float: right;">
                    Total Records: <strong>{{ $ledgerEntries->count() }}</strong>
                </div>
                <div style="clear: both;"></div>

                <div class="table-container" style="margin-top: 10px;">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    Date
                                    <span class="urdu">(تاریخ)</span>
                                </th>
                                <th>
                                    Voucher
                                    <span class="urdu">(واؤچر)</span>
                                </th>
                                <th>
                                    Description
                                    <span class="urdu">(تفصیلات)</span>
                                </th>
                                <th>
                                    Credit
                                    <span class="urdu">(جمع)</span>
                                </th>
                                <th>
                                    Debit
                                    <span class="urdu">(نام)</span>
                                </th>
                                <th>
                                    Balance
                                    <span class="urdu">(بقایا)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Opening Balance Row -->
                            <tr class="opening-balance-row">
                                <td>@businessDate(request('from_date', $selectedParty->created_at))</td>
                                <td>-</td>
                                <td><strong>*** Opening Balance ***</strong></td>
                                <td class="amount">-</td>
                                <td class="amount">-</td>
                                <td class="amount">{{ number_format($openingBalance, 2) }}</td>
                            </tr>

                            @foreach($ledgerEntries as $entry)
                                <tr>
                                    <td>@businessDate($entry->date_added)</td>
                                    <td>{{ $entry->voucher_type }} #{{ $entry->voucher_id }}</td>
                                    <td>
                                        @if($entry->voucher_type == 'Party Transfer' && $entry->partyTransfer)
                                            @if($entry->debit_amount > 0)
                                                Debit Party: {{ $entry->partyTransfer->creditParty->name }}
                                                @if($entry->partyTransfer->details)
                                                    , {{ $entry->partyTransfer->details }}
                                                @endif
                                            @else
                                                Credit Party: {{ $entry->partyTransfer->debitParty->name }}
                                                @if($entry->partyTransfer->details)
                                                    , {{ $entry->partyTransfer->details }}
                                                @endif
                                            @endif
                                        @else
                                            {{ $entry->voucher_type }}
                                        @endif
                                    </td>
                                    <td class="amount">
                                        @if($entry->credit_amount > 0)
                                            <span class="credit-amount">{{ number_format($entry->credit_amount, 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="amount">
                                        @if($entry->debit_amount > 0)
                                            <span class="debit-amount">{{ number_format($entry->debit_amount, 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="amount">{{ number_format($entry->running_balance, 2) }}</td>
                                </tr>
                            @endforeach

                            <!-- Total Row -->
                            <tr class="total-row">
                                <td colspan="3" style="text-align: center"><strong>Total</strong></td>
                                <td class="amount credit-amount">{{ number_format($totals['credit'], 2) }}</td>
                                <td class="amount debit-amount">{{ number_format($totals['debit'], 2) }}</td>
                                <td class="amount">{{ number_format($totals['balance'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Report Footer -->
                <div style="margin-top: 20px; text-align: right; font-size: 11px; color: #666;">
                    <p>Generated by: {{ auth()->user()->name }} | Print time: @businessDateTime(now())</p>
                    <p style="margin-top: 5px;">Powered By GrowBusiness 365</p>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <p>Please select a party to view their ledger report.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chosen JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Chosen select
            $('.chosen-select').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'Select a party'
            });
        });
    </script>
</body>
</html> 