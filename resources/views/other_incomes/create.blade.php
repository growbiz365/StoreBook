<x-app-layout>
    @section('title', 'Create Other Income - Income Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => route('other-incomes.index'), 'label' => 'Other Incomes'],
        ['url' => '#', 'label' => 'Create']
    ]" />

    <x-dynamic-heading title="Create Other Income" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Income Details</h2>
            <p class="text-sm text-gray-600">Fill in the information below to record other income.</p>
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

        <form method="POST" action="{{ route('other-incomes.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Income Date -->
                <div>
                    <x-input-label for="income_date">Income Date <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="income_date" name="income_date" type="date" class="mt-1 block w-full"
                        :value="old('income_date', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('income_date')" class="mt-1" />
                </div>

                <!-- Amount -->
                <div>
                    <x-input-label for="amount">Amount <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="amount" name="amount" type="number" step="1" min="1" class="mt-1 block w-full"
                        :value="old('amount')" required placeholder="0" />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                <!-- Bank Account -->
                <div>
                    <x-input-label for="bank_id">Bank Account <span class="text-red-600">*</span></x-input-label>
                    <select id="bank_id" name="bank_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Bank Account</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                {{ strtoupper($bank->chartOfAccount->name ?? $bank->account_name) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('bank_id')" class="mt-1" />
                </div>

                <!-- Income Account Head -->
                <div>
                    <x-input-label for="chart_of_account_id">Income Head <span class="text-red-600">*</span></x-input-label>
                    <select id="chart_of_account_id" name="chart_of_account_id" data-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm chosen-select">
                        <option value="">Select Income Head</option>
                        @php
                            $incomeHeadGroupStarted = false;
                            $chartAccountGroupStarted = false;
                        @endphp
                        @foreach($incomeAccounts as $account)
                            @php
                                $isIncomeHead = $account['is_income_head'] ?? false;
                                $accountId = $account['id'];
                                $accountName = $account['name'];
                                $accountCode = $account['code'] ?? '';
                            @endphp
                            @if($isIncomeHead && !$incomeHeadGroupStarted)
                                <optgroup label="Income Heads (Created)">
                                @php $incomeHeadGroupStarted = true; @endphp
                            @elseif(!$isIncomeHead && !$chartAccountGroupStarted)
                                @if($incomeHeadGroupStarted)
                                    </optgroup>
                                @endif
                                <optgroup label="Chart of Accounts">
                                @php $chartAccountGroupStarted = true; @endphp
                            @endif
                            <option value="{{ $accountId }}" {{ old('chart_of_account_id') == $accountId ? 'selected' : '' }}>
                                {{ $accountName }}@if($accountCode) ({{ $accountCode }})@endif
                            </option>
                        @endforeach
                        @if($incomeHeadGroupStarted || $chartAccountGroupStarted)
                            </optgroup>
                        @endif
                    </select>
                    <div class="chosen-error-container">
                    <x-input-error :messages="$errors->get('chart_of_account_id')" class="mt-1" />
                        <p id="income_head_client_error" class="mt-1 text-sm text-red-600 hidden">Please select an income head.</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <x-input-label for="description">Description</x-input-label>
                    <textarea id="description" name="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Describe the source of income">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <!-- Attachments -->
                <div class="md:col-span-2">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900">Attachments</h3>
                            <p class="text-xs text-gray-500">Upload relevant documents (PDF, Word, Images)</p>
                        </div>

                        <div id="attachments-container">
                            <div class="attachment-group mb-4">
                                @include('other_incomes._attachment_fields')
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
                    Create Income
                </button>
                <a href="{{ route('other-incomes.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
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
                const $incomeHeadSelect = $('#chart_of_account_id');
                const clientErrorEl = document.getElementById('income_head_client_error');
                const form = document.querySelector('form');

                $incomeHeadSelect.chosen({
                    placeholder_text_single: 'Select Income Head',
                    search_contains: true,
                    width: '100%'
                });

                const $chosenContainer = $incomeHeadSelect.next('.chosen-container');
                const serverErrorExists = $incomeHeadSelect.parent().find('.chosen-error-container .text-red-600').not('#income_head_client_error').length > 0;

                if (serverErrorExists) {
                    highlightChosenError();
                }

                $incomeHeadSelect.on('change', function () {
                    if ($incomeHeadSelect.val()) {
                        hideChosenError();
                    }
                });

                form.addEventListener('submit', function (event) {
                    if (!$incomeHeadSelect.val()) {
                        event.preventDefault();
                        highlightChosenError();
                        clientErrorEl?.classList.remove('hidden');
                        $incomeHeadSelect.trigger('chosen:activate');
                        setTimeout(() => $incomeHeadSelect.trigger('chosen:open'), 0);
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