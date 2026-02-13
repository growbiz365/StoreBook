<x-app-layout>
    @section('title', 'Create Party Transfer - Party Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/party-management', 'label' => 'Party Management'], ['url' => '/party-transfers', 'label' => 'Party Transfers'], ['url' => '#', 'label' => 'Add Transfer']]" />
    
    <x-dynamic-heading title="Add Party Transfer" />

    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-6">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Transfer Information</h2>
            <p class="text-sm text-gray-600 mb-6">Record party transfer details and upload relevant documents.</p>
        </div>

        <form action="{{ route('party-transfers.store') }}" method="POST" id="transferForm" enctype="multipart/form-data">
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

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-4">
                <!-- Date -->
                <div>
                    <x-input-label for="date" class="mb-2 text-sm font-bold text-gray-700">
                        Date <span class="text-red-600">*</span>
                    </x-input-label>
                    <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                    @error('date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Debit Party -->
                <div>
                    <x-input-label for="debit_party_id" class="mb-2 text-sm font-bold text-gray-700">
                        Debit (بنـــام) Party <span class="text-red-600">*</span>
                    </x-input-label>
                    <select id="debit_party_id" name="debit_party_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Debit Party</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('debit_party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                        @endforeach
                    </select>
                    <div id="debit_party_balance" class="mt-1 text-sm hidden">
                        <span class="font-medium">Balance:</span>
                        <span id="debit_balance_amount" class="ml-1"></span>
                    </div>
                    @error('debit_party_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Credit Party -->
                <div>
                    <x-input-label for="credit_party_id" class="mb-2 text-sm font-bold text-gray-700">
                        Credit (جمـــع) Party <span class="text-red-600">*</span>
                    </x-input-label>
                    <select id="credit_party_id" name="credit_party_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Credit Party</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('credit_party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                        @endforeach
                    </select>
                    <div id="credit_party_balance" class="mt-1 text-sm hidden">
                        <span class="font-medium">Balance:</span>
                        <span id="credit_balance_amount" class="ml-1"></span>
                    </div>
                    @error('credit_party_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Amount -->
                <div>
                    <x-input-label for="transfer_amount" class="mb-2 text-sm font-bold text-gray-700">
                        Amount <span class="text-red-600">*</span>
                    </x-input-label>
                    <input type="number" id="transfer_amount" name="transfer_amount" step="1" value="{{ old('transfer_amount') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="0" />
                    @error('transfer_amount') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Details -->
                <div>
                    <x-input-label for="details" class="mb-2 text-sm font-bold text-gray-700">Details</x-input-label>
                    <textarea id="details" name="details" rows="1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('details') }}</textarea>
                    @error('details') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Attachments Section -->
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <x-input-label value="Attachments" class="text-sm font-bold text-gray-700" />
                    <button type="button" onclick="addAttachmentField()" class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add More
                    </button>
                </div>
                
                <div id="attachments-container" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-lg bg-gray-50">
                        <div>
                            <x-input-label for="attachment_title_1" value="Document Title" class="mb-2 text-sm font-bold text-gray-700" />
                            <x-text-input id="attachment_title_1" name="attachment_titles[]" type="text" class="mt-1 block w-full" placeholder="Enter document title" />
                        </div>
                        <div>
                            <x-input-label for="attachment_file_1" value="Document File" class="mb-2 text-sm font-bold text-gray-700" />
                            <input type="file" id="attachment_file_1" name="attachment_files[]" class="mt-1 block w-full" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                        </div>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Upload relevant documents (max 10MB each)</p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-4 pt-4 mt-6 border-t">
                <button type="submit" id="submitBtn" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Save Transfer
                </button>
                <a href="{{ route('party-transfers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
let attachmentCount = 1;

function addAttachmentField() {
    attachmentCount++;
    const container = document.getElementById('attachments-container');

    const newFields = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-lg bg-gray-50">
            <div>
                <label for="attachment_title_${attachmentCount}" class="mb-2 text-sm font-bold text-gray-700">Document Title</label>
                <input id="attachment_title_${attachmentCount}" name="attachment_titles[]" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter document title" />
            </div>
            <div>
                <label for="attachment_file_${attachmentCount}" class="mb-2 text-sm font-bold text-gray-700">Document File</label>
                <input type="file" id="attachment_file_${attachmentCount}" name="attachment_files[]" class="mt-1 block w-full" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', newFields);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Party transfer form loaded');
    const form = document.getElementById('transferForm');
    const debitPartySelect = document.getElementById('debit_party_id');
    const creditPartySelect = document.getElementById('credit_party_id');
    const submitBtn = document.getElementById('submitBtn');

    // Add event listeners for party selection
    debitPartySelect.addEventListener('change', function() {
        if (this.value) {
            fetchPartyBalance(this.value, 'debit');
        } else {
            hidePartyBalance('debit');
        }
        filterCreditPartyOptions();
    });

    creditPartySelect.addEventListener('change', function() {
        if (this.value) {
            fetchPartyBalance(this.value, 'credit');
        } else {
            hidePartyBalance('credit');
        }
    });

    // Add event listener for date changes to refresh party balances
    const dateInput = document.getElementById('date');
    dateInput.addEventListener('change', function() {
        // Refresh balances for both parties if they are selected
        if (debitPartySelect.value) {
            fetchPartyBalance(debitPartySelect.value, 'debit');
        }
        if (creditPartySelect.value) {
            fetchPartyBalance(creditPartySelect.value, 'credit');
        }
    });

    // Filter Credit Party options on page load
    filterCreditPartyOptions();

    // Function to fetch party balance
    function fetchPartyBalance(partyId, type) {
        console.log(`Fetching balance for party ${partyId}, type: ${type}`);
        
        // Get the current date from the date input field
        const currentDate = document.getElementById('date').value;
        
        // Build the URL with date parameter
        const url = `/parties/${partyId}/balance${currentDate ? `?date=${currentDate}` : ''}`;
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Balance data:', data);
                if (data.balance !== undefined) {
                    showPartyBalance(type, data.formatted_balance, data.status);
                } else {
                    hidePartyBalance(type);
                }
            })
            .catch(error => {
                console.error('Error fetching party balance:', error);
                hidePartyBalance(type);
            });
    }

    // Function to show party balance
    function showPartyBalance(type, balance, status) {
        const balanceDiv = document.getElementById(`${type}_party_balance`);
        const balanceSpan = document.getElementById(`${type}_balance_amount`);
        
        balanceSpan.textContent = balance;
        
        // Set color based on balance status
        balanceSpan.className = 'ml-1 font-semibold';
        if (status === 'positive') {
            balanceSpan.classList.add('text-green-600');
        } else if (status === 'negative') {
            balanceSpan.classList.add('text-red-600');
        } else {
            balanceSpan.classList.add('text-gray-600');
        }
        
        balanceDiv.classList.remove('hidden');
    }

    // Function to hide party balance
    function hidePartyBalance(type) {
        const balanceDiv = document.getElementById(`${type}_party_balance`);
        balanceDiv.classList.add('hidden');
    }

    // Filter Credit Party options to exclude selected Debit Party
    function filterCreditPartyOptions() {
        const debitPartySelect = document.getElementById('debit_party_id');
        const creditPartySelect = document.getElementById('credit_party_id');
        const selectedDebitParty = debitPartySelect.value;
        
        // Reset all options to visible
        const creditOptions = creditPartySelect.querySelectorAll('option');
        creditOptions.forEach(option => {
            option.style.display = 'block';
            option.disabled = false;
        });
        
        // Hide and disable the selected Debit Party option
        if (selectedDebitParty) {
            const debitOption = creditPartySelect.querySelector(`option[value="${selectedDebitParty}"]`);
            if (debitOption) {
                debitOption.style.display = 'none';
                debitOption.disabled = true;
            }
            
            // If the currently selected Credit Party is the same as Debit Party, clear it
            if (creditPartySelect.value === selectedDebitParty) {
                creditPartySelect.value = '';
                hidePartyBalance('credit');
            }
        }
    }

    form.addEventListener('submit', function(e) {
        // Clear previous validation messages
        clearValidationMessages();
        
        let hasErrors = false;
        
        // Check if debit and credit parties are the same
        if (debitPartySelect.value && creditPartySelect.value && debitPartySelect.value === creditPartySelect.value) {
            showValidationMessage('Payer and Receiver cannot be the same party', 'error');
            hasErrors = true;
        }
        
        // Check if debit party is selected
        if (!debitPartySelect.value) {
            showValidationMessage('Please select a Debit Party', 'error');
            hasErrors = true;
        }
        
        // Check if credit party is selected
        if (!creditPartySelect.value) {
            showValidationMessage('Please select a Credit Party', 'error');
            hasErrors = true;
        }
        
        // Check if amount is entered
        const amountInput = document.getElementById('transfer_amount');
        if (!amountInput.value || parseFloat(amountInput.value) <= 0) {
            showValidationMessage('Please enter a valid amount', 'error');
            hasErrors = true;
        }
        
        if (hasErrors) {
            e.preventDefault();
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
        
        // Allow form to submit
        return true;
    });
    
    function showValidationMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `px-4 py-3 rounded relative mb-4 ${type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-green-100 border border-green-400 text-green-700'}`;
        alertDiv.innerHTML = `
            <strong class="font-bold">${type === 'error' ? 'Error!' : 'Success!'}</strong>
            <span class="block sm:inline">${message}</span>
        `;
        
        // Insert at the top of the form
        form.insertBefore(alertDiv, form.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    function clearValidationMessages() {
        const messages = form.querySelectorAll('.bg-red-100, .bg-green-100');
        messages.forEach(msg => msg.remove());
    }
});
</script> 