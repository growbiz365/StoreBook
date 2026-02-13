<x-app-layout>
    @section('title', 'Edit Arms Opening Stock - Arms Management')
    
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/arms-dashboard', 'label' => 'Arms Dashboard'],['url' => route('arms.opening-stock'), 'label' => 'Arms opening stock'],['url' => '#', 'label' => 'Edit Arms Opening Stock']]" />

    <x-dynamic-heading title="Edit Arms Opening Stock" />

    <form action="{{ route('arms.update', $arm->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-4">
            
            <!-- Basic Information Section -->
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <x-input-label for="arm_type_id">Arm Type <span class="text-red-500">*</span></x-input-label>
                        <select name="arm_type_id" id="arm_type_id" 
                                class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 chosen-select @error('arm_type_id') border-red-500 @enderror">
                            <option value="">Select Arm Type</option>
                            @foreach($armTypes as $armType)
                                <option value="{{ $armType->id }}" {{ old('arm_type_id', $arm->arm_type_id) == $armType->id ? 'selected' : '' }}>
                                    {{ $armType->arm_type }}
                                </option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('arm_type_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-input-label for="arm_category_id">Arm Category <span class="text-red-500">*</span></x-input-label>
                        <select name="arm_category_id" id="arm_category_id" 
                                class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 chosen-select @error('arm_category_id') border-red-500 @enderror">
                            <option value="">Select Arm Category</option>
                            @foreach($armCategories as $armCategory)
                                <option value="{{ $armCategory->id }}" {{ old('arm_category_id', $arm->arm_category_id) == $armCategory->id ? 'selected' : '' }}>
                                    {{ $armCategory->arm_category }}
                                </option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('arm_category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-input-label for="make">Make <span class="text-red-500">*</span></x-input-label>
                        <select name="make" id="make" 
                                class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 chosen-select @error('make') border-red-500 @enderror">
                            <option value="">Select Make</option>
                            @foreach($armMakes as $armMake)
                                <option value="{{ $armMake->id }}" {{ old('make', $arm->make) == $armMake->id ? 'selected' : '' }}>
                                    {{ $armMake->arm_make }}
                                </option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('make')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-input-label for="arm_caliber_id">Caliber <span class="text-red-500">*</span></x-input-label>
                        <select name="arm_caliber_id" id="arm_caliber_id" 
                                class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 chosen-select @error('arm_caliber_id') border-red-500 @enderror">
                            <option value="">Select Caliber</option>
                            @foreach($armCalibers as $armCaliber)
                                <option value="{{ $armCaliber->id }}" {{ old('arm_caliber_id', $arm->arm_caliber_id) == $armCaliber->id ? 'selected' : '' }}>
                                    {{ $armCaliber->arm_caliber }}
                                </option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('arm_caliber_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-input-label for="arm_condition_id">Condition <span class="text-red-500">*</span></x-input-label>
                        <select name="arm_condition_id" id="arm_condition_id" 
                                class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 chosen-select @error('arm_condition_id') border-red-500 @enderror">
                            <option value="">Select Condition</option>
                            @foreach($armConditions as $armCondition)
                                <option value="{{ $armCondition->id }}" {{ old('arm_condition_id', $arm->arm_condition_id) == $armCondition->id ? 'selected' : '' }}>
                                    {{ $armCondition->arm_condition }}
                                </option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('arm_condition_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-input-label for="serial_no">Serial Number <span class="text-red-500">*</span></x-input-label>
                            <input type="text" name="serial_no" id="serial_no" value="{{ old('serial_no', $arm->serial_no) }}" required 
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full @error('serial_no') border-red-500 @enderror"
                               placeholder="987654321" />
                            @error('serial_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Status Section -->
            <div class="mb-4">
                <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Pricing & Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <x-input-label for="purchase_price">Cost Price <span class="text-red-500">*</span></x-input-label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                                </div>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $arm->purchase_price) }}" required step="1" min="0"
                                   class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-12 @error('purchase_price') border-red-500 @enderror"
                                       placeholder="0.00" />
                            </div>
                            @error('purchase_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="sale_price">Sale Price <span class="text-red-500">*</span></x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">PKR</span>
                            </div>
                            <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $arm->sale_price) }}" required step="1" min="0"
                                   class="mt-1 text-sm border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm w-full pl-12 @error('sale_price') border-red-500 @enderror"
                                   placeholder="0.00" />
                        </div>
                        @error('sale_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="purchase_date">Opening Date <span class="text-red-500">*</span></x-input-label>
                            <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $arm->purchase_date ? $arm->purchase_date->format('Y-m-d') : date('Y-m-d')) }}" required 
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full @error('purchase_date') border-red-500 @enderror" />
                            @error('purchase_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <!-- Status field hidden - keeping existing status -->
                    <input type="hidden" name="status" value="{{ old('status', $arm->status) }}">
                </div>
            </div>

            <!-- Additional Information Section -->
                    <div class="mb-4">
                <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-200 pb-1">Additional Information</h3>
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <x-input-label for="notes">Notes</x-input-label>
                            <textarea name="notes" id="notes" rows="3" 
                                  class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full resize-none @error('notes') border-red-500 @enderror"
                                      placeholder="Additional notes about the arm...">{{ old('notes', $arm->notes) }}</textarea>
                            @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="arm-title-preview">Auto-generated Arm Title</x-input-label>
                        <div id="arm-title-preview" class="mt-1 text-sm text-gray-700 font-mono bg-gray-50 px-3 py-2 rounded-md border border-gray-300">
                                {{ $arm->arm_title }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500">This title will be used for display and identification purposes</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-x-4">
                <a href="{{ route('arms.show', $arm->id) }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Update Arm</button>
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
        height: 42px; /* similar to form inputs */
        line-height: 40px;
        border: 1px solid #d1d5db; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        padding: 0 2.25rem 0 0.75rem; /* right space for arrow */
        background: #fff;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); /* shadow-sm */
        font-size: 0.875rem; /* text-sm */
        color: #111827; /* text-gray-900 */
    }
    .chosen-container-single .chosen-single span { margin-right: 0.5rem; }
    .chosen-container-single .chosen-single div { right: 0.5rem; }
    .chosen-container-active .chosen-single,
    .chosen-container .chosen-single:focus {
        border-color: #6366f1; /* indigo-500 */
        box-shadow: 0 0 0 1px #6366f1 inset, 0 0 0 1px rgba(99,102,241,0.2);
    }
    /* Error state for Chosen dropdowns */
    .chosen-container .chosen-single.border-red-500 {
        border-color: #ef4444 !important; /* red-500 */
        box-shadow: 0 0 0 1px #ef4444 inset, 0 0 0 1px rgba(239,68,68,0.2);
    }
    .chosen-container .chosen-search input {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0 0.75rem;
    }
    </style>

    <script>
        // Auto-generate arm title preview
        function updateArmTitle() {
            const makeSelect = document.getElementById('make');
            const caliberSelect = document.getElementById('arm_caliber_id');
            const typeSelect = document.getElementById('arm_type_id');
            const serialNo = document.getElementById('serial_no');
            
            const make = makeSelect ? makeSelect.options[makeSelect.selectedIndex]?.text : '';
            const caliber = caliberSelect ? caliberSelect.options[caliberSelect.selectedIndex]?.text : '';
            const type = typeSelect ? typeSelect.options[typeSelect.selectedIndex]?.text : '';
            const serial = serialNo ? serialNo.value : '';
            
            let title = '';
            if (make && caliber && type && serial) {
                title = `${make} ${caliber} ${type} (SN: ${serial})`;
            } else if (make || caliber || type || serial) {
                title = `${make || ''} ${caliber || ''} ${type || ''} ${serial ? `(SN: ${serial})` : ''}`.trim();
            } else {
                title = 'Arm title will be generated automatically based on the information provided above.';
            }
            
            document.getElementById('arm-title-preview').textContent = title;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize jQuery Chosen
            $('.chosen-select').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });
            
            // Handle error display for Chosen dropdowns
            $('.chosen-select').each(function() {
                var $select = $(this);
                var $errorContainer = $select.next('.chosen-error-container');
                
                // If there's a validation error, style the Chosen dropdown
                if ($errorContainer.find('p.text-red-600').length > 0) {
                    var $chosenContainer = $select.next('.chosen-container');
                    $chosenContainer.find('.chosen-single').addClass('border-red-500');
                }
            });
            
            // Add event listeners to all select elements for title generation
            document.getElementById('arm_type_id').addEventListener('change', updateArmTitle);
            document.getElementById('arm_category_id').addEventListener('change', updateArmTitle);
            document.getElementById('make').addEventListener('change', updateArmTitle);
            document.getElementById('arm_caliber_id').addEventListener('change', updateArmTitle);
            document.getElementById('arm_condition_id').addEventListener('change', updateArmTitle);
            document.getElementById('serial_no').addEventListener('input', updateArmTitle);

            // Initialize on page load
            updateArmTitle();
        });
    </script>
</x-app-layout>
