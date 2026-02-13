<x-app-layout>
    @section('title', 'Party Transfers List - Party Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/party-management', 'label' => 'Party Management'], ['url' => '#', 'label' => 'Party Transfers']]" />

    <x-dynamic-heading title="Party Transfers" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('party-transfers.index') }}" placeholder="Search by party name or details..." />
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('party-transfers.create') }}">Add Transfer</x-button>
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
                <x-table-header>Debit (بنـــام) Party</x-table-header>
                <x-table-header>Credit (جمـــع) Party</x-table-header>
                <x-table-header>Amount</x-table-header>
                <x-table-header>Details</x-table-header>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $transfer)
                <tr
                    onclick="window.location.href='{{ route('party-transfers.show', $transfer) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                    title="Click to view transfer"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>@businessDate($transfer->date)</x-table-cell>
                    <x-table-cell>{{ $transfer->debitParty->name }}</x-table-cell>
                    <x-table-cell>{{ $transfer->creditParty->name }}</x-table-cell>
                    <x-table-cell>@currency($transfer->transfer_amount)</x-table-cell>
                    <x-table-cell>{{ $transfer->details ?? 'N/A' }}</x-table-cell>
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="6" class="text-center text-gray-500 py-6">No transfers found</x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $transfers->links() }}
    </div>
</x-app-layout> 