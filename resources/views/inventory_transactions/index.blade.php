<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],[ 'url' => '/arms-dashboard', 'label' => 'Arms Management'], ['url' => route('inventory-transactions.index'), 'label' => 'Inventory Transactions']]" />
    <x-dynamic-heading title="Inventory Transactions" />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Transactions</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $transactions->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Inbound</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $transactions->where('tx_type', 'purchase')->count() + $transactions->where('tx_type', 'opening')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Outbound</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $transactions->where('tx_type', 'sale')->count() + $transactions->where('tx_type', 'adjustment')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Value</p>
                    <p class="text-lg font-semibold text-gray-900">PKR {{ number_format($transactions->sum('total_cost'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200">
        <!-- Enhanced Filter Section -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('inventory-transactions.index') }}" class="space-y-4">
                <!-- Search and Filter Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="item_id">Filter by Item</x-input-label>
                        <select name="item_id" id="item_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Items</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->item_name }} ({{ $item->item_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="batch_id">Filter by Batch</x-input-label>
                        <select name="batch_id" id="batch_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Batches</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->item->item_name }} - {{ $batch->received_date->format('Y-m-d') }} ({{ $batch->qty_received }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date_range">Date Range</x-input-label>
                        <div class="mt-1 flex space-x-2">
                            <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            <span class="text-gray-500 self-center">to</span>
                            <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons Row -->
                <div class="flex items-center justify-between pt-2">
                    <div class="text-sm text-gray-600">
                        @if(request('item_id') || request('batch_id') || request('from_date') || request('to_date'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Filters Applied
                            </span>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('inventory-transactions.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L6.293 13H5a1 1 0 01-1-1V4z" />
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Enhanced Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Details</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item & Batch</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity & Cost</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @php
                                            $iconClass = match($tx->tx_type) {
                                                'purchase' => 'bg-green-100 text-green-600',
                                                'sale' => 'bg-red-100 text-red-600',
                                                'opening' => 'bg-blue-100 text-blue-600',
                                                'adjustment' => 'bg-yellow-100 text-yellow-600',
                                                default => 'bg-gray-100 text-gray-600'
                                            };
                                            $icon = match($tx->tx_type) {
                                                'purchase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />',
                                                'sale' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />',
                                                'opening' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                                'adjustment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />',
                                                default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                                            };
                                        @endphp
                                        <div class="h-10 w-10 rounded-lg {{ $iconClass }} flex items-center justify-center">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $icon !!}
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @php
                                                $typeText = match($tx->tx_type) {
                                                    'purchase' => 'Purchase',
                                                    'sale' => 'Sale',
                                                    'opening' => 'Opening Stock',
                                                    'adjustment' => 'Adjustment',
                                                    default => ucfirst($tx->tx_type)
                                                };
                                            @endphp
                                            {{ $typeText }}
                                        </div>
                                        <div class="text-sm text-gray-500">Transaction #{{ $tx->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm">
                                        <span class="text-gray-500">Item:</span>
                                        <span class="font-medium text-gray-900 ml-2">{{ $tx->item->item_name }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-500">Code:</span>
                                        <span class="font-mono text-gray-700 ml-2">{{ $tx->item->item_code }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-500">Batch:</span>
                                        <span class="text-gray-700 ml-2">#{{ $tx->batch->id }} ({{ $tx->batch->received_date->format('M d, Y') }})</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Quantity:</span>
                                        <span class="font-medium text-gray-900">{{ $tx->qty }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Unit Cost:</span>
                                        <span class="font-medium text-gray-900">PKR {{ number_format($tx->unit_cost, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Total:</span>
                                        <span class="font-semibold text-gray-900">PKR {{ number_format($tx->total_cost, 2) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $tx->date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $tx->date->diffForHumans() }}</div>
                                @if($tx->user)
                                    <div class="text-xs text-gray-400 mt-1">by {{ $tx->user->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = match($tx->tx_type) {
                                        'purchase' => 'bg-green-100 text-green-800',
                                        'sale' => 'bg-red-100 text-red-800',
                                        'opening' => 'bg-blue-100 text-blue-800',
                                        'adjustment' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    @php
                                        $typeIcon = match($tx->tx_type) {
                                            'purchase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />',
                                            'sale' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />',
                                            'opening' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                            'adjustment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />',
                                            default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                                        };
                                    @endphp
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $typeIcon !!}
                                    </svg>
                                    {{ ucfirst($tx->tx_type) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                                    <p class="text-gray-500">Get started by creating your first transaction or adjust your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>


