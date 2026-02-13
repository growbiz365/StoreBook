<x-app-layout>
    @section('title', 'General Vouchers List - Finance Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/finance', 'label' => 'Finance'], ['url' => '#', 'label' => 'General Vouchers']]" />

    <x-dynamic-heading title="General Vouchers" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                    <x-search-form action="{{ route('general-vouchers.index') }}" placeholder="Search by account name or details..." />
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                @can('create general vouchers')
                <x-button href="{{ route('general-vouchers.create') }}">Add Voucher</x-button>
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
                <x-table-header>Bank Account</x-table-header>
                <x-table-header>Party</x-table-header>
                <x-table-header>Type</x-table-header>
                <x-table-header>Amount</x-table-header>
                <x-table-header>Details</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @forelse($vouchers as $voucher)
                <tr
                    onclick="window.location.href='{{ route('general-vouchers.show', $voucher) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                    title="Click to view voucher"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>@businessDate($voucher->entry_date)</x-table-cell>
                    <x-table-cell>{{ strtoupper($voucher->bank->chartOfAccount->name ?? $voucher->bank->account_name) }}</x-table-cell>
                    <x-table-cell>{{ $voucher->party->name }}</x-table-cell>
                    <x-table-cell>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $voucher->entry_type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($voucher->entry_type) }}
                        </span>
                    </x-table-cell>
                    <x-table-cell>@currency($voucher->amount)</x-table-cell>
                    <x-table-cell>{{ $voucher->details ?? 'N/A' }}</x-table-cell>
                    <x-table-cell>
                        <div class="flex items-center space-x-3" onclick="event.stopPropagation()">
                            @can('delete general vouchers')
                            <form action="{{ route('general-vouchers.destroy', $voucher->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this general voucher? This action cannot be undone and will reverse all ledger entries.')">
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
                    <x-table-cell colspan="8" class="text-center text-gray-500 py-6">No vouchers found</x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $vouchers->links() }}
    </div>
</x-app-layout>