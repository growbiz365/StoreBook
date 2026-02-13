<x-app-layout>
    @section('title', 'Stock Impacts - Purchase #' . $purchase->id . ' - Purchases Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => route('purchases.index'), 'label' => 'Purchases'], ['url' => route('purchases.show', $purchase), 'label' => 'Purchase #' . $purchase->id], ['url' => '#', 'label' => 'Stock Impacts']]" />
    
    <x-dynamic-heading 
        :title="'Stock Impacts - Purchase #' . $purchase->id" 
        :subtitle="'Preview inventory impact before posting this purchase'"
        :icon="'fas fa-chart-line'"
    />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            

            <!-- Header Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <!-- Status Badge -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if($purchase->status == 'draft') bg-gray-100 text-gray-800
                                @elseif($purchase->status == 'posted') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                @if($purchase->status == 'draft')
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                @elseif($purchase->status == 'posted')
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                {{ ucfirst($purchase->status) }}
                            </span>
                            
                            <span class="text-sm text-gray-500">
                                Created {{ $purchase->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('purchases.show', $purchase) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Purchase
                            </a>
                            
                            @if($purchase->canBePosted())
                                <form action="{{ route('purchases.post', $purchase) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200" 
                                            onclick="return confirm('Are you sure you want to post this purchase?')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Post Purchase
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Purchase Summary
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label class="text-sm font-medium text-gray-700">Vendor</x-input-label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $purchase->party->name }}</p>
                        </div>
                        <div>
                            <x-input-label class="text-sm font-medium text-gray-700">Invoice Date</x-input-label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $purchase->formatted_invoice_date }}</p>
                        </div>
                        <div>
                            <x-input-label class="text-sm font-medium text-gray-700">Total Amount</x-input-label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold text-blue-600">{{ number_format($purchase->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Items to be Created -->
            @if($purchase->generalLines->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        General Items to be Created
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($purchase->generalLines as $line)
                                @php
                                    $allocation = $allocations[$line->id] ?? null;
                                    $effectiveUnitCost = $allocation ? $allocation['effective_unit_cost'] : $line->getEffectiveUnitCost();
                                    $batchCode = 'PUR-' . $purchase->id . '-' . $line->line_no;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $line->line_no }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->generalItem->item_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $batchCode }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($line->qty, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ number_format($effectiveUnitCost, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-blue-600">{{ number_format($line->qty * $effectiveUnitCost, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Arms to be Created -->
            @if($purchase->armLines->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Arms to be Created
                    </h3>
                    @foreach($purchase->armLines as $line)
                    <div class="border border-gray-200 rounded-lg p-6 mb-6 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
                            <div>
                                <x-input-label class="text-sm font-medium text-gray-700">Line</x-input-label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $line->line_no }}</p>
                            </div>
                            <div>
                                <x-input-label class="text-sm font-medium text-gray-700">Quantity</x-input-label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $line->qty }}</p>
                            </div>
                            <div>
                                <x-input-label class="text-sm font-medium text-gray-700">Unit Price</x-input-label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ number_format($line->unit_price, 2) }}</p>
                            </div>
                            <div>
                                <x-input-label class="text-sm font-medium text-gray-700">Line Total</x-input-label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($line->line_total, 2) }}</p>
                            </div>
                        </div>

                        @if($line->armSerials->count() > 0)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Serials to be Created
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg shadow">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial No</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arm Title</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Make</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caliber</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($line->armSerials as $serial)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium font-mono bg-gray-100 px-2 py-1 rounded">{{ $serial->serial_no }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $serial->arm_title ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $serial->make->make ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $serial->caliber->arm_caliber ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $serial->category->arm_category ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ number_format($serial->purchase_price ?? $line->unit_price, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Validation Status -->
            @if($purchase->validateForPosting())
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-red-800">Validation Issues</h3>
                </div>
                <ul class="mt-3 list-disc list-inside text-red-700 space-y-1">
                    @foreach($purchase->validateForPosting() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-green-800">@if($purchase->isPosted()) Posted Successfully @else Ready to Post @endif</h3>
                </div>
                <p class="mt-2 text-green-700">@if($purchase->isPosted()) This purchase has been posted successfully. All inventory entries have been created. @else This purchase is ready to be posted. All validation checks have passed. @endif</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
