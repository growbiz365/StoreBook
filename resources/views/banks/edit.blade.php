<x-app-layout>
    @section('title', 'Edit Bank Account - Bank Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
       ['url' => '/bank-management', 'label' => 'Bank Management'],
        ['url' => '/banks', 'label' => 'Bank Accounts'],
        ['url' => '#', 'label' => 'Edit']
    ]" />

    <x-dynamic-heading title="Edit Account" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Account Details</h2>
            <p class="text-sm text-gray-600">Update the account information below.</p>
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

        <form method="POST" action="{{ route('banks.update', $bank) }}">
            @csrf
            @method('PUT')

            <!-- Account Type Display -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Account Type</label>
                <div class="inline-flex items-center px-4 py-2 rounded-md {{ $bank->account_type === 'bank' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                    <span class="text-lg mr-2">{{ $bank->account_type === 'bank' ? '🏦' : '💵' }}</span>
                    <span class="font-medium">{{ ucfirst($bank->account_type) }} Account</span>
                </div>
                <input type="hidden" name="account_type" value="{{ $bank->account_type }}">
                <x-input-error :messages="$errors->get('account_type')" class="mt-1" />
            </div>

            <!-- Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Name -->
                <div>
                    <x-input-label for="account_name">Account Name <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full"
                        :value="old('account_name', $bank->account_name)" placeholder="e.g. Main Office Cash" required />
                    <x-input-error :messages="$errors->get('account_name')" class="mt-1" />
                </div>

                <!-- Bank Name (only if bank) -->
                @if($bank->account_type === 'bank')
                    <div>
                        <x-input-label for="bank_name">Bank Name <span class="text-red-600">*</span></x-input-label>
                        <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full"
                            :value="old('bank_name', $bank->bank_name)" placeholder="e.g. HBL, Meezan, etc." required />
                        <x-input-error :messages="$errors->get('bank_name')" class="mt-1" />
                    </div>
                @endif

                <!-- Opening Balance -->
                <div>
                    <x-input-label for="opening_balance">Opening Balance</x-input-label>
                    <x-text-input id="opening_balance" name="opening_balance" type="number" step="1" class="mt-1 block w-full"
                        :value="old('opening_balance', round($bank->opening_balance))" placeholder="0" />
                    <x-input-error :messages="$errors->get('opening_balance')" class="mt-1" />
                </div>

                <!-- Status -->
                <div>
                    <x-input-label for="status">Status <span class="text-red-600">*</span></x-input-label>
                    <select id="status" name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="1" {{ old('status', $bank->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $bank->status) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <x-input-label for="description">Description</x-input-label>
                    <textarea id="description" name="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Optional account description...">{{ old('description', $bank->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>
            </div>

            <!-- Account Summary -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Account Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Account Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $bank->chartOfAccount->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Chart of Account Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $bank->chartOfAccount->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Current Balance</label>
                        @php
                            $balance = $bank->ledgerEntries->sum('deposit_amount') - $bank->ledgerEntries->sum('withdrawal_amount');
                        @endphp
                        <p class="mt-1 text-sm {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format(round($balance), 0) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-start gap-4 mt-8 border-t pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Update Account
                </button>
                <a href="{{ route('banks.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>


</x-app-layout>

