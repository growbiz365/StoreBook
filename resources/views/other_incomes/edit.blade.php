<x-app-layout>
    @section('title', 'Edit Other Income - Income Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => route('other-incomes.index'), 'label' => 'Other Incomes'],
        ['url' => route('other-incomes.show', $otherIncome), 'label' => 'Income #' . $otherIncome->id],
        ['url' => '#', 'label' => 'Edit']
    ]" />

    <x-dynamic-heading title="Edit Other Income" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Income Details</h2>
            <p class="text-sm text-gray-600">Update the information below to modify this income record.</p>
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

        <form method="POST" action="{{ route('other-incomes.update', $otherIncome) }}" enctype="multipart/form-data" id="editOtherIncomeForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Income Date -->
                <div>
                    <x-input-label for="income_date">Income Date <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="income_date" name="income_date" type="date" class="mt-1 block w-full"
                        :value="old('income_date', $otherIncome->income_date->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('income_date')" class="mt-1" />
                </div>

                <!-- Amount -->
                <div>
                    <x-input-label for="amount">Amount <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="amount" name="amount" type="number" step="1" min="1" class="mt-1 block w-full"
                        :value="old('amount', round($otherIncome->amount))" required placeholder="0" />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                <!-- Bank Account -->
                <div>
                    <x-input-label for="bank_id">Bank Account <span class="text-red-600">*</span></x-input-label>
                    <select id="bank_id" name="bank_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Bank Account</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ (old('bank_id', $otherIncome->bank_id) == $bank->id) ? 'selected' : '' }}>
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
                            <option value="{{ $accountId }}" {{ (old('chart_of_account_id', $otherIncome->chart_of_account_id) == $accountId) ? 'selected' : '' }}>
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
                        placeholder="Describe the source of income">{{ old('description', $otherIncome->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <!-- Existing Attachments -->
                @if($otherIncome->attachments->count() > 0)
                <div class="md:col-span-2">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900">Existing Attachments</h3>
                            <p class="text-xs text-gray-500">Current files attached to this income record</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($otherIncome->attachments as $attachment)
                                <div class="bg-white rounded-lg p-3 flex flex-col border">
                                    <div class="flex items-center mb-1">
                                        @if($attachment->isImage())
                                            <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        @endif
                                        <span class="text-sm font-medium text-gray-900 truncate flex-1" title="{{ $attachment->file_name }}">
                                            {{ $attachment->file_name }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mb-2">
                                        {{ $attachment->file_size_formatted }}
                                    </div>
                                    <div class="flex items-center justify-between mt-auto">
                                        <a href="{{ route('other-incomes.attachments.download', $attachment) }}" 
                                            class="text-sm text-indigo-600 hover:text-indigo-900">
                                            Download
                                        </a>
                                        <button type="button" onclick="deleteAttachment({{ $attachment->id }})" 
                                            class="text-sm text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- New Attachments -->
                <div class="md:col-span-2">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900">Add New Attachments</h3>
                            <p class="text-xs text-gray-500">Upload additional documents (PDF, Word, Images)</p>
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
                    Update Income
                </button>
                <a href="{{ route('other-incomes.show', $otherIncome) }}"
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

        function deleteAttachment(id) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                fetch(`/other-incomes/attachments/${id}`, {
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
                const form = document.getElementById('editOtherIncomeForm');

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