<x-app-layout>
    @section('title', 'Create Bank Account - Bank Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/bank-management', 'label' => 'Bank Management'],
        ['url' => '/banks', 'label' => 'Bank Accounts'],
        ['url' => '#', 'label' => 'Create']
    ]" />

    <x-dynamic-heading title="Create New Account" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Account Details</h2>
            <p class="text-sm text-gray-600">Fill in the information below to create a bank or cash account.</p>
        </div>

        @php
            $fieldErrors = ['account_name', 'bank_name', 'account_type', 'opening_balance', 'status', 'description'];
            $hasNonFieldErrors = $errors->any() && !empty(array_diff($errors->keys(), $fieldErrors));
        @endphp
        
        @if ($hasNonFieldErrors)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops! Something went wrong.</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('banks.store') }}">
            @csrf

            <!-- Account Type Toggle -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Account Type</label>
                <div class="flex gap-4">
                    <button type="button" onclick="setAccountType('bank')" id="bankButton"
                        class="flex-1 py-4 px-6 rounded-lg border-2 text-center account-type-btn transition-all duration-150">
                        🏦 <span class="ml-2 font-medium">Bank Account</span>
                    </button>
                    <button type="button" onclick="setAccountType('cash')" id="cashButton"
                        class="flex-1 py-4 px-6 rounded-lg border-2 text-center account-type-btn transition-all duration-150">
                        💵 <span class="ml-2 font-medium">Cash Account</span>
                    </button>
                </div>
                <input type="hidden" name="account_type" id="accountType" value="bank">
                <x-input-error :messages="$errors->get('account_type')" class="mt-1" />
            </div>

            <!-- Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Name -->
                <div>
                    <x-input-label for="account_name">Account Name <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full"
                        :value="old('account_name')" required />
                    <x-input-error :messages="$errors->get('account_name')" class="mt-1" />
                </div>

                <!-- Bank Name -->
                <div id="bankNameField">
                    <x-input-label for="bank_name">Bank Name <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full"
                        :value="old('bank_name')" placeholder="e.g. HBL, Meezan, etc." />
                    <x-input-error :messages="$errors->get('bank_name')" class="mt-1" />
                </div>

                <!-- Opening Balance -->
                <div>
                    <x-input-label for="opening_balance">Opening Balance</x-input-label>
                    <x-text-input id="opening_balance" name="opening_balance" type="number" step="1" class="mt-1 block w-full"
                        :value="old('opening_balance')" placeholder="0" />
                    <x-input-error :messages="$errors->get('opening_balance')" class="mt-1" />
                </div>

                <!-- Status -->
                <div>
                    <x-input-label for="status">Status <span class="text-red-600">*</span></x-input-label>
                    <select id="status" name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <x-input-label for="description">Description</x-input-label>
                    <textarea id="description" name="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Optional account description...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-start gap-4 mt-8 border-t pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Create Account
                </button>
                <a href="{{ route('banks.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

<style>
    .account-type-btn {
        border-color: #d1d5db;
        background-color: white;
        color: #374151;
    }
    .account-type-btn.active {
        border-color: #6366f1;
        background-color: #eef2ff;
        color: #1e3a8a;
    }
</style>

<script>
    function setAccountType(type) {
        document.getElementById('accountType').value = type;

        const bankBtn = document.getElementById('bankButton');
        const cashBtn = document.getElementById('cashButton');
        const bankField = document.getElementById('bankNameField');
        const bankInput = document.getElementById('bank_name');

        if (type === 'bank') {
            bankBtn.classList.add('active');
            cashBtn.classList.remove('active');
            bankField.style.display = 'block';
            bankInput.required = true;
        } else {
            cashBtn.classList.add('active');
            bankBtn.classList.remove('active');
            bankField.style.display = 'none';
            bankInput.required = false;
            bankInput.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const defaultType = document.getElementById('accountType').value || 'bank';
        setAccountType(defaultType);
    });
</script>
