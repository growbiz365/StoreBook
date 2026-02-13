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
    <div class="mb-4 flex flex-wrap justify-start gap-2">
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

    <div class="mb-4 flex flex-wrap justify-end gap-2">
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
    </div>
                            
    <div class="bg-white border border-gray-200 w-full rounded-lg shadow-lg overflow-hidden" id="printable-invoice">
        <div class="p-4 border-b-2 border-gray-300 bg-white header">
            <div class="flex justify-between items-center mb-3">
                <div class="flex-1">
                    <div class="text-xl font-bold text-gray-900 mb-1">{{ $saleInvoice->business->business_name ?? 'Business Name' }}</div>
                    <div class="space-y-0.5">
                        @if($saleInvoice->business && $saleInvoice->business->address)
                            <div class="text-xs text-gray-600">{{ $saleInvoice->business->address }}</div>
                        @endif
                        @if($saleInvoice->business && $saleInvoice->business->phone)
                            <div class="text-xs text-gray-600">Phone: {{ $saleInvoice->business->phone }}</div>
                        @endif
                        @if($saleInvoice->business && $saleInvoice->business->email)
                            <div class="text-xs text-gray-600">Email: {{ $saleInvoice->business->email }}</div>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-black text-gray-900 mb-1 tracking-tight">
                        SALE INVOICE #{{ $saleInvoice->invoice_number }}
                    </div>
                    <div class="text-xs text-gray-600">
                        Date: @businessDate($saleInvoice->invoice_date)
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                @if($saleInvoice->sale_type === 'cash')
                    <div class="font-semibold text-gray-800 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.607 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Customer Details
                    </div>
                    @if($saleInvoice->name_of_customer)
                        <div class="text-gray-900 font-medium text-base">{{ $saleInvoice->name_of_customer }}</div>
                        @if($saleInvoice->father_name)
                            <div class="text-gray-600">Father: {{ $saleInvoice->father_name }}</div>
                        @endif
                        @if($saleInvoice->contact)
                            <div class="text-gray-600">Contact: {{ $saleInvoice->contact }}</div>
                        @endif
                        @if($saleInvoice->cnic)
                            <div class="text-gray-600">CNIC: {{ $saleInvoice->cnic }}</div>
                        @endif
                        @if($saleInvoice->address)
                            <div class="text-gray-600">Address: {{ $saleInvoice->address }}</div>
                        @endif
                    @else
                        <div class="text-gray-600">No customer details provided</div>
                    @endif
                @else
                    <div class="font-semibold text-gray-800 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.607 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Party Details
                    </div>
                    <div class="text-gray-900 font-medium text-base">{{ $saleInvoice->party->name ?? 'No Party' }}</div>
                    @if($saleInvoice->party)
                        @if($saleInvoice->party->cnic)
                            <div class="text-gray-600">CNIC: {{ $saleInvoice->party->cnic }}</div>
                        @endif
                        @if($saleInvoice->party->phone_no)
                            <div class="text-gray-600">Phone: {{ $saleInvoice->party->phone_no }}</div>
                        @endif
                        @if($saleInvoice->party->address)
                            <div class="text-gray-600">Address: {{ $saleInvoice->party->address }}</div>
                        @endif
                    @else
                        <div class="text-gray-600">No party details</div>
                    @endif
                @endif
            </div>
            
            @if($saleInvoice->sale_type === 'cash' && ($saleInvoice->licence_no || $saleInvoice->licence_issue_date || $saleInvoice->licence_valid_upto || $saleInvoice->licence_issued_by || $saleInvoice->re_reg_no || $saleInvoice->dc || $saleInvoice->Date))
            <div class="text-left">
                <div class="font-semibold text-gray-800 mb-2 flex items-center justify-left gap-1">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Other Details
                </div>
                <div class="text-left">
                    @if($saleInvoice->licence_no)
                        <div class="text-gray-600">Licence: {{ $saleInvoice->licence_no }}</div>
                    @endif
                    @if($saleInvoice->licence_issue_date)
                        <div class="text-gray-600">Issue Date: @businessDate($saleInvoice->licence_issue_date)</div>
                    @endif
                    @if($saleInvoice->licence_valid_upto)
                        <div class="text-gray-600">Valid Until: @businessDate($saleInvoice->licence_valid_upto)</div>
                    @endif
                    @if($saleInvoice->licence_issued_by)
                        <div class="text-gray-600">Issued By: {{ $saleInvoice->licence_issued_by }}</div>
                    @endif
                    @if($saleInvoice->re_reg_no)
                        <div class="text-gray-600">Re-Reg No: {{ $saleInvoice->re_reg_no }}</div>
                    @endif
                    @if($saleInvoice->dc)
                        <div class="text-gray-600">DC: {{ $saleInvoice->dc }}</div>
                    @endif
                    @if($saleInvoice->Date)
                        <div class="text-gray-600">Date: @businessDate($saleInvoice->Date)</div>
                    @endif
                </div>
            </div>
            @elseif($saleInvoice->sale_type === 'credit' && ($saleInvoice->party_license_no || $saleInvoice->party_license_issue_date || $saleInvoice->party_license_valid_upto || $saleInvoice->party_license_issued_by || $saleInvoice->party_re_reg_no || $saleInvoice->party_dc || $saleInvoice->party_dc_date))
            <div class="text-left">
                <div class="font-semibold text-gray-800 mb-2 flex items-center justify-left gap-1">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Party License Details
                </div>
                <div class="text-left">
                    @if($saleInvoice->party_license_no)
                        <div class="text-gray-600">Licence: {{ $saleInvoice->party_license_no }}</div>
                    @endif
                    @if($saleInvoice->party_license_issue_date)
                        <div class="text-gray-600">Issue Date: @businessDate($saleInvoice->party_license_issue_date)</div>
                    @endif
                    @if($saleInvoice->party_license_valid_upto)
                        <div class="text-gray-600">Valid Until: {{ $saleInvoice->party_license_valid_upto->format('M d, Y') }}</div>
                    @endif
                    @if($saleInvoice->party_license_issued_by)
                        <div class="text-gray-600">Issued By: {{ $saleInvoice->party_license_issued_by }}</div>
                    @endif
                    @if($saleInvoice->party_re_reg_no)
                        <div class="text-gray-600">Re-Reg No: {{ $saleInvoice->party_re_reg_no }}</div>
                    @endif
                    @if($saleInvoice->party_dc)
                        <div class="text-gray-600">DC: {{ $saleInvoice->party_dc }}</div>
                    @endif
                    @if($saleInvoice->party_dc_date)
                        <div class="text-gray-600">DC Date: {{ $saleInvoice->party_dc_date->format('M d, Y') }}</div>
                    @endif
                </div>
            </div>
            @else
            <div></div>
            @endif
                <div class="text-right">
                    <div class="font-semibold text-gray-800 mb-2 flex items-center gap-1 justify-end">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2z" />
                    </svg>
                        Payment Details
                </div>
                <div class="text-gray-900">Type: <span class="font-medium">{{ ucfirst($saleInvoice->sale_type) }}</span></div>
                @if($saleInvoice->bank)
                    <div class="text-gray-600">Bank: {{ $saleInvoice->bank->chartOfAccount->name ?? $saleInvoice->bank->account_name }}</div>
                @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($saleInvoice->generalLines->count() > 0)
            <div class="mb-6">
                <div class="text-xs font-medium text-green-600 mb-2">General Items</div>
                <div class="overflow-x-auto">
                <table class="w-full text-xs border border-gray-200 rounded shadow-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Item</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Qty</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Sale Price</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($saleInvoice->generalLines as $line)
                        <tr class="hover:bg-green-50">
                            <td class="px-3 py-2 text-gray-900">{{ $line->generalItem->item_name }}</td>
                            <td class="px-3 py-2 text-gray-900 text-right">{{ number_format(round($line->quantity), 0) }}</td>
                            <td class="px-3 py-2 text-gray-900 text-right">{{ number_format(round($line->sale_price), 0) }}</td>
                            <td class="px-3 py-2 text-gray-900 text-right font-semibold">{{ number_format(round($line->quantity * $line->sale_price), 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            @if($saleInvoice->armLines->count() > 0)
            <div class="mb-6">
                <div class="text-xs font-medium text-red-600 mb-2">Arms (Serial-based)</div>
                <div class="overflow-x-auto">
                <table class="w-full text-xs border border-gray-200 rounded shadow-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Arm</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Serial</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Sale Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($saleInvoice->armLines as $line)
                        <tr class="hover:bg-red-50">
                            <td class="px-3 py-2 text-gray-900">{{ $line->arm->arm_title }}</td>
                            <td class="px-3 py-2 text-gray-600">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xxs px-1 py-0.5 rounded font-mono tracking-wider">
                                    {{ $line->arm->serial_no }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-gray-900 text-right font-semibold">{{ number_format(round($line->sale_price), 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif
        </div>
                                    
        </div>
                                    
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col items-end">
                <div class="w-full max-w-xs">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900">{{ number_format(round($saleInvoice->subtotal), 0) }}</span>
                    </div>
                    @if($saleInvoice->shipping_charges > 0)
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium text-gray-900">{{ number_format(round($saleInvoice->shipping_charges), 0) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span>@currency($saleInvoice->total_amount)</span>
                    </div>
                </div>
            </div>

        <div class="p-6 border-t border-gray-200 text-xs text-gray-600 flex flex-col md:flex-row justify-between gap-4 bg-white">
            <div>
                <div class="mb-1">
                    <span class="font-semibold text-gray-700">Notes:</span>
                    <span>All items are subject to verification upon delivery.</span>
                </div>
            </div>
            <div class="text-right">
                <div>By: <span class="font-medium text-gray-800">{{ $saleInvoice->createdBy->name ?? 'System' }}</span></div>
                <div>Created: {{ $saleInvoice->created_at->format('M d, Y H:i') }}</div>
                @if($saleInvoice->updated_at->diffInSeconds($saleInvoice->created_at) > 0)
                    <div>Updated: {{ $saleInvoice->updated_at->format('M d, Y H:i') }}</div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable-invoice, #printable-invoice * { visibility: visible; }
            .no-print { display: none !important; }
            body, html { margin: 0; padding: 0; background: white; font-family: 'Arial', sans-serif; }
            #printable-invoice {
                position: absolute; left: 0; top: 0; width: 100%; max-width: none; margin: 0; padding: 0;
                box-shadow: none; border: none; background: white; font-size: 11pt; line-height: 1.3; color: black;
            }
            #printable-invoice .p-6 { padding: 15px !important; }
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
                font-size: 9pt; width: 100%; border-collapse: collapse; margin-bottom: 15px;
            }
            #printable-invoice th {
                background: #f8f9fa !important; border: 1px solid #dee2e6; padding: 8px 6px !important;
                font-weight: bold; text-align: left; color: #495057;
            }
            #printable-invoice td {
                border: 1px solid #dee2e6; padding: 6px !important; vertical-align: top;
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
            .breadcrumb, .dynamic-heading, button, a[href*="edit"], a[href*="audit-log"], form[action*="post"], .mb-4.flex {
                display: none !important;
            }
            @page { size: A4 portrait; margin: 15mm; }
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
</x-app-layout>
