<x-app-layout>
    @section('title', 'Add General Item - General Items Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'], ['url' => route('general-items.index'), 'label' => 'General Items'], ['url' => '#', 'label' => 'Add General Item']]" />

    <x-dynamic-heading title="Add General Item" />

    <form action="{{ route('general-items.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-4">
            
            <!-- Basic Information Section -->
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <x-input-label for="item_name">Item Name <span class="text-red-500">*</span></x-input-label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" 
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required />
                            @error('item_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="item_type_id">Item Type <span class="text-red-500">*</span></x-input-label>
                            <select id="item_type_id" name="item_type_id" class="mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Item Type</option>
                                @foreach($itemTypes as $itemType)
                                    <option value="{{ $itemType->id }}" {{ old('item_type_id') == $itemType->id ? 'selected' : '' }}>{{ $itemType->item_type }}</option>
                                @endforeach
                            </select>
                            @error('item_type_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="item_code">Item Code</x-input-label>
                        <input type="text" name="item_code" id="item_code" value="{{ old('item_code', '') }}" 
                               placeholder="Auto-generate" class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                        <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate</p>
                            @error('item_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="mb-4">
                <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Pricing</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="sale_price">Sale Price <span class="text-red-500">*</span></x-input-label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                                </div>
                            <input type="number" name="sale_price" id="sale_price" step="1" min="0" value="{{ old('sale_price', 0) }}" 
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12" required />
                            </div>
                            @error('sale_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="cost_price">Cost Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                            </div>
                            <input type="number" name="cost_price" id="cost_price" step="1" min="0" value="{{ old('cost_price', 0) }}" 
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12" required />
                        </div>
                        @error('cost_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Stock Information Section -->
            <div class="mb-4">
                <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Stock Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <x-input-label for="min_stock_limit">Min Stock Limit</x-input-label>
                        <input type="number" name="min_stock_limit" id="min_stock_limit" value="{{ old('min_stock_limit') }}" 
                               min="0" placeholder="10" class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                        @error('min_stock_limit')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <div>
                        <x-input-label for="carton_or_pack_size">Pack Size</x-input-label>
                        <input type="text" name="carton_or_pack_size" id="carton_or_pack_size" value="{{ old('carton_or_pack_size') }}" 
                               placeholder="12 pieces per pack" class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                        @error('carton_or_pack_size')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="opening_stock">Opening Stock</x-input-label>
                        <input type="number" name="opening_stock" id="opening_stock" value="{{ old('opening_stock', 0) }}" 
                               min="0" placeholder="100" class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                            @error('opening_stock')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="opening_total">Opening Total</x-input-label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                            </div>
                            <input type="text" id="opening_total" value="0.00" 
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12 bg-gray-50" readonly />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-calculated</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-x-4">
                <a href="{{ route('general-items.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate item code
            const itemCodeInput = document.getElementById('item_code');
            
            function generateItemCode() {
                if (!itemCodeInput.value) {
                    const randomString = Math.random().toString(36).substring(2, 10).toUpperCase();
                    const itemCode = 'ITM-' + randomString;
                    itemCodeInput.value = itemCode;
                }
            }

            // Generate item code on page load
            generateItemCode();

            // Regenerate item code when user clears the field
            itemCodeInput.addEventListener('input', function() {
                if (!this.value) {
                    generateItemCode();
                }
            });

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
