<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Ledger - {{ $business->business_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            background: white;
            font-size: 14px;
        }

        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px;
            box-sizing: border-box;
            background: white;
        }

        .report-container {
            width: 100%;
            position: relative;
        }

        .back-button {
            margin-bottom: 30px;
        }

        .back-button a {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .back-button a:hover {
            background-color: #5a6268;
        }

        .back-button a svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
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

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .summary-card {
            background: white;
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .summary-card h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: 600;
            color: #666666;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            font-family: 'Inter', monospace;
        }

        .summary-value.positive {
            color: #28a745;
        }

        .summary-value.negative {
            color: #dc3545;
        }

        .filters {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
        }
        
        .filter-inputs {
            display: flex;
            flex-wrap: wrap;
            gap: 16px 24px;
            flex: 1;
        }
        
        .filter-buttons {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
        
        .filter-form .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
            min-width: 160px;
            max-width: 200px;
            flex: 0 0 auto;
        }
        
        .filter-form label {
            font-size: 13px;
            margin-bottom: 4px;
            font-weight: 500;
            color: #666666;
        }
        
        .filter-form input[type="date"] {
            font-size: 13px;
            padding: 8px 12px;
            height: 36px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        .filter-form select {
            font-size: 13px;
            padding: 8px 12px;
            height: 36px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            background-color: white;
            cursor: pointer;
        }
        
        .filter-buttons button,
        .filter-buttons .btn-secondary {
            font-size: 13px;
            padding: 8px 16px;
            height: 36px;
            min-width: 80px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .filter-buttons button {
            background-color: #007bff;
            color: white;
            border: none;
        }
        
        .filter-buttons button:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .filter-form a {
            text-decoration: none;
        }
        
        @media (max-width: 700px) {
            .filter-form {
                flex-direction: column;
                gap: 10px 0;
                align-items: stretch;
            }
            .filter-form .form-group {
                max-width: 100%;
            }
        }

        .general-ledger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 30px;
            background: white;
        }

        .general-ledger-table th {
            background-color: white;
            font-weight: 700;
            font-size: 14px;
            color: #1a1a1a;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            font-family: 'Inter', sans-serif;
            position: relative;
        }

        .general-ledger-table th:first-child {
            text-align: left;
        }

        .general-ledger-table th:not(:first-child) {
            text-align: right;
        }

        .general-ledger-table th.sortable {
            cursor: pointer;
            user-select: none;
        }

        .general-ledger-table th.sortable:hover {
            background-color: #f8f9fa;
        }

        .sort-icon {
            display: inline-block;
            margin-left: 8px;
            font-size: 12px;
            color: #666;
        }

        .general-ledger-table td {
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
            font-family: 'Inter', sans-serif;
        }

        .general-ledger-table td:first-child {
            text-align: left;
        }

        .general-ledger-table td:not(:first-child) {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 500;
        }

        .account-name {
            color: #1a1a1a;
            font-weight: 400;
        }

        .account-code {
            color: #666666;
            font-size: 12px;
            margin-right: 8px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 12px;
            color: #666666;
        }

        .footer p {
            margin: 4px 0;
        }

        .excluded-accounts-notice {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
        }

        .excluded-accounts-notice h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
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

        @media print {
            body {
                background: white;
            }
            .page-container {
                padding: 0;
            }
            .filters, .back-button {
                display: none;
            }
            .general-ledger-table th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            

            <x-report-header :business="$business" title="General Ledger">
                <div>Basis: {{ ucfirst($basis) }}</div>
                <div>For the period @businessDate($fromDate) to @businessDate($toDate)</div>
            </x-report-header>

            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Debit</h4>
                    <div class="summary-value">{{ number_format($generalLedgerData['totals']['total_debit'], 2) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Credit</h4>
                    <div class="summary-value">{{ number_format($generalLedgerData['totals']['total_credit'], 2) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Balance</h4>
                    <div class="summary-value {{ $generalLedgerData['totals']['total_balance'] >= 0 ? 'positive' : 'negative' }}">
                        {{ number_format($generalLedgerData['totals']['total_balance'], 2) }}
                    </div>
                </div>
            </div>

            @if($basis === 'cash' && !empty($generalLedgerData['excluded_accounts']))
            <div class="excluded-accounts-notice">
                <h4>
                    <svg style="width: 16px; height: 16px; margin-right: 6px; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Cash Basis Notice
                </h4>
                <p>
                    The following accounts are excluded in cash basis accounting as they represent non-cash transactions:
                </p>
                <ul>
                    @foreach($generalLedgerData['excluded_accounts'] as $excludedAccount)
                        <li>{{ $excludedAccount }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="filters">
                <form action="{{ route('general-ledger.index') }}" method="GET" class="filter-form">
                    <div class="filter-inputs">
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
                    </div>
                    <div class="filter-buttons">
                        <button type="submit">Apply Filters</button>
                        <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                        <a href="{{ route('finance.index') }}">
                            <button type="button" class="btn-secondary">Back to Finance</button>
                        </a>
                    </div>
                </form>
            </div>

            <table class="general-ledger-table">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0)">
                            ACCOUNT
                            <span class="sort-icon">↕</span>
                        </th>
                        <th>DEBIT</th>
                        <th>CREDIT</th>
                        <th>BALANCE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($generalLedgerData['accounts'] as $account)
                    <tr>
                        <td class="account-name">
                            <span class="account-code">{{ $account['code'] }}</span>
                            {{ $account['name'] }}
                        </td>
                        <td>{{ number_format($account['debit'], 2) }}</td>
                        <td>{{ number_format($account['credit'], 2) }}</td>
                        <td>{{ number_format($account['balance'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #666;">
                            No accounts found for the selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="footer">
                <p>This is a computer generated report and does not require a signature.</p>
                <p>Generated by {{ config('app.name') }} on @businessDateTime(now())</p>
            </div>
        </div>
    </div>

    <script>
        function sortTable(columnIndex) {
            const table = document.querySelector('.general-ledger-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Toggle sort direction
            const isAscending = table.getAttribute('data-sort-direction') !== 'asc';
            table.setAttribute('data-sort-direction', isAscending ? 'asc' : 'desc');
            
            rows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();
                
                // Handle numeric columns
                if (columnIndex > 0) {
                    const aNum = parseFloat(aValue.replace(/,/g, '')) || 0;
                    const bNum = parseFloat(bValue.replace(/,/g, '')) || 0;
                    return isAscending ? aNum - bNum : bNum - aNum;
                }
                
                // Handle text columns
                return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            });
            
            // Clear tbody and append sorted rows
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
            
            // Update sort icon
            const sortIcon = table.querySelector('.sort-icon');
            sortIcon.textContent = isAscending ? '↑' : '↓';
        }
    </script>
</body>
</html>
