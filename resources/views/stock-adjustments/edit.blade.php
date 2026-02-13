<x-app-layout>
    @section('title', 'Edit Stock Adjustment - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'],
        ['url' => route('stock-adjustments.index'), 'label' => 'Stock Adjustments'],
        ['url' => '#', 'label' => 'Edit Adjustment'],
    ]" />

    <x-dynamic-heading title="Edit Stock Adjustment" />

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

    <form action="{{ route('stock-adjustments.update', $stockAdjustment) }}" method="POST" id="adjustmentForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-4">
            
            <!-- Adjustment Details Section -->
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Adjustment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <x-input-label for="adjustment_type">Adjustment Type <span class="text-red-500">*</span></x-input-label>
                        <select name="adjustment_type" id="adjustment_type" required
                                class="mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('adjustment_type') border-red-500 @enderror">
                            <option value="">Select Type</option>
                            <option value="addition" {{ old('adjustment_type', $stockAdjustment->adjustment_type) == 'addition' ? 'selected' : '' }}>Addition (+)</option>
                            <option value="subtraction" {{ old('adjustment_type', $stockAdjustment->adjustment_type) == 'subtraction' ? 'selected' : '' }}>Subtraction (-)</option>
                        </select>
                        @error('adjustment_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="adjustment_date">Adjustment Date <span class="text-red-500">*</span></x-input-label>
                        <input type="date" name="adjustment_date" id="adjustment_date" 
                               value="{{ old('adjustment_date', $stockAdjustment->adjustment_date->format('Y-m-d')) }}" required
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full @error('adjustment_date') border-red-500 @enderror" />
                        @error('adjustment_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="description">Description</x-input-label>
                        <input type="text" name="description" id="description" 
                               value="{{ old('description', $stockAdjustment->description) }}" 
                               placeholder="Reason for adjustment..."
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full @error('description') border-red-500 @enderror" />
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Existing Items -->
            <div class="mb-4">
                <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Existing Items</h3>
                @forelse($stockAdjustment->itemLines as $line)
                <div class="border border-gray-200 rounded-lg p-4 mb-3 existing-item-row" data-line-id="{{ $line->id }}">
                    <input type="hidden" name="existing_items[{{ $line->id }}][_delete]" value="0" class="existing-delete-flag">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-900">Item</h4>
                        <button type="button" class="remove-existing-item inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove
                        </button>
                    </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                            <x-input-label>Item</x-input-label>
                            <select name="existing_items[{{ $line->id }}][general_item_id]" class="item-select chosen-select mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select Item</option>
                            @foreach($generalItems as $item)
                                    <option value="{{ $item->id }}" data-cost-price="{{ $item->cost_price }}" {{ $line->general_item_id == $item->id ? 'selected' : '' }}>{{ $item->item_name }} ({{ $item->item_code }})</option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container"></div>
                        </div>
                        <div>
                            <x-input-label>Quantity</x-input-label>
                            <input type="number" step="1" min="1" name="existing_items[{{ $line->id }}][quantity]" value="{{ round($line->quantity) }}" class="existing-qty mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                        <div class="error-container"></div>
                    </div>
                    <div>
                            <x-input-label>Unit Cost</x-input-label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">PKR</span>
                                </div>
                                <input type="number" step="1" min="0" name="existing_items[{{ $line->id }}][unit_cost]" value="{{ round($line->unit_cost) }}" class="existing-unit-cost mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12" required>
                            </div>
                        <div class="error-container"></div>
                    </div>
                    <div>
                            <x-input-label>Total</x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                                </div>
                                <input type="text" value="{{ number_format(round($line->total_amount), 0) }}" class="existing-total mt-1 text-sm border-gray-300 rounded-md shadow-sm w-full pl-12 bg-gray-50 text-gray-600" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-500">No existing item lines.</p>
                @endforelse
            </div>

            <!-- Add More Items (Optional) -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-200 pb-1">Add More Items</h3>
                    <button type="button" id="addItemBtn"
                            class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Item
                    </button>
                </div>
                <div id="itemsContainer"></div>
                @error('items')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
            </div>

            <!-- Existing Arms - Hidden: StoreBook is items-only -->
            <div class="mb-4" style="display: none;">
                <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Existing Arms</h3>
                @forelse($stockAdjustment->armLines as $line)
                <div class="border border-gray-200 rounded-lg p-4 mb-3 existing-arm-row" data-line-id="{{ $line->id }}">
                    <input type="hidden" name="existing_arms[{{ $line->id }}][_delete]" value="0" class="existing-arm-delete-flag">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-900">Arm</h4>
                        <button type="button" class="remove-existing-arm inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <x-input-label>Arm</x-input-label>
                            <select name="existing_arms[{{ $line->id }}][arm_id]" class="arm-select chosen-select mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @php $current = $line->arm; @endphp
                                @if($current)
                                    <option value="{{ $current->id }}" data-price="{{ $current->purchase_price }}" selected>{{ $current->arm_title }}</option>
                                @endif
                                @foreach($arms as $arm)
                                    @if(!$current || $arm->id !== $current->id)
                                        <option value="{{ $arm->id }}" data-price="{{ $arm->purchase_price }}">{{ $arm->arm_title }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="chosen-error-container"></div>
                        </div>
                        <div>
                            <x-input-label>Reason</x-input-label>
                            <select name="existing_arms[{{ $line->id }}][reason]" class="mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="damage" {{ $line->reason == 'damage' ? 'selected' : '' }}>Damaged</option>
                                <option value="theft" {{ $line->reason == 'theft' ? 'selected' : '' }}>Stolen</option>
                            </select>
                            <div class="error-container"></div>
                        </div>
                    <div>
                            <x-input-label>Price</x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                                </div>
                                <input type="number" step="1" min="0" name="existing_arms[{{ $line->id }}][price]" value="{{ round($line->price) }}" class="existing-arm-price mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12">
                            </div>
                            <div class="error-container"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-500">No existing arm lines.</p>
                @endforelse
            </div>

            <!-- Add More Arms (Optional) - Hidden: StoreBook is items-only -->
            <div class="mb-4" id="armSection" style="display: none !important;">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-200 pb-1">Add More Arms</h3>
                    <button type="button" id="addArmBtn"
                            class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Arm
                    </button>
                </div>
                <div id="armsContainer"></div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-x-4">
                <a href="{{ route('stock-adjustments.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Update Adjustment
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
    </style>
    <script>
        let itemCount = 0;
        let armCount = 0;

        // Keep arms section always hidden - StoreBook is items-only
        function keepArmsSectionHidden() {
            const armSection = document.getElementById('armSection');
            const existingArmsSection = document.querySelector('.mb-4:has(.existing-arm-row)');
            if (armSection) {
                armSection.style.setProperty('display', 'none', 'important');
            }
            if (existingArmsSection) {
                existingArmsSection.style.setProperty('display', 'none', 'important');
            }
        }

        // Existing logic for items kept
        document.addEventListener('DOMContentLoaded', function() {
            // Keep arms sections hidden on page load
            keepArmsSectionHidden();
            
            // Monitor adjustment type changes to keep arms hidden
            const adjustmentTypeSelect = document.getElementById('adjustment_type');
            if (adjustmentTypeSelect) {
                adjustmentTypeSelect.addEventListener('change', function() {
                    keepArmsSectionHidden();
                });
            }
            
            // Set up observer to keep arms sections hidden if they somehow become visible
            const observer = new MutationObserver(function(mutations) {
                keepArmsSectionHidden();
            });
            
            const armSection = document.getElementById('armSection');
            if (armSection) {
                observer.observe(armSection, { attributes: true, attributeFilter: ['style'] });
            }
            // Initialize chosen for all existing selects
            $('.chosen-select').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });
            
            refreshItemOptions();
            refreshArmOptions();

            // Hook existing item rows for auto-calc and cost fill
            document.querySelectorAll('.existing-item-row').forEach(function(row) {
                const select = row.querySelector('select.item-select');
                const qty = row.querySelector('input.existing-qty');
                const unit = row.querySelector('input.existing-unit-cost');
                const total = row.querySelector('input.existing-total');
                const deleteFlag = row.querySelector('input.existing-delete-flag');
                const removeBtn = row.querySelector('button.remove-existing-item');
                function recalc() {
                    const q = parseFloat(qty.value) || 0;
                    const u = parseFloat(unit.value) || 0;
                    total.value = Math.round(q * u);
                }
                if (select) {
                    $(select).on('change', function() {
                        const opt = this.options[this.selectedIndex];
                        const cp = opt && opt.getAttribute('data-cost-price');
                        if (cp) { unit.value = Math.round(parseFloat(cp)); }
                        recalc();
                        refreshItemOptions();
                        // Remove error styling
                        const chosenContainer = $(this).next('.chosen-container');
                        chosenContainer.find('.chosen-single').removeClass('border-red-500');
                    });
                }
                if (qty) {
                    qty.addEventListener('input', function() {
                        recalc();
                        // Remove error styling
                        this.classList.remove('border-red-500');
                        const errorContainer = this.nextElementSibling;
                        if (errorContainer && errorContainer.classList.contains('error-container')) {
                            errorContainer.innerHTML = '';
                        }
                    });
                }
                if (unit) {
                    unit.addEventListener('input', function() {
                        recalc();
                        // Remove error styling
                        this.classList.remove('border-red-500');
                        const errorContainer = this.parentElement.nextElementSibling;
                        if (errorContainer && errorContainer.classList.contains('error-container')) {
                            errorContainer.innerHTML = '';
                        }
                    });
                }
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        if (!confirm('Remove this item line?')) return;
                        if (deleteFlag) deleteFlag.value = '1';
                        if (select) {
                            $(select).chosen('destroy');
                            select.value = '';
                        }
                        row.style.display = 'none';
                        refreshItemOptions();
                    });
                }
                recalc();
            });

            // Hook existing arm rows for auto price fill
            document.querySelectorAll('.existing-arm-row').forEach(function(row) {
                const select = row.querySelector('select.arm-select');
                const reasonSelect = row.querySelector('select[name*="[reason]"]');
                const price = row.querySelector('input.existing-arm-price');
                if (select) {
                    $(select).on('change', function() {
                        const opt = this.options[this.selectedIndex];
                        const p = opt && opt.getAttribute('data-price');
                        if (p) price.value = Math.round(parseFloat(p));
                        refreshArmOptions();
                        // Remove error styling
                        const chosenContainer = $(this).next('.chosen-container');
                        chosenContainer.find('.chosen-single').removeClass('border-red-500');
                    });
                }
                if (reasonSelect) {
                    reasonSelect.addEventListener('change', function() {
                        this.classList.remove('border-red-500');
                        const errorContainer = this.nextElementSibling;
                        if (errorContainer && errorContainer.classList.contains('error-container')) {
                            errorContainer.innerHTML = '';
                        }
                    });
                }
            });
            
            // Check if we have new items from old input (validation errors occurred)
            const hasOldItems = @json(old('items', []));
            const hasOldArmItems = @json(old('arm_items', []));
            
            if (hasOldItems && Object.keys(hasOldItems).length > 0) {
                // Restore item rows from old input
                Object.keys(hasOldItems).forEach(function(key) {
                    addItemRow();
                    const lastRow = document.querySelector('.item-row:last-child');
                    const item = hasOldItems[key];
                    
                    // Set values
                    const select = lastRow.querySelector('.item-select');
                    const qty = lastRow.querySelector('input[name*="[quantity]"]');
                    const cost = lastRow.querySelector('input[name*="[unit_cost]"]');
                    
                    if (select && item.general_item_id) {
                        select.value = item.general_item_id;
                        $(select).trigger('chosen:updated');
                        $(select).trigger('change');
                    }
                    if (qty && item.quantity) qty.value = item.quantity;
                    if (cost && item.unit_cost) cost.value = item.unit_cost;
                });
            }
            
            if (hasOldArmItems && Object.keys(hasOldArmItems).length > 0) {
                // Restore arm rows from old input
                Object.keys(hasOldArmItems).forEach(function(key) {
                    addArmRow();
                    const lastRow = document.querySelector('.arm-row:last-child');
                    const arm = hasOldArmItems[key];
                    
                    // Set values
                    const armSelect = lastRow.querySelector('.arm-select');
                    const reasonSelect = lastRow.querySelector('select[name*="[reason]"]');
                    const priceInput = lastRow.querySelector('input[name*="[price]"]');
                    
                    if (armSelect && arm.arm_id) {
                        armSelect.value = arm.arm_id;
                        $(armSelect).trigger('chosen:updated');
                        $(armSelect).trigger('change');
                    }
                    if (reasonSelect && arm.reason) reasonSelect.value = arm.reason;
                    if (priceInput && arm.price) priceInput.value = arm.price;
                });
            }
            
            // Display server-side validation errors
            displayServerErrors();
        });
        
        // Function to display server-side Laravel validation errors
        function displayServerErrors() {
            const errors = @json($errors->messages());
            
            // Display errors for existing items
            Object.keys(errors).forEach(function(fieldName) {
                const messages = errors[fieldName];
                
                // Check if it's an existing item field error
                const existingItemMatch = fieldName.match(/existing_items\.(\d+)\.(general_item_id|quantity|unit_cost)/);
                if (existingItemMatch) {
                    const id = existingItemMatch[1];
                    const field = existingItemMatch[2];
                    const row = document.querySelector(`.existing-item-row[data-line-id="${id}"]`);
                    
                    if (row && row.style.display !== 'none') {
                        if (field === 'general_item_id') {
                            const select = row.querySelector('.item-select');
                            const chosenContainer = $(select).next('.chosen-container');
                            chosenContainer.find('.chosen-single').addClass('border-red-500');
                            const errorContainer = select.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('chosen-error-container')) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        } else if (field === 'quantity') {
                            const input = row.querySelector('input.existing-qty');
                            input.classList.add('border-red-500');
                            const errorContainer = input.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('error-container')) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        } else if (field === 'unit_cost') {
                            const input = row.querySelector('input.existing-unit-cost');
                            input.classList.add('border-red-500');
                            const errorContainer = input.parentElement.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('error-container')) {
                                errorContainer.innerHTML = '<p class="mt-1 text-xs text-red-600">' + messages[0] + '</p>';
                            }
                        }
                    }
                }
                
                // Check if it's a new item field error
                const itemMatch = fieldName.match(/items\.(\d+)\.(general_item_id|quantity|unit_cost)/);
                if (itemMatch) {
                    const index = parseInt(itemMatch[1]);
                    const field = itemMatch[2];
                    const row = Array.from(document.querySelectorAll('.item-row'))[index];
                    
                    if (row) {
                        if (field === 'general_item_id') {
                            const select = row.querySelector('.item-select');
                            const chosenContainer = $(select).next('.chosen-container');
                            chosenContainer.find('.chosen-single').addClass('border-red-500');
                            const errorContainer = select.nextElementSibling;
                            if (errorContainer && errorContainer.classList.contains('chosen-error-container')) {
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
                
                // Check if it's an existing arm field error
                const existingArmMatch = fieldName.match(/existing_arms\.(\d+)\.(arm_id|reason)/);
                if (existingArmMatch) {
                    const id = existingArmMatch[1];
                    const field = existingArmMatch[2];
                    const row = document.querySelector(`.existing-arm-row[data-line-id="${id}"]`);
                    
                    if (row && row.style.display !== 'none') {
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
                
                // Check if it's a new arm field error
                const armMatch = fieldName.match(/arm_items\.(\d+)\.(arm_id|reason)/);
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

        const addItemBtn = document.getElementById('addItemBtn');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', function() {
                addItemRow();
            });
        }

        function addItemRow() {
            itemCount++;
            const container = document.getElementById('itemsContainer');
            const itemRow = document.createElement('div');
            itemRow.className = 'item-row border border-gray-200 rounded-lg p-4 mb-3';
            itemRow.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Item ${itemCount}</h4>
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
                        <select name="items[${itemCount}][general_item_id]" required
                                class="item-select chosen-select mt-1 block w-full rounded-md text-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Item</option>
                            @foreach($generalItems as $item)
                                <option value="{{ $item->id }}" data-cost-price="{{ $item->cost_price }}">{{ $item->item_name }} ({{ $item->item_code }})</option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="items[${itemCount}][quantity]" step="1" min="1" required
                               value="1" placeholder="1.00"
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        <div class="error-container"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                            </div>
                            <input type="number" name="items[${itemCount}][unit_cost]" step="1" min="0" required
                                   placeholder="0"
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12">
                        </div>
                        <div class="error-container"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                            </div>
                            <input type="text" readonly
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12 bg-gray-50 text-gray-600">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-calculated</p>
                    </div>
                </div>
            `;

            container.appendChild(itemRow);
            
            // Initialize chosen for the new select
            $(itemRow).find('.chosen-select').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });
            
            // Add validation styling handler
            const itemSelectEl = itemRow.querySelector('.item-select');
            $(itemSelectEl).on('change', function() {
                // Remove error styling when user selects an option
                const chosenContainer = $(this).next('.chosen-container');
                chosenContainer.find('.chosen-single').removeClass('border-red-500');
            });
            
            refreshItemOptions();

            const itemSelect = itemRow.querySelector('.item-select');
            const quantityInput = itemRow.querySelector('input[name*="[quantity]"]');
            const unitCostInput = itemRow.querySelector('input[name*="[unit_cost]"]');
            const totalAmountInput = itemRow.querySelector('input[readonly]');
            const removeBtn = itemRow.querySelector('.remove-item');

            function calculateTotal() {
                const quantity = parseFloat(quantityInput.value) || 1;
                const unitCost = parseFloat(unitCostInput.value) || 0;
                const total = quantity * unitCost;
                totalAmountInput.value = Math.round(total);
            }

            $(itemSelect).on('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const costPrice = selectedOption.getAttribute('data-cost-price');
                if (costPrice) {
                    unitCostInput.value = Math.round(parseFloat(costPrice));
                    calculateTotal();
                } else {
                    unitCostInput.value = '';
                    totalAmountInput.value = '0';
                }
                refreshItemOptions();
            });

            calculateTotal();
            quantityInput.addEventListener('input', function() {
                calculateTotal();
                // Remove error styling
                this.classList.remove('border-red-500');
                const errorContainer = this.nextElementSibling;
                if (errorContainer && errorContainer.classList.contains('error-container')) {
                    errorContainer.innerHTML = '';
                }
            });
            unitCostInput.addEventListener('input', function() {
                calculateTotal();
                // Remove error styling
                this.classList.remove('border-red-500');
                const errorContainer = this.parentElement.nextElementSibling;
                if (errorContainer && errorContainer.classList.contains('error-container')) {
                    errorContainer.innerHTML = '';
                }
            });

            removeBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    $(itemRow).find('.chosen-select').chosen('destroy');
                    itemRow.remove();
                    refreshItemOptions();
                }
            });
        }

        function refreshItemOptions() {
            const selects = Array.from(document.querySelectorAll('select.item-select'));
            const selectedValues = new Set(selects.map(s => s.value).filter(v => v));
            selects.forEach(select => {
                Array.from(select.options).forEach(opt => {
                    if (!opt.value) return;
                    const shouldDisable = selectedValues.has(opt.value) && opt.value !== select.value;
                    opt.disabled = shouldDisable;
                    if (shouldDisable) opt.classList.add('hidden'); else opt.classList.remove('hidden');
                });
                // Update chosen dropdown
                $(select).trigger('chosen:updated');
            });
        }

        // Arms add
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
                                    <span class="text-gray-500 text-sm">PKR</span>
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
            
            refreshArmOptions();

            const armSelect = row.querySelector('select.arm-select');
            const reasonSelect = row.querySelector('select[name^="arm_items"][name$="[reason]"]');
            const priceInput = row.querySelector('input[name^="arm_items"][name$="[price]"]');
            const removeBtn = row.querySelector('.remove-arm');

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

            removeBtn.addEventListener('click', function() {
                if (confirm('Remove this ARM entry?')) {
                    $(row).find('.chosen-select').chosen('destroy');
                    row.remove();
                    refreshArmOptions();
                }
            });
        }

        function refreshArmOptions() {
            const selects = Array.from(document.querySelectorAll('select.arm-select'));
            const selectedValues = new Set(selects.map(s => s.value).filter(v => v));
            selects.forEach(select => {
                Array.from(select.options).forEach(opt => {
                    if (!opt.value) return;
                    const disable = selectedValues.has(opt.value) && opt.value !== select.value;
                    opt.disabled = disable;
                    if (disable) opt.classList.add('hidden'); else opt.classList.remove('hidden');
                });
                // Update chosen dropdown
                $(select).trigger('chosen:updated');
            });
        }
        
        // Form validation on submit
        document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
            let hasErrors = false;
            
            // Validate existing item rows (not hidden)
            document.querySelectorAll('.existing-item-row').forEach(function(row) {
                if (row.style.display === 'none') return; // Skip deleted rows
                
                const select = row.querySelector('.item-select');
                const qty = row.querySelector('input.existing-qty');
                const cost = row.querySelector('input.existing-unit-cost');
                
                // Clear previous errors
                row.querySelectorAll('.error-container, .chosen-error-container').forEach(container => {
                    container.innerHTML = '';
                });
                
                // Validate item select
                if (!select.value) {
                    hasErrors = true;
                    const chosenContainer = $(select).next('.chosen-container');
                    chosenContainer.find('.chosen-single').addClass('border-red-500');
                    const errorContainer = select.nextElementSibling;
                    if (errorContainer && errorContainer.classList.contains('chosen-error-container')) {
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
            
            // Validate new item rows
            document.querySelectorAll('.item-row').forEach(function(row) {
                const select = row.querySelector('.item-select');
                const qty = row.querySelector('input[name*="[quantity]"]');
                const cost = row.querySelector('input[name*="[unit_cost]"]');
                
                // Clear previous errors
                row.querySelectorAll('.error-container, .chosen-error-container').forEach(container => {
                    container.innerHTML = '';
                });
                
                // Validate item select
                if (!select.value) {
                    hasErrors = true;
                    const chosenContainer = $(select).next('.chosen-container');
                    chosenContainer.find('.chosen-single').addClass('border-red-500');
                    const errorContainer = select.nextElementSibling;
                    if (errorContainer && errorContainer.classList.contains('chosen-error-container')) {
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
            
            // Validate existing arm rows (not hidden)
            document.querySelectorAll('.existing-arm-row').forEach(function(row) {
                if (row.style.display === 'none') return; // Skip deleted rows
                
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
            
            // Validate new arm rows
            document.querySelectorAll('.arm-row').forEach(function(row) {
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
    </script>
</x-app-layout>