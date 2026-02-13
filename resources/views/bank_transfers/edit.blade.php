<x-app-layout>
    @section('title', 'Edit Bank Transfer - Bank Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/bank-management', 'label' => 'Bank Management'],
        ['url' => '/bank-transfers', 'label' => 'Bank Transfers'],
        ['url' => '#', 'label' => 'Edit']
    ]" />

    <x-dynamic-heading title="Edit Bank Transfer" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Transfer Details</h2>
            <p class="text-sm text-gray-600">Update the transfer information below.</p>
        </div>

        @if ($errors->any())
            <x-error-alert title="Whoops! Something went wrong.">
                <ul class="mt-2 text-sm list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-error-alert>
        @endif

        @if (session('error'))
            <x-error-alert message="{{ session('error') }}" />
        @endif

        <form method="POST" action="{{ route('bank-transfers.update', $bankTransfer) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- From Account -->
                <div>
                    <x-input-label for="from_account_id">From Account <span class="text-red-600">*</span></x-input-label>
                    <select id="from_account_id" name="from_account_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Source Account</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('from_account_id', $bankTransfer->from_account_id) == $bank->id ? 'selected' : '' }}>
                                {{ $bank->chartOfAccount->name ?? $bank->account_name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="from_account_balance" class="mt-1 text-sm hidden">
                        <span class="font-medium">Balance:</span>
                        <span id="from_balance_amount" class="ml-1"></span>
                    </div>
                    <x-input-error :messages="$errors->get('from_account_id')" class="mt-1" />
                </div>

                <!-- To Account -->
                <div>
                    <x-input-label for="to_account_id">To Account <span class="text-red-600">*</span></x-input-label>
                    <select id="to_account_id" name="to_account_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Destination Account</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('to_account_id', $bankTransfer->to_account_id) == $bank->id ? 'selected' : '' }}>
                                {{ $bank->chartOfAccount->name ?? $bank->account_name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="to_account_balance" class="mt-1 text-sm hidden">
                        <span class="font-medium">Balance:</span>
                        <span id="to_balance_amount" class="ml-1"></span>
                    </div>
                    <x-input-error :messages="$errors->get('to_account_id')" class="mt-1" />
                </div>

                <!-- Amount -->
                <div>
                    <x-input-label for="amount">Amount <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="amount" name="amount" type="number" step="1" class="mt-1 block w-full"
                        :value="old('amount', round($bankTransfer->amount))" required placeholder="0" />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                <!-- Transfer Date -->
                <div>
                    <x-input-label for="transfer_date">Transfer Date <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="transfer_date" name="transfer_date" type="date" class="mt-1 block w-full"
                        :value="old('transfer_date', $bankTransfer->transfer_date->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('transfer_date')" class="mt-1" />
                </div>

                <!-- Details -->
                <div class="md:col-span-2">
                    <x-input-label for="details">Details</x-input-label>
                    <textarea id="details" name="details" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Optional transfer details...">{{ old('details', $bankTransfer->details) }}</textarea>
                    <x-input-error :messages="$errors->get('details')" class="mt-1" />
                </div>

                <!-- Attachments -->
                <div class="md:col-span-2">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900">Attachments</h3>
                            <p class="text-xs text-gray-500">Upload relevant documents (PDF, Word, Images)</p>
                        </div>

                        @if($bankTransfer->attachments->count() > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Current Attachments</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($bankTransfer->attachments as $attachment)
                                        <div class="bg-gray-50 rounded-lg p-4 flex flex-col" data-attachment-id="{{ $attachment->id }}">
                                            <div class="flex items-center mb-2">
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span class="text-sm font-medium text-gray-900 truncate flex-1" title="{{ $attachment->original_name }}">
                                                    {{ $attachment->original_name }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-500 mb-2">
                                                {{ number_format(round($attachment->file_size / 1024), 0) }} KB
                                            </div>
                                            <div class="flex items-center justify-between mt-auto">
                                                <a href="{{ route('files.bank-transfer-attachments.download', $attachment) }}" target="_blank"
                                                    class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    View
                                                </a>
                                                <button onclick="deleteAttachment({{ $attachment->id }})" class="text-sm text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div id="attachments-container">
                            <div class="attachment-group mb-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="attachment_titles[]">Title</x-input-label>
                                        <x-text-input type="text" name="attachment_titles[]" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <x-input-label for="attachment_files[]">File</x-input-label>
                                        <input type="file" name="attachment_files[]"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addAttachmentField()"
                            class="mt-2 inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add More
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-start gap-4 mt-8 border-t pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Update Transfer
                </button>
                <a href="{{ route('bank-transfers.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const fromAccountSelect = document.getElementById('from_account_id');
            const toAccountSelect = document.getElementById('to_account_id');

            // Add event listeners for bank selection
            fromAccountSelect.addEventListener('change', function() {
                fetchBankBalance(this.value, 'from');
                filterToAccountOptions();
            });

            toAccountSelect.addEventListener('change', function() {
                fetchBankBalance(this.value, 'to');
            });

            // Fetch bank balance for pre-selected accounts (from existing transfer data)
            if (fromAccountSelect.value) {
                fetchBankBalance(fromAccountSelect.value, 'from');
            }
            if (toAccountSelect.value) {
                fetchBankBalance(toAccountSelect.value, 'to');
            }
            
            // Filter To Account options on page load
            filterToAccountOptions();

            form.addEventListener('submit', function(e) {
                if (fromAccountSelect.value === toAccountSelect.value) {
                    e.preventDefault();
                    alert('Source and destination accounts cannot be the same.');
                }
            });
        });

        function fetchBankBalance(bankId, type) {
            if (!bankId) {
                // Hide balance if no bank selected
                const balanceDiv = document.getElementById(`${type}_account_balance`);
                balanceDiv.classList.add('hidden');
                return;
            }

            console.log('Fetching balance for bank:', bankId, 'type:', type);
            
            fetch(`/banks/${bankId}/balance`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Balance data received:', data);
                    const balanceDiv = document.getElementById(`${type}_account_balance`);
                    const balanceAmount = document.getElementById(`${type}_balance_amount`);
                    
                    if (data.balance !== undefined) {
                        // Show current balance (no adjustment for transfer amount)
                        let currentBalance = parseFloat(data.balance);
                        
                        const formattedBalance = Math.round(currentBalance).toLocaleString('en-US', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                        
                        balanceAmount.textContent = formattedBalance;
                        balanceAmount.className = `ml-1 font-medium ${currentBalance >= 0 ? 'text-green-600' : 'text-red-600'}`;
                        balanceDiv.classList.remove('hidden');
                        
                        // Store balance for validation
                        balanceAmount.dataset.balance = currentBalance;
                        
                        console.log('Balance set:', currentBalance);
                    } else {
                        console.log('No balance data received');
                        balanceDiv.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching bank balance:', error);
                    const balanceDiv = document.getElementById(`${type}_account_balance`);
                    balanceDiv.classList.add('hidden');
                });
        }

        // Filter To Account options to exclude selected From Account
        function filterToAccountOptions() {
            const fromAccountSelect = document.getElementById('from_account_id');
            const toAccountSelect = document.getElementById('to_account_id');
            const selectedFromAccount = fromAccountSelect.value;
            
            // Reset all options to visible
            const toOptions = toAccountSelect.querySelectorAll('option');
            toOptions.forEach(option => {
                option.style.display = 'block';
                option.disabled = false;
            });
            
            // Hide and disable the selected From Account option
            if (selectedFromAccount) {
                const fromOption = toAccountSelect.querySelector(`option[value="${selectedFromAccount}"]`);
                if (fromOption) {
                    fromOption.style.display = 'none';
                    fromOption.disabled = true;
                }
                
                // If the currently selected To Account is the same as From Account, clear it
                if (toAccountSelect.value === selectedFromAccount) {
                    toAccountSelect.value = '';
                }
            }
        }

        // Validate transfer amount against available balance
        function validateTransferAmount() {
            const fromAccountSelect = document.getElementById('from_account_id');
            const amountInput = document.getElementById('amount');
            const fromBalanceAmount = document.getElementById('from_balance_amount');
            
            console.log('Validating transfer amount:', {
                fromAccount: fromAccountSelect.value,
                amount: amountInput.value,
                balance: fromBalanceAmount.dataset.balance
            });
            
            if (fromAccountSelect.value && amountInput.value && fromBalanceAmount.dataset.balance) {
                const currentBalance = parseFloat(fromBalanceAmount.dataset.balance);
                const transferAmount = parseFloat(amountInput.value);
                
                // Calculate available balance for editing (current balance + original transfer amount)
                const originalTransferAmount = {{ $bankTransfer->amount }};
                const availableBalance = currentBalance + originalTransferAmount;
                
                console.log('Balance check:', {
                    currentBalance,
                    originalTransferAmount,
                    availableBalance,
                    transferAmount,
                    isInsufficient: transferAmount > availableBalance
                });
                
                if (transferAmount > availableBalance) {
                    const formattedAvailable = Math.round(availableBalance).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                    
                    amountInput.setCustomValidity(`Insufficient balance. You can transfer up to ${formattedAvailable}.`);
                    amountInput.classList.add('border-red-500');
                    
                    // Show user-friendly error message
                    let errorDiv = document.getElementById('amount-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.id = 'amount-error';
                        errorDiv.className = 'mt-1 text-sm text-red-600';
                        amountInput.parentNode.appendChild(errorDiv);
                    }
                    errorDiv.innerHTML = `Insufficient balance. You can transfer up to <strong>${formattedAvailable}</strong> (current balance + original transfer amount).`;
                } else {
                    amountInput.setCustomValidity('');
                    amountInput.classList.remove('border-red-500');
                    
                    // Remove error message
                    const errorDiv = document.getElementById('amount-error');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            }
        }

        // Add event listeners for validation
        document.getElementById('amount').addEventListener('input', validateTransferAmount);
        document.getElementById('from_account_id').addEventListener('change', function() {
            // Re-validate when from account changes
            setTimeout(validateTransferAmount, 500);
        });

        function addAttachmentField() {
            const container = document.getElementById('attachments-container');
            const newGroup = document.createElement('div');
            newGroup.className = 'attachment-group mb-4';
            newGroup.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="attachment_titles[]">Title</x-input-label>
                        <x-text-input type="text" name="attachment_titles[]" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="attachment_files[]">File</x-input-label>
                        <input type="file" name="attachment_files[]"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                    <svg class="h-4 w-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Remove
                </button>
            `;
            container.appendChild(newGroup);
        }

        function deleteAttachment(attachmentId) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                fetch(`/bank-transfers/attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the attachment element from the DOM
                        const attachmentElement = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
                        if (attachmentElement) {
                            attachmentElement.remove();
                        }
                        // Reload the page to reflect changes
                        window.location.reload();
                    } else {
                        alert('Error deleting attachment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting attachment');
                });
            }
        }
    </script>
</x-app-layout>