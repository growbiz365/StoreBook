<x-app-layout>
    @section('title', 'Bank Accounts List - Bank Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/bank-management', 'label' => 'Bank Management'], ['url' => '#', 'label' => 'Bank Accounts']]" />

    <x-dynamic-heading title="Bank & Cash Accounts" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <!-- Filters & Search -->
            <div class="flex gap-4">
                <x-search-form action="{{ route('banks.index') }}" placeholder="Search by account or bank name..." />
                
                <select name="type"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="window.location.href='{{ route('banks.index') }}?type='+this.value+'&status={{ request('status') }}&search={{ request('search') }}'">
                    <option value="">All Accounts</option>
                    <option value="bank" {{ request('type') === 'bank' ? 'selected' : '' }}>Bank Accounts</option>
                    <option value="cash" {{ request('type') === 'cash' ? 'selected' : '' }}>Cash Accounts</option>
                </select>
                
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    onchange="window.location.href='{{ route('banks.index') }}?status='+this.value+'&type={{ request('type') }}&search={{ request('search') }}'">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Add New Button -->
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                @can('create banks')
                <x-button href="{{ route('banks.create') }}">Add New Account</x-button>
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
            <x-table-header>Type</x-table-header>
            <x-table-header>Account Name</x-table-header>
            <x-table-header>Bank</x-table-header>
            <x-table-header>Code</x-table-header>
            <x-table-header>Opening Balance</x-table-header>
            <x-table-header>Current Balance</x-table-header>
            <x-table-header>Status</x-table-header>
        </tr>
    </thead>
    <tbody>
        @forelse($banks as $bank)
            <tr
                onclick="window.location.href='{{ route('banks.edit', $bank) }}'"
                class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
            >
                <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                <x-table-cell>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $bank->account_type === 'bank' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($bank->account_type) }}
                    </span>
                </x-table-cell>
                <x-table-cell class="font-medium text-gray-900">{{ strtoupper($bank->chartOfAccount->name ?? $bank->account_name) }}</x-table-cell>
                <x-table-cell>{{ $bank->bank_name ?: '-' }}</x-table-cell>
                <x-table-cell>{{ $bank->chartOfAccount->code ?? '-' }}</x-table-cell>
                <x-table-cell>
                    {{ number_format(round($bank->opening_balance ?? 0), 0) }}
                </x-table-cell>
                <x-table-cell>
                    @php
                        $balance = $bank->ledgerEntries->sum('deposit_amount') - $bank->ledgerEntries->sum('withdrawal_amount');
                    @endphp
                    <span class="font-semibold {{ $balance < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format(round($balance), 0) }}
                    </span>
                </x-table-cell>
                <x-table-cell>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bank->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $bank->status == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </x-table-cell>
            </tr>
        @empty
            <tr>
                <x-table-cell colspan="8" class="text-center text-gray-500 py-6">
                    No bank or cash accounts found.
                </x-table-cell>
            </tr>
        @endforelse
    </tbody>
</x-table-wrapper>


    <div class="mt-4">
        {{ $banks->links() }}
    </div>
</x-app-layout>
