<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approvals Report - {{ $business ? $business->business_name : 'Business' }} - StoreBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
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
            padding: 10px;
            box-sizing: border-box;
            background: white;
        }

        .report-container {
            width: 100%;
            max-width: 100%;
            position: relative;
        }

        .business-info {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .business-logo {
            height: 40px;
            margin-bottom: 5px;
        }

        .business-info h2 {
            margin: 3px 0;
            font-size: 16px;
            font-weight: 700;
        }

        .business-info-details {
            font-size: 11px;
            color: #444;
            line-height: 1.4;
        }

        .report-title {
            text-align: center;
            margin: 12px 0;
            padding: 8px;
            border-bottom: 2px solid #333;
        }

        .report-title h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .report-title div {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            background: #fff;
        }

        .summary-card h4 {
            margin: 0 0 6px 0;
            font-size: 10px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .summary-card.open .summary-value {
            color: #f59e0b;
        }

        .summary-card.closed .summary-value {
            color: #6b7280;
        }

        .summary-card.sold .summary-value {
            color: #059669;
        }

        .summary-card.returned .summary-value {
            color: #3b82f6;
        }

        .filters {
            margin: 12px 0;
            padding: 14px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-inputs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 14px;
            flex: 1;
        }

        .filter-buttons {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
            min-width: 140px;
            max-width: 180px;
            flex: 1 1 140px;
        }

        label {
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 11px;
            color: #444;
        }

        input[type="date"],
        select {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 12px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            height: 32px;
        }

        button {
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: white;
            background-color: #0d6efd;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            height: 32px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 11px;
            table-layout: fixed;
        }

        thead {
            background-color: #333;
            color: white;
        }

        th {
            padding: 10px 6px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 10px;
            border: 1px solid #444;
        }

        td {
            padding: 8px 6px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Column widths */
        th:nth-child(1), td:nth-child(1) { width: 8%; }  /* Approval # */
        th:nth-child(2), td:nth-child(2) { width: 9%; }  /* Date */
        th:nth-child(3), td:nth-child(3) { width: 15%; } /* Party */
        th:nth-child(4), td:nth-child(4) { width: 7%; }  /* Status */
        th:nth-child(5), td:nth-child(5) { width: 18%; } /* Items Summary */
        th:nth-child(6), td:nth-child(6) { width: 12%; } /* Arms Summary */
        th:nth-child(7), td:nth-child(7) { width: 11%; } /* Approved Value */
        th:nth-child(8), td:nth-child(8) { width: 10%; } /* Sold */
        th:nth-child(9), td:nth-child(9) { width: 10%; } /* Remaining */

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-open {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-closed {
            background-color: #e5e7eb;
            color: #374151;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 700;
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }

        tfoot {
            background-color: #f3f4f6;
            font-weight: 700;
        }

        tfoot td {
            border-top: 2px solid #333;
            padding: 10px 6px;
        }

        .item-list, .arm-list {
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 10px;
            line-height: 1.6;
        }

        .item-list li, .arm-list li {
            padding: 2px 0;
            border-bottom: 1px dotted #dee2e6;
        }

        .item-list li:last-child, .arm-list li:last-child {
            border-bottom: none;
        }

        .qty-badge {
            display: inline-block;
            background: #e0e7ff;
            color: #3730a3;
            padding: 1px 6px;
            border-radius: 2px;
            font-weight: 600;
            font-size: 9px;
        }

        .arm-status {
            font-size: 9px;
            padding: 1px 4px;
            border-radius: 2px;
            font-weight: 600;
        }

        .arm-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .arm-status.sold {
            background: #d1fae5;
            color: #065f46;
        }

        .arm-status.returned {
            background: #dbeafe;
            color: #1e40af;
        }

        @media print {
            body {
                background: white;
            }
            .filters, .filter-buttons {
                display: none !important;
            }
            .page-container {
                padding: 0;
            }
        }

        @media screen and (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .filter-inputs {
                width: 100%;
            }
            .form-group {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Approvals Report">
                <div>Generated on @businessDateTime(now())</div>
                @if($fromDate || $toDate)
                    <div>
                        Period: 
                        @if($fromDate)
                            @businessDate($fromDate)
                        @endif
                        @if($fromDate && $toDate)
                            to
                        @endif
                        @if($toDate)
                            @businessDate($toDate)
                        @endif
                    </div>
                @endif
            </x-report-header>

            <!-- Summary Cards -->
            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Approvals</h4>
                    <div class="summary-value">{{ $totalApprovals }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Approved Value</h4>
                    <div class="summary-value">{{ number_format($totalApprovedValue, 2) }}</div>
                </div>
                <div class="summary-card sold">
                    <h4>Total Sold Value</h4>
                    <div class="summary-value">PKR {{ number_format($totalSoldValue, 2) }}</div>
                </div>
                <div class="summary-card returned">
                    <h4>Total Returned Value</h4>
                    <div class="summary-value">PKR {{ number_format($totalReturnedValue, 2) }}</div>
                </div>
                <div class="summary-card open">
                    <h4>Open Approvals</h4>
                    <div class="summary-value">{{ $openApprovals }}</div>
                </div>
                <div class="summary-card closed">
                    <h4>Closed Approvals</h4>
                    <div class="summary-value">{{ $closedApprovals }}</div>
                </div>
                <div class="summary-card">
                    <h4>Pending Approvals</h4>
                    <div class="summary-value">{{ $pendingApprovals }}</div>
                </div>
                <div class="summary-card">
                    <h4>Remaining Value</h4>
                    <div class="summary-value">PKR {{ number_format($totalRemainingValue, 2) }}</div>
                </div>
                
            </div>

            <!-- Filters -->
            <div class="filters">
                <form method="GET" action="{{ route('approvals.report') }}" class="filter-form">
                    <div class="filter-inputs">
                        <div class="form-group">
                            <label for="party_id">Party</label>
                            <select name="party_id" id="party_id" class="chosen-select">
                                <option value="">All Parties</option>
                                @foreach($parties as $party)
                                    <option value="{{ $party->id }}" {{ $partyId == $party->id ? 'selected' : '' }}>
                                        {{ $party->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>Closed</option>
                                
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
                    </div>

                    <div class="filter-buttons">
                        <button type="submit">Apply Filters</button>
                        <button type="button" class="btn-secondary" onclick="window.print()">Print</button>
                        <a href="{{ route('approvals.report') }}" style="display: inline-block; padding: 6px 14px; font-size: 12px; font-weight: 600; color: white; background-color: #6c757d; border-radius: 4px; text-decoration: none; height: 32px; line-height: 20px; box-sizing: border-box;">Clear</a>
                        <a href="{{ route('approvals.index') }}" style="display: inline-block; padding: 6px 14px; font-size: 12px; font-weight: 600; color: white; background-color: #198754; border-radius: 4px; text-decoration: none; height: 32px; line-height: 20px; box-sizing: border-box;">Back to List</a>
                    </div>
                </form>
            </div>

            <!-- Report Table -->
            <table>
                <thead>
                    <tr>
                        <th>Approval #</th>
                        <th>Date</th>
                        <th>Party</th>
                        <th>Status</th>
                        <th>General Items</th>
                        <th>Arms</th>
                        <th class="text-right">Approved Value</th>
                        <th class="text-right">Sold</th>
                        <th class="text-right">Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvals as $approval)
                        <tr>
                            <td class="font-bold">{{ $approval->approval_number }}</td>
                            <td>{{ $approval->approval_date->format('d M Y') }}</td>
                            <td>{{ $approval->party->name }}</td>
                            <td class="text-center">
                                <span class="status-badge status-{{ $approval->status }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </td>
                            <td>
                                @if($approval->generalItems->count() > 0)
                                    @php
                                        $totalItems = $approval->generalItems->count();
                                        $totalQty = $approval->generalItems->sum('quantity');
                                        $totalSoldQty = $approval->generalItems->sum('sold_quantity');
                                        $totalReturnedQty = $approval->generalItems->sum('returned_quantity');
                                        $totalRemainingQty = $approval->generalItems->sum('remaining_quantity');
                                        $uniqueItems = $approval->generalItems->groupBy('general_item_id')->count();
                                    @endphp
                                    <div style="font-size: 10px; line-height: 1.5;">
                                        <div style="font-weight: 600; margin-bottom: 3px;">
                                            {{ $uniqueItems }} {{ $uniqueItems == 1 ? 'Item' : 'Items' }} ({{ $totalItems }} {{ $totalItems == 1 ? 'Line' : 'Lines' }})
                                        </div>
                                        <div style="color: #374151;">
                                            Total Qty: <strong>{{ number_format(round($totalQty), 0) }}</strong>
                                        </div>
                                        @if($totalSoldQty > 0)
                                            <div style="color: #059669; margin-top: 2px;">
                                                Sold: <strong>{{ number_format(round($totalSoldQty), 0) }}</strong>
                                            </div>
                                        @endif
                                        @if($totalReturnedQty > 0)
                                            <div style="color: #3b82f6; margin-top: 2px;">
                                                Returned: <strong>{{ number_format(round($totalReturnedQty), 0) }}</strong>
                                            </div>
                                        @endif
                                        @if($totalRemainingQty > 0)
                                            <div style="color: #6b7280; margin-top: 2px;">
                                                Remaining: <strong>{{ number_format(round($totalRemainingQty), 0) }}</strong>
                                            </div>
                                        @endif
                                        @if($totalItems > 5)
                                            <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dotted #dee2e6; font-size: 9px; color: #6b7280;">
                                                Showing summary ({{ $totalItems }} lines total)
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #9ca3af; font-size: 10px;">No items</span>
                                @endif
                            </td>
                            <td>
                                @if($approval->arms->count() > 0)
                                    @php
                                        $totalArms = $approval->arms->count();
                                        $pendingArms = $approval->arms->where('status', 'pending')->count();
                                        $soldArms = $approval->arms->where('status', 'sold')->count();
                                        $returnedArms = $approval->arms->where('status', 'returned')->count();
                                    @endphp
                                    <div style="font-size: 10px; line-height: 1.5;">
                                        <div style="font-weight: 600; margin-bottom: 3px;">
                                            {{ $totalArms }} {{ $totalArms == 1 ? 'Arm' : 'Arms' }}
                                        </div>
                                        @if($pendingArms > 0)
                                            <div style="color: #f59e0b; margin-top: 2px;">
                                                Pending: <strong>{{ $pendingArms }}</strong>
                                            </div>
                                        @endif
                                        @if($soldArms > 0)
                                            <div style="color: #059669; margin-top: 2px;">
                                                Sold: <strong>{{ $soldArms }}</strong>
                                            </div>
                                        @endif
                                        @if($returnedArms > 0)
                                            <div style="color: #3b82f6; margin-top: 2px;">
                                                Returned: <strong>{{ $returnedArms }}</strong>
                                            </div>
                                        @endif
                                        @if($totalArms > 10)
                                            <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dotted #dee2e6; font-size: 9px; color: #6b7280;">
                                                Showing summary ({{ $totalArms }} arms total)
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #9ca3af; font-size: 10px;">No arms</span>
                                @endif
                            </td>
                            <td class="text-right">PKR {{ number_format($approval->total_approved_value, 2) }}</td>
                            <td class="text-right" style="color: #059669; font-weight: 600;">PKR {{ number_format($approval->total_sold_value, 2) }}</td>
                            <td class="text-right">PKR {{ number_format($approval->remaining_value, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="no-data">
                                No approval records found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($approvals->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right">TOTALS:</td>
                        <td class="text-right">PKR {{ number_format($totalApprovedValue, 2) }}</td>
                        <td class="text-right" style="color: #059669;">PKR {{ number_format($totalSoldValue, 2) }}</td>
                        <td class="text-right">PKR {{ number_format($totalRemainingValue, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>

            <!-- Footer -->
            <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #dee2e6; font-size: 10px; color: #6b7280; text-align: center;">
                <p style="margin: 5px 0;">Generated by: {{ auth()->user()->name ?? 'System' }} | Print time: {{ now()->format('d M Y h:i A') }}</p>
                <p style="margin: 5px 0;">Total Records: {{ $approvals->count() }}</p>
            </div>
        </div>
    </div>

    <!-- jQuery Chosen for Party Dropdown -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <style>
    /* Make Chosen match existing form input styles */
    .chosen-container { width: 100% !important; }
    .chosen-container-single .chosen-single {
        height: 32px;
        line-height: 30px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 0 2rem 0 0.625rem; /* Match padding: 6px 10px, with space for arrow */
        background: #fff;
        box-shadow: none;
        font-size: 12px;
        font-family: 'Inter', sans-serif;
        color: #1a1a1a;
    }
    .chosen-container-single .chosen-single span { 
        margin-right: 0;
        display: block;
    }
    .chosen-container-single .chosen-single div { 
        right: 0.625rem;
        top: 50%;
        transform: translateY(-50%);
    }
    .chosen-container-active .chosen-single,
    .chosen-container .chosen-single:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .chosen-container .chosen-search input {
        height: 32px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 6px 10px;
        font-size: 12px;
        font-family: 'Inter', sans-serif;
    }
    .chosen-container .chosen-results li.highlighted {
        background-color: #0d6efd;
        background-image: none;
    }
    @media print {
        .chosen-container {
            display: none !important;
        }
        select.chosen-select {
            display: block !important;
        }
    }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize jQuery Chosen for party dropdown
            $('#party_id').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'All Parties'
            });
        });
    </script>
</body>
</html>

