<x-app-layout>
    @section('title', 'Process Approval #' . $approval->id . ' - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => route('approvals.index'), 'label' => 'Approvals'],
        ['url' => route('approvals.show', $approval), 'label' => 'Approval #' . $approval->id],
        ['url' => '#', 'label' => 'Process']
    ]" />

    <x-dynamic-heading 
        :title="'Process Approval ' . $approval->approval_number" 
        :subtitle="'Return or generate sale invoice for approved items'"
        :icon="'fas fa-cog'"
    />

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif

    <form method="POST" action="{{ route('approvals.process-action', $approval) }}" id="processForm">
        @csrf

        <div class="bg-white shadow-lg rounded-lg border border-gray-200">
            <!-- Action Selection -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-gray-50">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Action</h3>
                <div class="flex flex-col sm:flex-row sm:space-x-8 space-y-3 sm:space-y-0">
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 hover:bg-purple-50 transition-colors">
                        <input type="radio" name="action_type" value="return" 
                            class="mr-3 h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Return</div>
                            <div class="text-xs text-gray-500">Return items/arms from approval</div>
                        </div>
                    </label>
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 hover:bg-purple-50 transition-colors">
                        <input type="radio" name="action_type" value="sale" 
                            class="mr-3 h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Proceed to Sale</div>
                            <div class="text-xs text-gray-500">Generate sale invoice for items/arms</div>
                        </div>
                    </label>
                </div>
                @error('action_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-6 space-y-6">
                <!-- Approval Info -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Party</label>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $approval->party->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Approval Date</label>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $approval->approval_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- General Items Table -->
                @if($approval->generalItems->where('remaining_quantity', '>', 0)->count() > 0)
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-semibold text-gray-900">General Items</h3>
                        <span class="text-xs text-gray-500">
                            <span id="selected_items_count">0</span> selected
                        </span>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-12">
                                        <input type="checkbox" id="select_all_general" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Item</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Approved</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-blue-600 uppercase tracking-wider">Returned</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-green-600 uppercase tracking-wider">Sold</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-yellow-600 uppercase tracking-wider">Remaining</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-28">Qty to Process</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">Sale Price</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($approval->generalItems->where('remaining_quantity', '>', 0) as $item)
                                    <tr class="general-item-row hover:bg-gray-50 transition-colors" data-item-id="{{ $item->id }}" data-remaining="{{ (int) $item->remaining_quantity }}" data-price="{{ $item->sale_price }}">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="selected_general_items[]" 
                                                value="{{ $item->id }}" 
                                                class="general-item-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item->generalItem->item_name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ number_format(round($item->quantity), 0) }}</td>
                                        <td class="px-4 py-3 text-sm text-blue-600 text-right">{{ number_format(round($item->returned_quantity), 0) }}</td>
                                        <td class="px-4 py-3 text-sm text-green-600 text-right">{{ number_format(round($item->sold_quantity), 0) }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-yellow-600 text-right">{{ number_format(round($item->remaining_quantity), 0) }}</td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                name="general_items[{{ $item->id }}][qty]" 
                                                step="1" 
                                                min="1" 
                                                max="{{ (int) $item->remaining_quantity }}"
                                                value="{{ (int) $item->remaining_quantity }}"
                                                inputmode="numeric"
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500 general-qty-input"
                                                placeholder="Qty"
                                                disabled>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                name="general_items[{{ $item->id }}][sale_price]" 
                                                step="1" 
                                                min="0"
                                                value="{{ $item->sale_price }}"
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500 general-price-input"
                                                placeholder="Price"
                                                disabled>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Arms Table -->
                @if($approval->arms->where('status', 'pending')->count() > 0)
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-semibold text-gray-900">Arms</h3>
                        <span class="text-xs text-gray-500">
                            <span id="selected_arms_count">0</span> selected
                        </span>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-12">
                                        <input type="checkbox" id="select_all_arms" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Serial No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Arm Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">Sale Price</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($approval->arms->where('status', 'pending') as $approvalArm)
                                    <tr class="arm-row hover:bg-gray-50 transition-colors" data-arm-id="{{ $approvalArm->id }}" data-price="{{ $approvalArm->sale_price }}">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="selected_arms[]" 
                                                value="{{ $approvalArm->id }}" 
                                                class="arm-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $approvalArm->arm->serial_no }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $approvalArm->arm->arm_title }}</td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                name="arms[{{ $approvalArm->id }}][sale_price]" 
                                                step="1" 
                                                min="0"
                                                value="{{ $approvalArm->sale_price }}"
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500 arm-price-input"
                                                placeholder="Price"
                                                disabled>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end space-x-3">
                <a href="{{ route('approvals.show', $approval) }}" 
                    class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                    Cancel
                </a>
                <button type="submit" 
                    class="rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                    Process Selected Items
                </button>
            </div>
        </div>
    </form>

    <script>
        // Update selection counts
        function updateSelectionCounts() {
            const selectedItems = document.querySelectorAll('.general-item-checkbox:checked').length;
            const selectedArms = document.querySelectorAll('.arm-checkbox:checked').length;
            document.getElementById('selected_items_count').textContent = selectedItems;
            document.getElementById('selected_arms_count').textContent = selectedArms;
        }

        // Enable/disable inputs based on checkbox and action type
        function toggleInputs() {
            const actionType = document.querySelector('input[name="action_type"]:checked');
            const actionValue = actionType ? actionType.value : null;

            // General items
            document.querySelectorAll('.general-item-row').forEach(row => {
                const checkbox = row.querySelector('.general-item-checkbox');
                const qtyInput = row.querySelector('.general-qty-input');
                const priceInput = row.querySelector('.general-price-input');
                const isChecked = checkbox.checked;
                
                if ((actionValue === 'sale' || actionValue === 'return') && isChecked) {
                    qtyInput.disabled = false;
                    qtyInput.classList.remove('bg-gray-100');
                    qtyInput.classList.add('bg-white');
                    if (actionValue === 'sale') {
                        priceInput.disabled = false;
                        priceInput.classList.remove('bg-gray-100');
                        priceInput.classList.add('bg-white');
                    } else {
                        priceInput.disabled = true;
                        priceInput.classList.add('bg-gray-100');
                        priceInput.classList.remove('bg-white');
                    }
                } else {
                    qtyInput.disabled = true;
                    qtyInput.classList.add('bg-gray-100');
                    qtyInput.classList.remove('bg-white');
                    priceInput.disabled = true;
                    priceInput.classList.add('bg-gray-100');
                    priceInput.classList.remove('bg-white');
                }
            });

            // Arms
            document.querySelectorAll('.arm-row').forEach(row => {
                const checkbox = row.querySelector('.arm-checkbox');
                const priceInput = row.querySelector('.arm-price-input');
                const isChecked = checkbox.checked;
                
                if ((actionValue === 'sale' || actionValue === 'return') && isChecked) {
                    priceInput.disabled = false;
                    priceInput.classList.remove('bg-gray-100');
                    priceInput.classList.add('bg-white');
                } else {
                    priceInput.disabled = true;
                    priceInput.classList.add('bg-gray-100');
                    priceInput.classList.remove('bg-white');
                }
            });

            updateSelectionCounts();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleInputs();
            updateSelectionCounts();
        });

        // Listen to action type changes
        document.querySelectorAll('input[name="action_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                toggleInputs();
                // Update radio button styling
                document.querySelectorAll('label[for*="action_type"]').forEach(label => {
                    label.classList.remove('border-purple-500', 'bg-purple-100');
                    label.classList.add('border-gray-200');
                });
                if (this.checked) {
                    this.closest('label').classList.add('border-purple-500', 'bg-purple-100');
                    this.closest('label').classList.remove('border-gray-200');
                }
            });
        });

        // Listen to checkbox changes
        document.querySelectorAll('.general-item-checkbox, .arm-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                toggleInputs();
                updateSelectionCounts();
            });
        });

        // Select all general items
        document.getElementById('select_all_general')?.addEventListener('change', function() {
            document.querySelectorAll('.general-item-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleInputs();
        });

        // Select all arms
        document.getElementById('select_all_arms')?.addEventListener('change', function() {
            document.querySelectorAll('.arm-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleInputs();
        });

        // Validate quantity inputs
        document.querySelectorAll('.general-qty-input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.disabled) return;
                
                const row = this.closest('.general-item-row');
                const maxQty = parseFloat(row.dataset.remaining);
                const currentQty = parseFloat(this.value) || 0;
                
                if (currentQty > maxQty) {
                    this.value = maxQty;
                    alert(`Quantity cannot exceed remaining quantity: ${maxQty}`);
                }
                if (currentQty < 1) {
                    this.value = 1;
                }
            });
        });

        // Form validation and submission
        document.getElementById('processForm').addEventListener('submit', function(e) {
            const actionType = document.querySelector('input[name="action_type"]:checked');
            const selectedGeneral = document.querySelectorAll('.general-item-checkbox:checked').length;
            const selectedArms = document.querySelectorAll('.arm-checkbox:checked').length;

            if (!actionType) {
                e.preventDefault();
                alert('Please select an action type (Return or Proceed to Sale)');
                return false;
            }

            if (selectedGeneral === 0 && selectedArms === 0) {
                e.preventDefault();
                alert('Please select at least one item or arm to process');
                return false;
            }

            // Enable all disabled inputs before validation so they're included in form data
            document.querySelectorAll('input[disabled]').forEach(input => {
                input.disabled = false;
            });

            // Validate quantities and prices
            let hasError = false;
            let errorMessage = '';

            // Validate general items
            document.querySelectorAll('.general-item-checkbox:checked').forEach(checkbox => {
                if (hasError) return;
                
                const row = checkbox.closest('.general-item-row');
                const qtyInput = row.querySelector('.general-qty-input');
                const priceInput = row.querySelector('.general-price-input');
                const maxQty = parseFloat(row.dataset.remaining);
                const qty = parseFloat(qtyInput.value) || 0;

                if (!qty || qty < 1) {
                    hasError = true;
                    errorMessage = 'Please enter a valid whole-number quantity for selected items';
                } else if (qty > maxQty) {
                    hasError = true;
                    errorMessage = `Quantity cannot exceed remaining quantity: ${maxQty}`;
                }

                // Validate price only for sale action
                if (!hasError && actionType.value === 'sale') {
                    const price = parseFloat(priceInput.value) || 0;
                    if (!price || price <= 0) {
                        hasError = true;
                        errorMessage = 'Please enter a valid sale price for selected items';
                    }
                }
            });

            // Validate arms (price optional for return, required for sale)
            if (!hasError) {
                document.querySelectorAll('.arm-checkbox:checked').forEach(checkbox => {
                    if (hasError) return;
                    
                    const row = checkbox.closest('.arm-row');
                    const priceInput = row.querySelector('.arm-price-input');
                    const price = parseFloat(priceInput.value) || 0;

                    // Price is required for sale action
                    if (actionType.value === 'sale' && (!price || price <= 0)) {
                        hasError = true;
                        errorMessage = 'Please enter a valid sale price for selected arms';
                    }
                    // Price is optional for return, but if provided must be valid
                    else if (actionType.value === 'return' && priceInput.value && price < 0) {
                        hasError = true;
                        errorMessage = 'Sale price cannot be negative';
                    }
                });
            }

            if (hasError) {
                toggleInputs();
                e.preventDefault();
                alert(errorMessage);
                return false;
            }

            const actionText = actionType.value === 'return' ? 'return' : 'generate sale invoice for';
            if (!confirm(`Are you sure you want to ${actionText} the selected items?`)) {
                toggleInputs();
                e.preventDefault();
                return false;
            }

            // Form will submit with all inputs enabled
        });
    </script>
</x-app-layout>
