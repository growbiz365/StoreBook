<x-app-layout>
    @section('title', 'Create Expense Head - Expense Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],  
        ['url' => '/expenses/dashboard', 'label' => 'Expense dashboard'],
        ['url' => route('expense-heads.index'), 'label' => 'Expense Heads'],
        ['url' => '#', 'label' => 'Create']
    ]" />

    <x-dynamic-heading title="Create New Expense Head" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Expense Head Details</h2>
            <p class="text-sm text-gray-600">Fill in the information below to create a new expense head. This will automatically create a linked chart of account entry under Operating Expenses.</p>
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

        <form method="POST" action="{{ route('expense-heads.store') }}">
            @csrf

            <!-- Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Expense Head Name -->
                <div>
                    <x-input-label for="expense_head">Expense Head Name <span class="text-red-600">*</span></x-input-label>
                    <x-text-input id="expense_head" name="expense_head" type="text" class="mt-1 block w-full"
                        :value="old('expense_head')" placeholder="e.g., Office Supplies, Travel Expenses, etc." required />
                    <x-input-error :messages="$errors->get('expense_head')" class="mt-1" />
                    <p class="mt-1 text-sm text-gray-500">This will create a chart of account entry under Operating Expenses (4300).</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-start gap-4 mt-8 border-t pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Create Expense Head
                </button>
                <a href="{{ route('expense-heads.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout> 