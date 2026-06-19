<x-app-layout>
    @section('title', 'Add General Item - General Items Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'], ['url' => route('general-items.index'), 'label' => 'General Items'], ['url' => '#', 'label' => 'Add General Item']]" />

    <x-dynamic-heading title="Add General Item" />

    <form action="{{ route('general-items.store') }}" method="POST" class="max-w-5xl">
        @csrf
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-3 sm:p-4 space-y-4">

            <section>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200 pb-1 mb-2">Item kind</h3>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="radio" name="item_kind" value="goods" class="item-kind-radio text-indigo-600 focus:ring-indigo-500"
                               {{ old('item_kind', 'goods') === 'goods' ? 'checked' : '' }}>
                        <span>Goods</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="radio" name="item_kind" value="service" class="item-kind-radio text-indigo-600 focus:ring-indigo-500"
                               {{ old('item_kind') === 'service' ? 'checked' : '' }}>
                        <span>Service</span>
                    </label>
                </div>
                @error('item_kind')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </section>

            <section class="service-only-field">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200 pb-1 mb-2">Service details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                    <div class="space-y-0.5">
                        <x-input-label for="item_name" class="!mb-0">Service Name <span class="text-red-500">*</span></x-input-label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}"
                               class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" required />
                        @error('item_name')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="item_code" class="!mb-0">Item Code</x-input-label>
                        <input type="text" name="item_code" id="item_code" value="{{ old('item_code', '') }}"
                               placeholder="Auto-generated if empty"
                               class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" />
                        <p class="text-[11px] text-gray-500 leading-tight">Clear the field to regenerate a code.</p>
                        @error('item_code')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="sale_price" class="!mb-0">Sale Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-500 text-xs">{{ $businessCurrencyLabel }}</span>
                            <input type="number" name="sale_price" id="sale_price" step="any" min="0" value="{{ old('sale_price', 0) }}"
                                   class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-11 py-1.5" required />
                        </div>
                        @error('sale_price')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="goods-only-field">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200 pb-1 mb-2">Basic information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                    <div class="space-y-0.5">
                        <x-input-label for="item_name_goods" class="!mb-0">Item Name <span class="text-red-500">*</span></x-input-label>
                        <input type="text" id="item_name_goods" value="{{ old('item_name') }}"
                               class="shared-item-name block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" />
                        @error('item_name')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="item_type_id" class="!mb-0">Item Type <span class="text-red-500">*</span></x-input-label>
                        <select id="item_type_id" name="item_type_id" class="block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
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
                        <x-input-label for="item_code_goods" class="!mb-0">Item Code</x-input-label>
                        <input type="text" id="item_code_goods" value="{{ old('item_code', '') }}"
                               placeholder="Auto-generated if empty"
                               class="shared-item-code block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1.5" />
                        <p class="text-[11px] text-gray-500 leading-tight">Clear the field to regenerate a code.</p>
                        @error('item_code')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="goods-only-field">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200 pb-1 mb-2">Pricing</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    <div class="space-y-0.5">
                        <x-input-label for="sale_price_goods" class="!mb-0">Sale Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-500 text-xs">{{ $businessCurrencyLabel }}</span>
                            <input type="number" id="sale_price_goods" step="any" min="0" value="{{ old('sale_price', 0) }}"
                                   class="shared-sale-price block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-11 py-1.5" />
                        </div>
                        @error('sale_price')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-0.5">
                        <x-input-label for="cost_price" class="!mb-0">Cost Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-500 text-xs">{{ $businessCurrencyLabel }}</span>
                            <input type="number" name="cost_price" id="cost_price" step="any" min="0" value="{{ old('cost_price', 0) }}"
                                   class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-11 py-1.5" />
                        </div>
                        @error('cost_price')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section id="goods-stock-section" class="goods-only-field">
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
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-500 text-xs">{{ $businessCurrencyLabel }}</span>
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
            const kindRadios = document.querySelectorAll('.item-kind-radio');
            const goodsFields = document.querySelectorAll('.goods-only-field');
            const serviceFields = document.querySelectorAll('.service-only-field');
            const itemTypeSelect = document.getElementById('item_type_id');
            const costPriceInput = document.getElementById('cost_price');
            const openingStockInput = document.getElementById('opening_stock');
            const openingTotalInput = document.getElementById('opening_total');

            const serviceNameInput = document.getElementById('item_name');
            const goodsNameInput = document.getElementById('item_name_goods');
            const serviceCodeInput = document.getElementById('item_code');
            const goodsCodeInput = document.getElementById('item_code_goods');
            const serviceSaleInput = document.getElementById('sale_price');
            const goodsSaleInput = document.getElementById('sale_price_goods');

            function selectedKind() {
                const checked = document.querySelector('.item-kind-radio:checked');
                return checked ? checked.value : 'goods';
            }

            function syncSharedFields(fromService) {
                if (fromService) {
                    if (goodsNameInput) goodsNameInput.value = serviceNameInput.value;
                    if (goodsCodeInput) goodsCodeInput.value = serviceCodeInput.value;
                    if (goodsSaleInput) goodsSaleInput.value = serviceSaleInput.value;
                } else {
                    if (serviceNameInput) serviceNameInput.value = goodsNameInput.value;
                    if (serviceCodeInput) serviceCodeInput.value = goodsCodeInput.value;
                    if (serviceSaleInput) serviceSaleInput.value = goodsSaleInput.value;
                }
            }

            function setFieldNames(isService) {
                const pairs = [
                    [serviceNameInput, goodsNameInput, 'item_name'],
                    [serviceCodeInput, goodsCodeInput, 'item_code'],
                    [serviceSaleInput, goodsSaleInput, 'sale_price'],
                ];
                pairs.forEach(([serviceEl, goodsEl, fieldName]) => {
                    if (serviceEl) {
                        if (isService) serviceEl.setAttribute('name', fieldName);
                        else serviceEl.removeAttribute('name');
                    }
                    if (goodsEl) {
                        if (isService) goodsEl.removeAttribute('name');
                        else goodsEl.setAttribute('name', fieldName);
                    }
                });
            }

            function toggleKindFields() {
                const isService = selectedKind() === 'service';

                goodsFields.forEach((el) => {
                    el.style.display = isService ? 'none' : '';
                });
                serviceFields.forEach((el) => {
                    el.style.display = isService ? '' : 'none';
                });

                if (isService) {
                    syncSharedFields(true);
                    serviceNameInput.setAttribute('required', 'required');
                    serviceSaleInput.setAttribute('required', 'required');
                    goodsNameInput.removeAttribute('required');
                    goodsSaleInput.removeAttribute('required');
                    if (itemTypeSelect) itemTypeSelect.removeAttribute('required');
                    if (costPriceInput) costPriceInput.removeAttribute('required');
                } else {
                    syncSharedFields(false);
                    serviceNameInput.removeAttribute('required');
                    serviceSaleInput.removeAttribute('required');
                    goodsNameInput.setAttribute('required', 'required');
                    goodsSaleInput.setAttribute('required', 'required');
                    if (itemTypeSelect) itemTypeSelect.setAttribute('required', 'required');
                    if (costPriceInput) costPriceInput.setAttribute('required', 'required');
                }

                setFieldNames(isService);
            }

            kindRadios.forEach((radio) => radio.addEventListener('change', toggleKindFields));
            toggleKindFields();

            function activeCodeInput() {
                return selectedKind() === 'service' ? serviceCodeInput : goodsCodeInput;
            }

            function generateItemCode() {
                const input = activeCodeInput();
                if (input && !input.value) {
                    input.value = 'ITM-' + Math.random().toString(36).substring(2, 10).toUpperCase();
                    syncSharedFields(selectedKind() === 'service');
                }
            }

            generateItemCode();

            [serviceCodeInput, goodsCodeInput].forEach((input) => {
                if (!input) return;
                input.addEventListener('input', function() {
                    if (!this.value) {
                        generateItemCode();
                    } else if (selectedKind() === 'service') {
                        if (goodsCodeInput) goodsCodeInput.value = this.value;
                    } else {
                        if (serviceCodeInput) serviceCodeInput.value = this.value;
                    }
                });
            });

            function calculateOpeningTotal() {
                if (!costPriceInput || !openingStockInput || !openingTotalInput) return;
                const costPrice = parseFloat(costPriceInput.value) || 0;
                const openingStock = parseInt(openingStockInput.value, 10) || 0;
                openingTotalInput.value = (costPrice * openingStock).toFixed(2);
            }

            if (costPriceInput) costPriceInput.addEventListener('input', calculateOpeningTotal);
            if (openingStockInput) openingStockInput.addEventListener('input', calculateOpeningTotal);
            calculateOpeningTotal();
        });
    </script>
</x-app-layout>
