<x-app-layout>
    @section('title', 'Create General Voucher - Finance Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => '/general-vouchers', 'label' => 'General Vouchers'],
        ['url' => '#', 'label' => 'Create']
    ]" />

    <x-dynamic-heading title="Create General Voucher" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-4">

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

        <form method="POST" action="{{ route('general-vouchers.store') }}" enctype="multipart/form-data">
            @csrf

            @include('general_vouchers._form_fields')

            <!-- Form Actions -->
            <div class="flex items-center justify-start gap-3 mt-3 border-t pt-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Create Voucher
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
            const entryTypeRadios = document.querySelectorAll('input[name="entry_type"]');

            // Store current balances
            let currentBankBalance = 0;
            let currentPartyBalance = 0;

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

            // Add event listener for amount input
            amountInput.addEventListener('input', function() {
                // Optional: Add any real-time validation here if needed
            });

            // Add event listener for entry type selection
            entryTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (bankSelect.value) {
                        fetchBankBalance(bankSelect.value);
                    }
                    if (partySelect.value) {
                        fetchPartyBalance(partySelect.value);
                    }
                });
            });

            // Function to fetch party balance
            function fetchPartyBalance(partyId) {
                fetch(`/parties/${partyId}/balance`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.balance !== undefined) {
                            currentPartyBalance = parseFloat(data.balance);
                            showPartyBalance(data.formatted_balance, data.status);
                        } else {
                            currentPartyBalance = 0;
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
                fetch(`/banks/${bankId}/balance`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.balance !== undefined) {
                            currentBankBalance = parseFloat(data.balance);
                            showBankBalance(data.balance, data.status);
                        } else {
                            currentBankBalance = 0;
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
                
                const formattedBalance = Math.round(parseFloat(balance)).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                
                balanceSpan.textContent = formattedBalance;
                
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

            // Initialize bank balance for pre-selected bank (from old form data)
            if (bankSelect.value) {
                fetchBankBalance(bankSelect.value);
            }

            form.addEventListener('submit', function(e) {
                if (!bankSelect.value || !partySelect.value) {
                    e.preventDefault();
                    alert('Please select both Bank Account and Party.');
                    return;
                }

                const entryTypeSelected = Array.from(entryTypeRadios).some(radio => radio.checked);
                if (!entryTypeSelected) {
                    e.preventDefault();
                    alert('Please select an entry type.');
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
            newGroup.className = 'attachment-group';
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
    </script>
</x-app-layout>