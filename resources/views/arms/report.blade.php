<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arms Report - {{ $business ? $business->business_name : 'Business' }} - Arms Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A3;
            margin: 20mm;
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
            padding: 20mm;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            background: #fff;
        }

        .summary-card h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            font-weight: 500;
            color: #444;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .filters {
            margin: 20px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
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
            min-width: 150px;
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
            margin-top: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            overflow-x: auto;
            overflow-y: hidden;
        }

        table {
            width: 100%;
            min-width: auto;
            border-collapse: collapse;
            font-size: 12px;
            background: #fff;
        }

        th {
            background-color: #fafafa;
            font-weight: 600;
            text-transform: none;
            font-size: 12px;
            color: #1a1a1a;
            border-bottom: 1px solid #e0e0e0;
            padding: 8px 10px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        td {
            border-bottom: 1px solid #f0f0f0;
            padding: 8px 10px;
            background-color: #fff;
            word-wrap: break-word;
            overflow-wrap: break-word;
            vertical-align: top;
            white-space: normal;
            font-size: 11.5px;
        }

        /* Column width adjustments - optimized for no-scroll display */
        th:nth-child(1), td:nth-child(1) { width: 50px; text-align: center; } /* # */
        th:nth-child(2), td:nth-child(2) { width: auto; max-width: 250px; } /* Arm Title */
        th:nth-child(3), td:nth-child(3) { width: 100px; } /* Condition */
        th:nth-child(4), td:nth-child(4) { width: 130px; } /* Cost price */
        th:nth-child(5), td:nth-child(5) { width: 130px; } /* Sale Price */
        th:nth-child(6), td:nth-child(6) { width: 110px; } /* Purchase Date */
        th:nth-child(7), td:nth-child(7) { width: 110px; } /* Status */

        .amount {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 600;
            font-size: 11.5px;
        }

        tfoot td {
            background: #f6f8ff;
            font-weight: 700;
            border-top: 2px solid #d9e0ff;
        }

        .totals-row td {
            background: #f6f8ff;
        }

        .totals-label {
            text-align: right;
            color: #374151;
            letter-spacing: .2px;
        }

        .totals-amount {
            color: #0f172a;
            font-weight: 700;
        }
        
        /* Numbering column styling */
        .row-number {
            font-weight: 600;
            color: #6b7280;
            font-size: 11px;
        }
        
        /* Improve readability for arm title column */
        td:nth-child(2) {
            max-width: 250px;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        
        /* Make table responsive and fit to screen */
        @media screen and (min-width: 769px) {
            .table-container {
                overflow-x: visible;
            }
        }

        .status-available {
            color: #009900;
            font-weight: 600;
        }

        .status-sold {
            color: #cc0000;
            font-weight: 600;
        }

        .status-under_repair {
            color: #ff6600;
            font-weight: 600;
        }

        .status-decommissioned {
            color: #666666;
            font-weight: 600;
        }

        .status-pending_approval {
            color: #ff9900;
            font-weight: 600;
        }

        .arm-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .arm-link:hover {
            color: #0b5ed7;
            text-decoration: underline;
        }

        @media print {
            .arm-link {
                color: #000;
                text-decoration: none;
            }
        }

        .pagination-info {
            margin: 20px 0 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #666;
        }

        .pagination-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .per-page-selector {
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 12px;
            background: white;
            color: #333;
        }

        .pagination-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pagination-controls {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .pagination-btn {
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pagination-btn:hover:not(.disabled) {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
        }

        .pagination-btn.active {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
            font-weight: 600;
        }

        .pagination-page {
            min-width: 40px;
            height: 40px;
            padding: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pagination-ellipsis {
            padding: 8px 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .page-info-text {
            padding: 8px 16px;
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
        }

        @media print {
            body {
                background: white;
            }
            .page-container {
                padding: 0;
                max-width: 100%;
            }
            .filters,
            .pagination-info,
            .pagination-controls {
                display: none !important;
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
            div[style*="Total Records"] {
                float: right !important;
                margin: 15px 0 10px 0 !important;
            }
            table {
                min-width: 100%;
            }
            th {
                background-color: #f8f8f8 !important;
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

            .pagination-info {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .pagination-left,
            .pagination-right {
                justify-content: center;
            }

            .pagination-controls {
                flex-wrap: wrap;
                gap: 6px;
            }

            .pagination-btn {
                padding: 6px 12px;
                font-size: 12px;
            }

            .pagination-page {
                min-width: 36px;
                height: 36px;
            }

            .pagination-ellipsis {
                display: none;
            }
        }
    </style>
</head>
<body>
    
    <div class="page-container">
        <div class="report-container">
            <x-report-header :business="$business" title="Arms Inventory Report">
                        <div>Generated on @businessDateTime(now())</div>
            </x-report-header>

            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Arms</h4>
                    <div class="summary-value">{{ number_format($summary['total_arms']) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Available</h4>
                    <div class="summary-value status-available">{{ number_format($summary['available']) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Sold</h4>
                    <div class="summary-value status-sold">{{ number_format($summary['sold']) }}</div>
                </div>
                <div class="summary-card">
                    <h4>Available Value</h4>
                    <div class="summary-value">{{ number_format(round($summary['total_value']), 0) }}</div>
                </div>
            </div>

            <style>
                .filters {
                    margin-bottom: 24px;
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
                    gap: 6px;
                    align-items: flex-end;
                }
                .filter-form .form-group {
                    display: flex;
                    flex-direction: column;
                    margin-bottom: 0;
                    min-width: 120px;
                    max-width: 180px;
                    flex: 1 1 120px;
                }
                .filter-form label {
                    font-size: 13px;
                    margin-bottom: 4px;
                }
                .filter-form input[type="text"],
                .filter-form input[type="date"],
                .filter-form select {
                    font-size: 13px;
                    padding: 4px 8px;
                    height: 30px;
                    border-radius: 4px;
                    border: 1px solid #ccc;
                    width: 100%;
                    min-width: 0;
                    box-sizing: border-box;
                }
                /* Make the status select same size as other fields */
                .filter-form select#status {
                    min-width: 0;
                    max-width: 100%;
                    width: 100%;
                    height: 30px;
                }
                .filter-buttons button,
                .filter-buttons .btn-secondary {
                    font-size: 12px;
                    padding: 6px 12px;
                    height: 32px;
                    min-width: 80px;
                    border-radius: 4px;
                    font-weight: 500;
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
            </style>
                         <div class="filters">
                 <form action="{{ route('arms.report') }}" method="GET" class="filter-form">
                     <div class="filter-inputs">
                         <div class="form-group">
                             <label for="search">Search</label>
                             <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Serial no, title...">
                         </div>
                         <div class="form-group">
                             <label for="status">Status</label>
                             <select name="status" id="status">
                                 <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                                 <optgroup label="Acquisition Type">
                                     <option value="purchase" {{ $status == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                     <option value="opening" {{ $status == 'opening' ? 'selected' : '' }}>Opening Stock</option>
                                 </optgroup>
                                <optgroup label="Current Status">
                                    <option value="available" {{ $status == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="sold" {{ $status == 'sold' ? 'selected' : '' }}>Sold</option>
                                   
                                    <option value="decommissioned" {{ $status == 'decommissioned' ? 'selected' : '' }}>Decommissioned</option>
                                    <option value="pending_approval" {{ $status == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                </optgroup>
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
                     <!-- <a href="{{ route('arms.report.export', request()->query()) }}">
                         <button type="button" class="btn-success">Export CSV</button>
                     </a> -->
                     <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                     <a href="{{ route('arms.dashboard') }}">
                         <button type="button" class="btn-secondary">Back to Arms</button>
                     </a>
                 </div>
                </form>
            </div>

            @if($arms && $arms->count() > 0)
                <div class="pagination-info">
                    <div class="pagination-left">
                        <span>Showing {{ $arms->firstItem() ?? 0 }} to {{ $arms->lastItem() ?? 0 }} of {{ $arms->total() }} entries</span>
                    </div>
                    <div class="pagination-right">
                        <span>Records per page:</span>
                    <select name="per_page" id="per_page" onchange="changePerPage(this.value)" class="per-page-selector">
                        @php $perPageSelection = $perPageParam ?? $perPage; @endphp
                        <option value="25" {{ (string)$perPageSelection === '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ (string)$perPageSelection === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (string)$perPageSelection === '100' ? 'selected' : '' }}>100</option>
                        <option value="250" {{ (string)$perPageSelection === '250' ? 'selected' : '' }}>250</option>
                        <option value="500" {{ (string)$perPageSelection === '500' ? 'selected' : '' }}>500</option>
                        <option value="1000" {{ (string)$perPageSelection === '1000' ? 'selected' : '' }}>1000</option>
                        <option value="all" {{ (string)$perPageSelection === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                    </div>
                </div>

                <!-- Total Records Info -->
                <div style="margin: 15px 0 10px 0; text-align: right; font-size: 13px; color: #444; font-weight: 600; padding: 8px 12px; background-color: #f8f9fa; border-radius: 4px; display: inline-block; float: right;">
                    Total Records: <strong>{{ $arms->total() }}</strong>
                </div>
                <div style="clear: both;"></div>

                <div class="table-container" style="margin-top: 10px;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Arm Title</th>
                                <th>Condition</th>
                                <th style="text-align:right;">Cost Price</th>
                                <th style="text-align:right;">Sale Price</th>
                                <th>Purchase Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalCost = $arms->sum('purchase_price');
                                $totalSale = $arms->sum('sale_price');
                            @endphp
                            @foreach($arms as $index => $arm)
                                <tr>
                                    <td class="row-number">{{ ($arms->currentPage() - 1) * $arms->perPage() + $loop->iteration }}</td>
                                    <td><a href="{{ route('arms-history', ['arm_id' => $arm->id]) }}" target="_blank" class="arm-link">{{ $arm->arm_title }}</a></td>
                                    <td>{{ $arm->armCondition->arm_condition ?? 'N/A' }}</td>
                                    <td class="amount">{{ number_format($arm->purchase_price, 2) }}</td>
                                    <td class="amount">{{ number_format($arm->sale_price, 2) }}</td>
                                    <td>{{ $arm->purchase_date->format('d-m-Y') }}</td>
                                    <td class="status-{{ $arm->status }}">{{ ucfirst(str_replace('_', ' ', $arm->status)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="totals-row">
                                <td colspan="3" class="totals-label">Totals</td>
                                <td class="amount totals-amount">{{ number_format(round($totalCost), 0) }}</td>
                                <td class="amount totals-amount">{{ number_format(round($totalSale), 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pagination Controls -->
                @if($arms->hasPages())
                    <div class="pagination-controls">
                        {{-- Previous Button --}}
                        @if($arms->onFirstPage())
                            <span class="pagination-btn disabled">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M11 1l-7 7 7 7V1z"/>
                                </svg>
                                Previous
                            </span>
                        @else
                            <a href="{{ $arms->previousPageUrl() }}" class="pagination-btn">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M11 1l-7 7 7 7V1z"/>
                                </svg>
                                Previous
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $arms->currentPage();
                            $lastPage = $arms->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($start > 1)
                            <a href="{{ $arms->url(1) }}" class="pagination-btn pagination-page">1</a>
                            @if($start > 2)
                                <span class="pagination-ellipsis">...</span>
                            @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $currentPage)
                                <span class="pagination-btn pagination-page active">{{ $page }}</span>
                            @else
                                <a href="{{ $arms->url($page) }}" class="pagination-btn pagination-page">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span class="pagination-ellipsis">...</span>
                            @endif
                            <a href="{{ $arms->url($lastPage) }}" class="pagination-btn pagination-page">{{ $lastPage }}</a>
                        @endif

                        {{-- Next Button --}}
                        @if($arms->hasMorePages())
                            <a href="{{ $arms->nextPageUrl() }}" class="pagination-btn">
                                Next
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M5 1l7 7-7 7V1z"/>
                                </svg>
                            </a>
                        @else
                            <span class="pagination-btn disabled">
                                Next
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M5 1l7 7-7 7V1z"/>
                                </svg>
                            </span>
                        @endif
                    </div>

                    {{-- Page Info --}}
                    <div style="text-align: center; margin-top: 10px;">
                        <span class="page-info-text">
                            Page {{ $arms->currentPage() }} of {{ $arms->lastPage() }}
                        </span>
                    </div>
                @endif

                <!-- Report Footer -->
                <div style="margin-top: 20px; text-align: right; font-size: 11px; color: #666;">
                    <p>Generated by: {{ auth()->user() ? auth()->user()->name : 'System' }} | Print time: @businessDateTime(now())</p>
                    <p style="margin-top: 5px;">Powered By GrowBusiness 365</p>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <p>No arms found for the selected criteria.</p>
                    <p>Try adjusting your filters or search terms.</p>
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
            const filters = ['status', 'from_date', 'to_date'];
            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', function() {
                        document.querySelector('form').submit();
                    });
                }
            });

            // Smooth scroll to top on pagination click
            const paginationLinks = document.querySelectorAll('.pagination-controls a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            // Keyboard navigation for pagination
            document.addEventListener('keydown', function(e) {
                // Don't trigger if user is typing in input/textarea
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                    return;
                }

                // Left arrow - Previous page
                if (e.key === 'ArrowLeft') {
                    const prevBtn = document.querySelector('.pagination-controls a[href*="page"]:not(.disabled)');
                    if (prevBtn && prevBtn.textContent.includes('Previous')) {
                        e.preventDefault();
                        window.location.href = prevBtn.href;
                    }
                }

                // Right arrow - Next page
                if (e.key === 'ArrowRight') {
                    const nextBtn = document.querySelector('.pagination-controls a[href*="page"]:last-child:not(.disabled)');
                    if (nextBtn && nextBtn.textContent.includes('Next')) {
                        e.preventDefault();
                        window.location.href = nextBtn.href;
                    }
                }
            });
        });
    </script>
</body>
</html>
