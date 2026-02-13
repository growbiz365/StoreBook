<x-app-layout>
    @section('title', 'General Items List - General Items Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'], ['url' => '#', 'label' => 'General Items']]" />

    <x-dynamic-heading title="General Items" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex gap-4">
                <x-search-form action="{{ route('general-items.index') }}" placeholder="Search by item name or code..." />
            </div>
            @can('create items')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto flex flex-col sm:flex-row gap-3">
                
                <x-button href="{{ route('general-items.create') }}">Add General Item</x-button>
            </div>
             @endcan           
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if ($errors->has('delete_error'))
        <x-error-alert message="{{ $errors->first('delete_error') }}" />
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('general-items.index', array_merge(request()->query(), ['sort_by' => 'item_name', 'sort_order' => request('sort_by') == 'item_name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Item Name</span>
                                @if(request('sort_by') == 'item_name')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('sort_order') == 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Type</th>
                        
                    
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('general-items.index', array_merge(request()->query(), ['sort_by' => 'cost_price', 'sort_order' => request('sort_by') == 'cost_price' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Cost Price</span>
                                @if(request('sort_by') == 'cost_price')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('sort_order') == 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('general-items.index', array_merge(request()->query(), ['sort_by' => 'sale_price', 'sort_order' => request('sort_by') == 'sale_price' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Sale Price</span>
                                @if(request('sort_by') == 'sale_price')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('sort_order') == 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($generalItems as $item)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('general-items.show', $item->id) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->item_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->itemType->item_type }}</div>
                            </td>
                            
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">PKR {{ number_format($item->cost_price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">PKR {{ number_format($item->sale_price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3" onclick="event.stopPropagation()">
                                    @can('edit items')
                                    <a href="{{ route('general-items.edit', $item->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('delete items')
                                    <form action="{{ route('general-items.destroy', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this item?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $generalItems->links() }}
    </div>
</x-app-layout>
