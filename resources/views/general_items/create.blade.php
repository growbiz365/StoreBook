<x-app-layout>
    @section('title', 'Add General Item - General Items Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'], ['url' => route('general-items.index'), 'label' => 'General Items'], ['url' => '#', 'label' => 'Add General Item']]" />

    <x-dynamic-heading title="Add General Item" />

    <form action="{{ route('general-items.store') }}" method="POST" class="max-w-5xl">
        @csrf
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-3 sm:p-4 space-y-4">

            <section>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200 pb-1 mb-2">Basic information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                    <div class="space-y-0.5">
                        <x-input-label for="item_name" class="!mb-0">Item Name <span class="text-red-500">*</span></x-input-label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}"
                               class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" required />
                        @error('item_name')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="item_type_id" class="!mb-0">Item Type <span class="text-red-500">*</span></x-input-label>
                        <select id="item_type_id" name="item_type_id" class="block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5" required>
                            <option value="">Select Item Type</option>
                            @foreach($itemTypes as $itemType)
                                <option value="{{ $itemType->id }}" {{ old('item_type_id') == $itemType->id ? 'selected' : '' }}>{{ $itemType->item_type }}</option>
                            @endforeach
                        </select>
                        @error('item_type_id')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5 sm:col-span-2 lg:col-span-1">
                        <x-input-label for="item_code" class="!mb-0">Item Code</x-input-label>
                        <input type="text" name="item_code" id="item_code" value="{{ old('item_code', '') }}"
                               placeholder="Auto-generated if empty"
                               class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" />
                        <p class="text-[11px] text-gray-500 leading-tight">Clear the field to regenerate a code.</p>
                        @error('item_code')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200 pb-1 mb-2">Pricing</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    <div class="space-y-0.5">
                        <x-input-label for="sale_price" class="!mb-0">Sale Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-500 text-xs">PKR</span>
                            <input type="number" name="sale_price" id="sale_price" step="1" min="0" value="{{ old('sale_price', 0) }}"
                                   class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-11 py-1.5" required />
                        </div>
                        @error('sale_price')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="cost_price" class="!mb-0">Cost Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-500 text-xs">PKR</span>
                            <input type="number" name="cost_price" id="cost_price" step="1" min="0" value="{{ old('cost_price', 0) }}"
                                   class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-11 py-1.5" required />
                        </div>
                        @error('cost_price')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200 pb-1 mb-2">Stock</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                    <div class="space-y-0.5">
                        <x-input-label for="min_stock_limit" class="!mb-0">Min stock limit</x-input-label>
                        <input type="number" name="min_stock_limit" id="min_stock_limit" value="{{ old('min_stock_limit') }}"
                               min="0" placeholder="10" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" />
                        @error('min_stock_limit')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="carton_or_pack_size" class="!mb-0">Pack size</x-input-label>
                        <input type="text" name="carton_or_pack_size" id="carton_or_pack_size" value="{{ old('carton_or_pack_size') }}"
                               placeholder="e.g. 12 / pack" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" />
                        @error('carton_or_pack_size')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="opening_stock" class="!mb-0">Opening stock</x-input-label>
                        <input type="number" name="opening_stock" id="opening_stock" value="{{ old('opening_stock', 0) }}"
                               min="0" placeholder="0" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" />
                        @error('opening_stock')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="opening_total" class="!mb-0">Opening total</x-input-label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-500 text-xs">PKR</span>
                            <input type="text" id="opening_total" value="0.00" readonly
                                   class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-11 py-1.5 bg-gray-50 text-gray-700" />
                        </div>
                        <p class="text-[11px] text-gray-500 leading-tight">Cost × opening stock</p>
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('general-items.index') }}" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemCodeInput = document.getElementById('item_code');

            function generateItemCode() {
                if (!itemCodeInput.value) {
                    const randomString = Math.random().toString(36).substring(2, 10).toUpperCase();
                    itemCodeInput.value = 'ITM-' + randomString;
                }
            }

            generateItemCode();

            itemCodeInput.addEventListener('input', function() {
                if (!this.value) {
                    generateItemCode();
                }
            });

            const costPriceInput = document.querySelector('input[name="cost_price"]');
            const openingStockInput = document.querySelector('input[name="opening_stock"]');
            const openingTotalInput = document.getElementById('opening_total');

            function calculateOpeningTotal() {
                const costPrice = parseFloat(costPriceInput.value) || 0;
                const openingStock = parseInt(openingStockInput.value, 10) || 0;
                openingTotalInput.value = (costPrice * openingStock).toFixed(2);
            }

            costPriceInput.addEventListener('input', calculateOpeningTotal);
            openingStockInput.addEventListener('input', calculateOpeningTotal);
            calculateOpeningTotal();
        });
    </script>
</x-app-layout>
