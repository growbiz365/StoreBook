<x-app-layout>
    @section('title', $generalItem->item_name . ' - General Item Details - General Items Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'],
        ['url' => route('general-items.index'), 'label' => 'General Items'],
        ['url' => '#', 'label' => $generalItem->item_name]
    ]" />

    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 mt-5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $generalItem->item_name }}</h1>
                            <p class="text-sm text-gray-500 font-mono">{{ $generalItem->item_code }}</p>
                        </div>
                    </div>
                </div>
        
        <div class="flex gap-3">
            <a href="{{ route('general-items.edit', $generalItem) }}" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                        Edit Item
            </a>
            <a href="{{ route('general-items.edit-opening-stock', $generalItem) }}" 
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                        Edit Opening Stock
            </a>
            <a href="{{ route('general-items.index') }}" 
                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Status Bar -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">Status:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                            {{ $generalItem->stock_status === 'low' ? 'bg-red-100 text-red-800' : 
                               ($generalItem->stock_status === 'normal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($generalItem->stock_status) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">Stock Level:</span>
                        <span class="text-lg font-bold text-gray-900">{{ $generalItem->opening_stock }}</span>
                        @if($generalItem->min_stock_limit)
                            <span class="text-sm text-gray-500">/ {{ $generalItem->min_stock_limit }} min</span>
                        @endif
        </div>
    </div>

    @if($generalItem->stock_status === 'low')
                    <div class="flex items-center space-x-2 text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                        <span class="text-sm font-medium">Low Stock Alert</span>
                </div>
                @endif
                    </div>
                </div>
            </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Item Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Item Information
                    </h3>
        </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                    <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Item Name</label>
                                <p class="text-base text-gray-900 font-medium">{{ $generalItem->item_name }}</p>
                    </div>
                    <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Item Code</label>
                                <p class="text-sm font-mono text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $generalItem->item_code }}</p>
                    </div>
                    <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Item Type</label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-base text-gray-900">{{ $generalItem->itemType->item_type }}</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $generalItem->itemType->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $generalItem->itemType->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Carton/Pack Size</label>
                                <p class="text-base text-gray-900">{{ $generalItem->carton_or_pack_size ?: 'Not specified' }}</p>
                    </div>
                    <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Business</label>
                                <p class="text-base text-gray-900">{{ $generalItem->business->business_name }}</p>
                    </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Created</label>
                                <p class="text-sm text-gray-600">{{ $generalItem->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Pricing Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        Financial Details
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <label class="block text-sm font-medium text-blue-600 mb-2">Cost Price</label>
                            <p class="text-2xl font-bold text-blue-900">PKR {{ number_format($generalItem->cost_price, 2) }}</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <label class="block text-sm font-medium text-green-600 mb-2">Sale Price</label>
                            <p class="text-2xl font-bold text-green-900">PKR {{ number_format($generalItem->sale_price, 2) }}</p>
                    </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <label class="block text-sm font-medium text-purple-600 mb-2">Opening Total</label>
                            <p class="text-2xl font-bold text-purple-900">PKR {{ number_format($generalItem->opening_total, 2) }}</p>
                    </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Opening Stock</label>
                            <p class="text-xl font-semibold text-gray-900">{{ number_format($generalItem->opening_stock) }}</p>
                    </div>
                    @if($generalItem->min_stock_limit)
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <label class="block text-sm font-medium text-yellow-600 mb-1">Minimum Stock Limit</label>
                            <p class="text-xl font-semibold text-yellow-900">{{ number_format($generalItem->min_stock_limit) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

            <!-- Journal Entries Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ journalEntriesOpen: true }">
                <div class="px-6 py-4 border-b border-gray-200">
                    <button @click="journalEntriesOpen = !journalEntriesOpen" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Journal Entries
                        </h3>
                        <svg class="w-5 h-5 text-indigo-600 transition-transform duration-200" :class="{ 'rotate-180': journalEntriesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
        </div>

                <div x-show="journalEntriesOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
                    <div class="p-6">
                    @php
                        $journalEntries = \App\Models\JournalEntry::where('voucher_id', $generalItem->id)
                            ->where('voucher_type', 'General Item')
                            ->with('account')
                            ->orderBy('debit_amount', 'desc')
                            ->get();
                    @endphp

                    @if($journalEntries->count() > 0)
                            <div class="overflow-hidden rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($journalEntries as $entry)
                                            <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $entry->account->code }} - {{ $entry->account->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($entry->debit_amount > 0)
                                                            <span class="font-medium text-green-600">{{ number_format(round($entry->debit_amount), 0) }}</span>
                                                    @else
                                                            <span class="text-gray-400">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($entry->credit_amount > 0)
                                                            <span class="font-medium text-red-600">{{ number_format(round($entry->credit_amount), 0) }}</span>
                                                    @else
                                                            <span class="text-gray-400">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-600">
                                                    {{ $entry->date_added->format('d-m-Y') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No journal entries</h3>
                                <p class="mt-1 text-sm text-gray-500">No journal entries found for this item.</p>
                            </div>
                    @endif
                </div>
            </div>
        </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Stock Status Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Stock Status
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gray-900 mb-2">{{ $generalItem->opening_stock }}</div>
                            <div class="text-sm text-gray-600">Current Stock</div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Stock Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $generalItem->stock_status === 'low' ? 'bg-red-100 text-red-800' : 
                                       ($generalItem->stock_status === 'normal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($generalItem->stock_status) }}
                                </span>
                            </div>
                            
                            @if($generalItem->min_stock_limit)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Min. Limit</span>
                                <span class="text-sm font-medium text-gray-900">{{ $generalItem->min_stock_limit }}</span>
                            </div>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Item Type</span>
                                <span class="text-sm font-medium text-gray-900">{{ $generalItem->itemType->item_type }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('general-items.edit', $generalItem) }}" 
                            class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Item
                        </a>
                        
                        <a href="{{ route('general-items.edit-opening-stock', $generalItem) }}" 
                            class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            Edit Opening Stock
                        </a>
                        
                        <a href="{{ route('general-items.index') }}" 
                            class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            View All Items
                        </a>
                </div>
            </div>
        </div>

            <!-- Additional Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ additionalInfoOpen: false }">
                <div class="px-6 py-4 border-b border-gray-200">
                    <button @click="additionalInfoOpen = !additionalInfoOpen" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Additional Info
                        </h3>
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': additionalInfoOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                </div>
                
                <div x-show="additionalInfoOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
                    <div class="p-6 space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created At</span>
                            <span class="text-gray-900">{{ $generalItem->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated</span>
                            <span class="text-gray-900">{{ $generalItem->updated_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Business</span>
                            <span class="text-gray-900">{{ $generalItem->business->business_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
