<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - {{ $business ? $business->business_name : 'Business' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
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
            max-width: 1400px;
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

        .business-info {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .business-logo {
            height: 50px;
            margin-bottom: 10px;
        }

        .business-info h2 {
            margin: 8px 0;
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .business-info-details {
            font-size: 13px;
            color: #666666;
            line-height: 1.6;
        }

        .report-title {
            text-align: center;
            margin: 30px 0;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .report-title h2 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .report-title div {
            font-size: 13px;
            color: #666666;
            margin: 2px 0;
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
        
        .filter-form input[type="text"],
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

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
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

        .activity-logs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 30px;
            background: white;
        }

        .activity-logs-table th {
            background-color: white;
            font-weight: 700;
            font-size: 14px;
            color: #1a1a1a;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            font-family: 'Inter', sans-serif;
        }

        .activity-logs-table th.sortable {
            cursor: pointer;
            user-select: none;
        }

        .activity-logs-table th.sortable:hover {
            background-color: #f8f9fa;
        }

        .sort-icon {
            display: inline-block;
            margin-left: 8px;
            font-size: 12px;
            color: #666;
        }

        .activity-logs-table td {
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
            font-family: 'Inter', sans-serif;
        }

        .activity-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            margin-top: 4px;
        }

        .activity-badge.created {
            background-color: #d4edda;
            color: #155724;
        }

        .activity-badge.updated {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .activity-badge.deleted {
            background-color: #f8d7da;
            color: #721c24;
        }

        .activity-badge.posted {
            background-color: #cce5ff;
            color: #004085;
        }

        .activity-badge.cancelled {
            background-color: #fff3cd;
            color: #856404;
        }

        .activity-badge.approved {
            background-color: #d1f2eb;
            color: #00695c;
        }

        .activity-badge.rejected {
            background-color: #ffebee;
            color: #c62828;
        }

        .activity-badge.suspended {
            background-color: #fce4ec;
            color: #880e4f;
        }

        .activity-badge.activated {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .activity-badge.deactivated {
            background-color: #f3e5f5;
            color: #6a1b9a;
        }

        .activity-badge.assigned {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .activity-badge.revoked {
            background-color: #fce4ec;
            color: #c2185b;
        }

        .user-info {
            font-size: 13px;
            color: #1a1a1a;
            font-weight: 400;
        }

        .ip-address {
            font-family: 'Inter', monospace;
            font-size: 11px;
            color: #666666;
            margin-top: 4px;
        }

        .module-name {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .subject-info {
            font-size: 11px;
            color: #666666;
            margin-top: 4px;
        }

        .changes-container {
            font-size: 11px;
            color: #666666;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #f0f0f0;
        }

        .change-item {
            margin-bottom: 4px;
        }

        .change-item strong {
            color: #1a1a1a;
        }

        .old-value {
            color: #dc3545;
        }

        .new-value {
            color: #28a745;
        }

        .pagination-info {
            margin: 20px 0;
            padding: 12px 16px;
            background: #f8f9fa;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #666666;
        }

        .pagination-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .per-page-selector {
            padding: 4px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            background: white;
            margin-left: 8px;
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
            .activity-logs-table th {
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
            

            <div class="business-info">
                @if($business && $business->logo)
                    <img src="{{ asset('storage/' . $business->logo) }}" alt="Business Logo" class="business-logo">
                @endif
                <h2>{{ $business ? $business->business_name : 'Business Name' }}</h2>
                <div class="business-info-details">
                    @if($business)
                        {{ $business->address ?? 'Address' }}<br>
                        @if($business->contact_no)
                            Phone: {{ $business->contact_no }} | 
                        @endif
                        @if($business->email)
                            Email: {{ $business->email }}
                        @endif
                    @else
                        Address: Not Available<br>
                        Phone: Not Available | Email: Not Available
                    @endif
                </div>
            </div>

            <div class="report-title">
                <h2>Activity Logs</h2>
                <div>For the period {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}</div>
            </div>

            <div class="filters">
                <form action="{{ route('activity-logs.index') }}" method="GET" class="filter-form">
                    <div class="filter-inputs">
                        

                        <div class="form-group">
                            <label for="date_from">From Date</label>
                            <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" required>
                        </div>

                        <div class="form-group">
                            <label for="date_to">To Date</label>
                            <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" required>
                        </div>
                    </div>
                    <div class="filter-buttons">
                        <button type="submit">Apply Filters</button>
                        <button type="button" onclick="window.print()" class="btn-secondary">Print</button>

                        <a href="{{ route('dashboard') }}" class="btn-secondary">Back to Dashboard</a>
                                            </div>
                </form>
            </div>

            @if($logs && $logs->count() > 0)
                <div class="pagination-info">
                    <div class="pagination-left">
                        <span>Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries</span>
                        <select name="per_page" id="per_page" onchange="changePerPage(this.value)" class="per-page-selector">
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                        </select>
                        <span>per page</span>
                    </div>
                </div>

                <table class="activity-logs-table">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable(0)" style="width: 12%;">
                                DATE
                                <span class="sort-icon">↕</span>
                            </th>
                            <th style="width: 14%;">MODULE</th>
                            <th style="width: 10%;">EVENT</th>
                            <th style="width: 13%;">SUBJECT</th>
                            <th style="width: 51%;">DESCRIPTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $defaultTimezone = 'Asia/Karachi';
                                $businessTimezone = $timezone ?? $defaultTimezone;
                                
                                if (isset($log->created_at_tz) && $log->created_at_tz instanceof \Carbon\Carbon) {
                                    $logDate = $log->created_at_tz;
                                } else {
                                    try {
                                        $logDate = \Carbon\Carbon::parse($log->created_at);
                                        if (!empty($businessTimezone)) {
                                            $logDate = $logDate->setTimezone($businessTimezone);
                                        }
                                    } catch (\Exception $e) {
                                        $logDate = \Carbon\Carbon::parse($log->created_at)->setTimezone($defaultTimezone);
                                    }
                                }

                                // Module name
                                $moduleNames = [
                                    // System & User Management
                                    'user' => 'User',
                                    'business' => 'Business',
                                    'role' => 'Role',
                                    'permission' => 'Permission',
                                    
                                    // Configuration & Master Data
                                    'armstype' => 'Arm Type',
                                    'armsmake' => 'Arm Make',
                                    'armscategory' => 'Arm Category',
                                    'armscaliber' => 'Arm Caliber',
                                    'armscondition' => 'Arm Condition',
                                    'itemtype' => 'Item Type',
                                    
                                    // Sales Module
                                    'saleinvoice' => 'Sale Invoice',
                                    'salereturn' => 'Sale Return',
                                    'purchase' => 'Purchase',
                                    'purchasereturn' => 'Purchase Return',
                                    
                                    // Inventory Module
                                    'arm' => 'Arm',
                                    'generalitem' => 'Item',
                                    'generalbatch' => 'Batch',
                                    'stockadjustment' => 'Stock Adjustment',
                                    
                                    // Party Module
                                    'party' => 'Party',
                                    'partyledger' => 'Party Ledger',
                                    'partytransfer' => 'Party Transfer',
                                    
                                    // Financial Module
                                    'bank' => 'Bank',
                                    'banktransfer' => 'Bank Transfer',
                                    'bankledger' => 'Bank Ledger',
                                    'expense' => 'Expense',
                                    'otherincome' => 'Other Income',
                                    'journalentry' => 'Journal Entry',
                                    'generalvoucher' => 'General Voucher',
                                    
                                    // Accounting Module
                                    'chartofaccount' => 'Chart of Account',
                                    
                                    // Approval Module
                                    'approval' => 'Approval',
                                    
                                    // Quotation Module
                                    'quotation' => 'Quotation',
                                ];
                                $moduleDisplay = $log->log_name ? ($moduleNames[strtolower($log->log_name)] ?? ucfirst(str_replace('_', ' ', $log->log_name))) : 'System';

                                // Event
                                $eventNames = [
                                    // Standard events
                                    'created' => 'Created',
                                    'updated' => 'Updated',
                                    'deleted' => 'Deleted',
                                    
                                    // Business events
                                    'posted' => 'Posted',
                                    'cancelled' => 'Cancelled',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    
                                    // User/Business events
                                    'suspended' => 'Suspended',
                                    'activated' => 'Activated',
                                    'deactivated' => 'Deactivated',
                                    
                                    // Role/Permission events
                                    'assigned' => 'Assigned',
                                    'revoked' => 'Revoked',
                                ];
                                $eventDisplay = $log->event ? ($eventNames[strtolower($log->event)] ?? ucfirst($log->event)) : '';

                                // Subject
                                $subjectDisplay = '';
                                if ($log->subject_type && $log->subject_id) {
                                    $subjectType = class_basename($log->subject_type);
                                    $subjectTypes = [
                                        // System & User Management
                                        'User' => 'User',
                                        'Business' => 'Business',
                                        'Role' => 'Role',
                                        'Permission' => 'Permission',
                                        
                                        // Configuration & Master Data
                                        'ArmsType' => 'Arm Type',
                                        'ArmsMake' => 'Arm Make',
                                        'ArmsCategory' => 'Arm Category',
                                        'ArmsCaliber' => 'Arm Caliber',
                                        'ArmsCondition' => 'Arm Condition',
                                        'ItemType' => 'Item Type',
                                        
                                        // Sales Module
                                        'SaleInvoice' => 'Invoice',
                                        'SaleReturn' => 'Sale Return',
                                        'Purchase' => 'Purchase',
                                        'PurchaseReturn' => 'Purchase Return',
                                        
                                        // Inventory Module
                                        'Arm' => 'Arm',
                                        'GeneralItem' => 'Item',
                                        'GeneralBatch' => 'Batch',
                                        'StockAdjustment' => 'Stock Adjustment',
                                        
                                        // Party Module
                                        'Party' => 'Party',
                                        'PartyLedger' => 'Party Ledger',
                                        'PartyTransfer' => 'Party Transfer',
                                        
                                        // Financial Module
                                        'Bank' => 'Bank',
                                        'BankTransfer' => 'Bank Transfer',
                                        'BankLedger' => 'Bank Ledger',
                                        'Expense' => 'Expense',
                                        'OtherIncome' => 'Other Income',
                                        'JournalEntry' => 'Journal Entry',
                                        'GeneralVoucher' => 'General Voucher',
                                        
                                        // Accounting Module
                                        'ChartOfAccount' => 'Account',
                                        
                                        // Approval Module
                                        'Approval' => 'Approval',
                                        
                                        // Quotation Module
                                        'Quotation' => 'Quotation',
                                    ];
                                    $subjectTypeDisplay = $subjectTypes[$subjectType] ?? $subjectType;
                                    $subjectDisplay = "{$subjectTypeDisplay}: #{$log->subject_id}";
                                }

                                // Description
                                $description = $log->description;
                                if (preg_match('/^(\w+)\s+#(\d+)\s+(created|updated|deleted)$/i', $description, $matches)) {
                                    $modelName = $matches[1];
                                    $modelId = $matches[2];
                                    $action = ucfirst($matches[3]);
                                    $modelDisplayNames = [
                                        // System & User Management
                                        'User' => 'User',
                                        'Business' => 'Business',
                                        'Role' => 'Role',
                                        'Permission' => 'Permission',
                                        
                                        // Configuration & Master Data
                                        'ArmsType' => 'Arm Type',
                                        'ArmsMake' => 'Arm Make',
                                        'ArmsCategory' => 'Arm Category',
                                        'ArmsCaliber' => 'Arm Caliber',
                                        'ArmsCondition' => 'Arm Condition',
                                        'ItemType' => 'Item Type',
                                        
                                        // Sales Module
                                        'SaleInvoice' => 'Invoice',
                                        'SaleReturn' => 'Sale Return',
                                        'Purchase' => 'Purchase Order',
                                        'PurchaseReturn' => 'Purchase Return',
                                        
                                        // Inventory Module
                                        'Arm' => 'Arm',
                                        'GeneralItem' => 'Item',
                                        'GeneralBatch' => 'Batch',
                                        'StockAdjustment' => 'Stock Adjustment',
                                        
                                        // Party Module
                                        'Party' => 'Party',
                                        'PartyLedger' => 'Party Ledger',
                                        'PartyTransfer' => 'Party Transfer',
                                        
                                        // Financial Module
                                        'Bank' => 'Bank',
                                        'BankTransfer' => 'Bank Transfer',
                                        'BankLedger' => 'Bank Ledger',
                                        'Expense' => 'Expense',
                                        'OtherIncome' => 'Other Income',
                                        'JournalEntry' => 'Journal Entry',
                                        'GeneralVoucher' => 'General Voucher',
                                        
                                        // Accounting Module
                                        'ChartOfAccount' => 'Account',
                                        
                                        // Approval Module
                                        'Approval' => 'Approval',
                                        
                                        // Quotation Module
                                        'Quotation' => 'Quotation',
                                    ];
                                    $displayModelName = $modelDisplayNames[$modelName] ?? $modelName;
                                    $cleanDescription = "{$displayModelName} #{$modelId} was {$action}";
                                } else {
                                    $cleanDescription = $description;
                                }
                            @endphp
                            <tr>
                                <td>
                                    {{ $logDate->format('d M Y') }}<br>
                                    <span style="font-size: 12px; color: #666666;">{{ $logDate->format('h:i A') }}</span>
                                </td>
                                <td><div class="module-name">{{ $moduleDisplay }}</div></td>
                                <td>
                                    @if($log->event)
                                        <span class="activity-badge {{ $log->event }}">
                                            {{ $eventDisplay }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($subjectDisplay)
                                        <div class="subject-info">{{ $subjectDisplay }}</div>
                                    @else
                                        <span style="color: #999;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: #1a1a1a; margin-bottom: 4px; line-height: 1.4;">
                                        {{ $cleanDescription }}
                                        @if($log->properties && is_array($log->properties) && isset($log->properties['old']) && isset($log->properties['new']))
                                            @php
                                                $changes = [];
                                                foreach($log->properties['old'] as $key => $value) {
                                                    if(isset($log->properties['new'][$key]) && $log->properties['new'][$key] != $value) {
                                                        $fieldName = ucwords(str_replace('_', ' ', $key));
                                                        $oldValue = is_null($value) ? 'Empty' : (is_bool($value) ? ($value ? 'Yes' : 'No') : $value);
                                                        $newValue = is_null($log->properties['new'][$key]) ? 'Empty' : (is_bool($log->properties['new'][$key]) ? ($log->properties['new'][$key] ? 'Yes' : 'No') : $log->properties['new'][$key]);
                                                        $changes[] = "{$fieldName}: {$oldValue} → {$newValue}";
                                                    }
                                                }
                                            @endphp
                                            @if(!empty($changes))
                                                <span style="margin-left: 8px; font-size: 11px; color: #666666; font-weight: 400;">
                                                    @foreach($changes as $change)
                                                        <span style="margin-right: 12px;">{{ $change }}</span>
                                                    @endforeach
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    <div style="margin-top: 4px; font-size: 11px; color: #666666;">
                                        by {{ $log->user ? $log->user->name : 'System' }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="footer">
                    <p>This is a computer generated report and does not require a signature.</p>
                    <p>Generated by {{ config('app.name') }} on {{ now()->format('d M, Y h:i A') }}</p>
                    <p>Total Count: {{ $logs->total() }}</p>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #666666;">
                    <p style="font-size: 14px;">No activity logs found for the selected criteria.</p>
                    <p style="font-size: 13px; margin-top: 8px;">Try adjusting your filters or date range.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function changePerPage(value) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', value);
            window.location.href = url.toString();
        }

        function sortTable(columnIndex) {
            const table = document.querySelector('.activity-logs-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            const isAscending = table.getAttribute('data-sort-direction') !== 'asc';
            table.setAttribute('data-sort-direction', isAscending ? 'asc' : 'desc');
            
            rows.sort((a, b) => {
                if (columnIndex === 0) {
                    // Sort by date - get date and time from the date column
                    const aText = a.cells[0].textContent.trim().replace(/\n/g, ' ');
                    const bText = b.cells[0].textContent.trim().replace(/\n/g, ' ');
                    
                    const aDate = new Date(aText);
                    const bDate = new Date(bText);
                    return isAscending ? aDate - bDate : bDate - aDate;
                }
                
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();
                return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            });
            
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
            
            const sortIcon = table.querySelector('.sort-icon');
            if (sortIcon) {
                sortIcon.textContent = isAscending ? '↑' : '↓';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            ['date_from', 'date_to'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function() {
                        document.querySelector('form').submit();
                    });
                }
            });
        });
    </script>
</body>
</html>
