<x-app-layout>
    @section('title', 'Parties List - Party Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/party-management', 'label' => 'Party Management'],['url' => '/parties', 'label' => 'Parties']]" />

    <x-dynamic-heading title="Parties" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex gap-4">
                <x-search-form action="{{ route('parties.index') }}" placeholder="Search by name, phone, or NTN..." />
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    onchange="window.location.href='{{ route('parties.index') }}?status='+this.value+'&search={{ request('search') }}'">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('parties.create') }}">Add Party</x-button>
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
                <x-table-header>Name</x-table-header>
                <x-table-header>Opening Balance</x-table-header>
                <x-table-header>Opening Type</x-table-header>
                <x-table-header>Status</x-table-header>
            </tr>
        </thead>
        <tbody>
            @forelse($parties as $party)
                <tr
                    onclick="window.location.href='{{ route('parties.edit', $party) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                    title="Click to edit party"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>
                        <div>
                            <div>{{ $party->name }}</div>
                            <div class="text-sm text-gray-500">CNIC: {{ $party->cnic ?? 'N/A' }}</div>
                        </div>
                    </x-table-cell>
                    <x-table-cell>{{ number_format(round($party->opening_balance), 0) }}</x-table-cell>
                    <x-table-cell>{{ ucfirst($party->opening_type ?? '-') }}</x-table-cell>
                    <x-table-cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $party->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $party->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </x-table-cell>
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="6" class="text-center text-gray-500">No parties found</x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $parties->links() }}
    </div>
</x-app-layout> 