<x-app-layout>
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => route('chart-of-accounts.index'), 'label' => 'Chart of Accounts'],
        ['url' => '#', 'label' => 'Edit Account'],
    ]" />

    <div class="">
        <div class="flex justify-between items-center">
            <x-dynamic-heading title="Edit Account" />
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                <p class="mt-1 text-sm text-gray-500">Update the account details below.</p>
            </div>

            <form action="{{ route('chart-of-accounts.update', $chartOfAccount) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information Section -->
                    <div class="space-y-6">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">
                                Account Code <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="code" id="code" value="{{ old('code', $chartOfAccount->code) }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter account code">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Account Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name', $chartOfAccount->name) }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter account name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Account Type <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select name="type" id="type"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select Account Type</option>
                                    @foreach(['asset' => 'Asset', 'liability' => 'Liability', 'income' => 'Income', 'expense' => 'Expense', 'equity' => 'Equity'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('type', $chartOfAccount->type) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Account</label>
                            <div class="mt-1">
                                <select name="parent_id" id="parent_id"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">No Parent (Top Level)</option>
                                    @foreach($parentAccounts as $parentAccount)
                                        <option value="{{ $parentAccount->id }}"
                                            {{ old('parent_id', $chartOfAccount->parent_id) == $parentAccount->id ? 'selected' : '' }}>
                                            {{ $parentAccount->code }} - {{ $parentAccount->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea name="description" id="description" rows="4"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter account description">{{ old('description', $chartOfAccount->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center mt-4">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $chartOfAccount->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active Account
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Bank Account Details Section -->
                <div id="bankAccountFields" class="hidden border-t border-gray-200 pt-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Bank Account Details</h3>
                        <p class="mt-1 text-sm text-gray-500">Additional information required for bank accounts.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $chartOfAccount->bank_name) }}"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter bank name">
                            @error('bank_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                            <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $chartOfAccount->account_number) }}"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter account number">
                            @error('account_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="branch_code" class="block text-sm font-medium text-gray-700">Branch Code</label>
                            <input type="text" name="branch_code" id="branch_code" value="{{ old('branch_code', $chartOfAccount->branch_code) }}"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter branch code">
                            @error('branch_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="iban" class="block text-sm font-medium text-gray-700">IBAN</label>
                            <input type="text" name="iban" id="iban" value="{{ old('iban', $chartOfAccount->iban) }}"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter IBAN">
                            @error('iban')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="swift_code" class="block text-sm font-medium text-gray-700">SWIFT Code</label>
                            <input type="text" name="swift_code" id="swift_code" value="{{ old('swift_code', $chartOfAccount->swift_code) }}"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter SWIFT code">
                            @error('swift_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label for="bank_address" class="block text-sm font-medium text-gray-700">Bank Address</label>
                            <textarea name="bank_address" id="bank_address" rows="3"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter bank address">{{ old('bank_address', $chartOfAccount->bank_address) }}</textarea>
                            @error('bank_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('chart-of-accounts.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const parentAccountSelect = document.getElementById('parent_id');
            const bankAccountFields = document.getElementById('bankAccountFields');

            function toggleBankFields() {
                const selectedOption = parentAccountSelect.options[parentAccountSelect.selectedIndex];
                const isBankAccount = selectedOption.text.toLowerCase().includes('bank account');
                bankAccountFields.style.display = isBankAccount ? 'block' : 'none';
            }

            parentAccountSelect.addEventListener('change', toggleBankFields);
            toggleBankFields();
        });
    </script>
</x-app-layout>
