<x-app-layout>
    @section('title', 'Create Approval - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => route('approvals.index'), 'label' => 'Approvals'],
        ['url' => '#', 'label' => 'Create Approval']
    ]" />

    <x-dynamic-heading 
        :title="'Create Approval'" 
        :subtitle="'Give items and arms on approval to customers'"
        :icon="'fas fa-file-invoice'"
    />

    <style>
        .searchable-select-container {
            position: relative;
        }

        .searchable-dropdown {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .search-results-container {
            max-height: 160px;
            overflow-y: auto;
        }

        .result-item {
            transition: background-color 0.15s ease-in-out;
        }

        .result-item:hover {
            background-color: #f3f4f6;
        }

        .result-item.selected {
            background-color: #dbeafe;
        }

        .loading-indicator {
            pointer-events: none;
        }

        .pagination-container {
            background-color: #f9fafb;
        }
    </style>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">Please correct the following errors:</p>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('approvals.store') }}" id="approvalForm">
        @csrf

        <div class="bg-white shadow-lg rounded-lg border border-gray-200">
            <!-- Header Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Approval Details</h3>
            </div>

            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="party_search_input" class="block text-sm font-medium text-gray-700 mb-1">
                            Party <span class="text-red-500">*</span>
                        </label>
                        <div class="searchable-select-container relative">
                            <input type="text" 
                                   id="party_search_input" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 searchable-input bg-white @error('party_id') border-red-500 @enderror" 
                                   placeholder="Search parties..."
                                   autocomplete="off"
                                   value="{{ old('party_search_input') }}">
                            <input type="hidden" name="party_id" id="party_id" class="selected-item-id" value="{{ old('party_id') }}">
                            
                            <!-- Dropdown -->
                            <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                                <div class="search-results-container max-h-40 overflow-y-auto">
                                    <!-- Results will be populated here -->
                                </div>
                                <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                                    <div class="flex justify-between items-center text-xs">
                                        <button type="button" class="prev-page text-teal-500 hover:text-teal-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-teal-50 transition-colors">Previous</button>
                                        <span class="page-info text-gray-500 text-xs"></span>
                                        <button type="button" class="next-page text-teal-500 hover:text-teal-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-teal-50 transition-colors">Next</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Loading indicator -->
                            <div class="loading-indicator hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                                <svg class="animate-spin h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('party_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="approval_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Approval Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="approval_date" id="approval_date" 
                            value="{{ old('approval_date', date('Y-m-d')) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('approval_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notes
                        </label>
                        <input type="text" name="notes" id="notes" 
                            value="{{ old('notes') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            placeholder="Optional notes">
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- General Items Section -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <label class="text-sm font-semibold text-gray-900">General Items</label>
                        <button type="button" id="add_general_item" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Item
                        </button>
                    </div>
                    
                    <div class="overflow-visible border border-gray-200 rounded-md">
                        <table class="min-w-full divide-y divide-gray-200" style="table-layout: auto;">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Qty</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Sale Price</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Total</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16"></th>
                                </tr>
                            </thead>
                            <tbody id="general_items_container" class="bg-white divide-y divide-gray-200">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                        <div id="general_items_empty" class="p-6 text-center text-gray-400 text-sm bg-gray-50">
                            <p>No items added. Click "Add Item" to add general items.</p>
                        </div>
                    </div>
                </div>

                <!-- Arms Section - Hidden: StoreBook is items-only -->
                <div class="border-t border-gray-200 pt-4" style="display: none;">
                    <div class="flex justify-between items-center mb-3">
                        <label class="text-sm font-semibold text-gray-900">Arms</label>
                        <button type="button" id="add_arm" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Arm
                        </button>
                    </div>
                    
                    <div class="overflow-visible border border-gray-200 rounded-md">
                        <table class="min-w-full divide-y divide-gray-200" style="table-layout: auto;">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Arm</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Sale Price</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16"></th>
                                </tr>
                            </thead>
                            <tbody id="arms_container" class="bg-white divide-y divide-gray-200">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                        <div id="arms_empty" class="p-6 text-center text-gray-400 text-sm bg-gray-50">
                            <p>No arms added. Click "Add Arm" to add arms.</p>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center bg-gray-50 rounded-md px-4 py-3">
                        <div class="flex items-center space-x-6">
                            <div>
                                <span class="text-xs text-gray-500">Items:</span>
                                <span class="ml-2 text-sm font-semibold text-gray-900" id="total_items">0</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Arms:</span>
                                <span class="ml-2 text-sm font-semibold text-gray-900" id="total_arms">0</span>
                            </div>
                            <div class="border-l border-gray-300 pl-6">
                                <span class="text-xs text-gray-500">Total Value:</span>
                                <span class="ml-2 text-base font-bold text-teal-600" id="total_value">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end space-x-3">
                <a href="{{ route('approvals.index') }}" 
                    class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                    Cancel
                </a>
                <button type="submit" 
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Save Approval
                </button>
            </div>
        </div>
    </form>

    <!-- Templates -->
    <template id="general_item_template">
        <tr class="general-item-row hover:bg-gray-50">
            <td class="px-3 py-2">
                <div class="searchable-select-container relative">
                    <input type="text" 
                           class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500 searchable-input bg-white" 
                           placeholder="Search items..."
                           autocomplete="off"
                           data-index="INDEX">
                    <input type="hidden" name="general_lines[INDEX][general_item_id]" class="selected-item-id">
                    
                    <!-- Dropdown -->
                    <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                        <div class="search-results-container max-h-40 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                        <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                            <div class="flex justify-between items-center text-xs">
                                <button type="button" class="prev-page text-purple-500 hover:text-purple-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-purple-50 transition-colors">Previous</button>
                                <span class="page-info text-gray-500 text-xs"></span>
                                <button type="button" class="next-page text-purple-500 hover:text-purple-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-purple-50 transition-colors">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Loading indicator -->
                    <div class="loading-indicator hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                        <svg class="animate-spin h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Error display for general item selection -->
                <div class="general-item-error mt-1 text-xs text-red-600 hidden"></div>
            </td>
            <td class="px-3 py-2">
                <input type="number" name="general_lines[INDEX][qty]" step="1" min="1" 
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500 general-qty" 
                    value="1">
            </td>
            <td class="px-3 py-2">
                <input type="number" name="general_lines[INDEX][sale_price]" step="1" min="0" 
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500 general-sale-price">
            </td>
            <td class="px-3 py-2">
                <span class="text-sm font-medium text-gray-900 general-line-total">0</span>
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="remove-general-item text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <template id="arm_template">
        <tr class="arm-row hover:bg-gray-50">
            <td class="px-3 py-2">
                <div class="searchable-select-container relative">
                    <input type="text" 
                           class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 searchable-input bg-white" 
                           placeholder="Search arms..."
                           autocomplete="off"
                           data-index="INDEX">
                    <input type="hidden" name="arm_lines[INDEX][arm_id]" class="selected-arm-id">
                    
                    <!-- Dropdown -->
                    <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-hidden">
                        <div class="search-results-container max-h-40 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                        <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-25">
                            <div class="flex justify-between items-center text-xs">
                                <button type="button" class="prev-page text-blue-500 hover:text-blue-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-blue-50 transition-colors">Previous</button>
                                <span class="page-info text-gray-500 text-xs"></span>
                                <button type="button" class="next-page text-blue-500 hover:text-blue-700 disabled:opacity-40 disabled:cursor-not-allowed px-2 py-1 rounded hover:bg-blue-50 transition-colors">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Loading indicator -->
                    <div class="loading-indicator hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                        <svg class="animate-spin h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Error display for arm selection -->
                <div class="arm-error mt-1 text-xs text-red-600 hidden"></div>
            </td>
            <td class="px-3 py-2">
                <input type="number" name="arm_lines[INDEX][sale_price]" step="1" min="0" 
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 arm-sale-price">
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="remove-arm text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        </tr>
    </template>


    <script>
        let generalItemIndex = 0;
        let armIndex = 0;

        // Global arrays to track selected IDs
        window.selectedGeneralItemIds = window.selectedGeneralItemIds || [];
        window.selectedArmIds = window.selectedArmIds || [];

        // Helper function to get selected general item IDs
        function getSelectedGeneralItemIds() {
            const selectedIds = [];
            document.querySelectorAll('.general-item-row .selected-item-id').forEach(input => {
                if (input.value) {
                    selectedIds.push(input.value);
                }
            });
            return selectedIds;
        }

        // Helper function to get selected arm IDs
        function getSelectedArmIds() {
            const selectedIds = [];
            document.querySelectorAll('.arm-row .selected-arm-id').forEach(input => {
                if (input.value) {
                    selectedIds.push(input.value);
                }
            });
            return selectedIds;
        }

        // Update global arrays
        function updateSelectedIds() {
            window.selectedGeneralItemIds = getSelectedGeneralItemIds();
            window.selectedArmIds = getSelectedArmIds();
        }

        // General Item Searchable Dropdown Class
        class GeneralItemSearchableDropdown {
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
                
                this.itemsPerPage = options.itemsPerPage || 15;
                this.debounceDelay = options.debounceDelay || 300;
                this.minSearchLength = options.minSearchLength || 2;
                
                this.init();
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
                this.searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, this.debounceDelay);
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
                    
                    if (window.selectedGeneralItemIds && Array.isArray(window.selectedGeneralItemIds) && window.selectedGeneralItemIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedGeneralItemIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from search API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Search error:', error);
                    this.showError(`Search failed: ${error.message}`);
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
                    
                    if (window.selectedGeneralItemIds && Array.isArray(window.selectedGeneralItemIds) && window.selectedGeneralItemIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedGeneralItemIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial load error:', error);
                    this.showError(`Failed to load items: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            displayResults(items, meta) {
                this.resultsContainer.innerHTML = '';
                
                if (!items || !Array.isArray(items) || items.length === 0) {
                    this.resultsContainer.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            ${this.searchTerm ? 'No items found matching your search.' : 'No items available.'}
                        </div>
                    `;
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                
                items.forEach((item) => {
                    if (!item || !item.id || !item.item_name) {
                        return;
                    }
                    
                    const resultItem = document.createElement('div');
                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                    resultItem.dataset.itemId = item.id;
                    resultItem.dataset.itemName = item.item_name;
                    resultItem.dataset.salePrice = this.safeNumber(item.sale_price);
                    resultItem.dataset.availableStock = this.safeNumber(item.available_stock);
                    
                    resultItem.innerHTML = `
                        <div class="font-medium text-gray-900">${item.item_name}</div>
                        <div class="text-xs text-gray-500">Stock: ${item.available_stock || 0}</div>
                    `;
                    
                    resultItem.addEventListener('click', () => {
                        this.selectItem(item);
                    });
                    
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
                
                pageInfo.textContent = `Page ${meta.current_page} of ${meta.last_page}`;
                
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
            
            selectItem(item) {
                this.selectedItem = item;
                this.input.value = item.item_name;
                this.hiddenInput.value = item.id;
                
                // Add to global selected IDs
                if (!window.selectedGeneralItemIds.includes(item.id)) {
                    window.selectedGeneralItemIds.push(item.id);
                }
                
                // Update sale price
                const row = this.container.closest('.general-item-row');
                const salePriceInput = row.querySelector('.general-sale-price');
                if (salePriceInput) {
                    salePriceInput.value = item.sale_price ? item.sale_price : '';
                }
                
                // Show stock info
                const availableStock = item.available_stock || 0;
                const existingInfo = row.querySelector('.item-info');
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                const infoDiv = document.createElement('div');
                infoDiv.className = 'item-info text-xs text-gray-500 mt-1';
                infoDiv.innerHTML = `Stock: <span class="font-medium">${availableStock}</span>`;
                this.container.appendChild(infoDiv);
                
                // Calculate line total
                calculateLineTotal(row);
                updateSummary();
                
                this.hideDropdown();
            }
            
            selectHighlightedResult() {
                const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
                if (highlightedResult) {
                    const item = {
                        id: highlightedResult.dataset.itemId,
                        item_name: highlightedResult.dataset.itemName,
                        sale_price: this.safeNumber(highlightedResult.dataset.salePrice),
                        available_stock: this.safeNumber(highlightedResult.dataset.availableStock)
                    };
                    this.selectItem(item);
                } else {
                    const firstResult = this.resultsContainer.querySelector('.result-item');
                    if (firstResult) {
                        const item = {
                            id: firstResult.dataset.itemId,
                            item_name: firstResult.dataset.itemName,
                            sale_price: this.safeNumber(firstResult.dataset.salePrice),
                            available_stock: this.safeNumber(firstResult.dataset.availableStock)
                        };
                        this.selectItem(item);
                    }
                }
            }
            
            navigateResults(direction) {
                const results = this.resultsContainer.querySelectorAll('.result-item');
                const currentIndex = Array.from(results).findIndex(item => item.classList.contains('selected'));
                
                let newIndex;
                if (direction === 'down') {
                    newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                } else {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                }
                
                results.forEach(item => item.classList.remove('selected', 'bg-purple-100'));
                
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-purple-100');
                    results[newIndex].scrollIntoView({ block: 'nearest' });
                }
            }
            
            showDropdown() {
                this.dropdown.classList.remove('hidden');
            }
            
            hideDropdown() {
                this.dropdown.classList.add('hidden');
            }
            
            showLoading() {
                this.loadingIndicator.classList.remove('hidden');
            }
            
            hideLoading() {
                this.loadingIndicator.classList.add('hidden');
            }
            
            safeNumber(value) {
                if (value === null || value === undefined || value === '') {
                    return 0;
                }
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }
            
            showError(message) {
                this.resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-red-500 text-center">
                        <div class="font-medium">Error Loading Items</div>
                        <div class="mt-1">${message}</div>
                    </div>
                `;
            }
            
            clearSelection() {
                if (this.selectedItem && this.selectedItem.id && window.selectedGeneralItemIds) {
                    window.selectedGeneralItemIds = window.selectedGeneralItemIds.filter(id => id !== this.selectedItem.id);
                }
                
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
                
                const row = this.container.closest('.general-item-row');
                const salePriceInput = row.querySelector('.general-sale-price');
                const qtyInput = row.querySelector('.general-qty');
                
                if (salePriceInput) {
                    salePriceInput.value = '';
                }
                if (qtyInput) {
                    qtyInput.value = '1';
                }
                
                const existingInfo = row.querySelector('.item-info');
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                calculateLineTotal(row);
                updateSummary();
            }
        }

        // Arm Searchable Dropdown Class
        class ArmSearchableDropdown {
            constructor(container, options = {}) {
                this.container = container;
                this.input = container.querySelector('.searchable-input');
                this.hiddenInput = container.querySelector('.selected-arm-id');
                this.dropdown = container.querySelector('.searchable-dropdown');
                this.resultsContainer = container.querySelector('.search-results-container');
                this.paginationContainer = container.querySelector('.pagination-container');
                this.loadingIndicator = container.querySelector('.loading-indicator');
                
                this.searchTimeout = null;
                this.currentPage = 1;
                this.searchTerm = '';
                this.selectedItem = null;
                
                this.itemsPerPage = options.itemsPerPage || 15;
                this.debounceDelay = options.debounceDelay || 300;
                this.minSearchLength = options.minSearchLength || 2;
                
                this.init();
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
                this.searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, this.debounceDelay);
            }
            
            async performSearch() {
                if (this.searchTerm.length < this.minSearchLength) {
                    this.showInitialResults();
                    return;
                }
                
                this.showLoading();
                
                try {
                    const url = new URL('/api/arms/search', window.location.origin);
                    url.searchParams.set('q', this.searchTerm);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    url.searchParams.set('status', 'available');
                    
                    if (window.selectedArmIds && Array.isArray(window.selectedArmIds) && window.selectedArmIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedArmIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from search API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Search error:', error);
                    this.showError(`Search failed: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            async showInitialResults() {
                this.showLoading();
                
                try {
                    const url = new URL('/api/arms', window.location.origin);
                    url.searchParams.set('page', this.currentPage);
                    url.searchParams.set('limit', this.itemsPerPage);
                    url.searchParams.set('status', 'available');
                    
                    if (window.selectedArmIds && Array.isArray(window.selectedArmIds) && window.selectedArmIds.length > 0) {
                        url.searchParams.set('exclude_ids', window.selectedArmIds.join(','));
                    }
                    
                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial load error:', error);
                    this.showError(`Failed to load arms: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            displayResults(items, meta) {
                this.resultsContainer.innerHTML = '';
                
                if (!items || !Array.isArray(items) || items.length === 0) {
                    this.resultsContainer.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            ${this.searchTerm ? 'No arms found matching your search.' : 'No arms available.'}
                        </div>
                    `;
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                
                items.forEach((arm) => {
                    if (!arm || !arm.id) {
                        return;
                    }
                    
                    const resultItem = document.createElement('div');
                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                    resultItem.dataset.armId = arm.id;
                    resultItem.dataset.armTitle = arm.arm_title || '';
                    resultItem.dataset.serialNo = arm.serial_no || '';
                    resultItem.dataset.salePrice = this.safeNumber(arm.sale_price);
                    
                    resultItem.innerHTML = `
                        <div class="font-medium text-gray-900">${arm.arm_title || arm.serial_no || ''}</div>
                    `;
                    
                    resultItem.addEventListener('click', () => {
                        this.selectArm(arm);
                    });
                    
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
                
                pageInfo.textContent = `Page ${meta.current_page} of ${meta.last_page}`;
                
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
            
            selectArm(arm) {
                this.selectedItem = arm;
                const displayText = (arm.arm_title || arm.serial_no || '').trim();
                this.input.value = displayText;
                this.hiddenInput.value = arm.id;
                
                // Add to global selected IDs
                if (!window.selectedArmIds.includes(arm.id)) {
                    window.selectedArmIds.push(arm.id);
                }
                
                // Update sale price
                const row = this.container.closest('.arm-row');
                const salePriceInput = row.querySelector('.arm-sale-price');
                if (salePriceInput) {
                    salePriceInput.value = arm.sale_price ? arm.sale_price : '';
                }
                
                updateSummary();
                this.hideDropdown();
            }
            
            selectHighlightedResult() {
                const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
                if (highlightedResult) {
                    const arm = {
                        id: highlightedResult.dataset.armId,
                        serial_no: highlightedResult.dataset.serialNo,
                        arm_title: highlightedResult.dataset.armTitle,
                        sale_price: this.safeNumber(highlightedResult.dataset.salePrice)
                    };
                    this.selectArm(arm);
                } else {
                    const firstResult = this.resultsContainer.querySelector('.result-item');
                    if (firstResult) {
                        const arm = {
                            id: firstResult.dataset.armId,
                            serial_no: firstResult.dataset.serialNo,
                            arm_title: firstResult.dataset.armTitle,
                            sale_price: this.safeNumber(firstResult.dataset.salePrice)
                        };
                        this.selectArm(arm);
                    }
                }
            }
            
            navigateResults(direction) {
                const results = this.resultsContainer.querySelectorAll('.result-item');
                const currentIndex = Array.from(results).findIndex(item => item.classList.contains('selected'));
                
                let newIndex;
                if (direction === 'down') {
                    newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                } else {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                }
                
                results.forEach(item => item.classList.remove('selected', 'bg-blue-100'));
                
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-blue-100');
                    results[newIndex].scrollIntoView({ block: 'nearest' });
                }
            }
            
            showDropdown() {
                this.dropdown.classList.remove('hidden');
            }
            
            hideDropdown() {
                this.dropdown.classList.add('hidden');
            }
            
            showLoading() {
                this.loadingIndicator.classList.remove('hidden');
            }
            
            hideLoading() {
                this.loadingIndicator.classList.add('hidden');
            }
            
            safeNumber(value) {
                if (value === null || value === undefined || value === '') {
                    return 0;
                }
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }
            
            showError(message) {
                this.resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-red-500 text-center">
                        <div class="font-medium">Error Loading Arms</div>
                        <div class="mt-1">${message}</div>
                    </div>
                `;
            }
            
            clearSelection() {
                if (this.selectedItem && this.selectedItem.id && window.selectedArmIds) {
                    window.selectedArmIds = window.selectedArmIds.filter(id => id !== this.selectedItem.id);
                }
                
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
                
                const row = this.container.closest('.arm-row');
                const salePriceInput = row.querySelector('.arm-sale-price');
                
                if (salePriceInput) {
                    salePriceInput.value = '';
                }
                
                updateSummary();
            }
        }

        // Party Searchable Dropdown Class
        class PartySearchableDropdown {
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
                
                this.itemsPerPage = options.itemsPerPage || 15;
                this.debounceDelay = options.debounceDelay || 300;
                this.minSearchLength = options.minSearchLength || 2;
                
                this.init();
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
                this.searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, this.debounceDelay);
            }
            
            async performSearch() {
                if (this.searchTerm.length < this.minSearchLength) {
                    this.showInitialResults();
                    return;
                }
                
                this.showLoading();
                
                try {
                    const response = await fetch(`/api/parties/search?q=${encodeURIComponent(this.searchTerm)}&page=${this.currentPage}&limit=${this.itemsPerPage}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from search API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Search error:', error);
                    this.showError(`Search failed: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            async showInitialResults() {
                this.showLoading();
                
                try {
                    const response = await fetch(`/api/parties?page=${this.currentPage}&limit=${this.itemsPerPage}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    
                    if (!Array.isArray(data.data)) {
                        throw new Error('Invalid data format received from API');
                    }
                    
                    this.displayResults(data.data || [], data.meta || {});
                } catch (error) {
                    console.error('Initial load error:', error);
                    this.showError(`Failed to load parties: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }
            
            displayResults(parties, meta) {
                this.resultsContainer.innerHTML = '';
                
                if (!parties || !Array.isArray(parties) || parties.length === 0) {
                    this.resultsContainer.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            ${this.searchTerm ? 'No parties found matching your search.' : 'No parties available.'}
                        </div>
                    `;
                    this.paginationContainer.classList.add('hidden');
                    return;
                }
                
                parties.forEach((party) => {
                    if (!party || !party.id || !party.name) {
                        return;
                    }
                    
                    const resultItem = document.createElement('div');
                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                    resultItem.dataset.partyId = party.id;
                    resultItem.dataset.partyName = party.name;
                    resultItem.dataset.partyCnic = party.cnic || '';
                    
                    resultItem.innerHTML = `
                        <div class="font-medium text-gray-900">${party.name}</div>
                        ${party.cnic ? `<div class="text-xs text-gray-500">CNIC: ${party.cnic}</div>` : ''}
                    `;
                    
                    resultItem.addEventListener('click', () => {
                        this.selectParty(party);
                    });
                    
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
                
                pageInfo.textContent = `Page ${meta.current_page} of ${meta.last_page}`;
                
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
            
            selectParty(party) {
                this.selectedItem = party;
                let displayText = party.name;
                if (party.cnic) {
                    displayText += ` (CNIC: ${party.cnic})`;
                }
                this.input.value = displayText;
                this.hiddenInput.value = party.id;
                this.hideDropdown();
            }
            
            selectHighlightedResult() {
                const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
                if (highlightedResult) {
                    const party = {
                        id: highlightedResult.dataset.partyId,
                        name: highlightedResult.dataset.partyName,
                        cnic: highlightedResult.dataset.partyCnic
                    };
                    this.selectParty(party);
                } else {
                    const firstResult = this.resultsContainer.querySelector('.result-item');
                    if (firstResult) {
                        const party = {
                            id: firstResult.dataset.partyId,
                            name: firstResult.dataset.partyName,
                            cnic: firstResult.dataset.partyCnic
                        };
                        this.selectParty(party);
                    }
                }
            }
            
            navigateResults(direction) {
                const results = this.resultsContainer.querySelectorAll('.result-item');
                const currentIndex = Array.from(results).findIndex(item => item.classList.contains('selected'));
                
                let newIndex;
                if (direction === 'down') {
                    newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                } else {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                }
                
                results.forEach(item => item.classList.remove('selected', 'bg-teal-100'));
                
                if (results[newIndex]) {
                    results[newIndex].classList.add('selected', 'bg-teal-100');
                    results[newIndex].scrollIntoView({ block: 'nearest' });
                }
            }
            
            showDropdown() {
                this.dropdown.classList.remove('hidden');
            }
            
            hideDropdown() {
                this.dropdown.classList.add('hidden');
            }
            
            showLoading() {
                this.loadingIndicator.classList.remove('hidden');
            }
            
            hideLoading() {
                this.loadingIndicator.classList.add('hidden');
            }
            
            showError(message) {
                this.resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-red-500 text-center">
                        <div class="font-medium">Error Loading Parties</div>
                        <div class="mt-1">${message}</div>
                    </div>
                `;
            }
            
            clearSelection() {
                this.selectedItem = null;
                this.input.value = '';
                this.hiddenInput.value = '';
            }
        }

        function updateSummary() {
            const itemRows = document.querySelectorAll('.general-item-row').length;
            const armRows = document.querySelectorAll('.arm-row').length;
            
            let totalValue = 0;
            document.querySelectorAll('.general-line-total').forEach(span => {
                totalValue += parseFloat(span.textContent) || 0;
            });
            document.querySelectorAll('.arm-sale-price').forEach(input => {
                totalValue += parseFloat(input.value) || 0;
            });
            
            document.getElementById('total_items').textContent = itemRows;
            document.getElementById('total_arms').textContent = armRows;
            document.getElementById('total_value').textContent = totalValue.toFixed(2);
            
            document.getElementById('general_items_empty').style.display = itemRows > 0 ? 'none' : 'block';
            document.getElementById('arms_empty').style.display = armRows > 0 ? 'none' : 'block';
        }

        document.getElementById('add_general_item').addEventListener('click', function() {
            const container = document.getElementById('general_items_container');
            const template = document.getElementById('general_item_template');
            const clone = template.content.cloneNode(true);
            
            clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
                element.name = element.name.replace('INDEX', generalItemIndex);
            });
            
            // Update data-index attribute for the searchable input
            clone.querySelectorAll('[data-index="INDEX"]').forEach(element => {
                element.setAttribute('data-index', generalItemIndex);
            });
            
            container.appendChild(clone);
            
            const newRow = container.lastElementChild;
            const searchableContainer = newRow.querySelector('.searchable-select-container');
            const qtyInput = newRow.querySelector('.general-qty');
            const priceInput = newRow.querySelector('.general-sale-price');
            
            // Initialize searchable dropdown
            if (searchableContainer) {
                new GeneralItemSearchableDropdown(searchableContainer);
            }
            
            [qtyInput, priceInput].forEach(input => {
                input.addEventListener('input', function() {
                    calculateLineTotal(newRow);
                    updateSummary();
                    updateSelectedIds();
                });
            });
            
            generalItemIndex++;
            updateSelectedIds();
            updateSummary();
        });

        document.getElementById('add_arm').addEventListener('click', function() {
            const container = document.getElementById('arms_container');
            const template = document.getElementById('arm_template');
            const clone = template.content.cloneNode(true);
            
            clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
                element.name = element.name.replace('INDEX', armIndex);
            });
            
            // Update data-index attribute for the searchable input
            clone.querySelectorAll('[data-index="INDEX"]').forEach(element => {
                element.setAttribute('data-index', armIndex);
            });
            
            container.appendChild(clone);
            
            const newRow = container.lastElementChild;
            const searchableContainer = newRow.querySelector('.searchable-select-container');
            const priceInput = newRow.querySelector('.arm-sale-price');
            
            // Initialize searchable dropdown
            if (searchableContainer) {
                new ArmSearchableDropdown(searchableContainer);
            }
            
            priceInput.addEventListener('input', function() {
                updateSummary();
                updateSelectedIds();
            });
            
            armIndex++;
            updateSelectedIds();
            updateSummary();
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-general-item')) {
                const row = e.target.closest('.general-item-row');
                const hiddenInput = row.querySelector('.selected-item-id');
                if (hiddenInput && hiddenInput.value) {
                    // Remove from selected IDs
                    window.selectedGeneralItemIds = window.selectedGeneralItemIds.filter(id => id !== hiddenInput.value);
                }
                row.remove();
                updateSelectedIds();
                updateSummary();
            }
            if (e.target.closest('.remove-arm')) {
                const row = e.target.closest('.arm-row');
                const hiddenInput = row.querySelector('.selected-arm-id');
                if (hiddenInput && hiddenInput.value) {
                    // Remove from selected IDs
                    window.selectedArmIds = window.selectedArmIds.filter(id => id !== hiddenInput.value);
                }
                row.remove();
                updateSelectedIds();
                updateSummary();
            }
        });

        function calculateLineTotal(row) {
            const qty = parseFloat(row.querySelector('.general-qty')?.value) || 0;
            const price = parseFloat(row.querySelector('.general-sale-price')?.value) || 0;
            const total = qty * price;
            const totalSpan = row.querySelector('.general-line-total');
            if (totalSpan) {
                totalSpan.textContent = total.toFixed(2);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Party searchable dropdown
            const partyContainer = document.querySelector('#party_search_input')?.closest('.searchable-select-container');
            if (partyContainer) {
                new PartySearchableDropdown(partyContainer);
            }
            
            // Initialize existing general item searchable dropdowns
            document.querySelectorAll('#general_items_container .general-item-row .searchable-select-container').forEach(container => {
                new GeneralItemSearchableDropdown(container);
            });
            
            // Initialize existing arm searchable dropdowns
            document.querySelectorAll('#arms_container .arm-row .searchable-select-container').forEach(container => {
                new ArmSearchableDropdown(container);
            });
            
            updateSelectedIds();
            updateSummary();
            
            // Form validation before submission
            const form = document.getElementById('approvalForm');
            if (!form) {
                console.error('Approval form not found!');
                return;
            }
            
            form.addEventListener('submit', handleFormSubmit);
        });

        // Form validation before submission
        function handleFormSubmit(e) {
            // Clear previous errors
            document.querySelectorAll('.validation-error').forEach(el => el.remove());
            
            // Remove required attributes from all fields to prevent HTML5 validation interference
            document.querySelectorAll('[required]').forEach(el => {
                el.removeAttribute('required');
            });
            
            let hasErrors = false;
            
            // Validate party selection
            // For Chosen, we need to get the value from the actual select element, not the Chosen container
            const partySelect = document.getElementById('party_id');
            const partyId = partySelect ? partySelect.value : '';
            console.log('Party ID:', partyId);
            if (!partyId) {
                showFieldError('party_id', 'Please select a party');
                hasErrors = true;
            }
            
            // Validate general item rows - collect empty rows first, then remove them
            const generalRows = Array.from(document.querySelectorAll('.general-item-row'));
            const generalRowsToRemove = [];
            let validGeneralRows = 0;
            
            
            // First pass: identify completely empty rows and validate filled/partial rows
            generalRows.forEach((row, index) => {
                const itemHiddenInput = row.querySelector('.selected-item-id');
                const qtyInput = row.querySelector('.general-qty');
                const priceInput = row.querySelector('.general-sale-price');
                const searchableInput = row.querySelector('.searchable-input');
                
                // Get value from hidden input
                const itemValue = itemHiddenInput ? itemHiddenInput.value : '';
                const hasItem = !!itemValue;
                const qtyValue = qtyInput ? parseFloat(qtyInput.value) || 0 : 0;
                const hasQty = qtyValue > 0;
                const priceValue = priceInput ? parseFloat(priceInput.value) || 0 : 0;
                const hasPrice = priceValue >= 0 && priceInput && priceInput.value !== '';
                
                
                // If row is completely empty (no item, default qty, no price), mark for removal
                if (!hasItem && qtyValue <= 1 && !hasPrice) {
                    generalRowsToRemove.push(row);
                    return;
                }
                
                // If row is partially filled, validate it - this is an ERROR
                if (!hasItem) {
                    const errorTarget = searchableInput || itemHiddenInput;
                    showFieldError(errorTarget, `Please select an item for row ${index + 1}`);
                    hasErrors = true;
                }
                
                if (!hasQty) {
                    showFieldError(qtyInput, `Please enter a valid quantity for row ${index + 1}`);
                    hasErrors = true;
                }
                
                if (!hasPrice) {
                    showFieldError(priceInput, `Please enter a valid price for row ${index + 1}`);
                    hasErrors = true;
                }
                
                // Only count as valid if ALL fields are present
                if (hasItem && hasQty && hasPrice) {
                    validGeneralRows++;
                }
            });
            
            // Second pass: remove completely empty rows
            generalRowsToRemove.forEach(row => {
                row.remove();
            });
            
            // Validate arm rows - collect empty rows first, then remove them
            const armRows = Array.from(document.querySelectorAll('.arm-row'));
            const armRowsToRemove = [];
            let validArmRows = 0;
            
            
            // First pass: identify completely empty rows and validate filled/partial rows
            armRows.forEach((row, index) => {
                const armHiddenInput = row.querySelector('.selected-arm-id');
                const priceInput = row.querySelector('.arm-sale-price');
                const searchableInput = row.querySelector('.searchable-input');
                
                // Get value from hidden input
                const armValue = armHiddenInput ? armHiddenInput.value : '';
                const hasArm = !!armValue && armValue !== '';
                const priceValue = priceInput ? parseFloat(priceInput.value) || 0 : 0;
                const hasPrice = priceValue >= 0 && priceInput && priceInput.value !== '';
                
                
                // If row is completely empty (no arm selected AND no price), mark for removal
                if (!hasArm && !hasPrice) {
                    armRowsToRemove.push(row);
                    return;
                }
                
                // If row is partially filled, validate it
                if (!hasArm) {
                    const errorTarget = searchableInput || armHiddenInput;
                    showFieldError(errorTarget, `Please select an arm for row ${index + 1}`);
                    hasErrors = true;
                }
                
                if (!hasPrice) {
                    showFieldError(priceInput, `Please enter a valid price for row ${index + 1}`);
                    hasErrors = true;
                }
                
                // Only count as valid if BOTH arm and price are present
                if (hasArm && hasPrice) {
                    validArmRows++;
                }
            });
            
            // Second pass: remove completely empty rows
            armRowsToRemove.forEach(row => {
                row.remove();
            });
            
            // Validate at least one item or arm
            if (validGeneralRows === 0 && validArmRows === 0) {
                alert('Please add at least one complete item or arm to the approval.');
                hasErrors = true;
            }
            
            // Check FormData to ensure all arm_ids are captured
            const form = e.target;
            const formData = new FormData(form);
            
            // Verify arm_lines structure in form data
            const armLinesInForm = [];
            const armPricesInForm = [];
            for (let [key, value] of formData.entries()) {
                if (key.includes('arm_lines') && key.includes('arm_id')) {
                    armLinesInForm.push({key, value});
                }
                if (key.includes('arm_lines') && key.includes('sale_price')) {
                    armPricesInForm.push({key, value});
                }
            }
            
            // If FormData is missing arm_ids, manually build and submit
            if (armPricesInForm.length > armLinesInForm.length) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                        
                        // Manually build form data by reading all inputs
                        const manualFormData = new FormData();
                        
                        // Add CSRF token
                        const csrfToken = form.querySelector('input[name="_token"]').value;
                        manualFormData.append('_token', csrfToken);
                        
                        // Add party_id
                        const partyId = form.querySelector('[name="party_id"]').value;
                        manualFormData.append('party_id', partyId);
                        
                        // Add approval_date
                        const approvalDate = form.querySelector('[name="approval_date"]').value;
                        manualFormData.append('approval_date', approvalDate);
                        
                        // Add notes
                        const notes = form.querySelector('[name="notes"]').value;
                        if (notes) {
                            manualFormData.append('notes', notes);
                        }
                        
                        // Add general items manually
                        const generalRows = Array.from(document.querySelectorAll('.general-item-row'));
                        generalRows.forEach((row, idx) => {
                            const itemId = row.querySelector('.selected-item-id')?.value;
                            const qty = row.querySelector('.general-qty')?.value;
                            const price = row.querySelector('.general-sale-price')?.value;
                            
                            if (itemId && qty && price) {
                                manualFormData.append(`general_lines[${idx}][general_item_id]`, itemId);
                                manualFormData.append(`general_lines[${idx}][qty]`, qty);
                                manualFormData.append(`general_lines[${idx}][sale_price]`, price);
                            }
                        });
                        
                        // Add arm lines manually
                        const armRows = Array.from(document.querySelectorAll('.arm-row'));
                        armRows.forEach((row, idx) => {
                            const armHiddenInput = row.querySelector('.selected-arm-id');
                            const priceInput = row.querySelector('.arm-sale-price');
                            
                            const armId = armHiddenInput?.value;
                            const price = priceInput?.value;
                            
                            if (armId && price) {
                                manualFormData.append(`arm_lines[${idx}][arm_id]`, armId);
                                manualFormData.append(`arm_lines[${idx}][sale_price]`, price);
                            }
                        });
                        
                        // Submit via fetch
                        fetch(form.action, {
                            method: 'POST',
                            body: manualFormData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        })
                        .then(response => {
                            if (response.redirected) {
                                window.location.href = response.url;
                            } else if (response.ok) {
                                return response.json();
                            } else {
                                throw new Error('Server returned ' + response.status);
                            }
                        })
                        .then(data => {
                            if (data && data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                // Redirect to index with success message
                                window.location.href = '{{ route("approvals.index") }}?success=Approval created successfully.';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while submitting the form. Please try again.');
                        });
                        
                        return false;
            }
            
            if (hasErrors) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                // Scroll to first error
                const firstError = document.querySelector('.validation-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            
            // If no errors, allow form to submit naturally
            // Don't prevent default - let the form submit normally
        }
        
        function showFieldError(field, message) {
            let targetElement;
            
            if (typeof field === 'string') {
                targetElement = document.getElementById(field);
            } else {
                targetElement = field;
            }
            
            if (!targetElement) return;
            
            const errorDiv = document.createElement('p');
            errorDiv.className = 'validation-error mt-1 text-xs text-red-600';
            errorDiv.textContent = message;
            targetElement.parentNode.appendChild(errorDiv);
            
            targetElement.style.borderColor = '#ef4444';
        }
    </script>
</x-app-layout>
