<x-app-layout>
    @section('title', 'Add Arms Opening Stock - Arms Management')
    
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/arms-dashboard', 'label' => 'Arms Dashboard'],['url' => route('arms.opening-stock'), 'label' => 'Arms opening stock'],['url' => '#', 'label' => 'Add Arms Opening Stock']]" />

    <x-dynamic-heading title="Add Arms Opening Stock" />

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    <form action="{{ route('arms.store') }}" method="POST" id="arms-form">
        @csrf
        @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Whoops! Something went wrong.</strong>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Add Arms Opening Stock</h3>
                <div class="flex gap-2">
                    <a href="{{ route('arms.dashboard') }}" class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-500">Cancel</a>
                    <button type="button" id="back-to-previous" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 hidden">
                        ← Back to Previous
                    </button>
                    <button type="button" id="add-another-arm" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                        + Add Another Arm
                    </button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                        Save Arms
                    </button>
                </div>
            </div>
            
            <!-- Arm Counter and Saved Arms -->
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-800">Arms to be added:</span>
                    <div class="flex items-center gap-2">
                        <span id="arm-count" class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-bold">1</span>
                        <span class="text-sm text-blue-600">arm(s)</span>
                    </div>
                </div>
            </div>
            
            <!-- Saved Arms List -->
            <div id="saved-arms-container" class="mb-4 hidden">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Saved Arms:</h4>
                <div id="saved-arms-list" class="space-y-2">
                    <!-- Saved arms will be displayed here -->
                </div>
            </div>
            
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
                                <option value="{{ $armType->id }}" {{ old('arm_type_id') == $armType->id ? 'selected' : '' }}>
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
                                <option value="{{ $armCategory->id }}" {{ old('arm_category_id') == $armCategory->id ? 'selected' : '' }}>
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
                                <option value="{{ $armMake->id }}" {{ old('make') == $armMake->id ? 'selected' : '' }}>
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
                                <option value="{{ $armCaliber->id }}" {{ old('arm_caliber_id') == $armCaliber->id ? 'selected' : '' }}>
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
                                <option value="{{ $armCondition->id }}" {{ old('arm_condition_id') == $armCondition->id ? 'selected' : '' }}>
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
                            <input type="text" name="serial_no" id="serial_no" value="{{ old('serial_no') }}" required 
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
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required step="1" min="0"
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
                            <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" required step="1" min="0"
                                   class="mt-1 text-sm border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm w-full pl-12 @error('sale_price') border-red-500 @enderror"
                                   placeholder="0.00" />
                        </div>
                        @error('sale_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="purchase_date">Opening Date<span class="text-red-500">*</span></x-input-label>
                            <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required 
                               class="mt-1 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full @error('purchase_date') border-red-500 @enderror" />
                            @error('purchase_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <!-- Status field hidden - defaulting to 'available' -->
                    <input type="hidden" name="status" value="available">
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
                                      placeholder="Additional notes about the arm...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="arm-title-preview">Auto-generated Arm Title</x-input-label>
                        <div id="arm-title-preview" class="mt-1 text-sm text-gray-700 font-mono bg-gray-50 px-3 py-2 rounded-md border border-gray-300">
                                Arm title will be generated automatically based on the information provided above.
                            </div>
                            <p class="mt-1 text-xs text-gray-500">This title will be used for display and identification purposes</p>
                    </div>
                </div>
            </div>
            
            <!-- Hidden field to store arm data for multiple arms -->
            <input type="hidden" id="arm-data" name="arm_data" value="">
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
        // Single form with data storage functionality
        let armCount = 1;
        let armData = [];
        let currentFormData = {};
        let currentArmIndex = -1; // -1 means we're on a new arm, 0+ means we're editing an existing arm
        
        // Load saved data from localStorage on page load
        function loadSavedData() {
            try {
                const savedArmData = localStorage.getItem('arms_form_data');
                const savedArmCount = localStorage.getItem('arms_form_count');
                
                if (savedArmData) {
                    armData = JSON.parse(savedArmData);
                    updateSavedArmsDisplay();
                }
                
                if (savedArmCount) {
                    armCount = parseInt(savedArmCount);
                    document.getElementById('arm-count').textContent = armCount;
                }
                
                // Update back button visibility based on saved data
                updateBackButtonVisibility();
            } catch (error) {
                console.error('Error loading saved data:', error);
                // Reset to defaults if there's an error
                armData = [];
                armCount = 1;
                updateBackButtonVisibility();
            }
        }
        
        // Save data to localStorage
        function saveToLocalStorage() {
            try {
                localStorage.setItem('arms_form_data', JSON.stringify(armData));
                localStorage.setItem('arms_form_count', armCount.toString());
            } catch (error) {
                console.error('Error saving to localStorage:', error);
            }
        }
        
        // Clear localStorage
        function clearLocalStorage() {
            try {
                localStorage.removeItem('arms_form_data');
                localStorage.removeItem('arms_form_count');
            } catch (error) {
                console.error('Error clearing localStorage:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Load saved data from localStorage
            loadSavedData();
            
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
            
            // Add event listeners
            document.getElementById('add-another-arm').addEventListener('click', addAnotherArm);
            document.getElementById('back-to-previous').addEventListener('click', backToPrevious);
            document.getElementById('serial_no').addEventListener('input', updateArmTitle);
            
            // Add event listeners to all select elements for title generation
            document.getElementById('arm_type_id').addEventListener('change', updateArmTitle);
            document.getElementById('arm_category_id').addEventListener('change', updateArmTitle);
            document.getElementById('make').addEventListener('change', updateArmTitle);
            document.getElementById('arm_caliber_id').addEventListener('change', updateArmTitle);
            document.getElementById('arm_condition_id').addEventListener('change', updateArmTitle);
            
            // Add event listeners to clear error highlighting when user starts typing
            const formFields = document.querySelectorAll('input, select, textarea');
            formFields.forEach(field => {
                field.addEventListener('input', clearErrorHighlighting);
                field.addEventListener('change', clearErrorHighlighting);
            });
            
            // Initialize title
            updateArmTitle();
        });

        async function checkSerialNumberExists(serialNo) {
            try {
                const response = await fetch(`/api/arms/check-serial?serial_no=${encodeURIComponent(serialNo)}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    console.error('Error checking serial number:', response.statusText);
                    return false; // If we can't check, allow it to proceed (server will catch it)
                }
                
                const data = await response.json();
                return data.exists || false;
            } catch (error) {
                console.error('Error checking serial number:', error);
                return false; // If we can't check, allow it to proceed (server will catch it)
            }
        }

        async function validateCurrentForm() {
            // Get all required field values
            const armTypeId = document.getElementById('arm_type_id').value;
            const armCategoryId = document.getElementById('arm_category_id').value;
            const make = document.getElementById('make').value;
            const armCaliberId = document.getElementById('arm_caliber_id').value;
            const armConditionId = document.getElementById('arm_condition_id').value;
            const serialNo = document.getElementById('serial_no').value;
            const purchasePrice = document.getElementById('purchase_price').value;
            const salePrice = document.getElementById('sale_price').value;
            const purchaseDate = document.getElementById('purchase_date').value;
            
            // Clear previous error highlighting
            clearErrorHighlighting();
            
            // Check if any required field is empty and highlight them
            let missingFields = [];
            
            if (!armTypeId) {
                missingFields.push('Arm Type');
                highlightField('#arm_type_id');
            }
            if (!armCategoryId) {
                missingFields.push('Arm Category');
                highlightField('#arm_category_id');
            }
            if (!make) {
                missingFields.push('Make');
                highlightField('#make');
            }
            if (!armCaliberId) {
                missingFields.push('Caliber');
                highlightField('#arm_caliber_id');
            }
            if (!armConditionId) {
                missingFields.push('Condition');
                highlightField('#arm_condition_id');
            }
            if (!serialNo.trim()) {
                missingFields.push('Serial Number');
                highlightField('#serial_no');
            }
            if (!purchasePrice) {
                missingFields.push('Purchase Price');
                highlightField('#purchase_price');
            }
            if (!salePrice) {
                missingFields.push('Sale Price');
                highlightField('#sale_price');
            }
            if (!purchaseDate) {
                missingFields.push('Purchase Date');
                highlightField('#purchase_date');
            }
            
            if (missingFields.length > 0) {
                showTemporaryMessage(`Missing: ${missingFields.join(', ')}`, 'error');
                return false;
            }
            
            // Check if serial number already exists in saved arms (current session)
            const existingSerial = armData.find(arm => arm.serial_no === serialNo.trim());
            if (existingSerial) {
                highlightField('#serial_no');
                showTemporaryMessage(`Serial no "${serialNo}" already used`, 'error');
                return false;
            }
            
            // Check if serial number already exists in database
            if (await checkSerialNumberExists(serialNo.trim())) {
                highlightField('#serial_no');
                showTemporaryMessage(`Serial no "${serialNo}" exists in database`, 'error');
                return false;
            }
            
            // Validate numeric values
            if (isNaN(parseFloat(purchasePrice)) || parseFloat(purchasePrice) < 0) {
                highlightField('#purchase_price');
                showTemporaryMessage('Invalid purchase price', 'error');
                return false;
            }
            
            if (isNaN(parseFloat(salePrice)) || parseFloat(salePrice) < 0) {
                highlightField('#sale_price');
                showTemporaryMessage('Invalid sale price', 'error');
                return false;
            }
            
            return true;
        }

        function highlightField(selector) {
            const field = document.querySelector(selector);
            if (field) {
                field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                field.focus();
            }
        }

        function clearErrorHighlighting() {
            const fields = document.querySelectorAll('input, select, textarea');
            fields.forEach(field => {
                field.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
            });
        }

        async function addAnotherArm() {
            // Show loading indicator
            const addButton = document.getElementById('add-another-arm');
            const originalText = addButton.textContent;
            addButton.textContent = 'Checking...';
            addButton.disabled = true;
            
            try {
                // Validate current form data before saving
                if (!(await validateCurrentForm())) {
                    // Don't proceed - just show error and return
                    return;
                }
                
                // Check for duplicate serial numbers before saving
                const currentSerialNo = document.getElementById('serial_no').value.trim();
                const existingSerial = armData.find(arm => arm.serial_no === currentSerialNo);
                if (existingSerial) {
                    highlightField('#serial_no');
                    showTemporaryMessage(`Serial no "${currentSerialNo}" has already been used`, 'error');
                    return;
                }
                
                // Save current form data only if validation passes
                saveCurrentFormData();
                
                // Clear only the serial number field
                document.getElementById('serial_no').value = '';
                
                // Update arm count
                armCount++;
                document.getElementById('arm-count').textContent = armCount;
                
                // Update current arm index to indicate we're on a new arm
                currentArmIndex = -1;
                
                // Show back button if we have saved arms
                updateBackButtonVisibility();
                
                // Update button text
                updateAddButtonText();
                
                // Update arm title
                updateArmTitle();
                
                // Show success message
                const message = currentArmIndex >= 0 ? 
                    `Arm edited and saved. Please enter serial number for Arm #${armCount}.` : 
                    `Arm #${armCount - 1} data saved. Please enter serial number for Arm #${armCount}.`;
                showTemporaryMessage(message, 'success');
                
            } finally {
                // Restore button state
                addButton.textContent = originalText;
                addButton.disabled = false;
            }
        }

        function saveCurrentFormData(forceSave = false) {
            const formData = {
                arm_type_id: document.getElementById('arm_type_id').value,
                arm_category_id: document.getElementById('arm_category_id').value,
                make: document.getElementById('make').value,
                arm_caliber_id: document.getElementById('arm_caliber_id').value,
                arm_condition_id: document.getElementById('arm_condition_id').value,
                serial_no: document.getElementById('serial_no').value,
                purchase_price: document.getElementById('purchase_price').value,
                sale_price: document.getElementById('sale_price').value,
                purchase_date: document.getElementById('purchase_date').value,
                status: document.querySelector('input[name="status"]').value,
                notes: document.getElementById('notes').value,
                // Store display values for dropdowns
                arm_type_display: document.getElementById('arm_type_id').options[document.getElementById('arm_type_id').selectedIndex]?.text || '',
                arm_category_display: document.getElementById('arm_category_id').options[document.getElementById('arm_category_id').selectedIndex]?.text || '',
                make_display: document.getElementById('make').options[document.getElementById('make').selectedIndex]?.text || '',
                arm_caliber_display: document.getElementById('arm_caliber_id').options[document.getElementById('arm_caliber_id').selectedIndex]?.text || '',
                arm_condition_display: document.getElementById('arm_condition_id').options[document.getElementById('arm_condition_id').selectedIndex]?.text || '',
            };
            
            // Save if serial number is not empty OR if forceSave is true (for form submission)
            if (formData.serial_no.trim() !== '' || forceSave) {
                armData.push(formData);
                updateSavedArmsDisplay();
                saveToLocalStorage(); // Save to localStorage
            }
        }

        function updateSavedArmsDisplay() {
            const container = document.getElementById('saved-arms-container');
            const list = document.getElementById('saved-arms-list');
            
            if (armData.length === 0) {
                container.classList.add('hidden');
                return;
            }
            
            container.classList.remove('hidden');
            list.innerHTML = '';
            
            armData.forEach((arm, index) => {
                const armDiv = document.createElement('div');
                armDiv.className = 'flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm';
                armDiv.innerHTML = `
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${arm.make_display} ${arm.arm_caliber_display} ${arm.arm_type_display}</div>
                        <div class="text-sm text-gray-600">Serial: ${arm.serial_no} | Price: PKR ${arm.purchase_price} | Sale: PKR ${arm.sale_price}</div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="editArm(${index})" class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                        Edit
                    </button>
                        <button type="button" onclick="removeArm(${index})" class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1 rounded hover:bg-red-50 transition-colors">
                        Remove
                    </button>
                    </div>
                `;
                list.appendChild(armDiv);
            });
        }
        
        
        function editArm(index) {
            if (index < 0 || index >= armData.length) {
                showTemporaryMessage('Invalid arm index.', 'error');
                return;
            }
            
            const arm = armData[index];
            
            // Populate form with the selected arm's data
            document.getElementById('arm_type_id').value = arm.arm_type_id;
            document.getElementById('arm_category_id').value = arm.arm_category_id;
            document.getElementById('make').value = arm.make;
            document.getElementById('arm_caliber_id').value = arm.arm_caliber_id;
            document.getElementById('arm_condition_id').value = arm.arm_condition_id;
            document.getElementById('serial_no').value = arm.serial_no;
            document.getElementById('purchase_price').value = arm.purchase_price ? arm.purchase_price : '';
            document.getElementById('sale_price').value = arm.sale_price ? arm.sale_price : '';
            document.getElementById('purchase_date').value = arm.purchase_date;
            document.getElementById('notes').value = arm.notes || '';
            
            // Remove the arm from saved data (it will be re-added when saved again)
            armData.splice(index, 1);
            
            // Update arm count
            armCount = Math.max(1, armData.length + 1);
            document.getElementById('arm-count').textContent = armCount;
            
            // Update current arm index to indicate we're editing
            currentArmIndex = index;
            
            // Update saved arms display
            updateSavedArmsDisplay();
            
            // Update back button visibility
            updateBackButtonVisibility();
            
            // Update button text
            updateAddButtonText();
            
            // Update title
            updateArmTitle();
            
            // Save to localStorage
            saveToLocalStorage();
            
            showTemporaryMessage(`Editing arm: ${arm.make_display} ${arm.arm_caliber_display} (Serial: ${arm.serial_no})`, 'success');
        }
        
        function removeArm(index) {
            if (confirm('Are you sure you want to remove this arm?')) {
                armData.splice(index, 1);
                updateSavedArmsDisplay();
                
                // Update arm count
                armCount = Math.max(1, armData.length + 1);
                document.getElementById('arm-count').textContent = armCount;
                
                // Update back button visibility
                updateBackButtonVisibility();
                
                // Save to localStorage
                saveToLocalStorage();
                
                showTemporaryMessage('Arm removed successfully.', 'info');
            }
        }

        function backToPrevious() {
                if (armData.length === 0) {
                showTemporaryMessage('No previous arms to go back to.', 'error');
                return;
            }
            
            // Get the last saved arm
            const lastArm = armData[armData.length - 1];
            
            // Populate form with the last arm's data
            document.getElementById('arm_type_id').value = lastArm.arm_type_id;
            document.getElementById('arm_category_id').value = lastArm.arm_category_id;
            document.getElementById('make').value = lastArm.make;
            document.getElementById('arm_caliber_id').value = lastArm.arm_caliber_id;
            document.getElementById('arm_condition_id').value = lastArm.arm_condition_id;
            document.getElementById('serial_no').value = lastArm.serial_no;
            document.getElementById('purchase_price').value = lastArm.purchase_price ? lastArm.purchase_price : '';
            document.getElementById('sale_price').value = lastArm.sale_price ? lastArm.sale_price : '';
            document.getElementById('purchase_date').value = lastArm.purchase_date;
            document.getElementById('notes').value = lastArm.notes || '';
            
            // Remove the last arm from saved data
            armData.pop();
            
            // Update arm count
            armCount = Math.max(1, armData.length + 1);
            document.getElementById('arm-count').textContent = armCount;
            
            // Update current arm index
            currentArmIndex = armData.length - 1;
            
            // Update saved arms display
            updateSavedArmsDisplay();
            
            // Update back button visibility
            updateBackButtonVisibility();
            
            // Update button text
            updateAddButtonText();
            
            // Update title
            updateArmTitle();
            
            // Save to localStorage
            saveToLocalStorage();
            
            showTemporaryMessage('Returned to previous arm. You can now edit or submit.', 'success');
        }

        function updateBackButtonVisibility() {
            const backButton = document.getElementById('back-to-previous');
            
            if (armData.length > 0) {
                backButton.classList.remove('hidden');
            } else {
                backButton.classList.add('hidden');
            }
        }

        function updateAddButtonText() {
            const addButton = document.getElementById('add-another-arm');
            if (currentArmIndex >= 0) {
                addButton.textContent = 'Save Changes';
                addButton.className = 'bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700';
            } else {
                addButton.textContent = '+ Add Another Arm';
                addButton.className = 'bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700';
            }
        }



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
        
        function showTemporaryMessage(message, type = 'info') {
            // Remove existing message
            const existingMessage = document.querySelector('.temp-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Create new message
            const messageDiv = document.createElement('div');
            messageDiv.className = `temp-message fixed top-4 right-4 px-4 py-2 rounded text-sm text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            }`;
            messageDiv.textContent = message;
            
            document.body.appendChild(messageDiv);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 3000);
        }

        // Override form submission to include stored arm data
        document.getElementById('arms-form').addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent default submission
            
            // Validate current form data before submission
            if (!(await validateCurrentForm())) {
                return false;
            }
            
            // Check for duplicate serial numbers in the complete arm data (including current form)
            const currentSerialNo = document.getElementById('serial_no').value.trim();
            
            // Ensure current form has a serial number
            if (!currentSerialNo) {
                highlightField('#serial_no');
                showTemporaryMessage('Please enter a serial number', 'error');
                return false;
            }
            
            const allSerials = armData.map(arm => arm.serial_no);
            
            // Add current form's serial number to the list for checking
            allSerials.push(currentSerialNo);
            
            // Check for duplicates in the complete list
            const duplicateSerials = allSerials.filter((serial, index) => allSerials.indexOf(serial) !== index);
            if (duplicateSerials.length > 0) {
                showTemporaryMessage(`Duplicate serial nos: ${duplicateSerials.join(', ')}`, 'error');
                return false;
            }
            
            // Save current form data first (force save for form submission)
            saveCurrentFormData(true);
            
            // Validate that we have at least one arm (including the current form)
            if (armData.length === 0) {
                showTemporaryMessage('Please add at least one arm', 'error');
                return false;
            }
            
            // Set the arm data in hidden field
            document.getElementById('arm-data').value = JSON.stringify(armData);
            
            // Now submit the form
            this.submit();
            
            // Clear localStorage on successful form submission
            setTimeout(() => {
                clearLocalStorage();
            }, 100);
        });
    </script>

    <style>
        /* Button styling */
        #add-another-arm, #back-to-previous {
            transition: all 0.2s ease;
        }
        
        #add-another-arm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(34, 197, 94, 0.3);
        }
        
        #back-to-previous:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
        }
        
        /* Focus styling for serial number input */
        #serial_no:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        /* Arm counter styling */
        #arm-count {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        /* Temporary message styling */
        .temp-message {
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</x-app-layout>
