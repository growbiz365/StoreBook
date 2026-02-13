<x-app-layout>
    @section('title', 'Stock Adjustment Details - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'],
        ['url' => route('stock-adjustments.index'), 'label' => 'Stock Adjustments'],
        ['url' => '#', 'label' => 'Adjustment Details'],
    ]" />

    <x-dynamic-heading title="Stock Adjustment Details" />

    <!-- Adjustment Information -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Adjustment #{{ $stockAdjustment->id }}</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('stock-adjustments.edit', $stockAdjustment) }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('stock-adjustments.destroy', $stockAdjustment) }}" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this adjustment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Adjustment Type</dt>
                        <dd class="mt-1">
                            @if($stockAdjustment->adjustment_type === 'addition')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Addition</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Subtraction</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->adjustment_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->user->name ?? 'N/A' }}</dd>
                    </div>
                    @if($stockAdjustment->description)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->description }}</dd>
                    </div>
                    @endif
                </div>
                <div class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Items Count</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->itemLines->count() }}</dd>
                    </div>
                    {{-- Arms Count hidden - StoreBook is items-only --}}
                    {{-- <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Arms Count</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->armLines->count() }}</dd>
                    </div> --}}
                </div>
                <div class="space-y-3">
                    <div>
                        @php $itemsTotal = $stockAdjustment->itemLines->sum('total_amount'); /* $armsTotal = $stockAdjustment->armLines->sum('price'); */ @endphp
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">Rs. {{ number_format(round($itemsTotal), 0) }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stockAdjustment->itemLines->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stockAdjustment->itemLines as $line)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $line->item->item_name ?? 'N/A' }} ({{ $line->item->item_code ?? '' }})</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format(round($line->quantity), 0) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rs. {{ number_format(round($line->unit_cost), 0) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rs. {{ number_format(round($line->total_amount), 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Arms section hidden - StoreBook is items-only --}}
    @if(false && $stockAdjustment->armLines->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden" style="display: none;">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Arms</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arm</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stockAdjustment->armLines as $line)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $line->arm->arm_title ?? 'N/A' }} (SN: {{ $line->arm->serial_no ?? '-' }})</td>
                            <td class="px-4 py-3 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($line->reason) }}</span></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rs. {{ number_format(round($line->price ?? 0), 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Stock Ledger Entries -->
    @if($stockAdjustment->stockLedgerEntries->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Related Stock Ledger Entries</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stockAdjustment->stockLedgerEntries as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $entry->transaction_date->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                    {{ $entry->transaction_type === 'stock_adjustment' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $entry->transaction_type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <span class="{{ $entry->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $entry->quantity > 0 ? '+' : '' }}{{ number_format(round($entry->quantity), 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                Rs. {{ number_format(round($entry->unit_cost), 0) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                Rs. {{ number_format(round($entry->total_cost), 0) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $entry->remarks }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-app-layout>
