<x-app-layout>
    @section('title', 'Purchase Return Details - Purchases Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/purchases-dashboard', 'label' => 'Purchases Dashboard'],
        ['url' => '/purchase-returns', 'label' => 'Purchase Returns'],
        ['url' => '#', 'label' => 'Return #' . $purchaseReturn->id]
    ]" />

    <x-dynamic-heading 
        :title="'Return #' . $purchaseReturn->id" 
        :subtitle="'Detailed Return Summary'"
        :icon="'fas fa-undo'"
    />

    <!-- Status Badges -->
    <div class="mb-4 flex flex-wrap justify-start gap-2">
        @if($purchaseReturn->status == 'draft')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Draft
            </span>
        @elseif($purchaseReturn->status == 'posted')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Posted
            </span>
        @elseif($purchaseReturn->status == 'cancelled')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Cancelled
            </span>
        @endif
    </div>

    <div class="mb-4 flex flex-wrap justify-end gap-2">
        @if($purchaseReturn->canBeEdited())
            <a href="{{ route('purchase-returns.edit', $purchaseReturn) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md shadow transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Return
            </a>
        @endif
        
        @if($purchaseReturn->canBeCancelled())
            <form action="{{ route('purchase-returns.cancel', $purchaseReturn) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this sale return? This will restore inventory and cannot be undone.')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel Return
                </button>
            </form>
        @endif
        
        @if($purchaseReturn->canBePosted())
            <form action="{{ route('purchase-returns.post', $purchaseReturn->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to post this sale return? This action cannot be undone.')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 shadow transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Post Purchase Return
                </button>
            </form>
        @endif
        
        <button onclick="printReturn()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Return
        </button>
    </div>
                            
    <div class="bg-white border border-gray-200 w-full rounded-lg shadow-lg overflow-hidden" id="printable-return">
        <div class="p-4 border-b-2 border-gray-300 bg-white header">
            <div class="flex justify-between items-center mb-3">
                <div class="flex-1">
                    <div class="text-xl font-bold text-gray-900 mb-1">{{ $purchaseReturn->business->business_name ?? 'Business Name' }}</div>
                    <div class="space-y-0.5">
                    @if($purchaseReturn->business && $purchaseReturn->business->address)
                            <div class="text-xs text-gray-600">{{ $purchaseReturn->business->address }}</div>
                    @endif
                    @if($purchaseReturn->business && $purchaseReturn->business->phone)
                            <div class="text-xs text-gray-600">Phone: {{ $purchaseReturn->business->phone }}</div>
                    @endif
                    @if($purchaseReturn->business && $purchaseReturn->business->email)
                            <div class="text-xs text-gray-600">Email: {{ $purchaseReturn->business->email }}</div>
                    @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-black text-gray-900 mb-1 tracking-tight">
                        PURCHASE RETURN #{{ $purchaseReturn->id }}
                    </div>
                    <div class="text-xs text-gray-600">
                        Date: @businessDate($purchaseReturn->return_date)
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div>
                    @if($purchaseReturn->return_type === 'cash')
                        <div class="font-semibold text-gray-800 mb-2 flex items-center gap-1">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.607 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Customer Details
                        </div>
                        @if($purchaseReturn->name_of_customer)
                            <div class="text-gray-900 font-medium text-base">{{ $purchaseReturn->name_of_customer }}</div>
                            @if($purchaseReturn->father_name)
                                <div class="text-gray-600">Father: {{ $purchaseReturn->father_name }}</div>
                            @endif
                            @if($purchaseReturn->contact)
                                <div class="text-gray-600">Contact: {{ $purchaseReturn->contact }}</div>
                            @endif
                            @if($purchaseReturn->cnic)
                                <div class="text-gray-600">CNIC: {{ $purchaseReturn->cnic }}</div>
                            @endif
                            @if($purchaseReturn->address)
                                <div class="text-gray-600">Address: {{ $purchaseReturn->address }}</div>
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
                        <div class="text-gray-900 font-medium text-base">{{ $purchaseReturn->party->name ?? 'No Party' }}</div>
                        @if($purchaseReturn->party)
                            @if($purchaseReturn->party->cnic)
                                <div class="text-gray-600">CNIC: {{ $purchaseReturn->party->cnic }}</div>
                            @endif
                            @if($purchaseReturn->party->phone_no)
                                <div class="text-gray-600">Phone: {{ $purchaseReturn->party->phone_no }}</div>
                            @endif
                            @if($purchaseReturn->party->address)
                                <div class="text-gray-600">Address: {{ $purchaseReturn->party->address }}</div>
                            @endif
                        @else
                            <div class="text-gray-600">No party details</div>
                        @endif
                    @endif
                </div>
            
            @if($purchaseReturn->return_type === 'cash' && ($purchaseReturn->licence_no || $purchaseReturn->licence_issue_date || $purchaseReturn->licence_valid_upto || $purchaseReturn->licence_issued_by || $purchaseReturn->re_reg_no || $purchaseReturn->dc || $purchaseReturn->Date))
            <div class="text-left">
                <div class="font-semibold text-gray-800 mb-2 flex items-center justify-left gap-1">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Other Details
                </div>
                <div class="text-left">
                    @if($purchaseReturn->licence_no)
                        <div class="text-gray-600">Licence: {{ $purchaseReturn->licence_no }}</div>
                    @endif
                    @if($purchaseReturn->licence_issue_date)
                        <div class="text-gray-600">Issue Date: @businessDate($purchaseReturn->licence_issue_date)</div>
                    @endif
                    @if($purchaseReturn->licence_valid_upto)
                        <div class="text-gray-600">Valid Until: @businessDate($purchaseReturn->licence_valid_upto)</div>
                    @endif
                    @if($purchaseReturn->licence_issued_by)
                        <div class="text-gray-600">Issued By: {{ $purchaseReturn->licence_issued_by }}</div>
                    @endif
                    @if($purchaseReturn->re_reg_no)
                        <div class="text-gray-600">Re-Reg No: {{ $purchaseReturn->re_reg_no }}</div>
                    @endif
                    @if($purchaseReturn->dc)
                        <div class="text-gray-600">DC: {{ $purchaseReturn->dc }}</div>
                    @endif
                    @if($purchaseReturn->Date)
                        <div class="text-gray-600">Date: @businessDate($purchaseReturn->Date)</div>
                    @endif
                </div>
            </div>
            @elseif($purchaseReturn->return_type === 'credit' && ($purchaseReturn->party_license_no || $purchaseReturn->party_license_issue_date || $purchaseReturn->party_license_valid_upto || $purchaseReturn->party_license_issued_by || $purchaseReturn->party_re_reg_no || $purchaseReturn->party_dc || $purchaseReturn->party_dc_date))
            <div class="text-left">
                <div class="font-semibold text-gray-800 mb-2 flex items-center justify-left gap-1">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Party License Details
                </div>
                <div class="text-left">
                    @if($purchaseReturn->party_license_no)
                        <div class="text-gray-600">Licence: {{ $purchaseReturn->party_license_no }}</div>
                    @endif
                    @if($purchaseReturn->party_license_issue_date)
                        <div class="text-gray-600">Issue Date: @businessDate($purchaseReturn->party_license_issue_date)</div>
                    @endif
                    @if($purchaseReturn->party_license_valid_upto)
                        <div class="text-gray-600">Valid Until: @businessDate($purchaseReturn->party_license_valid_upto)</div>
                    @endif
                    @if($purchaseReturn->party_license_issued_by)
                        <div class="text-gray-600">Issued By: {{ $purchaseReturn->party_license_issued_by }}</div>
                    @endif
                    @if($purchaseReturn->party_re_reg_no)
                        <div class="text-gray-600">Re-Reg No: {{ $purchaseReturn->party_re_reg_no }}</div>
                    @endif
                    @if($purchaseReturn->party_dc)
                        <div class="text-gray-600">DC: {{ $purchaseReturn->party_dc }}</div>
                    @endif
                    @if($purchaseReturn->party_dc_date)
                        <div class="text-gray-600">DC Date: @businessDate($purchaseReturn->party_dc_date)</div>
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
                        Return Details
                    </div>
                    <div class="text-gray-900">Type: <span class="font-medium">{{ ucfirst($purchaseReturn->return_type) }}</span></div>
                    @if($purchaseReturn->bank)
                        <div class="text-gray-600">Bank: {{ $purchaseReturn->bank->chartOfAccount->name ?? $purchaseReturn->bank->account_name }}</div>
                    @endif
                    @if($purchaseReturn->reason)
                        <div class="text-gray-600">Reason: {{ $purchaseReturn->reason }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($purchaseReturn->generalLines->count() > 0)
            <div class="mb-6">
                <div class="text-xs font-medium text-blue-600 mb-2">General Items</div>
                <div class="overflow-x-auto">
                <table class="w-full text-xs border border-gray-200 rounded shadow-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Item</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Description</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Qty</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Return Price</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseReturn->generalLines as $line)
                        <tr class="hover:bg-blue-50">
                            <td class="px-3 py-2 text-gray-900">{{ $line->generalItem->item_name }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $line->generalItem->item_type ?? 'General Item' }}</td>
                            <td class="px-3 py-2 text-gray-900 text-right">{{ number_format(round($line->quantity), 0) }}</td>
                            <td class="px-3 py-2 text-gray-900 text-right">{{ number_format($line->return_price, 2) }}</td>
                            <td class="px-3 py-2 text-gray-900 text-right font-semibold">{{ number_format($line->quantity * $line->return_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            @if($purchaseReturn->armLines->count() > 0)
            <div class="mb-6">
                <div class="text-xs font-medium text-red-600 mb-2">Arms (Serial-based)</div>
                <div class="overflow-x-auto">
                <table class="w-full text-xs border border-gray-200 rounded shadow-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Type</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Make</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Serial No.</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Return Price</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseReturn->armLines as $line)
                        <tr class="hover:bg-red-50">
                            <td class="px-3 py-2 text-gray-900">
                                @if($line->arm && $line->arm->armType)
                                    {{ $line->arm->armType->arm_type }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-900">
                                @if($line->arm && $line->arm->armMake)
                                    {{ $line->arm->armMake->arm_make }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xxs px-1 py-0.5 rounded font-mono tracking-wider">
                                    {{ $line->arm->serial_no }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-gray-900 text-right">{{ number_format($line->return_price, 2) }}</td>
                            <td class="px-3 py-2 text-gray-900 text-right font-semibold">{{ number_format($line->return_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

        </div>
                                    
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col items-end">
                <div class="w-full max-w-xs">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900">{{ number_format($purchaseReturn->subtotal, 2) }}</span>
                    </div>
                    @if($purchaseReturn->shipping_charges > 0)
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium text-gray-900">{{ number_format($purchaseReturn->shipping_charges, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span>@currency($purchaseReturn->total_amount)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 text-xs text-gray-600 flex flex-col md:flex-row justify-between gap-4 bg-white">
            <div>
                <div class="mb-1">
                    <span class="font-semibold text-gray-700">Notes:</span>
                    <span>All returned items are subject to verification upon receipt.</span>
                </div>
            </div>
            <div class="text-right">
                <div>By: <span class="font-medium text-gray-800">{{ $purchaseReturn->createdBy->name ?? 'System' }}</span></div>
                <div>Created: @businessDateTime($purchaseReturn->created_at)</div>
                @if($purchaseReturn->updated_at->diffInSeconds($purchaseReturn->created_at) > 0)
                    <div>Updated: @businessDateTime($purchaseReturn->updated_at)</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.title = "Purchase Return #{{ $purchaseReturn->id }} - {{ $purchaseReturn->business->business_name ?? 'Business' }}";
        function printReturn() {
            const originalTitle = document.title;
            document.title = "Purchase Return #{{ $purchaseReturn->id }} - {{ $purchaseReturn->business->business_name ?? 'Business' }}";
            document.body.classList.add('printing');
            window.print();
            setTimeout(() => {
                document.title = originalTitle;
                document.body.classList.remove('printing');
            }, 1000);
        }
        window.addEventListener('beforeprint', function() {
            console.log('Printing return...');
        });
        window.addEventListener('afterprint', function() {
            console.log('Print completed');
        });
    </script>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable-return, #printable-return * { visibility: visible; }
            body, html { margin: 0; padding: 0; background: white; font-family: 'Arial', sans-serif; }
            #printable-return {
                position: absolute; left: 0; top: 0; width: 100%; max-width: none; margin: 0; padding: 0;
                box-shadow: none; border: none; background: white; font-size: 11pt; line-height: 1.3; color: black;
            }
            #printable-return .p-6 { padding: 15px !important; }
            #printable-return .p-4 { padding: 12px !important; }
            #printable-return .flex { display: flex !important; }
            #printable-return .justify-between { justify-content: space-between !important; }
            #printable-return .items-start { align-items: flex-start !important; }
            #printable-return .text-right { text-align: right !important; }
            #printable-return .mb-4 { margin-bottom: 15px !important; }
            #printable-return .mt-2 { margin-top: 8px !important; }
            #printable-return .mt-1 { margin-top: 4px !important; }
            #printable-return .bg-gradient-to-r,
            #printable-return .shadow-lg,
            #printable-return .shadow-sm { background: white !important; box-shadow: none !important; }
            #printable-return .grid { display: grid !important; }
            #printable-return .md\:grid-cols-2 { grid-template-columns: 1fr 1fr !important; }
            #printable-return .gap-6 { gap: 20px !important; }
            #printable-return table {
                font-size: 9pt; width: 100%; border-collapse: collapse; margin-bottom: 15px;
            }
            #printable-return th {
                background: #f8f9fa !important; border: 1px solid #dee2e6; padding: 8px 6px !important;
                font-weight: bold; text-align: left; color: #495057;
            }
            #printable-return td {
                border: 1px solid #dee2e6; padding: 6px !important; vertical-align: top;
            }
            #printable-return tr:hover { background: white !important; }
            #printable-return .bg-green-100,
            #printable-return .bg-yellow-100,
            #printable-return .bg-gray-100 {
                background: #f8f9fa !important; border: 1px solid #dee2e6; color: #495057 !important;
            }
            #printable-return .bg-blue-100 {
                background: #e3f2fd !important; border: 1px solid #bbdefb; color: #1976d2 !important;
            }
            #printable-return svg { display: none !important; }
            .breadcrumb, .dynamic-heading, button, a[href*="edit"], a[href*="audit-log"], form[action*="post"], .mb-4.flex {
                display: none !important;
            }
            @page { size: A4 portrait; margin: 15mm; }
            #printable-return { page-break-inside: avoid; }
            #printable-return table { page-break-inside: auto; }
            #printable-return tr { page-break-inside: avoid; page-break-after: auto; }
            #printable-return .bg-gray-50 { background: #f8f9fa !important; }
            #printable-return .border-t { border-top: 2px solid #dee2e6 !important; }
            #printable-return .text-gray-600,
            #printable-return .text-gray-500 { color: #495057 !important; }
            #printable-return .text-gray-900 { color: #212529 !important; }
            #printable-return * { background-color: transparent !important; }
            #printable-return .bg-white { background: white !important; }
            #printable-return .bg-gray-50 { background: #f8f9fa !important; }
            #printable-return .bg-gray-100 { background: #f8f9fa !important; }
            #printable-return .text-xxs { font-size: 7pt !important; }
            #printable-return .text-xs { font-size: 8pt !important; }
            #printable-return .text-sm { font-size: 9pt !important; }
            #printable-return .text-base { font-size: 10pt !important; }
            #printable-return .text-lg { font-size: 12pt !important; }
            #printable-return .text-2xl { font-size: 16pt !important; }
            #printable-return .mb-6 { margin-bottom: 15px !important; }
            #printable-return .mb-3 { margin-bottom: 8px !important; }
            #printable-return .mb-2 { margin-bottom: 5px !important; }
            #printable-return .mb-1 { margin-bottom: 3px !important; }
            #printable-return .space-y-0\.5 > * + * { margin-top: 2px !important; }
            #printable-return .text-xl { font-size: 14pt !important; }
            #printable-return .text-3xl { font-size: 18pt !important; }
            #printable-return .text-lg { font-size: 12pt !important; }
            #printable-return .items .grid { display: block !important; }
            #printable-return .items .md\:grid-cols-2 > div { display: block !important; margin-bottom: 15px !important; }
            #printable-return .header .grid {
                display: grid !important; 
                grid-template-columns: 1fr 1fr 1fr !important; 
                grid-template-rows: auto !important;
                gap: 20px !important;
            }
            #printable-return .header .md\:grid-cols-3 { 
                grid-template-columns: 1fr 1fr 1fr !important; 
            }
            #printable-return .header .grid > div {
                display: block !important; 
                margin-bottom: 0 !important; 
                page-break-inside: avoid !important;
            }
            #printable-return .header .grid > div:first-child {
                grid-column: 1 !important; 
                text-align: left !important; 
                justify-self: start !important;
            }
            #printable-return .header .grid > div:nth-child(2) {
                grid-column: 2 !important; 
                text-align: left !important; 
                justify-self: start !important;
            }
            #printable-return .header .grid > div:last-child {
                grid-column: 3 !important; 
                text-align: right !important; 
                justify-self: end !important;
            }
            #printable-return .header .text-center {
                text-align: center !important;
            }
            #printable-return .header .text-left {
                text-align: left !important;
            }
            #printable-return .header .text-right {
                text-align: right !important;
            }
            #printable-return .header .justify-center {
                justify-content: center !important;
            }
            #printable-return .header .justify-left {
                justify-content: flex-start !important;
            }
            #printable-return .header .justify-end {
                justify-content: flex-end !important;
            }
            #printable-return .header .flex {
                display: flex !important; 
                flex-direction: row !important; 
                justify-content: space-between !important; 
                align-items: flex-start !important;
            }
            #printable-return .header .flex-1 { 
                flex: 1 !important; 
            }
            #printable-return .header .flex > div { 
                flex-shrink: 0 !important; 
                page-break-inside: avoid !important; 
            }
            #printable-return .items .flex { 
                display: block !important; 
            }
            #printable-return .items .justify-between { 
                text-align: left !important; 
            }
            #printable-return .items .justify-end { 
                text-align: left !important; 
            }
            #printable-return .items-end { 
                text-align: right !important; 
            }
            #printable-return .max-w-xs { 
                max-width: none !important; 
                width: 100% !important; 
            }
        }
        body.printing { background: white !important; }
    </style>
</x-app-layout>