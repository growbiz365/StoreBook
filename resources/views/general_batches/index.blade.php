<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/arms-dashboard', 'label' => 'Arms Management'], ['url' => route('general-batches.index'), 'label' => 'General Batches']]" />

    <x-dynamic-heading title="General Batches" />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Batches</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batches->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Active Batches</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batches->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batches->where('qty_remaining', '<', 10)->count() }}</p>
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
                    <p class="text-lg font-semibold text-gray-900">PKR {{ number_format($batches->sum('total_cost'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200">
        <!-- Enhanced Filter Section -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('general-batches.index') }}" class="space-y-4">
                <!-- Search and Item Filter Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="search">Search Item</x-input-label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm" 
                                   placeholder="Search by item name or code..." />
                        </div>
                    </div>
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
                        @if(request('search') || request('item_id') || request('from_date') || request('to_date'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Filters Applied
                            </span>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('general-batches.index') }}" 
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
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Details</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Information</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                       
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $batch->item->item_name }}</div>
                                        <div class="text-sm text-gray-500 font-mono">{{ $batch->item->item_code }}</div>
                                        <div class="text-xs text-gray-400">{{ $batch->item->itemType->item_type ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Received:</span>
                                        <span class="font-medium text-gray-900">{{ $batch->qty_received }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Remaining:</span>
                                        <span class="font-medium text-gray-900">{{ $batch->qty_remaining }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $percentage = $batch->qty_received > 0 ? ($batch->qty_remaining / $batch->qty_received) * 100 : 0;
                                            $colorClass = $percentage > 70 ? 'bg-green-500' : ($percentage > 30 ? 'bg-yellow-500' : 'bg-red-500');
                                        @endphp
                                        <div class="h-2 rounded-full {{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm">
                                        <span class="text-gray-500">Unit Cost:</span>
                                        <span class="font-medium text-gray-900 ml-2">PKR {{ number_format($batch->unit_cost, 2) }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-500">Total Cost:</span>
                                        <span class="font-medium text-gray-900 ml-2">PKR {{ number_format($batch->total_cost, 2) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $batch->received_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $batch->received_date->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = $batch->qty_remaining > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    $statusText = $batch->qty_remaining > 0 ? 'Active' : 'Depleted';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    @if($batch->qty_remaining > 0)
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{ $statusText }}
                                </span>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No batches found</h3>
                                    <p class="text-gray-500">Get started by creating your first batch or adjust your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        @if($batches->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $batches->links() }}
            </div>
        @endif
    </div>
</x-app-layout>


