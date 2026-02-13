<x-app-layout>
    @section('title', 'Other Incomes List - Income Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '#', 'label' => 'Other Incomes']
    ]" />

    <x-dynamic-heading title="Other Incomes" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('other-incomes.index') }}" placeholder="Search by description or amount..." />
            
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                @can('create other incomes')
                <x-button href="{{ route('other-incomes.create') }}">Add Other Income</x-button>
                @endcan
                @can('module', 'view income heads')
                <x-button href="{{ route('income-heads.index') }}" variant="secondary">Manage Income Heads</x-button>
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
                <x-table-header>Description</x-table-header>
                <x-table-header>Bank Account</x-table-header>
                <x-table-header>Income Account</x-table-header>
                <x-table-header>Amount</x-table-header>
            </tr>
        </thead>
        <tbody>
            @forelse($otherIncomes as $income)
                <tr
                    onclick="window.location.href='{{ route('other-incomes.show', $income) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                    title="Click to view income details"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>@businessDate($income->income_date)</x-table-cell>
                    <x-table-cell class="font-medium text-gray-900">{{ Str::limit($income->description, 50) }}</x-table-cell>
                    <x-table-cell>{{ strtoupper($income->bank->chartOfAccount->name ?? $income->bank->account_name) }}</x-table-cell>
                    <x-table-cell>{{ $income->chartOfAccount->name }}</x-table-cell>
                    <x-table-cell>
                        <span class="font-semibold text-green-600">
                            @currency($income->amount)
                        </span>
                    </x-table-cell>
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="6" class="text-center text-gray-500 py-6">
                        No other income records found.
                    </x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $otherIncomes->links() }}
    </div>
</x-app-layout>