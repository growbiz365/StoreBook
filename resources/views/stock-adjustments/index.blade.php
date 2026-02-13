<x-app-layout>
    @section('title', 'Stock Adjustments - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'],
        ['url' => '#', 'label' => 'Stock Adjustments'],
    ]" />

    <x-dynamic-heading title="Stock Adjustments" />

    <div class="space-y-4 pb-8">
        <div class="flex justify-end">
            <x-button href="{{ route('stock-adjustments.create') }}">New Adjustment</x-button>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Item name or code..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="w-40">
                <label for="adjustment_type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="adjustment_type" id="adjustment_type"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" {{ request('adjustment_type') == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="addition" {{ request('adjustment_type') == 'addition' ? 'selected' : '' }}>Addition (+)</option>
                    <option value="subtraction" {{ request('adjustment_type') == 'subtraction' ? 'selected' : '' }}>Subtraction (-)</option>
                </select>
            </div>
            <div class="w-40">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date', date('Y-m-d')) }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>Filter
                </button>
                <a href="{{ route('stock-adjustments.index') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>Clear
                </a>
            </div>
        </form>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif

    <!-- Stock Adjustments (Parent) -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        @if(isset($parents) && $parents->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lines</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($parents as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">@businessDate($row['date'])</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">Items: {{ $row['items_count'] }} | Arms: {{ $row['arms_count'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $t=$row['type']; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ in_array($t, ['addition']) ? 'bg-green-100 text-green-800' : (in_array($t, ['subtraction','damage','theft']) ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($t) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@currency($row['amount'] ?? 0)</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                            <a href="{{ route('stock-adjustments.show', $row['id']) }}" class="text-indigo-600 hover:text-indigo-900" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('stock-adjustments.edit', $row['id']) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('stock-adjustments.destroy', $row['id']) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this adjustment?')" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $parents->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No adjustments found</h3>
                <p class="text-gray-500 mb-6">Get started by creating your first adjustment.</p>
                <a href="{{ route('stock-adjustments.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Adjustment
                </a>
            </div>
        @endif
    </div>
</x-app-layout>