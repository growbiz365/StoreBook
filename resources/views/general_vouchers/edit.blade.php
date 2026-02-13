<x-app-layout>
    @section('title', 'Edit General Voucher - Finance Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => '/general-vouchers', 'label' => 'General Vouchers'],
        ['url' => '#', 'label' => 'Edit']
    ]" />

    <x-dynamic-heading title="Edit General Voucher" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Voucher Details</h2>
            <p class="text-sm text-gray-600">Update the voucher information below.</p>
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

        <form method="POST" action="{{ route('general-vouchers.update', $generalVoucher) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bank Account -->
                <div>
                    <x-input-label for="bank_id">Bank Account <span class="text-red-600">*</span></x-input-label>
                    <select id="bank_id" name="bank_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Bank Account</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id', $generalVoucher->bank_id) == $bank->id ? 'selected' : '' }}>
                                {{ strtoupper($bank->chartOfAccount->name ?? $bank->account_name) }}
                            </option>
                        @endforeach
                    </select>
                    <div id="bank_balance" class="mt-1 text-sm hidden">
                        <span class="font-medium">Balance:</span>
                        <span id="balance_amount" class="ml-1"></span>
                    </div>
                    <x-input-error :messages="$errors->get('bank_id')" class="mt-1" />
                </div>

                <!-- Party -->
                <div>
                    <x-input-label for="party_id">Party <span class="text-red-600">*</span></x-input-label>
                    <select id="party_id" name="party_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Party</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id', $generalVoucher->party_id) == $party->id ? 'selected' : '' }}>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="party_balance" class="mt-1 text-sm hidden">
                        <span class="font-medium">Balance:</span>
                        <span id="party_balance_amount" class="ml-1"></span>
                    </div>
                    <x-input-error :messages="$errors->get('party_id')" class="mt-1" />
                </div>

                <!-- Entry Type -->
                <div>
                    <x-input-label for="entry_type">Entry Type <span class="text-red-600">*</span></x-input-label>
                    <select id="entry_type" name="entry_type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Entry Type</option>
                        <option value="debit" {{ old('entry_type', $generalVoucher->entry_type) == 'debit' ? 'selected' : '' }}>Debit (بنـــام)</option>
                        <option value="credit" {{ old('entry_type', $generalVoucher->entry_type) == 'credit' ? 'selected' : '' }}>Credit (جمـــع)</option>
                    </select>
                    <x-input-error :messages="$errors->get('entry_type')" class="mt-1" />
                </div>

                <!-- Amount -->
                <div>
                    <x-input-label for="amount">Amount <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="amount" name="amount" type="number" step="1" class="mt-1 block w-full"
                        :value="old('amount', round($generalVoucher->amount))" required placeholder="0" />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                

                <!-- Entry Date -->
                <div>
                    <x-input-label for="entry_date">Entry Date <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="entry_date" name="entry_date" type="date" class="mt-1 block w-full"
                        :value="old('entry_date', $generalVoucher->entry_date->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('entry_date')" class="mt-1" />
                </div>

                <!-- Details -->
                <div class="md:col-span-2">
                    <x-input-label for="details">Details</x-input-label>
                    <textarea id="details" name="details" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Optional voucher details...">{{ old('details', $generalVoucher->details) }}</textarea>
                    <x-input-error :messages="$errors->get('details')" class="mt-1" />
                </div>

                <!-- Attachments -->
                <div class="md:col-span-2">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900">Attachments</h3>
                            <p class="text-xs text-gray-500">Upload relevant documents (PDF, Word, Images)</p>
                        </div>

                        <!-- Existing Attachments -->
                        @if($generalVoucher->attachments->isNotEmpty())
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Current Attachments</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($generalVoucher->attachments as $attachment)
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
                                                <a href="{{ route('files.general-voucher-attachments.download', $attachment) }}" target="_blank"
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

                        <!-- New Attachments -->
                        <div id="attachments-container">
                            <div class="attachment-group mb-4">
                                @include('general_vouchers._attachment_fields')
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
                    Update Voucher
                </button>
                <a href="{{ route('general-vouchers.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const bankSelect = document.getElementById('bank_id');
            const partySelect = document.getElementById('party_id');
            const amountInput = document.getElementById('amount');
            const entryTypeSelect = document.getElementById('entry_type');

            // Add event listener for bank selection
            bankSelect.addEventListener('change', function() {
                if (this.value) {
                    fetchBankBalance(this.value);
                } else {
                    hideBankBalance();
                }
            });

            // Add event listener for party selection
            partySelect.addEventListener('change', function() {
                if (this.value) {
                    fetchPartyBalance(this.value);
                } else {
                    hidePartyBalance();
                }
            });

            // Add event listener for entry type selection
            entryTypeSelect.addEventListener('change', function() {
                // Refresh balances when entry type changes
                if (bankSelect.value) {
                    fetchBankBalance(bankSelect.value);
                }
                if (partySelect.value) {
                    fetchPartyBalance(partySelect.value);
                }
            });

            // Load initial balances if already selected
            if (bankSelect.value) {
                fetchBankBalance(bankSelect.value);
            }
            if (partySelect.value) {
                fetchPartyBalance(partySelect.value);
            }

            // Function to fetch party balance
            function fetchPartyBalance(partyId) {
                if (!partyId) {
                    hidePartyBalance();
                    return;
                }

                fetch(`/parties/${partyId}/balance`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.balance !== undefined) {
                            let currentBalance = parseFloat(data.balance);
                            
                            const formattedBalance = Math.round(currentBalance).toLocaleString('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            
                            showPartyBalance(formattedBalance, currentBalance >= 0 ? 'positive' : 'negative');
                        } else {
                            hidePartyBalance();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching party balance:', error);
                        hidePartyBalance();
                    });
            }

            // Function to show party balance
            function showPartyBalance(balance, status) {
                const balanceDiv = document.getElementById('party_balance');
                const balanceSpan = document.getElementById('party_balance_amount');
                
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
            function hidePartyBalance() {
                const balanceDiv = document.getElementById('party_balance');
                balanceDiv.classList.add('hidden');
            }

            // Function to fetch bank balance
            function fetchBankBalance(bankId) {
                if (!bankId) {
                    hideBankBalance();
                    return;
                }

                fetch(`/banks/${bankId}/balance`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.balance !== undefined) {
                            let currentBalance = parseFloat(data.balance);
                            
                            const formattedBalance = Math.round(currentBalance).toLocaleString('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            
                            showBankBalance(formattedBalance, currentBalance >= 0 ? 'positive' : 'negative');
                        } else {
                            hideBankBalance();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching bank balance:', error);
                        hideBankBalance();
                    });
            }

            // Function to show bank balance
            function showBankBalance(balance, status) {
                const balanceDiv = document.getElementById('bank_balance');
                const balanceSpan = document.getElementById('balance_amount');
                
                balanceSpan.textContent = balance;
                
                // Set color based on balance status
                balanceSpan.className = 'ml-1 font-medium';
                if (status === 'positive') {
                    balanceSpan.classList.add('text-green-600');
                } else if (status === 'negative') {
                    balanceSpan.classList.add('text-red-600');
                } else {
                    balanceSpan.classList.add('text-gray-600');
                }
                
                balanceDiv.classList.remove('hidden');
            }

            // Function to hide bank balance
            function hideBankBalance() {
                const balanceDiv = document.getElementById('bank_balance');
                balanceDiv.classList.add('hidden');
            }

            // Simple form submission validation
            form.addEventListener('submit', function(e) {
                if (!bankSelect.value || !partySelect.value) {
                    e.preventDefault();
                    alert('Please select both Bank Account and Party.');
                    return;
                }
                
                const amount = parseFloat(amountInput.value) || 0;
                if (amount <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid amount.');
                    return;
                }
            });
        });

        function addAttachmentField() {
            const container = document.getElementById('attachments-container');
            const newGroup = document.createElement('div');
            newGroup.className = 'attachment-group mb-4';
            newGroup.innerHTML = `
                @include('general_vouchers._attachment_fields')
                <button type="button" onclick="this.parentElement.remove()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                    <svg class="h-4 w-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Remove
                </button>
            `;
            container.appendChild(newGroup);
        }

        function deleteAttachment(id) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                fetch(`/general-vouchers/attachments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
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