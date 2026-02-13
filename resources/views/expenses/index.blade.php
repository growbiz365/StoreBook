<x-app-layout>
    @section('title', 'Expenses List - Expense Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/expenses/dashboard', 'label' => 'Expense dashboard'], ['url' => '#', 'label' => 'Expenses']]" />

    <x-dynamic-heading title="Expenses" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('expenses.index') }}" placeholder="Search by expense head, bank, or details..." />
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto flex gap-2">
                @can('create expenses')
                <x-button href="{{ route('expenses.create') }}">Add Expense</x-button>
                @endcan
            </div>
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif

        <x-table-wrapper>
        <thead class="bg-gray-50">
            <tr>
                <x-table-header>#</x-table-header>
                <x-table-header>Date</x-table-header>
                <x-table-header>Expense Head</x-table-header>
                <x-table-header>Bank</x-table-header>
                <x-table-header>Amount</x-table-header>
                <x-table-header>Created By</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr
                    onclick="window.location.href='{{ route('expenses.show', $expense) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                    title="Click to view expense"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>@businessDate($expense->date_added)</x-table-cell>
                    <x-table-cell class="font-medium text-gray-900">{{ $expense->expenseHead->expense_head }}</x-table-cell>
                    <x-table-cell>{{ strtoupper($expense->bank->chartOfAccount->name ?? $expense->bank->account_name) }}</x-table-cell>
                    <x-table-cell>@currency($expense->amount)</x-table-cell>
                    <x-table-cell>{{ $expense->user->name }}</x-table-cell>
                    <x-table-cell>
                        <div class="flex items-center space-x-3" onclick="event.stopPropagation()">
                            @can('delete expenses')
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this expense? This action cannot be undone and will reverse all ledger entries.')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </x-table-cell>
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="7" class="text-center text-gray-500 py-6">No expenses found</x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
</x-app-layout> 