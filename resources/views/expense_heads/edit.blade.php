<x-app-layout>
    @section('title', 'Edit Expense Head - Expense Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/expenses/dashboard', 'label' => 'Expense dashboard'],
        ['url' => route('expense-heads.index'), 'label' => 'Expense Heads'],
        ['url' => '#', 'label' => 'Edit']
    ]" />

    <x-dynamic-heading title="Edit Expense Head" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Expense Head Details</h2>
            <p class="text-sm text-gray-600">Update the expense head details. This will also update the linked chart of account entry.</p>
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

        <form method="POST" action="{{ route('expense-heads.update', $expenseHead) }}">
            @csrf
            @method('PUT')

            <!-- Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Expense Head Name -->
                <div>
                    <x-input-label for="expense_head">Expense Head Name <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="expense_head" name="expense_head" type="text" class="mt-1 block w-full"
                        :value="old('expense_head', $expenseHead->expense_head)" placeholder="e.g., Office Supplies, Travel Expenses, etc." required />
                    <x-input-error :messages="$errors->get('expense_head')" class="mt-1" />
                </div>
            </div>

            <!-- Chart Account Info -->
            @if($expenseHead->chartOfAccount)
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Linked Chart Account</h3>
                    <p class="text-sm text-gray-600">
                        <strong>Code:</strong> {{ $expenseHead->chartOfAccount->code }}<br>
                        <strong>Name:</strong> {{ $expenseHead->chartOfAccount->name }}<br>
                        <strong>Type:</strong> {{ ucfirst($expenseHead->chartOfAccount->type) }}
                    </p>
                </div>
            @else
                <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-900 mb-2">No Chart Account Linked</h3>
                    <p class="text-sm text-yellow-700">This expense head is not linked to a chart of account.</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-start gap-4 mt-8 border-t pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Update Expense Head
                </button>
                <a href="{{ route('expense-heads.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout> 