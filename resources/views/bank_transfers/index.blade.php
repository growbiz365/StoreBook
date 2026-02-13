<x-app-layout>
    @section('title', 'Bank Transfers List - Bank Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/bank-management', 'label' => 'Bank Management'], ['url' => '#', 'label' => 'Bank Transfers']]" />

    <x-dynamic-heading title="Bank Transfers" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('bank-transfers.index') }}" placeholder="Search by account name or details..." />
            @can('create bank-transfers')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('bank-transfers.create') }}">Add Transfer</x-button>
            </div>
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

    <div class="mx-4 sm:mx-6 lg:mx-8">
        <x-table-wrapper>
        <thead class="bg-gray-50">
            <tr>
                <x-table-header>#</x-table-header>
                <x-table-header>Date</x-table-header>
                <x-table-header>From Account</x-table-header>
                <x-table-header>To Account</x-table-header>
                <x-table-header>Amount</x-table-header>
                <x-table-header>Details</x-table-header>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $transfer)
                <tr
                    onclick="window.location.href='{{ route('bank-transfers.show', $transfer) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                    title="Click to view transfer"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>@businessDate($transfer->transfer_date)</x-table-cell>
                    <x-table-cell>{{ strtoupper($transfer->fromAccount->chartOfAccount->name ?? $transfer->fromAccount->account_name) }}</x-table-cell>
                    <x-table-cell>{{ strtoupper($transfer->toAccount->chartOfAccount->name ?? $transfer->toAccount->account_name) }}</x-table-cell>
                    <x-table-cell>@currency($transfer->amount)</x-table-cell>
                    <x-table-cell>{{ $transfer->details ?? 'N/A' }}</x-table-cell>
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="6" class="text-center text-gray-500 py-6">No transfers found</x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>
    </div>

    <div class="mt-4 mx-4 sm:mx-6 lg:mx-8">
        {{ $transfers->links() }}
    </div>
</x-app-layout>