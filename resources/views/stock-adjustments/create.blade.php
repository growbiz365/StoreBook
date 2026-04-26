<x-app-layout>
    @section('title', 'Create Stock Adjustment - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'],
        ['url' => route('stock-adjustments.index'), 'label' => 'Stock Adjustments'],
        ['url' => '#', 'label' => 'Create Adjustment'],
    ]" />

    <x-dynamic-heading title="Create Stock Adjustment" />

    {{-- Error Display --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Whoops! Something went wrong.</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (Session::has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Error!</strong>
            <p>{{ Session::get('error') }}</p>
        </div>
    @endif

    <form action="{{ route('stock-adjustments.store') }}" method="POST" id="adjustmentForm">
        @csrf
        
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-4">
            
            <!-- Adjustment Details Section -->
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Adjustment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <x-input-label for="adjustment_type">Adjustment Type <span class="text-red-500">*</span></x-input-label>
                        <select name="adjustment_type" id="adjustment_type" required
                                class="mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('adjustment_type') border-red-500 @enderror">
                            <option value="">Select Type</option>
                            <option value="addition" {{ old('adjustment_type') == 'addition' ? 'selected' : '' }}>Addition (+)</option>
                            <option value="subtraction" {{ old('adjustment_type') == 'subtraction' ? 'selected' : '' }}>Subtraction (-)</option>
                        </select>
                        @error('adjustment_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="adjustment_date">Adjustment Date <span class="text-red-500">*</span></x-input-label>
                        <input type="date" name="adjustment_date" id="adjustment_date" 
                               value="{{ old('adjustment_date', date('Y-m-d')) }}" required
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full @error('adjustment_date') border-red-500 @enderror" />
                        @error('adjustment_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="description">Description</x-input-label>
                        <input type="text" name="description" id="description" 
                               value="{{ old('description') }}" 
                               placeholder="Reason for adjustment..."
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full @error('description') border-red-500 @enderror" />
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="mb-4">
                <div class="flex flex-wrap justify-between items-end gap-3 mb-3">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-200 pb-1 flex-1 min-w-[200px]">Items to Adjust</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label for="item_type_filter" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter by Type:</label>
                            <select id="item_type_filter"
                                    class="block w-full md:w-56 rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 adjustment-item-type-chosen">
                                <option value="">All Item Types</option>
                                @foreach($itemTypes as $itemType)
                                    <option value="{{ $itemType->id }}">{{ $itemType->item_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="addItemBtn"
                                class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Item
                        </button>
                    </div>
                </div>

                <div id="itemsContainer">
                    <!-- Items will be added here dynamically -->
                </div>

                @error('items')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- ARM Adjustments (Damage/Theft) - Hidden: StoreBook is items-only -->
            <div class="mb-4" id="armSection" style="display: none !important;">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-200 pb-1">ARM Adjustments</h3>
                    <button type="button" id="addArmBtn"
                            class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Arm
                    </button>
                </div>

                <p class="text-xs text-gray-600 mb-2">Select available arms to subtract due to damage or theft. This will decommission the selected arms.</p>

                <div id="armsContainer"></div>

                @error('arm_items')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4 flex items-center justify-end gap-x-4">
                <a href="{{ route('stock-adjustments.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Create Adjustment
                </button>
            </div>
        </div>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <style>
    /* Make Chosen match Tailwind input styles */
    .chosen-container { width: 100% !important; }
    .chosen-container-single .chosen-single {
        height: 42px;
        line-height: 40px;
        border: 1px solid #d1d5db; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        padding: 0 2.25rem 0 0.75rem;
        background: #fff;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); /* shadow-sm */
        font-size: 0.875rem; /* text-sm */
        color: #111827;
    }
    .chosen-container-single .chosen-single span { margin-right: 0.5rem; }
    .chosen-container-single .chosen-single div { right: 0.5rem; }
    .chosen-container-active .chosen-single,
    .chosen-container .chosen-single:focus {
        border-color: #6366f1; /* indigo-500 */
        box-shadow: 0 0 0 1px #6366f1 inset, 0 0 0 1px rgba(99,102,241,0.2);
    }
    .chosen-container .chosen-search input {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0 0.75rem;
    }
    .chosen-container .chosen-results {
        font-size: 0.875rem;
    }
    .chosen-container .chosen-results li {
        padding: 8px 12px;
    }
    /* Error state for Chosen dropdowns */
    .chosen-container .chosen-single.border-red-500 {
        border-color: #ef4444 !important; /* red-500 */
        box-shadow: 0 0 0 1px #ef4444 inset, 0 0 0 1px rgba(239,68,68,0.2) !important;
    }
    .chosen-error-container {
        margin-top: 0;
    }
    /* Item search dropdown (API) */
    .stock-adjustment-item-row { position: relative; overflow: visible; }
    .searchable-dropdown { max-height: 15rem; }
    .searchable-input.border-red-500 { border-color: #ef4444 !important; }
    </style>
    <script>
        class StockAdjustmentItemSearchableDropdown {
            constructor(container, options = {}) {
                this.container = container;
                this.input = container.querySelector('.searchable-input');
                this.hiddenInput = container.querySelector('.selected-item-id');
                this.dropdown = container.querySelector('.searchable-dropdown');
                this.resultsContainer = container.querySelector('.search-results-container');
                this.paginationContainer = container.querySelector('.pagination-container');
                this.loadingIndicator = container.querySelector('.loading-indicator');
                this.searchTimeout = null;
                this.currentPage = 1;
                this.searchTerm = '';
                this.selectedItem = null;
                this.itemsPerPage = options.itemsPerPage || 10;
                this.debounceDelay = options.debounceDelay || 300;
                this.minSearchLength = options.minSearchLength || 2;
                this.init();
            }
            closestItemRow() {
                return this.container.closest('.stock-adjustment-item-row, .existing-item-row');
            }
            getSelectedItemTypeId() {
                const filter = document.getElementById('item_type_filter');
                return filter ? (filter.value || '') : '';
            }
            getExcludeIds(exceptHidden) {
                const ids = [];
                document.querySelectorAll('.stock-adjustment-item-row .selected-item-id, .existing-item-row .selected-item-id').forEach((h) => {
                    if (h === exceptHidden || !h.value) return;
                    const row = h.closest('.stock-adjustment-item-row, .existing-item-row');
                    if (row && row.style.display === 'none') return;
                    ids.push(String(h.value));
                });
                return ids;
            }
            init() {
                this.bindEvents();
                this.setupGlobalClickHandler();
            }
            bindEvents() {
                this.input.addEventListener('focus', () => {
                    this.showDropdown();
                    this.performSearch();
                });
                this.input.addEventListener('input', (e) => {
                    this.searchTerm = e.target.value;
                    this.currentPage = 1;
                    this.showDropdown();
                    if (!this.searchTerm.trim() && this.selectedItem) {
                        this.clearSelection();
                    }
                    this.debounceSearch();
                });
                this.input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.selectHighlightedResult();
                    } else if (e.key === 'Escape') {
                        this.hideDropdown();
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        this.navigateResults('down');
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        this.navigateResults('up');
                    }
                });
            }
            setupGlobalClickHandler() {
                document.addEventListener('click', (e) => {
                    if (!this.container.contains(e.target)) {
                        this.hideDropdown();
                    }
                });
            }
            debounceSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => this.performSearch(), this.debounceDelay);
            }
            async performSearch() {
                if (this.searchTerm.length < this.minSearchLength) {
                    this.showInitialResults();
                    return;
                }
                this.showLoading();
                try {
                    const url = new URL('/api/general-items/search', window.location.origin);
                    url.searchParams.set('q', this.searchTerm);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    const itemTypeId = this.getSelectedItemTypeId();
                    if (itemTypeId) url.searchParams.set('item_type_id', itemTypeId);
                    const exclude = this.getExcludeIds(this.hiddenInput);
                    if (exclude.length) url.searchParams.set('exclude_ids', exclude.join(','));
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    const data = await response.json();
                    if (data.error) throw new Error(data.message || data.error);
                    if (!Array.isArray(data.data)) throw new Error('Invalid search response');
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Search error:', error);
                    this.showError('Search failed: ' + error.message);
                } finally {
                    this.hideLoading();
                }
            }
            async showInitialResults() {
                this.showLoading();
                try {
                    const url = new URL('/api/general-items', window.location.origin);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    const itemTypeId = this.getSelectedItemTypeId();
                    if (itemTypeId) url.searchParams.set('item_type_id', itemTypeId);
                    const exclude = this.getExcludeIds(this.hiddenInput);
                    if (exclude.length) url.searchParams.set('exclude_ids', exclude.join(','));
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    const data = await response.json();
                    if (data.error) throw new Error(data.message || data.error);
                    if (!Array.isArray(data.data)) throw new Error('Invalid API response');
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial load error:', error);
                    this.showError('Failed to load items: ' + error.message);
                } finally {
                    this.hideLoading();
                }
            }
            displayResults(items, meta) {
                this.resultsContainer.innerHTML = '';
                if (!items || !Array.isArray(items) || items.length === 0) {
                    this.resultsContainer.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">' +
                        (this.searchTerm ? 'No items found matching your search.' : 'No items available.') + '</div>';
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                items.forEach((item) => {
                    if (!item || !item.id || !item.item_name) return;
                    const resultItem = document.createElement('div');
                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                    resultItem.dataset.itemId = item.id;
                    resultItem.dataset.itemName = item.item_name;
                    resultItem.dataset.costPrice = this.safeNumber(item.cost_price);
                    resultItem.dataset.availableStock = this.safeNumber(item.available_stock);
                    resultItem.dataset.itemCode = item.item_code || '';
                    resultItem.innerHTML = '<div class="font-medium text-gray-900">' + item.item_name + '</div>' +
                        (item.item_code ? '<div class="text-xs text-gray-400">' + item.item_code + '</div>' : '');
                    resultItem.addEventListener('click', () => this.selectItem(item));
                    this.resultsContainer.appendChild(resultItem);
                });
                if (meta && meta.last_page > 1) {
                    this.showPagination(meta);
                } else {
                    this.paginationContainer.classList.add('hidden');
                }
            }
            showPagination(meta) {
                this.paginationContainer.classList.remove('hidden');
                const pageInfo = this.paginationContainer.querySelector('.page-info');
                const prevBtn = this.paginationContainer.querySelector('.prev-page');
                const nextBtn = this.paginationContainer.querySelector('.next-page');
                pageInfo.textContent = 'Page ' + meta.current_page + ' of ' + meta.last_page;
                prevBtn.disabled = meta.current_page <= 1;
                nextBtn.disabled = meta.current_page >= meta.last_page;
                prevBtn.onclick = () => {
                    if (meta.current_page > 1) {
                        this.currentPage = meta.current_page - 1;
                        this.performSearch();
                    }
                };
                nextBtn.onclick = () => {
                    if (meta.current_page < meta.last_page) {
                        this.currentPage = meta.current_page + 1;
                        this.performSearch();
                    }
                };
            }
            recalcRowTotals(row) {
                if (!row) return;
                const quantityInput = row.querySelector('input.existing-qty, input[name*="[quantity]"]');
                const unitCostInput = row.querySelector('input.existing-unit-cost, input[name*="[unit_cost]"]');
                const totalAmountInput = row.querySelector('input.existing-total, input.adjustment-line-total');
                if (!quantityInput || !unitCostInput || !totalAmountInput) return;
                const q = parseFloat(quantityInput.value) || 0;
                const u = parseFloat(unitCostInput.value) || 0;
                totalAmountInput.value = Math.round(q * u);
            }
            selectItem(item) {
                this.selectedItem = item;
                this.input.value = item.item_code ? (item.item_name + ' (' + item.item_code + ')') : item.item_name;
                this.hiddenInput.value = item.id;
                this.input.classList.remove('border-red-500');
                const errEl = this.container.querySelector('.item-search-error-container');
                if (errEl) errEl.innerHTML = '';
                const row = this.closestItemRow();
                const availableStock = item.available_stock || 0;
                this.container.querySelectorAll('.stock-adj-item-info').forEach((el) => el.remove());
                const infoDiv = document.createElement('div');
                infoDiv.className = 'stock-adj-item-info mt-1 text-xs text-gray-500';
                infoDiv.innerHTML = 'Available stock: <span class="font-medium">' + availableStock + '</span>';
                this.container.appendChild(infoDiv);
                const unitCostInput = row ? row.querySelector('input.existing-unit-cost, input[name*="[unit_cost]"]') : null;
                if (unitCostInput) {
                    const cp = item.cost_price != null ? parseFloat(item.cost_price) : NaN;
                    unitCostInput.value = !isNaN(cp) ? Math.round(cp) : '';
                    this.recalcRowTotals(row);
                }
                this.hideDropdown();
            }
            selectFirstResult() {
                const firstResult = this.resultsContainer.querySelector('.result-item');
                if (firstResult) {
                    this.selectItem({
                        id: firstResult.dataset.itemId,
                        item_name: firstResult.dataset.itemName,
                        item_code: firstResult.dataset.itemCode || '',
                        cost_price: this.safeNumber(firstResult.dataset.costPrice),
                        available_stock: this.safeNumber(firstResult.dataset.availableStock)
                    });
                }
            }
            selectHighlightedResult() {
                const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
                if (highlightedResult) {
                    this.selectItem({
                        id: highlightedResult.dataset.itemId,
                        item_name: highlightedResult.dataset.itemName,
                        item_code: highlightedResult.dataset.itemCode || '',
                        cost_price: this.safeNumber(highlightedResult.dataset.costPrice),
                        available_stock: this.safeNumber(highlightedResult.dataset.availableStock)
                    });
                } else {
                    this.selectFirstResult();
                }
            }
            navigateResults(direction) {
                const results = this.resultsContainer.querySelectorAll('.result-item');
                const currentIndex = Array.from(results).findIndex((r) => r.classList.contains('selected'));
                let newIndex;
                if (direction === 'down') {
                    newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                } else {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                }
                results.forEach((r) => r.classList.remove('selected', 'bg-indigo-50'));
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-indigo-50');
                    results[newIndex].scrollIntoView({ block: 'nearest' });
                }
            }
            showDropdown() { this.dropdown.classList.remove('hidden'); }
            hideDropdown() { this.dropdown.classList.add('hidden'); }
            showLoading() { this.loadingIndicator.classList.remove('hidden'); }
            hideLoading() { this.loadingIndicator.classList.add('hidden'); }
            safeNumber(value) {
                if (value === null || value === undefined || value === '') return 0;
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }
            showError(message) {
                this.resultsContainer.innerHTML = '<div class="px-4 py-3 text-sm text-red-500 text-center">' + message + '</div>';
            }
            clearSelection() {
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
                const row = this.closestItemRow();
                const unitCostInput = row ? row.querySelector('input.existing-unit-cost, input[name*="[unit_cost]"]') : null;
                const totalAmountInput = row ? row.querySelector('input.existing-total, input.adjustment-line-total') : null;
                if (unitCostInput) unitCostInput.value = '';
                if (totalAmountInput) totalAmountInput.value = '0';
                this.container.querySelectorAll('.stock-adj-item-info').forEach((el) => el.remove());
            }
        }

        function hydrateAdjustmentItemLabel(hiddenInput, textInput) {
            const id = hiddenInput && hiddenInput.value;
            if (!id) return;
            fetch('/api/general-items/' + id, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then((r) => r.json())
                .then((data) => {
                    if (data.item_name) {
                        textInput.value = data.item_code ? (data.item_name + ' (' + data.item_code + ')') : data.item_name;
                    }
                })
                .catch(function() {});
        }

        let itemCount = 0;
        let armCount = 0;

        document.getElementById('addItemBtn').addEventListener('click', function() {
            addItemRow();
        });

        function addItemRow(forcedIndex) {
            let idx;
            if (forcedIndex != null && forcedIndex !== '') {
                idx = parseInt(forcedIndex, 10);
                itemCount = Math.max(itemCount, idx);
            } else {
                idx = ++itemCount;
            }
            const container = document.getElementById('itemsContainer');
            const itemRow = document.createElement('div');
            itemRow.className = 'stock-adjustment-item-row border border-gray-200 rounded-lg p-4 mb-3';
            itemRow.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Item ${idx}</h4>
                    <button type="button" class="remove-item inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remove
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item <span class="text-red-500">*</span></label>
                        <div class="searchable-select-container relative z-20">
                            <input type="text" class="searchable-input mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Search by name or code…" autocomplete="off">
                            <input type="hidden" name="items[${idx}][general_item_id]" class="selected-item-id" value="">
                            <div class="item-search-error-container chosen-error-container"></div>
                            <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <div class="search-results-container"></div>
                                <div class="pagination-container hidden border-t border-gray-100 px-3 py-2 flex items-center justify-between text-xs bg-gray-50">
                                    <button type="button" class="prev-page px-2 py-1 rounded bg-white border border-gray-200">Prev</button>
                                    <span class="page-info text-gray-600"></span>
                                    <button type="button" class="next-page px-2 py-1 rounded bg-white border border-gray-200">Next</button>
                                </div>
                            </div>
                            <div class="loading-indicator hidden absolute right-2 top-8 text-xs text-gray-500">Loading…</div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="items[${idx}][quantity]" step="1" min="1" required
                               value="1" placeholder="1"
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        <div class="error-container"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">{{ $businessCurrencyLabel }}</span>
                            </div>
                            <input type="number" name="items[${idx}][unit_cost]" step="1" min="0" required
                                   placeholder="0"
                                   class="adjustment-unit-cost mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12">
                        </div>
                        <div class="error-container"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">{{ $businessCurrencyLabel }}</span>
                            </div>
                            <input type="text" readonly
                                   class="adjustment-line-total mt-1 text-sm border-gray-300 rounded-md shadow-sm w-full pl-12 bg-gray-50 text-gray-600">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-calculated</p>
                    </div>
                </div>
            `;

            container.appendChild(itemRow);

            const searchWrap = itemRow.querySelector('.searchable-select-container');
            new StockAdjustmentItemSearchableDropdown(searchWrap);

            const quantityInput = itemRow.querySelector('input[name*="[quantity]"]');
            const unitCostInput = itemRow.querySelector('input[name*="[unit_cost]"]');
            const totalAmountInput = itemRow.querySelector('input.adjustment-line-total');
            const removeBtn = itemRow.querySelector('.remove-item');

            function calculateTotal() {
                const quantity = parseFloat(quantityInput.value) || 1;
                const unitCost = parseFloat(unitCostInput.value) || 0;
                totalAmountInput.value = Math.round(quantity * unitCost);
            }

            calculateTotal();

            quantityInput.addEventListener('input', function() {
                calculateTotal();
                this.classList.remove('border-red-500');
                const errorContainer = this.nextElementSibling;
                if (errorContainer && errorContainer.classList.contains('error-container')) {
                    errorContainer.innerHTML = '';
                }
            });

            unitCostInput.addEventListener('input', function() {
                calculateTotal();
                this.classList.remove('border-red-500');
                const errorContainer = this.parentElement.nextElementSibling;
                if (errorContainer && errorContainer.classList.contains('error-container')) {
                    errorContainer.innerHTML = '';
                }
            });

            removeBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    itemRow.remove();
                }
            });
        }

        // ARM rows handling
        const addArmBtn = document.getElementById('addArmBtn');
        if (addArmBtn) {
            addArmBtn.addEventListener('click', function() {
                addArmRow();
            });
        }

        function addArmRow() {
            armCount++;
            const container = document.getElementById('armsContainer');
            const row = document.createElement('div');
            row.className = 'arm-row border border-gray-200 rounded-lg p-4 mb-3';
            row.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Arm ${armCount}</h4>
                    <button type="button" class="remove-arm inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remove
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Arm <span class="text-red-500">*</span></label>
                        <select name="arm_items[${armCount}][arm_id]" required
                                class="arm-select chosen-select mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Arm</option>
                            @foreach($arms as $arm)
                                <option value="{{ $arm->id }}" data-price="{{ $arm->purchase_price }}">{{ $arm->arm_title }}</option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                        <select name="arm_items[${armCount}][reason]" required
                                class="mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Reason</option>
                            <option value="damage">Damaged</option>
                            <option value="theft">Stolen</option>
                        </select>
                        <div class="error-container"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">{{ $businessCurrencyLabel }}</span>
                            </div>
                            <input type="number" name="arm_items[${armCount}][price]" step="1" min="0" placeholder="0"
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12">
                        </div>
                        <div class="error-container"></div>
                    </div>
                    
                </div>
            `;
            container.appendChild(row);
            
            // Initialize chosen for the new select
            $(row).find('.chosen-select').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });
            
            // Add validation styling handler
            const armSelectEl = row.querySelector('.arm-select');
            $(armSelectEl).on('change', function() {
                // Remove error styling when user selects an option
                const chosenContainer = $(this).next('.chosen-container');
                chosenContainer.find('.chosen-single').removeClass('border-red-500');
            });
            
            // apply duplicate prevention immediately for arms
            refreshArmOptions();

            const removeBtn = row.querySelector('.remove-arm');
            removeBtn.addEventListener('click', function() {
                if (confirm('Remove this ARM entry?')) {
                    $(row).find('.chosen-select').chosen('destroy');
                    row.remove();
                    refreshArmOptions();
                }
            });

            // Auto-fill price when arm is selected
            const armSelect = row.querySelector('select[name^="arm_items"][name$="[arm_id]"]');
            const reasonSelect = row.querySelector('select[name^="arm_items"][name$="[reason]"]');
            const priceInput = row.querySelector('input[name^="arm_items"][name$="[price]"]');
            $(armSelect).on('change', function() {
                const opt = this.options[this.selectedIndex];
                const price = opt && opt.getAttribute('data-price');
                priceInput.value = price ? Math.round(parseFloat(price)) : '';
                refreshArmOptions();
            });
            
            // Remove error styling on reason change
            if (reasonSelect) {
                reasonSelect.addEventListener('change', function() {
                    this.classList.remove('border-red-500');
                    const errorContainer = this.nextElementSibling;
                    if (errorContainer && errorContainer.classList.contains('error-container')) {
                        errorContainer.innerHTML = '';
                    }
                });
            }
        }

        // Arms section always hidden - StoreBook is items-only
        const adjustmentTypeSelect = document.getElementById('adjustment_type');
        function toggleArmSection() {
            // Always keep arms section hidden regardless of adjustment type
            const armSection = document.getElementById('armSection');
            if (armSection) {
                armSection.style.setProperty('display', 'none', 'important');
            }
        }
        // Keep arms section hidden on change and initial load
        if (adjustmentTypeSelect) {
            adjustmentTypeSelect.addEventListener('change', toggleArmSection);
        }
        // Ensure it's hidden on page load
        toggleArmSection();

        // Add first item row on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery && jQuery.fn.chosen) {
                jQuery('#item_type_filter').chosen({
                    width: '100%',
                    search_contains: true,
                    allow_single_deselect: true,
                    placeholder_text_single: 'All types'
                });
            }

            const hasOldItems = @json(old('items', []));
            const hasOldArmItems = @json(old('arm_items', []));

            if (hasOldItems && Object.keys(hasOldItems).length > 0) {
                Object.keys(hasOldItems)
                    .sort(function(a, b) { return parseInt(a, 10) - parseInt(b, 10); })
                    .forEach(function(key) {
                        addItemRow(key);
                        const lastRow = document.querySelector('.stock-adjustment-item-row:last-child');
                        const item = hasOldItems[key];
                        const hidden = lastRow.querySelector('.selected-item-id');
                        const textInput = lastRow.querySelector('.searchable-input');
                        const qty = lastRow.querySelector('input[name*="[quantity]"]');
                        const cost = lastRow.querySelector('input[name*="[unit_cost]"]');
                        const totalEl = lastRow.querySelector('input.adjustment-line-total');
                        if (hidden && item.general_item_id) {
                            hidden.value = item.general_item_id;
                            hydrateAdjustmentItemLabel(hidden, textInput);
                        }
                        if (qty && item.quantity != null) qty.value = item.quantity;
                        if (cost && item.unit_cost != null) cost.value = item.unit_cost;
                        if (totalEl && qty && cost) {
                            totalEl.value = Math.round((parseFloat(qty.value) || 0) * (parseFloat(cost.value) || 0));
                        }
                    });
            } else {
                addItemRow();
            }

            if (hasOldArmItems && Object.keys(hasOldArmItems).length > 0) {
                Object.keys(hasOldArmItems).forEach(function(key) {
                    addArmRow();
                    const lastRow = document.querySelector('.arm-row:last-child');
                    const arm = hasOldArmItems[key];
                    const armSelect = lastRow.querySelector('.arm-select');
                    const reasonSelect = lastRow.querySelector('select[name*="[reason]"]');
                    const priceInput = lastRow.querySelector('input[name*="[price]"]');
                    if (armSelect && arm.arm_id) {
                        armSelect.value = arm.arm_id;
                        jQuery(armSelect).trigger('chosen:updated');
                        jQuery(armSelect).trigger('change');
                    }
                    if (reasonSelect && arm.reason) reasonSelect.value = arm.reason;
                    if (priceInput && arm.price) priceInput.value = arm.price;
                });
            }

            toggleArmSection();
            refreshArmOptions();
            displayServerErrors();
        });
        
        // Function to display server-side Laravel validation errors
        function displayServerErrors() {
            const errors = @json($errors->messages());
            
            // Display errors for items
            Object.keys(errors).forEach(function(fieldName) {
                const messages = errors[fieldName];
                
                // Check if it's an item field error
                const itemMatch = fieldName.match(/items\.(\d+)\.(general_item_id|quantity|unit_cost)/);
                if (itemMatch) {
                    const errKey = parseInt(itemMatch[1], 10);
                    const field = itemMatch[2];
                    let row = null;
                    document.querySelectorAll('.stock-adjustment-item-row').forEach(function(r) {
                        const hid = r.querySelector('.selected-item-id');
                        if (hid && hid.name) {
                            const m = hid.name.match(/items\[(\d+)\]/);
                            if (m && parseInt(m[1], 10) === errKey) {
                                row = r;
                            }
                        }
                    });
                    if (row) {
                        if (field === 'general_item_id') {
                            const inp = row.querySelector('.searchable-input');
                            const errorContainer = row.querySelector('.item-search-error-container');
                            if (inp) inp.classList.add('border-red-500');
                            if (errorContainer) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        } else if (field === 'quantity') {
                            const input = row.querySelector('input[name*="[quantity]"]');
                            input.classList.add('border-red-500');
                            const errorContainer = input.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('error-container')) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        } else if (field === 'unit_cost') {
                            const input = row.querySelector('input[name*="[unit_cost]"]');
                            input.classList.add('border-red-500');
                            const errorContainer = input.parentElement.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('error-container')) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        }
                    }
                }
                
                // Check if it's an arm field error
                const armMatch = fieldName.match(/arm_items\.(\d+)\.(arm_id|reason|price)/);
                if (armMatch) {
                    const index = parseInt(armMatch[1]);
                    const field = armMatch[2];
                    const row = Array.from(document.querySelectorAll('.arm-row'))[index];
                    
                    if (row) {
                        if (field === 'arm_id') {
                            const select = row.querySelector('.arm-select');
                            const chosenContainer = $(select).next('.chosen-container');
                            chosenContainer.find('.chosen-single').addClass('border-red-500');
                            const errorContainer = select.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('chosen-error-container')) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        } else if (field === 'reason') {
                            const input = row.querySelector('select[name*="[reason]"]');
                            input.classList.add('border-red-500');
                            const errorContainer = input.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('error-container')) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        }
                    }
                }
            });
        }

        // Form validation
        document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
            const itemRows = document.querySelectorAll('.stock-adjustment-item-row');
            const armRows = document.querySelectorAll('.arm-row');
            if (itemRows.length === 0 && armRows.length === 0) {
                e.preventDefault();
                alert('Please add at least one general item or one arm to adjust.');
                return;
            }
            
            // Validate all item rows
            let hasErrors = false;
            itemRows.forEach(function(row) {
                const hiddenItem = row.querySelector('.selected-item-id');
                const searchInput = row.querySelector('.searchable-input');
                const qty = row.querySelector('input[name*="[quantity]"]');
                const cost = row.querySelector('input[name*="[unit_cost]"]');
                
                // Clear previous errors
                row.querySelectorAll('.error-container, .chosen-error-container, .item-search-error-container').forEach(container => {
                    container.innerHTML = '';
                });
                if (searchInput) searchInput.classList.remove('border-red-500');
                
                // Validate item
                if (!hiddenItem || !hiddenItem.value) {
                    hasErrors = true;
                    if (searchInput) searchInput.classList.add('border-red-500');
                    const errorContainer = row.querySelector('.item-search-error-container');
                    if (errorContainer) {
                        errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">Please select an item.</p>';
                    }
                }
                
                // Validate quantity
                if (!qty.value || parseFloat(qty.value) < 1) {
                    hasErrors = true;
                    qty.classList.add('border-red-500');
                    const errorContainer = qty.nextElementSibling;
                    if (errorContainer && errorContainer.classList.contains('error-container')) {
                        errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">Quantity must be at least 1.</p>';
                    }
                }
                
                // Validate unit cost
                if (!cost.value || parseFloat(cost.value) < 0) {
                    hasErrors = true;
                    cost.classList.add('border-red-500');
                    const costErrorContainer = cost.parentElement.nextElementSibling;
                    if (costErrorContainer && costErrorContainer.classList.contains('error-container')) {
                        costErrorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">Unit cost is required.</p>';
                    }
                }
            });
            
            // Validate all arm rows
            armRows.forEach(function(row) {
                const armSelect = row.querySelector('.arm-select');
                const reason = row.querySelector('select[name*="[reason]"]');
                
                // Clear previous errors
                row.querySelectorAll('.error-container, .chosen-error-container').forEach(container => {
                    container.innerHTML = '';
                });
                
                // Validate arm select
                if (!armSelect.value) {
                    hasErrors = true;
                    const chosenContainer = $(armSelect).next('.chosen-container');
                    chosenContainer.find('.chosen-single').addClass('border-red-500');
                    const errorContainer = armSelect.nextElementSibling;
                    if (errorContainer && errorContainer.classList.contains('chosen-error-container')) {
                        errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">Please select an arm.</p>';
                    }
                }
                
                // Validate reason
                if (!reason.value) {
                    hasErrors = true;
                    reason.classList.add('border-red-500');
                    const errorContainer = reason.nextElementSibling;
                    if (errorContainer && errorContainer.classList.contains('error-container')) {
                        errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">Please select a reason.</p>';
                    }
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert('Please fix the validation errors before submitting.');
                return;
            }
        });

        // Prevent duplicate selections across arm selects
        function refreshArmOptions() {
            const selects = Array.from(document.querySelectorAll('select.arm-select'));
            const selectedValues = new Set(selects.map(s => s.value).filter(v => v));
            selects.forEach(select => {
                Array.from(select.options).forEach(opt => {
                    if (!opt.value) return;
                    const shouldDisable = selectedValues.has(opt.value) && opt.value !== select.value;
                    opt.disabled = shouldDisable;
                    if (shouldDisable) {
                        opt.classList.add('hidden');
                    } else {
                        opt.classList.remove('hidden');
                    }
                });
                // Update chosen dropdown
                $(select).trigger('chosen:updated');
            });
        }
    </script>
</x-app-layout>