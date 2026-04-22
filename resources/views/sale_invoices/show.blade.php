<x-app-layout>
    @section('title', 'Sale #' . $saleInvoice->id . ' - Sale Details - Sales Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/sales-dashboard', 'label' => 'Sales Dashboard'],
        ['url' => '/sale-invoices', 'label' => 'Sales'],
        ['url' => '#', 'label' => 'Sale #' . $saleInvoice->id]
    ]" />

    <x-dynamic-heading 
        :title="'Sale #' . $saleInvoice->id" 
        :subtitle="'Detailed Invoice & Sale Summary'"
        :icon="'fas fa-file-invoice-dollar'"
    />

    <!-- Status Badges -->
    <div class="mb-2 flex flex-wrap justify-start gap-2">
        @if($saleInvoice->status == 'draft')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Draft
            </span>
        @elseif($saleInvoice->status == 'posted')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Posted
            </span>
        @elseif($saleInvoice->status == 'cancelled')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Cancelled
            </span>
        @endif
    </div>

    <div class="mb-2 flex flex-wrap justify-end gap-2">
        @if($saleInvoice->approval_id)
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 mr-auto">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generated from Approval
                <a href="{{ route('approvals.show', $saleInvoice->approval_id) }}" class="ml-2 underline hover:text-orange-900">
                    #{{ $saleInvoice->approval_id }}
                </a>
            </div>
        @endif
        @if($saleInvoice->quotation_id)
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mr-auto">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generated from Quotation
                <a href="{{ route('quotations.show', $saleInvoice->quotation_id) }}" class="ml-2 underline hover:text-blue-900">
                    {{ $saleInvoice->quotation->quotation_number ?? '#' . $saleInvoice->quotation_id }}
                </a>
            </div>
        @endif
        
        @if(!$saleInvoice->approval_id && $saleInvoice->canBeEdited())
            <a href="{{ route('sale-invoices.edit', $saleInvoice) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Invoice
            </a>
        @endif
        
        @if(!$saleInvoice->approval_id && $saleInvoice->canBeCancelled())
            <form action="{{ route('sale-invoices.cancel', $saleInvoice) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this sale invoice? This will restore inventory and cannot be undone.')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel Invoice
                </button>
            </form>
        @endif
        
        @if($saleInvoice->canBePosted())
            <form action="{{ route('sale-invoices.post', $saleInvoice->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to post this sale invoice? This action cannot be undone.')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Post Sale Invoice
                </button>
            </form>
        @endif
        
        <button onclick="printInvoice()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Invoice
        </button>
        <a href="{{ route('sale-invoices.thermal-print', $saleInvoice) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-md shadow transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Thermal print
        </a>
    </div>
                            
    @php
        $biz = $saleInvoice->business;
        $bizPhones = trim(implode(' — ', array_filter([$biz->store_phone ?? null, $biz->contact_no ?? null])));
        $bizAddr = $biz->store_address ?? $biz->address ?? '';
        $bizEmailLine = $biz->store_email ?? $biz->email ?? '';
        $billToName = $saleInvoice->sale_type === 'credit'
            ? ($saleInvoice->party?->name ?? 'Credit')
            : ($saleInvoice->name_of_customer ?: 'Cash');
    @endphp

    <div class="bg-white border border-gray-200 w-full rounded-lg overflow-hidden sale-invoice-doc-wrap" id="printable-invoice">
        <div class="sale-invoice-doc-inner relative px-4 py-4 md:px-6 md:py-5 shadow-[0_0_6px_rgba(0,0,0,0.12)]">

            <div class="invoice-ribbon no-print" aria-hidden="true">
                @if($saleInvoice->status === 'posted')
                    @if($saleInvoice->sale_type === 'credit')
                        <div class="invoice-ribbon-inner invoice-ribbon-credit-posted">Posted</div>
                    @else
                        <div class="invoice-ribbon-inner invoice-ribbon-posted">Paid</div>
                    @endif
                @elseif($saleInvoice->status === 'cancelled')
                    <div class="invoice-ribbon-inner invoice-ribbon-cancelled">Cancelled</div>
                @else
                    <div class="invoice-ribbon-inner invoice-ribbon-draft">Draft</div>
                @endif
            </div>

            <div class="header grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5 text-sm border-b border-gray-200 pb-4 mb-4">
                <div class="pl-5 min-w-0 sm:pl-6">
                    <h6 class="text-base font-bold text-gray-900 tracking-tight leading-tight">{{ $biz->store_name ?? $biz->business_name ?? 'Business' }}</h6>
                    @if($bizAddr !== '')
                        <p class="text-gray-600 mt-0.5 leading-snug text-xs">{{ $bizAddr }}</p>
                    @endif
                    @if($bizPhones !== '' || $bizEmailLine !== '')
                        <p class="text-gray-600 mt-0.5 text-xs">{{ $bizPhones }}@if($bizPhones !== '' && $bizEmailLine !== '') — @endif{{ $bizEmailLine }}</p>
                    @endif
                    @if(($biz->ntn ?? '') !== '' || ($biz->strn ?? '') !== '')
                        <p class="text-gray-600 mt-0.5 text-xs">NTN: {{ $biz->ntn ?? '—' }} — STRN: {{ $biz->strn ?? '—' }}</p>
                    @endif

                    <p class="text-gray-800 font-semibold mt-3 mb-0.5 text-xs uppercase tracking-wide">Bill To:</p>
                    <p class="text-sm font-medium text-gray-900">{{ $billToName }}</p>
                    @if($saleInvoice->sale_type === 'cash')
                        @if($saleInvoice->name_of_customer)
                            @if($saleInvoice->father_name)<p class="text-gray-600 mt-0.5 text-xs">Father: {{ $saleInvoice->father_name }}</p>@endif
                            @if($saleInvoice->contact)<p class="text-gray-600 text-xs">Contact: {{ $saleInvoice->contact }}</p>@endif
                            @if($saleInvoice->cnic)<p class="text-gray-600 text-xs">CNIC: {{ $saleInvoice->cnic }}</p>@endif
                            @if($saleInvoice->address)<p class="text-gray-600 text-xs">Address: {{ $saleInvoice->address }}</p>@endif
                        @endif
                    @else
                        @if($saleInvoice->party)
                            @if($saleInvoice->party->cnic)<p class="text-gray-600 mt-0.5 text-xs">CNIC: {{ $saleInvoice->party->cnic }}</p>@endif
                            @if($saleInvoice->party->phone_no)<p class="text-gray-600 text-xs">Phone: {{ $saleInvoice->party->phone_no }}</p>@endif
                            @if($saleInvoice->party->address)<p class="text-gray-600 text-xs">Address: {{ $saleInvoice->party->address }}</p>@endif
                        @endif
                    @endif
                </div>
                <div class="text-left md:text-right">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 leading-tight"><strong>SALE INVOICE</strong> # {{ $saleInvoice->invoice_number }}</h3>
                    <p class="text-gray-600 mt-1 text-xs">User: {{ $saleInvoice->createdBy?->name ?? 'System' }}</p>
                    <p class="text-gray-600 text-xs">Date: @businessDate($saleInvoice->invoice_date)</p>
                    <p class="text-gray-600 mt-0.5 capitalize text-xs">Sale type: {{ $saleInvoice->sale_type }}</p>
                    @if($saleInvoice->bank)
                        <p class="text-gray-600 mt-1.5 text-xs">Bank: {{ $saleInvoice->bank->chartOfAccount->name ?? $saleInvoice->bank->account_name }}</p>
                    @endif
                </div>
            </div>

            @if($saleInvoice->sale_type === 'cash' && ($saleInvoice->licence_no || $saleInvoice->licence_issue_date || $saleInvoice->licence_valid_upto || $saleInvoice->licence_issued_by || $saleInvoice->re_reg_no || $saleInvoice->dc || $saleInvoice->Date))
                <div class="mb-4 rounded-md border border-slate-200 bg-slate-50 p-2.5 text-xs">
                    <div class="font-semibold text-gray-800 mb-1 text-[11px]">Other details</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-0.5 text-gray-600">
                        @if($saleInvoice->licence_no)<div>Licence: {{ $saleInvoice->licence_no }}</div>@endif
                        @if($saleInvoice->licence_issue_date)<div>Issue: @businessDate($saleInvoice->licence_issue_date)</div>@endif
                        @if($saleInvoice->licence_valid_upto)<div>Valid until: @businessDate($saleInvoice->licence_valid_upto)</div>@endif
                        @if($saleInvoice->licence_issued_by)<div>Issued by: {{ $saleInvoice->licence_issued_by }}</div>@endif
                        @if($saleInvoice->re_reg_no)<div>Re-Reg: {{ $saleInvoice->re_reg_no }}</div>@endif
                        @if($saleInvoice->dc)<div>DC: {{ $saleInvoice->dc }}</div>@endif
                        @if($saleInvoice->Date)<div>Date: @businessDate($saleInvoice->Date)</div>@endif
                    </div>
                </div>
            @elseif($saleInvoice->sale_type === 'credit' && ($saleInvoice->party_license_no || $saleInvoice->party_license_issue_date || $saleInvoice->party_license_valid_upto || $saleInvoice->party_license_issued_by || $saleInvoice->party_re_reg_no || $saleInvoice->party_dc || $saleInvoice->party_dc_date))
                <div class="mb-4 rounded-md border border-slate-200 bg-slate-50 p-2.5 text-xs">
                    <div class="font-semibold text-gray-800 mb-1 text-[11px]">Party licence details</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-0.5 text-gray-600">
                        @if($saleInvoice->party_license_no)<div>Licence: {{ $saleInvoice->party_license_no }}</div>@endif
                        @if($saleInvoice->party_license_issue_date)<div>Issue: @businessDate($saleInvoice->party_license_issue_date)</div>@endif
                        @if($saleInvoice->party_license_valid_upto)<div>Valid until: @businessDate($saleInvoice->party_license_valid_upto)</div>@endif
                        @if($saleInvoice->party_license_issued_by)<div>Issued by: {{ $saleInvoice->party_license_issued_by }}</div>@endif
                        @if($saleInvoice->party_re_reg_no)<div>Re-Reg: {{ $saleInvoice->party_re_reg_no }}</div>@endif
                        @if($saleInvoice->party_dc)<div>DC: {{ $saleInvoice->party_dc }}</div>@endif
                        @if($saleInvoice->party_dc_date)<div>DC date: @businessDate($saleInvoice->party_dc_date)</div>@endif
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto mb-3">
                <table class="sale-invoice-detail-table w-full text-xs border-collapse">
                    <thead>
                        <tr>
                            <th class="w-[10%]">#</th>
                            <th class="w-[50%]">Items</th>
                            <th class="w-[12%] text-right">QTY</th>
                            <th class="w-[13%] text-right">Rate</th>
                            <th class="w-[15%] text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNo = 1; @endphp
                        @forelse($invoiceSections as $section)
                            <tr class="sale-invoice-group-row">
                                <th colspan="5">{{ $section['title'] }}</th>
                            </tr>
                            @foreach($section['lines'] as $line)
                                @if($section['kind'] === 'general')
                                    <tr>
                                        <td>{{ $rowNo }}</td>
                                        <td>{{ $line->generalItem->item_name }}</td>
                                        <td class="text-right">{{ (float) $line->quantity == floor((float) $line->quantity) ? number_format((float) $line->quantity, 0) : number_format((float) $line->quantity, 2) }}</td>
                                        <td class="text-right">{{ number_format((float) $line->sale_price, 2) }}</td>
                                        <td class="text-right font-medium">{{ number_format((float) $line->line_total, 2) }}</td>
                                    </tr>
                                    @php $rowNo++; @endphp
                                @else
                                    <tr>
                                        <td>{{ $rowNo }}</td>
                                        <td>
                                            <span class="font-medium text-gray-900">{{ $line->arm->arm_title ?? 'Arm' }}</span>
                                            @if($line->arm && $line->arm->serial_no)
                                                <span class="block text-xs text-gray-500 font-mono mt-0.5">{{ $line->arm->serial_no }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">1</td>
                                        <td class="text-right">{{ number_format((float) $line->sale_price, 2) }}</td>
                                        <td class="text-right font-medium">{{ number_format((float) ($line->line_total ?? $line->sale_price), 2) }}</td>
                                    </tr>
                                    @php $rowNo++; @endphp
                                @endif
                            @endforeach
                        @empty
                            <tr><td colspan="5" class="text-center text-gray-500 py-3">No line items.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        @if((float) $saleInvoice->subtotal !== (float) $saleInvoice->total_amount)
                            <tr>
                                <td colspan="4" class="text-right font-medium">Sub total</td>
                                <td class="text-right font-medium">{{ number_format((float) $saleInvoice->subtotal, 2) }}</td>
                            </tr>
                        @endif
                        @if((float) $saleInvoice->shipping_charges > 0)
                            <tr>
                                <td colspan="4" class="text-right">Shipping charges</td>
                                <td class="text-right">{{ number_format((float) $saleInvoice->shipping_charges, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-right"><strong>Invoice total</strong></td>
                            <td class="text-right"><strong>{{ number_format((float) $saleInvoice->total_amount, 2) }}</strong></td>
                        </tr>
                        @if($saleInvoice->sale_type === 'credit' && $partyPreviousBalance !== null && $partyTotalBalance !== null)
                            @php
                                $fmtPartyLedgerBal = static function (?float $raw): string {
                                    if ($raw === null || abs((float) $raw) < 0.005) {
                                        return number_format(0.0, 2);
                                    }
                                    if ($raw < 0) {
                                        return number_format(abs($raw), 2) . ' Dr';
                                    }
                                    return number_format($raw, 2) . ' Cr';
                                };
                            @endphp
                            <tr>
                                <td colspan="4" class="text-right">
                                    <strong>Previous balance</strong>
                                    
                                </td>
                                <td class="text-right sale-invoice-balance-cell whitespace-nowrap"><strong>{{ $currencyLabel }} {{ $fmtPartyLedgerBal($partyPreviousBalance) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Total balance</strong></td>
                                <td class="text-right sale-invoice-balance-cell whitespace-nowrap"><strong>{{ $currencyLabel }} {{ $fmtPartyLedgerBal($partyTotalBalance) }}</strong></td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

        {{-- Minimal print-friendly authorized signature (seller) --}}
        <div class="invoice-authorized-signature px-4 pb-3 pt-1.5 bg-white border-t border-gray-200">
            <div class="flex justify-end">
                <div class="signature-block w-full max-w-[11rem] text-center">
                    <div class="signature-pad min-h-[2.25rem] border-b-2 border-gray-900"></div>
                    <p class="text-[10px] text-gray-600 mt-1 tracking-wide uppercase">Authorized signature</p>
                </div>
            </div>
        </div>

        <div class="px-4 py-2.5 border-t border-gray-200 text-[11px] text-gray-600 flex flex-col md:flex-row justify-between gap-2 bg-white">
            <div>
                <div class="text-gray-600 leading-tight">
                    <span class="font-semibold text-gray-700">Powered by:</span>
                    Grow Business 365
                </div>
            </div>
            <div class="text-right">
                <div>By: <span class="font-medium text-gray-800">{{ $saleInvoice->createdBy->name ?? 'System' }}</span></div>
                <div>Created: @businessDateTime($saleInvoice->created_at)</div>
                @if($saleInvoice->updated_at->diffInSeconds($saleInvoice->created_at) > 0)
                    <div>Updated: @businessDateTime($saleInvoice->updated_at)</div>
                @endif
            </div>
        </div>
        </div>
    </div>

    <style>
        /* Sale invoice document (screen + print base) — aligned with invoice_detail layout */
        .sale-invoice-doc-wrap { position: relative; }
        .sale-invoice-doc-inner { overflow: hidden; line-height: 1.35; }
        .invoice-ribbon {
            position: absolute;
            top: 0;
            left: 0;
            width: 5.75rem;
            height: 5.75rem;
            overflow: hidden;
            z-index: 5;
            pointer-events: none;
        }
        .invoice-ribbon-inner {
            position: absolute;
            top: 0.65rem;
            left: -1.65rem;
            width: 7rem;
            transform: rotate(-45deg);
            text-align: center;
            font-size: 0.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.2rem 0;
            color: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }
        .invoice-ribbon-posted { background: #15803d; }
        .invoice-ribbon-credit-posted { background: #1e40af; }
        .invoice-ribbon-cancelled { background: #b91c1c; }
        .invoice-ribbon-draft { background: #64748b; }
        .sale-invoice-detail-table { border: 1px solid #e2e8f0; }
        .sale-invoice-detail-table th,
        .sale-invoice-detail-table td {
            border: 1px solid #e2e8f0;
            padding: 0.3rem 0.45rem;
            vertical-align: top;
        }
        .sale-invoice-detail-table thead th {
            background: #f8fafc;
            font-weight: 600;
            color: #334155;
        }
        .sale-invoice-group-row th {
            background: #e2e8f0 !important;
            color: #1e293b;
            font-weight: 600;
            text-align: left;
        }
        .sale-invoice-detail-table tfoot td {
            background: #f8fafc;
            border-top-width: 2px;
        }
        .sale-invoice-balance-cell { color: #0f172a; }

        @media print {
            body * { visibility: hidden; }
            #printable-invoice, #printable-invoice * { visibility: visible; }
            .no-print { display: none !important; }
            #printable-invoice .header.grid > div:first-child { padding-left: 0 !important; }
            body, html { margin: 0; padding: 0; background: white; font-family: 'Arial', sans-serif; }
            #printable-invoice {
                position: absolute; left: 0; top: 0; width: 100%; max-width: none; margin: 0; padding: 0;
                box-shadow: none; border: none; background: white; font-size: 10pt; line-height: 1.25; color: black;
            }
            #printable-invoice .sale-invoice-doc-inner {
                padding: 10px 12px !important;
            }
            #printable-invoice .p-6 { padding: 10px !important; }
            #printable-invoice .flex { display: flex !important; }
            #printable-invoice .justify-between { justify-content: space-between !important; }
            #printable-invoice .items-start { align-items: flex-start !important; }
            #printable-invoice .text-right { text-align: right !important; }
            #printable-invoice .mb-4 { margin-bottom: 15px !important; }
            #printable-invoice .mt-2 { margin-top: 8px !important; }
            #printable-invoice .mt-1 { margin-top: 4px !important; }
            #printable-invoice .bg-gradient-to-r,
            #printable-invoice .shadow-lg,
            #printable-invoice .shadow-sm { background: white !important; box-shadow: none !important; }
            #printable-invoice .grid { display: grid !important; }
            #printable-invoice .md\:grid-cols-2 { grid-template-columns: 1fr 1fr !important; }
            #printable-invoice .md\:grid-cols-3 { grid-template-columns: 1fr 1fr 1fr !important; }
            #printable-invoice .gap-6 { gap: 20px !important; }
            #printable-invoice table {
                font-size: 8.5pt; width: 100%; border-collapse: collapse; margin-bottom: 8px;
            }
            #printable-invoice th {
                background: #f8f9fa !important; border: 1px solid #dee2e6; padding: 5px 4px !important;
                font-weight: bold; text-align: left; color: #495057;
            }
            #printable-invoice td {
                border: 1px solid #dee2e6; padding: 4px 5px !important; vertical-align: top;
            }
            #printable-invoice .sale-invoice-detail-table th,
            #printable-invoice .sale-invoice-detail-table td {
                padding: 3px 4px !important;
            }
            #printable-invoice tr:hover { background: white !important; }
            #printable-invoice .bg-green-100,
            #printable-invoice .bg-yellow-100,
            #printable-invoice .bg-gray-100 {
                background: #f8f9fa !important; border: 1px solid #dee2e6; color: #495057 !important;
            }
            #printable-invoice .bg-blue-100 {
                background: #e3f2fd !important; border: 1px solid #bbdefb; color: #1976d2 !important;
            }
            #printable-invoice svg { display: none !important; }
            .breadcrumb, .dynamic-heading, button, a[href*="edit"], a[href*="audit-log"], form[action*="post"], .mb-2.flex, .mb-4.flex {
                display: none !important;
            }
            @page { size: A4 portrait; margin: 10mm; }
            #printable-invoice { page-break-inside: avoid; }
            #printable-invoice table { page-break-inside: auto; }
            #printable-invoice tr { page-break-inside: avoid; page-break-after: auto; }
            #printable-invoice .bg-gray-50 { background: #f8f9fa !important; }
            #printable-invoice .border-t { border-top: 2px solid #dee2e6 !important; }
            #printable-invoice .text-gray-600,
            #printable-invoice .text-gray-500 { color: #495057 !important; }
            #printable-invoice .text-gray-900 { color: #212529 !important; }
            #printable-invoice * { background-color: transparent !important; }
            #printable-invoice .bg-white { background: white !important; }
            #printable-invoice .bg-gray-50 { background: #f8f9fa !important; }
            #printable-invoice .bg-gray-100 { background: #f8f9fa !important; }
            #printable-invoice .sale-invoice-detail-table thead th {
                background: #f8fafc !important;
                color: #334155 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            #printable-invoice .sale-invoice-group-row th {
                background: #e2e8f0 !important;
                color: #1e293b !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            #printable-invoice .sale-invoice-detail-table tfoot td {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            #printable-invoice .text-xxs { font-size: 7pt !important; }
            #printable-invoice .text-xs { font-size: 8pt !important; }
            #printable-invoice .text-sm { font-size: 9pt !important; }
            #printable-invoice .text-base { font-size: 10pt !important; }
            #printable-invoice .text-lg { font-size: 12pt !important; }
            #printable-invoice .text-2xl { font-size: 16pt !important; }
            #printable-invoice .mb-6 { margin-bottom: 15px !important; }
            #printable-invoice .mb-3 { margin-bottom: 8px !important; }
            #printable-invoice .mb-2 { margin-bottom: 5px !important; }
            #printable-invoice .mb-1 { margin-bottom: 3px !important; }
            #printable-invoice .items .grid { display: block !important; }
            #printable-invoice .items .md\:grid-cols-2 > div { display: block !important; margin-bottom: 15px !important; }
            #printable-invoice .header .grid {
                display: grid !important; 
                grid-template-columns: 1fr 1fr 1fr !important; 
                grid-template-rows: auto !important;
                gap: 20px !important;
            }
            #printable-invoice .header .md\:grid-cols-3 { 
                grid-template-columns: 1fr 1fr 1fr !important; 
            }
            #printable-invoice .header .grid > div {
                display: block !important; 
                margin-bottom: 0 !important; 
                page-break-inside: avoid !important;
            }
            #printable-invoice .header .grid > div:first-child {
                grid-column: 1 !important; 
                text-align: left !important; 
                justify-self: start !important;
            }
            #printable-invoice .header .grid > div:nth-child(2) {
                grid-column: 2 !important; 
                text-align: left !important; 
                justify-self: start !important;
            }
            #printable-invoice .header .grid > div:last-child {
                grid-column: 3 !important; 
                text-align: right !important; 
                justify-self: end !important;
            }
            #printable-invoice .header .flex {
                display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: flex-start !important;
            }
            #printable-invoice .header .justify-between { justify-content: space-between !important; }
            #printable-invoice .header .items-start { align-items: flex-start !important; }
            #printable-invoice .header .text-right { text-align: right !important; }
            #printable-invoice .header .flex-1 { flex: 1 !important; }
            #printable-invoice .header .flex > div { flex-shrink: 0 !important; page-break-inside: avoid !important; }
            #printable-invoice .items .flex { display: block !important; }
            #printable-invoice .items .justify-between { text-align: left !important; }
            #printable-invoice .items .justify-end { text-align: left !important; }
            #printable-invoice .header .flex { display: flex !important; }
            #printable-invoice .header .justify-between { justify-content: space-between !important; }
            #printable-invoice .items-end { text-align: right !important; }
            #printable-invoice .max-w-xs { max-width: none !important; width: 100% !important; }
            #printable-invoice .invoice-authorized-signature {
                padding: 6px 12px 10px !important;
                border-top: 1px solid #dee2e6 !important;
                background: white !important;
            }
            #printable-invoice .invoice-authorized-signature .signature-pad {
                min-height: 11mm !important;
                border-bottom: 1.25pt solid #000 !important;
            }
            #printable-invoice .invoice-authorized-signature p {
                font-size: 7.5pt !important;
                color: #495057 !important;
                letter-spacing: 0.04em !important;
                margin-top: 4px !important;
            }
            #printable-invoice .invoice-authorized-signature .signature-block {
                max-width: 52mm !important;
            }
        }
        body.printing { background: white !important; }
    </style>

    <script>
        document.title = "Sale Invoice #{{ $saleInvoice->invoice_number }} - {{ $saleInvoice->business->business_name ?? 'Business' }}";
        function printInvoice() {
            const originalTitle = document.title;
            document.title = "Sale Invoice #{{ $saleInvoice->invoice_number }} - {{ $saleInvoice->business->business_name ?? 'Business' }}";
            document.body.classList.add('printing');
            window.print();
            setTimeout(() => {
                document.title = originalTitle;
                document.body.classList.remove('printing');
            }, 1000);
        }
        window.addEventListener('beforeprint', function() {
            console.log('Printing invoice...');
        });
        window.addEventListener('afterprint', function() {
            console.log('Print completed');
        });
    </script>
    @if (session('open_print_dialog'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof printInvoice === 'function') {
                    printInvoice();
                } else {
                    window.print();
                }
            });
        </script>
    @endif
</x-app-layout>
