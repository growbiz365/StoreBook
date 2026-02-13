<x-app-layout>
    @section('title', 'Edit Opening Stock - ' . $generalItem->item_name . ' - General Items Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'], 
        ['url' => route('general-items.index'), 'label' => 'General Items'], 
        ['url' => route('general-items.show', $generalItem), 'label' => $generalItem->item_name],
        ['url' => '#', 'label' => 'Edit Opening Stock']
    ]" />

    <x-dynamic-heading title="Edit Opening Stock - {{ $generalItem->item_name }}" />

    <form action="{{ route('general-items.update-opening-stock', $generalItem) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-4">
            
            <!-- Item Information Display -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Item Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        <p class="text-base text-gray-900">{{ $generalItem->itemType->item_type }}</p>
                    </div>
                </div>
            </div>

            <!-- Opening Stock Information Section -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Opening Stock Information</h3>
                
               
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="opening_stock">Opening Stock <span class="text-red-500">*</span></x-input-label>
                        <input type="number" name="opening_stock" id="opening_stock" value="{{ old('opening_stock', $generalItem->opening_stock) }}" 
                               min="0" placeholder="100" class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required />
                        @error('opening_stock')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="cost_price">Cost Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                            </div>
                            <input type="number" name="cost_price" id="cost_price" step="1" min="0" value="{{ old('cost_price', $generalItem->cost_price) }}" 
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12" required />
                        </div>
                        @error('cost_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="opening_total">Opening Total</x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                            </div>
                            <input type="text" id="opening_total" value="{{ number_format($generalItem->opening_total, 2) }}" 
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12 bg-gray-50" readonly />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-calculated</p>
                    </div>
                </div>
            </div>

            <!-- Hidden fields to preserve other data -->
            <input type="hidden" name="item_name" value="{{ $generalItem->item_name }}">
            <input type="hidden" name="item_type_id" value="{{ $generalItem->item_type_id }}">
            <input type="hidden" name="item_code" value="{{ $generalItem->item_code }}">
            <input type="hidden" name="sale_price" value="{{ $generalItem->sale_price }}">
            <input type="hidden" name="min_stock_limit" value="{{ $generalItem->min_stock_limit }}">
            <input type="hidden" name="carton_or_pack_size" value="{{ $generalItem->carton_or_pack_size }}">

            <div class="mt-6 flex items-center justify-end gap-x-4">
                <a href="{{ route('general-items.show', $generalItem) }}" class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-500">Cancel</a>
                <button type="submit" class="rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white hover:bg-yellow-500">Update Opening Stock</button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Opening total calculation
            const costPriceInput = document.querySelector('input[name="cost_price"]');
            const openingStockInput = document.querySelector('input[name="opening_stock"]');
            const openingTotalInput = document.getElementById('opening_total');

            function calculateOpeningTotal() {
                const costPrice = parseFloat(costPriceInput.value) || 0;
                const openingStock = parseInt(openingStockInput.value) || 0;
                const openingTotal = costPrice * openingStock;
                openingTotalInput.value = openingTotal.toFixed(2);
            }

            costPriceInput.addEventListener('input', calculateOpeningTotal);
            openingStockInput.addEventListener('input', calculateOpeningTotal);
            calculateOpeningTotal();
        });
    </script>
</x-app-layout>
