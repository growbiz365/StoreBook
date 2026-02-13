<x-app-layout>
    @section('title', 'Create Expense - Expense Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/expenses/dashboard', 'label' => 'Expense dashboard'],
        ['url' => route('expenses.index'), 'label' => 'Expenses'],
        ['url' => '#', 'label' => 'Create']
    ]" />

    <x-dynamic-heading title="Create New Expense" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Expense Details</h2>
            <p class="text-sm text-gray-600">Fill in the information below to create a new expense record. This will automatically create bank ledger and journal entries.</p>
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

        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="createExpenseForm">
            @csrf

            <!-- Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date -->
                <div>
                    <x-input-label for="date_added">Date <span class="text-red-600">*</span></x-input-label>
                    <input type="date" id="date_added" name="date_added" 
                        value="{{ old('date_added', date('Y-m-d')) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('date_added')" class="mt-1" />
                </div>

                <!-- Expense Head -->
                <div>
                    <x-input-label for="chart_of_account_id">Expense Head <span class="text-red-600">*</span></x-input-label>
                    <select id="chart_of_account_id" name="chart_of_account_id" data-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 chosen-select">
                        <option value="">Select Expense Head</option>
                        @php
                            $expenseHeadGroupStarted = false;
                            $chartAccountGroupStarted = false;
                        @endphp
                        @foreach($expenseAccounts as $expenseAccount)
                            @php
                                $isExpenseHead = $expenseAccount['is_expense_head'] ?? false;
                            @endphp
                            @if($isExpenseHead && !$expenseHeadGroupStarted)
                                <optgroup label="Expense Heads (Created)">
                                @php $expenseHeadGroupStarted = true; @endphp
                            @elseif(!$isExpenseHead && !$chartAccountGroupStarted)
                                @if($expenseHeadGroupStarted)
                                    </optgroup>
                                @endif
                                <optgroup label="Chart of Accounts">
                                @php $chartAccountGroupStarted = true; @endphp
                            @endif
                            <option value="{{ $expenseAccount['id'] }}"
                                {{ old('chart_of_account_id') == $expenseAccount['id'] ? 'selected' : '' }}>
                                {{ $expenseAccount['name'] }}
                            </option>
                        @endforeach
                        @if($expenseHeadGroupStarted || $chartAccountGroupStarted)
                            </optgroup>
                        @endif
                    </select>
                    <div class="chosen-error-container">
                        <x-input-error :messages="$errors->get('chart_of_account_id')" class="mt-1" />
                        <p id="chart_of_account_client_error" class="mt-1 text-sm text-red-600 hidden">Please select an expense head.</p>
                    </div>
                </div>

                <!-- Bank -->
                <div>
                    <x-input-label for="bank_id">Bank Account <span class="text-red-600">*</span></x-input-label>
                    <select id="bank_id" name="bank_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Bank Account</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}"
                                {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
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

                <!-- Amount -->
                <div>
                    <x-input-label for="amount">Amount <span class="text-red-600">*</span></x-input-label>
                    <input type="number" id="amount" name="amount" step="1" min="1"
                        value="{{ old('amount') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>
            </div>

            <!-- Details -->
            <div class="mt-6">
                <x-input-label for="details">Details</x-input-label>
                <textarea id="details" name="details" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Optional expense details...">{{ old('details') }}</textarea>
                <x-input-error :messages="$errors->get('details')" class="mt-1" />
            </div>

            <!-- Attachments -->
            <div class="mt-6">
                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-900 mb-1">Attachments</h3>
                    <p class="text-xs text-gray-600">Upload relevant documents (receipts, invoices, etc.)</p>
                </div>

                <div id="attachments-container" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border rounded-md bg-gray-50">
                        <div>
                            <x-input-label for="attachment_title_1" class="text-xs">Title</x-input-label>
                            <x-text-input id="attachment_title_1" name="attachment_titles[]" type="text" class="mt-1 block w-full text-sm" placeholder="Document title" />
                        </div>
                        <div>
                            <x-input-label for="attachment_file_1" class="text-xs">File</x-input-label>
                            <input type="file" id="attachment_file_1" name="attachment_files[]" class="mt-1 block w-full text-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-xs text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="addAttachmentField()" 
                    class="mt-3 inline-flex items-center px-2 py-1 bg-gray-100 border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-200">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Attachment
                </button>
                <p class="mt-1 text-xs text-gray-500">Max 10MB per file</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-start gap-4 mt-8 border-t pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Create Expense
                </button>
                <a href="{{ route('expenses.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        let attachmentCount = 1;

        document.addEventListener('DOMContentLoaded', function() {
            const bankSelect = document.getElementById('bank_id');

            // Add event listener for bank selection
            bankSelect.addEventListener('change', function() {
                if (this.value) {
                    fetchBankBalance(this.value);
                } else {
                    hideBankBalance();
                }
            });

            // Initialize bank balance for pre-selected bank (from old form data)
            if (bankSelect.value) {
                fetchBankBalance(bankSelect.value);
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
                            showBankBalance(data.balance, data.status);
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
                
                const formattedBalance = parseFloat(balance).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
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
        });

        function addAttachmentField() {
            attachmentCount++;
            const container = document.getElementById('attachments-container');

            const newFields = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border rounded-md bg-gray-50">
                    <div>
                        <label for="attachment_title_${attachmentCount}" class="block text-xs font-medium text-gray-700">Title</label>
                        <input id="attachment_title_${attachmentCount}" name="attachment_titles[]" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Document title" />
                    </div>
                    <div>
                        <label for="attachment_file_${attachmentCount}" class="block text-xs font-medium text-gray-700">File</label>
                        <input type="file" id="attachment_file_${attachmentCount}" name="attachment_files[]" class="mt-1 block w-full text-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-xs text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Remove
                        </button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', newFields);
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <style>
        .chosen-container { width: 100% !important; }
        .chosen-container-single .chosen-single {
            height: 42px;
            line-height: 40px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0 2.25rem 0 0.75rem;
            background: #fff;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            font-size: 0.875rem;
            color: #111827;
        }
        .chosen-container-single .chosen-single span { margin-right: 0.5rem; }
        .chosen-container-single .chosen-single div { right: 0.5rem; }
        .chosen-container-active .chosen-single,
        .chosen-container .chosen-single:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 1px #6366f1 inset, 0 0 0 1px rgba(99,102,241,0.2);
        }
        .chosen-container .chosen-search input {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.5rem;
        }
        .chosen-container .chosen-results {
            max-height: 240px;
        }
        .chosen-container .chosen-results li.highlighted {
            background-color: #e0e7ff;
            color: #312e81;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && $('#chart_of_account_id').length) {
                const $expenseHeadSelect = $('#chart_of_account_id');
                const clientErrorEl = document.getElementById('chart_of_account_client_error');
                const form = document.getElementById('createExpenseForm');

                $expenseHeadSelect.chosen({
                    placeholder_text_single: 'Select Expense Head',
                    search_contains: true,
                    width: '100%'
                });

                const $chosenContainer = $expenseHeadSelect.next('.chosen-container');
                const serverErrorExists = $expenseHeadSelect.parent().find('.chosen-error-container .text-red-600').not('#chart_of_account_client_error').length > 0;

                if (serverErrorExists) {
                    highlightChosenError();
                }

                $expenseHeadSelect.on('change', function () {
                    if ($expenseHeadSelect.val()) {
                        hideChosenError();
                    }
                });

                form.addEventListener('submit', function (event) {
                    if (!$expenseHeadSelect.val()) {
                        event.preventDefault();
                        highlightChosenError();
                        clientErrorEl?.classList.remove('hidden');
                        $expenseHeadSelect.trigger('chosen:activate');
                        setTimeout(() => $expenseHeadSelect.trigger('chosen:open'), 0);
                    }
                });

                function highlightChosenError() {
                    $chosenContainer.find('.chosen-single').addClass('border-red-500');
                    clientErrorEl?.classList.remove('hidden');
                }

                function hideChosenError() {
                    $chosenContainer.find('.chosen-single').removeClass('border-red-500');
                    clientErrorEl?.classList.add('hidden');
                }
            }
        });
    </script>
</x-app-layout> 