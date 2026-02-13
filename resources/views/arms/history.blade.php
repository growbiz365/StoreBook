<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arms History Report - {{ $business ? $business->name : 'Business' }} - Arms Management</title>
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
            max-width: 100%;
            margin: 0 auto;
            padding: 15mm;
            box-sizing: border-box;
            background: white;
        }

        .report-container {
            width: 100%;
            max-width: 100%;
            position: relative;
            overflow-x: visible;
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
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-card {
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .summary-card h4 {
            margin: 0 0 6px 0;
            font-size: 11px;
            font-weight: 500;
            color: #444;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 600;
        }

        .filters {
            margin: 18px 0;
            padding: 16px;
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
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

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-success {
            background-color: #198754;
        }

        .btn-success:hover {
            background-color: #157347;
        }

        .table-container {
            margin-top: 18px;
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background: #ffffff;
        }

        th {
            background-color: #fafafa;
            font-weight: 600;
            text-transform: none;
            font-size: 13px;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 12px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        th:nth-child(1) { width: 90px; }  /* Date */
        th:nth-child(2) { width: 210px; } /* Arm Name */
        th:nth-child(3) { width: 110px; } /* Action */
        th:nth-child(4) { width: 110px; } /* Price */
        th:nth-child(5) { width: 220px; } /* Remarks */
        th:nth-child(6) { width: 160px; } /* User */
        th:nth-child(7) { width: 340px; } /* Changes */
        th:nth-child(8) { width: 140px; } /* Created At */

        td {
            border-bottom: 1px solid #f2f2f2;
            padding: 9px 12px;
            background-color: #fff;
            word-wrap: break-word;
        }

        td:nth-child(7) {
            max-width: 340px;
            white-space: normal;
            line-height: 1.4;
        }

        td:nth-child(8),
        th:nth-child(8) {
            white-space: nowrap;
        }

        tbody tr:nth-child(odd) td {
            background-color: #fcfcfd;
        }

        tbody tr:hover td {
            background-color: #f9fafb;
        }

        .amount {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 500;
        }

        .action-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-align: center;
            display: inline-block;
        }

        .action-badge.opening {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .action-badge.purchase {
            background-color: #d4edda;
            color: #155724;
        }

        .action-badge.sale {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-badge.transfer {
            background-color: #e2d3f6;
            color: #4a148c;
        }

        .action-badge.repair {
            background-color: #fff3cd;
            color: #856404;
        }

        .action-badge.decommission {
            background-color: #f5c6cb;
            color: #721c24;
        }

        .action-badge.price_adjustment {
            background-color: #ffeaa7;
            color: #6c5ce7;
        }

        .action-badge.edit {
            background-color: #d6d8db;
            color: #1b1e21;
        }

        .action-badge.cancel {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-badge.return {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .action-badge.delete {
            background-color: #f8d7da;
            color: #721c24;
        }

        .pagination-info {
            margin: 16px 0;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .per-page-selector {
            margin-left: 12px;
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 12px;
            background: #ffffff;
        }

        .user-info {
            font-size: 11px;
            color: #374151;
            font-weight: 500;
        }

        .ip-address {
            font-family: monospace;
            font-size: 10px;
            color: #888;
        }

        .changes-container {
            font-size: 11px;
            color: #444;
            max-width: 100%;
            overflow-wrap: break-word;
        }

        .change-item {
            margin-bottom: 4px;
            line-height: 1.5;
        }

        .field-name {
            font-weight: 500;
        }

        .old-value, .new-value {
            font-family: 'Inter', monospace;
        }

        .arrow {
            margin: 0 4px;
        }

        .no-changes {
            font-style: italic;
            color: #888;
        }

        @media print {
            body {
                background: white;
            }
            .page-container {
                padding: 0;
                max-width: 100%;
            }
            .filters {
                display: none;
            }
            table {
                min-width: 100%;
            }
            th {
                background-color: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media screen and (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-group {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Arms History Report">
                        @if($armId)
                            @php
                                $selectedArm = $arms->firstWhere('id', $armId);
                            @endphp
                            @if($selectedArm)
                                <div><strong>Arm:</strong> {{ $selectedArm->arm_title }} ({{ $selectedArm->armType->arm_type ?? 'N/A' }})</div>
                            @endif
                        @endif
                        <div><strong>Duration:</strong> {{ $dateFrom }} to {{ $dateTo }}</div>
            </x-report-header>

            

            <div class="filters">
                <form action="{{ route('arms-history') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="arm_id">Select Arm</label>
                        <select name="arm_id" id="arm_id">
                            <option value="">All Arms</option>
                            @foreach($arms as $arm)
                                <option value="{{ $arm->id }}" {{ $armId == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->arm_title }} ({{ $arm->armType->arm_type ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_from">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}">
                    </div>

                    <div class="form-group">
                        <label for="date_to">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}">
                    </div>

                    <div class="form-group">
                        <label for="action">Action Type</label>
                        <select name="action" id="action">
                            <option value="">All Actions</option>
                            <option value="opening" {{ $action == 'opening' ? 'selected' : '' }}>Opening Stock</option>
                            <option value="purchase" {{ $action == 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="sale" {{ $action == 'sale' ? 'selected' : '' }}>Sale</option>
                            
                            <option value="decommission" {{ $action == 'decommission' ? 'selected' : '' }}>Decommission</option>
                           
                            <option value="edit" {{ $action == 'edit' ? 'selected' : '' }}>Edit</option>
                            <option value="cancel" {{ $action == 'cancel' ? 'selected' : '' }}>Cancel</option>
                            <option value="return" {{ $action == 'return' ? 'selected' : '' }}>Return</option>
                            <option value="delete" {{ $action == 'delete' ? 'selected' : '' }}>Delete</option>
                        </select>
                    </div>

                    <button type="submit">Apply Filters</button>
                    
                    <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                    <a href="{{ route('arms.dashboard') }}">
                        <button type="button" class="btn-secondary">Back</button>
                    </a>
                </form>
            </div>

            @if($historyEntries && $historyEntries->count() > 0)
                <div class="pagination-info">
                    Showing {{ $historyEntries ? ($historyEntries->firstItem() ?? 0) : 0 }} to {{ $historyEntries ? ($historyEntries->lastItem() ?? 0) : 0 }} 
                    of {{ $historyEntries ? $historyEntries->total() : 0 }} entries
                    <select name="per_page" id="per_page" onchange="changePerPage(this.value)" class="per-page-selector">
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>

                <!-- Total Records Info -->
                <div style="margin: 15px 0 10px 0; text-align: right; font-size: 13px; color: #444; font-weight: 600; padding: 8px 12px; background-color: #f8f9fa; border-radius: 4px; display: inline-block; float: right;">
                    Total Records: <strong>{{ $historyEntries ? $historyEntries->total() : 0 }}</strong>
                </div>
                <div style="clear: both;"></div>

                <div class="table-container" style="margin-top: 10px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Arm Name</th>
                                <th>Action</th>
                                <th>Price</th>
                                <th>Remarks</th>
                                <th>User</th>
                                <th>Changes</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyEntries as $entry)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d-m-Y') }}</td>
                                    <td>{{ $entry->arm->arm_title ?? 'N/A' }}</td>
                                    <td>
                                        <span class="action-badge {{ $entry->action }}">
                                            {{ ucfirst($entry->action) }}
                                        </span>
                                    </td>
                                    <td class="amount">{{ number_format($entry->price ?? 0, 2) }}</td>
                                    <td>{{ $entry->remarks ?? 'N/A' }}</td>
                                    <td>
                                        <div class="user-info">{{ $entry->user->name ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        @if($entry->old_values && $entry->new_values && is_array($entry->old_values) && is_array($entry->new_values))
                                            <div class="changes-container">
                                                @foreach($entry->old_values as $field => $oldValue)
                                                    @if(isset($entry->new_values[$field]) && $entry->new_values[$field] !== $oldValue && $field !== 'updated_at')
                                                        <div class="change-item">
                                                            <span class="field-name">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                            <span class="old-value">{{ $oldValue ?? 'N/A' }}</span>
                                                            <span class="arrow">→</span>
                                                            <span class="new-value">{{ $entry->new_values[$field] ?? 'N/A' }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="no-changes">No changes recorded</span>
                                        @endif
                                    </td>
                                    
                                    <td>{{ \Carbon\Carbon::parse($entry->created_at)->format('d-m-Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Report Footer -->
                <div style="margin-top: 20px; text-align: right; font-size: 11px; color: #666;">
                    <p>Generated by: {{ auth()->user() ? auth()->user()->name : 'System' }} | Print time: @businessDateTime(now())</p>
                    <p style="margin-top: 5px;">Powered By GrowBusiness 365</p>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <p>No history entries found for the selected criteria.</p>
                    <p>Try adjusting your filters or date range.</p>
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

        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filters = ['arm_id', 'action'];
            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', function() {
                        document.querySelector('form').submit();
                    });
                }
            });
        });
    </script>
</body>
</html>
