<x-app-layout>
    @section('title', 'Expense Heads List - Expense Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/expenses/dashboard', 'label' => 'Expense dashboard'], ['url' => '#', 'label' => 'Expense Heads']]" />

    <x-dynamic-heading title="Expense Heads" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('expense-heads.index') }}" placeholder="Search by expense head name..." />
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                @can('create expense heads')
                <x-button href="{{ route('expense-heads.create') }}">Add Expense Head</x-button>
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
                <x-table-header>Expense Head</x-table-header>
                <x-table-header>Chart Account</x-table-header>
                <x-table-header>Created At</x-table-header>
                
            </tr>
        </thead>
        <tbody>
            @forelse($expenseHeads as $expenseHead)
                <tr
                    onclick="window.location.href='{{ route('expense-heads.edit', $expenseHead) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell class="font-medium text-gray-900">{{ $expenseHead->expense_head }}</x-table-cell>
                    <x-table-cell>
                        @if($expenseHead->chartOfAccount)
                            {{ $expenseHead->chartOfAccount->code }} - {{ $expenseHead->chartOfAccount->name }}
                        @else
                            <span class="text-gray-500">Not linked</span>
                        @endif
                    </x-table-cell>
                    <x-table-cell>{{ $expenseHead->created_at->format('d-m-Y') }}</x-table-cell>
                    
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="5" class="text-center text-gray-500 py-6">
                        No expense heads found.
                    </x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $expenseHeads->links() }}
    </div>
</x-app-layout> 